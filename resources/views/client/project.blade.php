@extends('layouts.client')
@section('title', $project->name . ' - YourOrd')
@section('content')
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">{{ $project->name }}</h1>
        <h2 class="text-2xl font-semibold mb-4 text-gray-700">Описание</h2>
        <p class="mb-6">{{ $project->description ?? 'Нет описания' }}</p>

        <h2 class="text-2xl font-semibold mb-4 text-gray-700">Категории и услуги</h2>
        @foreach ($categories as $category)
            <h3 class="text-xl font-medium mb-2 text-gray-600">{{ $category->name }}</h3>
            @foreach ($services->where('category_id', $category->id) as $service)
                <p>- {{ $service->name }} ({{ $service->duration }} мин, {{ $service->price }} руб.)</p>
            @endforeach
        @endforeach

        <h2 class="text-2xl font-semibold mb-4 text-gray-700">Записаться</h2>
        <form method="POST" action="{{ route('client.project.booking', $project->slug) }}" class="space-y-4">
            @csrf
            <input type="hidden" name="project_id" value="{{ $project->id }}">
            <div>
                <label for="service_id" class="block text-sm font-medium text-gray-700">Услуга</label>
                <select name="service_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" @change="updateSlots($event)">
                    @foreach ($services as $service)
                        <option value="{{ $service->id }}" data-slots="{{ json_encode($slotsByService[$service->id]) }}">{{ $service->name }} ({{ $service->duration }} мин, {{ $service->price }} руб.)</option>
                    @endforeach
                </select>
                @error('service_id')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="date" class="block text-sm font-medium text-gray-700">Дата</label>
                <input type="date" name="date" value="{{ old('date', $date) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" @change="updateSlotsByDate($event)">
                @error('date')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="slot_start" class="block text-sm font-medium text-gray-700">Время</label>
                <select name="slot_start" id="slot_start" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @if (empty($slotsByService))
                        <option value="">Нет доступных слотов</option>
                    @else
                        @foreach ($slotsByService[$services->first()->id] ?? [] as $slot)
                            <option value="{{ $slot['start'] }}">{{ $slot['start'] }} - {{ $slot['end'] }}</option>
                        @endforeach
                    @endif
                </select>
                @error('slot_start')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Записаться</button>
        </form>

        <h2 class="text-2xl font-semibold mb-4 mt-6 text-gray-700">Добавить в избранное</h2>
        <form method="POST" action="{{ route('client.project.favorite', $project->slug) }}">
            @csrf
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                {{ auth('client')->user()->projects()->where('project_id', $project->id)->exists() ? 'Удалить из избранного' : 'Добавить в избранное' }}
            </button>
        </form>
    </div>

    @push('scripts')
        <script>
            function updateSlots(event) {
                const slots = JSON.parse(event.target.selectedOptions[0].dataset.slots);
                const slotSelect = document.getElementById('slot_start');
                slotSelect.innerHTML = '';
                if (slots.length === 0) {
                    slotSelect.innerHTML = '<option value="">Нет доступных слотов</option>';
                } else {
                    slots.forEach(slot => {
                        const option = document.createElement('option');
                        option.value = slot.start;
                        option.text = `${slot.start} - ${slot.end}`;
                        slotSelect.appendChild(option);
                    });
                }
            }

            function updateSlotsByDate(event) {
                const date = event.target.value;
                window.location.href = '{{ route('client.project', $project->slug) }}?date=' + date;
            }
        </script>
    @endpush
@endsection

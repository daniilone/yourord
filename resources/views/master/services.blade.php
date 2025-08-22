@extends('layouts.master')

@section('title', 'Услуги - YourOrd')

@section('header', 'Услуги мастера')

@push('styles')
    <style>
        .modal {
            transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
            z-index: 9999;
        }
        .modal-content {
            transform: scale(0.95);
            transition: transform 0.2s ease-in-out;
        }
        .modal:not(.hidden) .modal-content {
            transform: scale(1);
        }
    </style>
@endpush

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Ваши услуги</h2>

        <!-- Форма для создания новой услуги -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <form method="POST" action="{{ route('master.services.create') }}">
                @csrf
                <div class="mb-4">
                    <label for="project_id" class="block text-sm font-medium text-gray-700">Проект</label>
                    <select name="project_id" id="project_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        @foreach (App\Models\Project::where('master_id', Auth::guard('master')->id())->get() as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                    @error('project_id')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="category_id" class="block text-sm font-medium text-gray-700">Категория</label>
                    <select name="category_id" id="category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        @foreach (App\Models\Category::whereHas('project', function($query) {
                            $query->where('master_id', Auth::guard('master')->id());
                        })->get() as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Название услуги</label>
                    <input type="text" name="name" id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                    @error('name')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="duration" class="block text-sm font-medium text-gray-700">Длительность (минуты)</label>
                    <input type="number" name="duration" id="duration" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" min="1" required>
                    @error('duration')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="price" class="block text-sm font-medium text-gray-700">Цена (₽)</label>
                    <input type="number" name="price" id="price" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" min="0" step="0.01" required>
                    @error('price')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Добавить услугу</button>
                </div>
            </form>
        </div>

        <!-- Таблица услуг -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            @if ($services->isEmpty())
                <p class="text-gray-600">Услуги отсутствуют.</p>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Название</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Проект</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Категория</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Длительность</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Цена</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($services as $service)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $service->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $service->project->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $service->category->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $service->duration }} мин</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($service->price, 2) }} ₽</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <button type="button" class="edit-service text-indigo-600 hover:text-indigo-800 mr-2" data-id="{{ $service->id }}" data-project-id="{{ $service->project_id }}" data-category-id="{{ $service->category_id }}" data-name="{{ $service->name }}" data-duration="{{ $service->duration }}" data-price="{{ $service->price }}">Редактировать</button>
                                <form action="{{ route('master.services.destroy', $service->id) }}" method="POST" class="inline" onsubmit="return confirm('Вы уверены, что хотите удалить услугу?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Удалить</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <!-- Пагинация -->
                <div class="mt-4">
                    {{ $services->links() }}
                </div>
            @endif
        </div>

        <!-- Модальное окно для редактирования услуги -->
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full bg-black bg-opacity-50 flex items-center justify-center" id="editServiceModal" tabindex="-1">
            <div class="modal-content bg-white rounded-lg w-full max-w-md p-6">
                <form id="editServiceForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="service_id" id="edit_service_id">
                    <div class="mb-4">
                        <label for="edit_project_id" class="block text-sm font-medium text-gray-700">Проект</label>
                        <select name="project_id" id="edit_project_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                            @foreach (App\Models\Project::where('master_id', Auth::guard('master')->id())->get() as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                        @error('project_id')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="edit_category_id" class="block text-sm font-medium text-gray-700">Категория</label>
                        <select name="category_id" id="edit_category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                            @foreach (App\Models\Category::whereHas('project', function($query) {
                                $query->where('master_id', Auth::guard('master')->id());
                            })->get() as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="edit_name" class="block text-sm font-medium text-gray-700">Название услуги</label>
                        <input type="text" name="name" id="edit_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        @error('name')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="edit_duration" class="block text-sm font-medium text-gray-700">Длительность (минуты)</label>
                        <input type="number" name="duration" id="edit_duration" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" min="1" required>
                        @error('duration')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="edit_price" class="block text-sm font-medium text-gray-700">Цена (₽)</label>
                        <input type="number" name="price" id="edit_price" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" min="0" step="0.01" required>
                        @error('price')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" id="cancel_edit_modal" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Отменить</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const modal = document.getElementById('editServiceModal');
                const form = document.getElementById('editServiceForm');
                const cancelButton = document.getElementById('cancel_edit_modal');

                document.querySelectorAll('.edit-service').forEach(button => {
                    button.addEventListener('click', function () {
                        const serviceId = this.getAttribute('data-id');
                        form.action = '{{ route("master.services.update", ":id") }}'.replace(':id', serviceId);
                        document.getElementById('edit_service_id').value = serviceId;
                        document.getElementById('edit_project_id').value = this.getAttribute('data-project-id');
                        document.getElementById('edit_category_id').value = this.getAttribute('data-category-id');
                        document.getElementById('edit_name').value = this.getAttribute('data-name');
                        document.getElementById('edit_duration').value = this.getAttribute('data-duration');
                        document.getElementById('edit_price').value = this.getAttribute('data-price');
                        modal.classList.remove('hidden');
                    });
                });

                cancelButton.addEventListener('click', function () {
                    modal.classList.add('hidden');
                });

                form.addEventListener('submit', function (e) {
                    console.log('Edit form data:', new FormData(form));
                });
            });
        </script>
    @endpush
@endsection

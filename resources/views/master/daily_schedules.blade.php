@extends('layouts.master')

@section('title', 'Расписание - YourOrd')

@section('header', 'Расписание мастера')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        #calendar {
            max-width: 1000px;
            margin: 0 auto;
            padding: 1rem;
        }
        .fc-daygrid-day-number {
            font-size: 1rem;
            font-weight: 600;
        }
        .fc-event {
            cursor: pointer;
            border-radius: 0.25rem;
            padding: 0.25rem;
        }
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
        .break-row {
            display: flex;
            gap: 1rem;
            align-items: center;
            margin-bottom: 1rem;
        }
        .break-row input {
            width: 120px;
        }
    </style>
@endpush

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div id="calendar" class="bg-white rounded-lg shadow-lg"></div>

        <!-- Модальное окно для создания/редактирования расписания -->
        <div class="modal fade fixed top-0 left-0 hidden w-full h-full bg-black bg-opacity-50 flex items-center justify-center" id="scheduleModal" tabindex="-1">
            <div class="modal-content bg-white rounded-lg w-full max-w-md p-6">
                <form id="scheduleForm" method="POST" action="{{ route('master.daily_schedules.create') }}">
                    @csrf
                    <input type="hidden" name="_method" id="form_method" value="POST">
                    <input type="hidden" name="schedule_id" id="modal_schedule_id">
                    <div class="mb-4">
                        <label for="modal_project_id" class="block text-sm font-medium text-gray-700">Проект</label>
                        <select name="project_id" id="modal_project_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                            @foreach (App\Models\Project::where('master_id', Auth::guard('master')->id())->get() as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                        @error('project_id')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="modal_date" class="block text-sm font-medium text-gray-700">Дата</label>
                        <input type="date" name="date" id="modal_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        @error('date')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_working_day" id="modal_is_working_day" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" checked>
                            <span class="ml-2 text-sm text-gray-700">Рабочий день</span>
                        </label>
                    </div>
                    <div id="work_time_block">
                        <div class="mb-4">
                            <label for="modal_start_time" class="block text-sm font-medium text-gray-700">Время начала</label>
                            <input type="time" name="start_time" id="modal_start_time" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" step="60">
                            @error('start_time')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="modal_end_time" class="block text-sm font-medium text-gray-700">Время окончания</label>
                            <input type="time" name="end_time" id="modal_end_time" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" step="60">
                            @error('end_time')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div id="breaks_block">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Перерывы</h3>
                            <div id="breaks_container"></div>
                            <button type="button" id="add_break" class="mt-2 text-indigo-600 hover:text-indigo-800 text-sm font-medium">+ Добавить перерыв</button>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" id="cancel_modal" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Отменить</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const calendarEl = document.getElementById('calendar');
                const modal = document.getElementById('scheduleModal');
                const form = document.getElementById('scheduleForm');
                const formMethod = document.getElementById('form_method');

                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'ru',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    events: [
                            @foreach ($schedules as $schedule)
                        {
                            title: '{{ $schedule->is_working_day ? "Рабочий день" : "Выходной" }}',
                            start: '{{ \Carbon\Carbon::parse($schedule->date)->format('Y-m-d') }}',
                            color: '{{ $schedule->is_working_day ? "#10b981" : "#ef4444" }}',
                            extendedProps: {
                                schedule_id: {{ $schedule->id }},
                                project_id: {{ $schedule->project_id }},
                                start_time: '{{ $schedule->start_time }}',
                                end_time: '{{ $schedule->end_time }}',
                                breaks: @json($schedule->workBreaks->map(fn($break) => ['start_time' => $break->start_time, 'end_time' => $break->end_time]))
                            }
                        },
                        @endforeach
                    ],
                    eventClick: function (info) {
                        const event = info.event;
                        form.action = '{{ route("master.daily_schedules.update", ":id") }}'.replace(':id', event.extendedProps.schedule_id);
                        formMethod.value = 'PATCH';
                        document.getElementById('modal_schedule_id').value = event.extendedProps.schedule_id;
                        document.getElementById('modal_project_id').value = event.extendedProps.project_id;
                        document.getElementById('modal_date').value = event.startStr;
                        document.getElementById('modal_is_working_day').checked = event.title === 'Рабочий день';
                        document.getElementById('modal_start_time').value = formatTime(event.extendedProps.start_time) || '';
                        document.getElementById('modal_end_time').value = formatTime(event.extendedProps.end_time) || '';
                        const breaksContainer = document.getElementById('breaks_container');
                        breaksContainer.innerHTML = '';
                        event.extendedProps.breaks.forEach((b, index) => {
                            breaksContainer.innerHTML += `
                        <div class="break-row">
                            <input type="time" name="breaks[${index}][start_time]" value="${formatTime(b.start_time)}" class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" step="60" required>
                            <input type="time" name="breaks[${index}][end_time]" value="${formatTime(b.end_time)}" class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" step="60" required>
                            <button type="button" class="text-red-600 hover:text-red-800 remove-break"><i class="fas fa-trash"></i></button>
                        </div>
                    `;
                        });
                        toggleWorkTimeBlock();
                        modal.classList.remove('hidden');
                    },
                    dateClick: function (info) {
                        form.action = '{{ route("master.daily_schedules.create") }}';
                        formMethod.value = 'POST';
                        document.getElementById('modal_schedule_id').value = '';
                        document.getElementById('modal_date').value = info.dateStr;
                        document.getElementById('modal_is_working_day').checked = true;
                        document.getElementById('modal_start_time').value = '';
                        document.getElementById('modal_end_time').value = '';
                        document.getElementById('breaks_container').innerHTML = '';
                        toggleWorkTimeBlock();
                        modal.classList.remove('hidden');
                    }
                });
                calendar.render();

                // Форматирование времени в HH:MM
                function formatTime(time) {
                    if (!time) return '';
                    // Если время содержит секунды (HH:MM:SS), обрезаем их
                    if (time.match(/^\d{2}:\d{2}:\d{2}$/)) {
                        return time.substring(0, 5); // Возвращаем HH:MM
                    }
                    return time;
                }

                // Переключение видимости блока времени
                document.getElementById('modal_is_working_day').addEventListener('change', toggleWorkTimeBlock);
                function toggleWorkTimeBlock() {
                    const isWork = document.getElementById('modal_is_working_day').checked;
                    document.getElementById('work_time_block').style.display = isWork ? 'block' : 'none';
                    document.getElementById('modal_start_time').required = isWork;
                    document.getElementById('modal_end_time').required = isWork;
                    // Устанавливаем required для полей breaks
                    const breakInputs = document.querySelectorAll('#breaks_container input[type="time"]');
                    breakInputs.forEach(input => input.required = isWork);
                }

                // Добавление перерыва
                let breakIndex = 0;
                document.getElementById('add_break').addEventListener('click', function () {
                    const container = document.getElementById('breaks_container');
                    container.innerHTML += `
                <div class="break-row">
                    <input type="time" name="breaks[${breakIndex}][start_time]" class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" step="60" required>
                    <input type="time" name="breaks[${breakIndex}][end_time]" class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" step="60" required>
                    <button type="button" class="text-red-600 hover:text-red-800 remove-break"><i class="fas fa-trash"></i></button>
                </div>
            `;
                    toggleWorkTimeBlock();
                    breakIndex++;
                });

                // Удаление перерыва
                document.addEventListener('click', function (e) {
                    if (e.target.classList.contains('remove-break') || e.target.closest('.remove-break')) {
                        e.target.closest('.break-row').remove();
                    }
                });

                // Закрытие модала
                document.getElementById('cancel_modal').addEventListener('click', function () {
                    modal.classList.add('hidden');
                });

                // Отладка отправки формы
                form.addEventListener('submit', function (e) {
                    console.log('Form data:', new FormData(form));
                });
            });
        </script>
    @endpush
@endsection

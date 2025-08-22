@extends('layouts.master')

@section('title', 'Проекты')

@section('header', 'Управление проектами')

@section('content')
<div class="bg-white shadow rounded-lg mb-6">
    <div class="px-4 py-5 sm:px-6">
        <div class="sm:flex sm:items-center justify-between">
            <div class="sm:w-0 flex-1">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Список проектов
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    Здесь вы можете управлять своими проектами и их настройками.
                </p>
            </div>
            <div class="mt-4 sm:mt-0 sm:ml-4">
                <button type="button" onclick="document.getElementById('newProjectModal').classList.remove('hidden')" 
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Новый проект
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Список проектов -->
<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <ul class="divide-y divide-gray-200">
        @forelse($projects as $project)
            <li class="hover:bg-gray-50">
                <div class="px-4 py-4 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <span class="text-indigo-600 text-xl font-medium">{{ substr($project->name, 0, 1) }}</span>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $project->name }}</h3>
                                    @if($project->description)
                                        <p class="mt-1 text-sm text-gray-500">{{ $project->description }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex space-x-2">
                            <button type="button" onclick="openEditProjectModal({{ $project->id }}, '{{ addslashes($project->name) }}', '{{ addslashes($project->description) }}', '{{ $project->slug }}')" class="text-indigo-600 hover:text-indigo-900">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                            </button>
                            <form action="#" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить этот проект?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </li>
        @empty
            <li class="px-4 py-4 sm:px-6">
                <div class="text-center text-gray-500">
                    <p>У вас пока нет проектов. Создайте свой первый проект!</p>
                </div>
            </li>
        @endforelse
    </ul>
</div>

<!-- Модальное окно создания проекта -->
<div id="newProjectModal" class="hidden fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('newProjectModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div>
                <div class="mt-3 text-center sm:mt-5">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Новый проект
                    </h3>
                    <div class="mt-4">
                        <form method="POST" action="{{ route('master.projects.create') }}">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Название проекта</label>
                                    <div class="mt-1">
                                        <input type="text" name="name" id="name" required 
                                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border"
                                            oninput="updateSlugPreview(this.value)">
                                        @error('name')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div>
                                    <label for="slug" class="block text-sm font-medium text-gray-700">Символьный код (URL)</label>
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                            {{ config('app.url') }}/p/
                                        </span>
                                        <input type="text" name="slug" id="slug" 
                                            class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border"
                                            placeholder="avto-remont"
                                            oninput="document.getElementById('slug-preview').textContent = this.value || 'avto-remont'">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Будет доступно по адресу: 
                                        <span id="slug-preview" class="font-mono">avto-remont</span>
                                    </p>
                                    @error('slug')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700">Описание (необязательно)</label>
                                    <div class="mt-1">
                                        <textarea name="description" id="description" rows="3" 
                                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border"></textarea>
                                        @error('description')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                                <button type="submit" 
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm">
                                    Создать проект
                                </button>
                                <button type="button" 
                                    onclick="document.getElementById('newProjectModal').classList.add('hidden')" 
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                                    Отмена
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно редактирования проекта -->
<div id="editProjectModal" class="hidden fixed z-10 inset-0 overflow-y-auto" aria-labelledby="edit-modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeEditProjectModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div>
                <div class="mt-3 text-center sm:mt-5">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="edit-modal-title">
                        Редактировать проект
                    </h3>
                    <div class="mt-4">
                        <form id="editProjectForm" method="POST" action="">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="project_id" id="edit_project_id">
                            <div class="space-y-4">
                                <div>
                                    <label for="edit_name" class="block text-sm font-medium text-gray-700">Название проекта</label>
                                    <div class="mt-1">
                                        <input type="text" name="name" id="edit_name" required 
                                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border"
                                            oninput="updateEditSlugPreview(this.value)">
                                        @error('name')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div>
                                    <label for="edit_slug" class="block text-sm font-medium text-gray-700">Символьный код (URL)</label>
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                            {{ config('app.url') }}/p/
                                        </span>
                                        <input type="text" name="slug" id="edit_slug" 
                                            class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border"
                                            placeholder="avto-remont"
                                            oninput="document.getElementById('edit-slug-preview').textContent = this.value || 'avto-remont'">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Будет доступно по адресу: 
                                        <span id="edit-slug-preview" class="font-mono">avto-remont</span>
                                    </p>
                                    @error('slug')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="edit_description" class="block text-sm font-medium text-gray-700">Описание (необязательно)</label>
                                    <div class="mt-1">
                                        <textarea name="description" id="edit_description" rows="3" 
                                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md p-2"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm">
                                    Сохранить изменения
                                </button>
                                <button type="button" onclick="closeEditProjectModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                                    Отмена
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Закрытие модальных окон при нажатии на Esc
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.getElementById('newProjectModal').classList.add('hidden');
            document.getElementById('editProjectModal').classList.add('hidden');
        }
    });

    // Функция для обновления предпросмотра slug при создании
    function updateSlugPreview(name) {
        if (!name) return;
        
        // Генерируем slug из названия
        let slug = name
            .toLowerCase()
            .replace(/[^\w\s-]/g, '') // Удаляем все не-слова, не-пробелы, не-дефисы
            .replace(/\s+/g, '-') // Заменяем пробелы на дефисы
            .replace(/--+/g, '-') // Удаляем двойные дефисы
            .replace(/^-+|-+$/g, ''); // Удаляем дефисы в начале и конце
            
        // Обновляем поле slug, только если оно пустое или пользователь не вносил изменений вручную
        const slugInput = document.getElementById('slug');
        if (!slugInput.value || slugInput.value === slugInput.defaultValue) {
            slugInput.value = slug;
            document.getElementById('slug-preview').textContent = slug || 'avto-remont';
        }
    }

    // Функция для обновления предпросмотра slug при редактировании
    function updateEditSlugPreview(name) {
        if (!name) return;
        
        // Генерируем slug из названия
        let slug = name
            .toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/--+/g, '-')
            .replace(/^-+|-+$/g, '');
            
        // Обновляем поле slug, только если оно пустое или пользователь не вносил изменений вручную
        const slugInput = document.getElementById('edit_slug');
        if (!slugInput.value || slugInput.value === slugInput.defaultValue) {
            slugInput.value = slug;
            document.getElementById('edit-slug-preview').textContent = slug || 'avto-remont';
        }
    }

    // Открытие модального окна редактирования
    function openEditProjectModal(id, name, description, slug) {
        document.getElementById('edit_project_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_description').value = description || '';
        document.getElementById('edit_slug').value = slug || '';
        document.getElementById('edit-slug-preview').textContent = slug || 'avto-remont';
        
        // Устанавливаем action формы
        document.getElementById('editProjectForm').action = `/master/projects/${id}`;
        
        // Показываем модальное окно
        document.getElementById('editProjectModal').classList.remove('hidden');
    }

    // Закрытие модального окна редактирования
    function closeEditProjectModal() {
        document.getElementById('editProjectModal').classList.add('hidden');
    }
</script>
@endpush

@endsection
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

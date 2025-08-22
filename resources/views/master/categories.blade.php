@extends('layouts.master')

@section('title', 'Категории')

@section('header', 'Управление категориями')

@section('content')
<!-- Navigation is handled by the master layout -->

<div class="bg-white shadow rounded-lg mb-6">
    <div class="px-4 py-5 sm:px-6">
        <div class="sm:flex sm:items-center justify-between">
            <div class="sm:w-0 flex-1">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Список категорий
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    Здесь вы можете управлять категориями ваших услуг.
                </p>
            </div>
            <div class="mt-4 sm:mt-0 sm:ml-4">
                <button type="button" onclick="document.getElementById('newCategoryModal').classList.remove('hidden')" 
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Новая категория
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Список категорий -->
<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <ul class="divide-y divide-gray-200">
        @forelse($categories as $category)
            <li class="hover:bg-gray-50">
                <div class="px-4 py-4 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <span class="text-indigo-600 text-lg font-medium">{{ substr($category->name, 0, 1) }}</span>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $category->name }}</h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Проект: {{ $category->project->name }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex space-x-2">
                            <button type="button" 
                                onclick="openEditCategoryModal({{ $category->id }}, '{{ addslashes($category->name) }}', {{ $category->project->id }})" 
                                class="text-indigo-600 hover:text-indigo-900">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                            </button>
                            <form action="{{ route('master.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить эту категорию? Услуги в этой категории не будут удалены, но перейдут в категорию \'Без категории\'')">
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
                    <p>У вас пока нет категорий. Создайте свою первую категорию!</p>
                </div>
            </li>
        @endforelse
    </ul>
</div>

<!-- Модальное окно создания категории -->
<div id="newCategoryModal" class="hidden fixed z-10 inset-0 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
             onclick="document.getElementById('newCategoryModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Новая категория
                </h3>
                <form method="POST" action="{{ route('master.categories.create') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="project_id" class="block text-sm font-medium text-gray-700">Проект</label>
                            <select id="project_id" name="project_id" required
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                @foreach (App\Models\Project::where('master_id', Auth::guard('master')->id())->get() as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Название категории</label>
                            <input type="text" name="name" id="name" required 
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm">
                            Создать категорию
                        </button>
                        <button type="button" 
                            onclick="document.getElementById('newCategoryModal').classList.add('hidden')" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Отмена
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Модальное окно редактирования категории -->
<div id="editCategoryModal" class="hidden fixed z-10 inset-0 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
             onclick="document.getElementById('editCategoryModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Редактировать категорию
                </h3>
                <form id="editCategoryForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label for="edit_project_id" class="block text-sm font-medium text-gray-700">Проект</label>
                            <select id="edit_project_id" name="project_id" required
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                @foreach (App\Models\Project::where('master_id', Auth::guard('master')->id())->get() as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="edit_name" class="block text-sm font-medium text-gray-700">Название категории</label>
                            <input type="text" name="name" id="edit_name" required 
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm">
                            Сохранить изменения
                        </button>
                        <button type="button" 
                            onclick="document.getElementById('editCategoryModal').classList.add('hidden')" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Отмена
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openEditCategoryModal(id, name, projectId) {
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_project_id').value = projectId;
        document.getElementById('editCategoryForm').action = `/master/categories/${id}`;
        document.getElementById('editCategoryModal').classList.remove('hidden');
    }

    // Закрытие модальных окон при нажатии на клавишу Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.getElementById('newCategoryModal').classList.add('hidden');
            document.getElementById('editCategoryModal').classList.add('hidden');
        }
    });

    // Закрытие модальных окон при клике вне формы
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('fixed') && event.target.classList.contains('inset-0')) {
            document.getElementById('newCategoryModal').classList.add('hidden');
            document.getElementById('editCategoryModal').classList.add('hidden');
        }
    });
</script>
@endpush
@endsection

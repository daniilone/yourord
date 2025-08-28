<!-- Модальное окно добавления услуги -->
<div id="newServiceModal" class="hidden fixed z-10 inset-0 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
             onclick="document.getElementById('newServiceModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Добавление новой услуги
                </h3>
                <form method="POST" action="{{ route('master.services.create') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700">Категория</label>
                            <select id="category_id" name="category_id" required
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->project->name }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Название услуги</label>
                            <input type="text" name="name" id="name" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="duration" class="block text-sm font-medium text-gray-700">Длительность (минут)</label>
                                <input type="number" name="duration" id="duration" min="1" required
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700">Стоимость (руб.)</label>
                                <input type="number" name="price" id="price" min="0" step="100" required
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Описание (необязательно)</label>
                            <textarea name="description" id="description" rows="3"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm">
                            Создать услугу
                        </button>
                        <button type="button"
                            onclick="document.getElementById('newServiceModal').classList.add('hidden')"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Отмена
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно редактирования услуги -->
<div id="editServiceModal" class="hidden fixed z-10 inset-0 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
             onclick="document.getElementById('editServiceModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Редактирование услуги
                </h3>
                <form id="editServiceForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label for="edit_category_id" class="block text-sm font-medium text-gray-700">Категория</label>
                            <select id="edit_category_id" name="category_id" required
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->project->name }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="edit_name" class="block text-sm font-medium text-gray-700">Название услуги</label>
                            <input type="text" name="name" id="edit_name" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="edit_duration" class="block text-sm font-medium text-gray-700">Длительность (минут)</label>
                                <input type="number" name="duration" id="edit_duration" min="1" required
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="edit_price" class="block text-sm font-medium text-gray-700">Стоимость (руб.)</label>
                                <input type="number" name="price" id="edit_price" min="0" step="1" required
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                        </div>
                        <div>
                            <label for="edit_description" class="block text-sm font-medium text-gray-700">Описание (необязательно)</label>
                            <textarea name="description" id="edit_description" rows="3"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm">
                            Сохранить изменения
                        </button>
                        <button type="button"
                            onclick="document.getElementById('editServiceModal').classList.add('hidden')"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Отмена
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Функция открытия модального окна редактирования услуги
    function openEditServiceModal(id, name, categoryId, duration, price, description) {
        document.getElementById('editServiceForm').action = `/master/services/${id}`;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_category_id').value = categoryId;
        document.getElementById('edit_duration').value = duration;
        document.getElementById('edit_price').value = price;
        document.getElementById('edit_description').value = description || '';
        document.getElementById('editServiceModal').classList.remove('hidden');
    }
</script>

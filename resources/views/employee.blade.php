<x-app-layout>
    <section class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                @if (session('success'))
                    <div class="bg-green-500 text-white p-4 rounded-lg mb-6 shadow-md">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif
                <!-- Pesan Error -->
                @if (session('error'))
                    <div class="bg-red-500 text-white p-4 rounded-lg mb-6 shadow-md">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        {{ __('User List') }}
                    </h2>
                    <!-- Tombol Create User -->
                    <div class="m-4">
                        <button id="openModal" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                            Create
                        </button>
                    </div>
                    <hr>

                    <!-- Table -->
                    <div class="overflow-x-auto mt-4">
                        <table class="min-w-full bg-white shadow-md rounded-lg text-center">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="py-2 px-4">Name</th>
                                    <th class="py-2 px-4">Address</th>
                                    <th class="py-2 px-4">Contact</th>
                                    <th class="py-2 px-4">Email</th>
                                    <th class="py-2 px-4">Role</th>
                                    <th class="py-2 px-4">Superior</th>
                                    <th class="py-2 px-4">Action</th>
                                </tr>
                            </thead>
                            <tbody id="dataTable">
                                @forelse ($user_list as $item)
                                    <tr class="border-b hover:bg-gray-100">
                                        <td class="py-2 px-4">{{ $item->name }}</td>
                                        <td class="py-2 px-4">{{ $item->address }}</td>
                                        <td class="py-2 px-4">{{ $item->contact }}</td>
                                        <td class="py-2 px-4">{{ $item->email }}</td>
                                        <td class="py-2 px-4">{{ $item->role }}</td>
                                        <td class="py-2 px-4">
                                            {{ $item->superior ? $item->superior->name : 'Tidak ada superior' }}</td>
                                        <td class="py-2 px-4"><button
                                                class="editUser bg-yellow-500 text-white px-2 py-1 rounded-lg hover:bg-yellow-600"
                                                data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                                data-address="{{ $item->address }}"
                                                data-contact="{{ $item->contact }}" data-email="{{ $item->email }}"
                                                data-role="{{ $item->role }}"
                                                data-superior="{{ $item->id_superior }}">Edit</button></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-2 px-4">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>


                        </table>
                    </div>
                    <!-- Pagination Links -->
                    <div class="mt-6 flex justify-center">
                        {{ $user_list->links() }}
                    </div>


                </div>
            </div>
        </div>

    </section>

    <!-- Modal -->
    <div id="modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
            <h2 id="modalTitle" class="text-xl font-bold mb-4">Create New User</h2>
            <form id="userForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="id" id="userId">
                <div class="mb-4">
                    <label class="block text-gray-700">Name</label>
                    <input type="text" name="name" id="name" class="w-full p-2 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Address</label>
                    <input type="text" name="address" id="address" class="w-full p-2 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Contact</label>
                    <input type="text" name="contact" id="contact" class="w-full p-2 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Email</label>
                    <input type="email" name="email" id="email" class="w-full p-2 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Role</label>
                    <select name="role" id="role" class="w-full p-2 border rounded-lg" required>
                        <option value="Staff">Staff</option>
                        <option value="HRD">HRD</option>
                        <option value="Manager">Manager</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Superior</label>
                    <select name="id_superior" id="superiorSelect" class="w-full p-2 border rounded-lg">
                        <option value="">Tidak ada superior</option>
                        @foreach ($all_users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Password</label>
                    <input type="password" name="password" id="password" class="w-full p-2 border rounded-lg">
                    <small class="text-gray-500">Kosongkan jika tidak ingin mengubah password</small>
                </div>
                <div class="flex justify-end">
                    <button type="button" id="closeModal"
                        class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg mr-2 hover:bg-gray-400">Cancel</button>
                    <button type="submit" id="saveButton"
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Save</button>
                </div>
            </form>
        </div>
    </div>


</x-app-layout>
<!-- JavaScript untuk Modal -->
<script>
    const openModalBtn = document.getElementById('openModal');
    const closeModalBtn = document.getElementById('closeModal');
    const modal = document.getElementById('modal');
    const modalTitle = document.getElementById('modalTitle');
    const userForm = document.getElementById('userForm');
    const formMethod = document.getElementById('formMethod');

    // Inisialisasi Select2
    $(document).ready(function() {
        $('#superiorSelect').select2({
            placeholder: "Choose",
            allowClear: true,
            width: '100%'
        });

        // Debug form submission
        $('#userForm').on('submit', function(e) {
            console.log('Form submitted to:', this.action);
            console.log('Method:', formMethod.value);
        });
    });

    // Buka modal untuk create
    openModalBtn.addEventListener('click', () => {
        modalTitle.textContent = 'Create New User';
        userForm.action = "{{ route('employee.store') }}";
        formMethod.value = 'POST';
        document.getElementById('userId').value = '';
        document.getElementById('name').value = '';
        document.getElementById('address').value = '';
        document.getElementById('contact').value = '';
        document.getElementById('email').value = '';
        document.getElementById('role').value = '';
        $('#superiorSelect').val('').trigger('change');
        document.getElementById('password').value = '';
        modal.classList.remove('hidden');
    });

    // Tutup modal
    closeModalBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });

    // Buka modal untuk edit
    document.querySelectorAll('.editUser').forEach(button => {
        button.addEventListener('click', () => {
            modalTitle.textContent = 'Edit User';
            userForm.action = "{{ route('employee.update', '') }}/" + button.dataset.id;
            formMethod.value = 'PUT';
            document.getElementById('userId').value = button.dataset.id;
            document.getElementById('name').value = button.dataset.name;
            document.getElementById('address').value = button.dataset.address;
            document.getElementById('contact').value = button.dataset.contact;
            document.getElementById('email').value = button.dataset.email;
            document.getElementById('role').value = button.dataset.role || '';
            console.log('Role saat ini:', button.dataset.role); // Debugging
            $('#superiorSelect').val(button.dataset.superior || '').trigger('change');
            document.getElementById('password').value = '';
            modal.classList.remove('hidden');
        });
    });
</script>

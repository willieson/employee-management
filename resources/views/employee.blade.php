<x-app-layout>
    <section class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        {{ __('User List') }}
                    </h2>
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
                                @foreach ($user_list as $item)
                                    <tr class="border-b hover:bg-gray-100">
                                        <td class="py-2 px-4">{{ $item->name }}</td>
                                        <td class="py-2 px-4">{{ $item->address }}</td>
                                        <td class="py-2 px-4">{{ $item->contact }}</td>
                                        <td class="py-2 px-4">{{ $item->email }}</td>
                                        <td class="py-2 px-4">{{ $item->role }}</td>
                                        <td class="py-2 px-4">
                                            {{ $item->superior ? $item->superior->name : 'Tidak ada superior' }}</td>
                                        <td class="py-2 px-4">Edit</td>
                                    </tr>
                                @endforeach
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



</x-app-layout>

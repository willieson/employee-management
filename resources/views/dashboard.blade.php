<x-app-layout>
    <section class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
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
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        {{ __('Your info') }}
                    </h2>
                    <hr>
                    <table class="text-left mt-4">

                        <tr>
                            <th>Name</th>
                            <td>: {{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th>Address</th>
                            <td>: {{ $user->address }}</td>
                        </tr>
                        <tr>
                            <th>Contact</th>
                            <td>: {{ $user->contact }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>: {{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <td>: {{ $user->role }}</td>
                        </tr>
                        <tr>
                            <th>Superior</th>
                            <td>: <span style="color: red">{{ $superior_name }}</span></td>
                        </tr>
                    </table>

                </div>


            </div>
        </div>

    </section>

    <section class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        {{ __('Leave info') }}
                    </h2>
                    <hr>
                    <table class="text-left mt-4">

                        <tr>
                            <th>Entitlement</th>
                            <td>: {{ $total_entitlement }}</td>
                        </tr>
                        <tr>
                            <th>Used</th>
                            <td>: {{ $total_used }}</td>
                        </tr>
                        <tr>
                            <th>Remaining</th>
                            <td>: {{ $total_remaining }}</td>
                        </tr>
                        <tr>
                            <th>Sick</th>
                            <td>: {{ $total_sick }}</td>
                        </tr>

                    </table>

                </div>
            </div>
        </div>

    </section>

    <section class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        {{ __('Leave Request') }}
                    </h2>
                    <hr>
                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white shadow-md rounded-lg text-center">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="py-2 px-4">Name</th>
                                    <th class="py-2 px-4">Leave Number</th>
                                    <th class="py-2 px-4">Type</th>
                                    <th class="py-2 px-4">From</th>
                                    <th class="py-2 px-4">To</th>
                                    <th class="py-2 px-4">Day's</th>
                                    <th class="py-2 px-4">Note</th>
                                    <th class="py-2 px-4">Action</th>
                                </tr>
                            </thead>
                            <tbody id="dataTable">
                                @foreach ($my_staff_request as $leave)
                                    <tr class="border-b hover:bg-gray-100">
                                        <td class="py-2 px-4">{{ $leave->user->name }}</td>
                                        <td class="py-2 px-4">{{ $leave->leaves_number }}</td>
                                        <td class="py-2 px-4">{{ $leave->leaveType->name }}</td>
                                        <td class="py-2 px-4">{{ $leave->start_date }}</td>
                                        <td class="py-2 px-4">{{ $leave->end_date }}</td>
                                        <td class="py-2 px-4">{{ $leave->days }}</td>
                                        <td class="py-2 px-4">{{ $leave->note }}</td>
                                        <td class="py-2 px-4">
                                            <!-- Tombol Approve -->
                                            <div class="flex justify-center gap-6">
                                                <form action="{{ route('leave.approve', $leave->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button title="Approve" type="submit"
                                                        class="text-green-500 hover:text-green-700">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>

                                                <!-- Tombol Reject -->
                                                <form action="{{ route('leave.reject', $leave->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button title="Reject" type="submit"
                                                        class="text-red-500 hover:text-red-700">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </section>

</x-app-layout>

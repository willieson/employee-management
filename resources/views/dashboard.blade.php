<x-app-layout>
    <section class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
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

</x-app-layout>

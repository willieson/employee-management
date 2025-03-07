<x-app-layout>
    <section class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        {{ __('History') }}
                    </h2>
                    <div class="container mt-4">
                        @if (session('error'))
                            <div class="bg-red-500 text-white p-2 mb-4 rounded">
                                {{ session('error') }}
                            </div>
                        @endif



                        <form method="GET">
                            <!-- Filter Tanggal -->
                            <label for="start_date" class="font-semibold">From:</label>
                            <input type="date" name="start_date" id="start_date" class="border rounded p-2"
                                value="{{ request('start_date', now()->startOfYear()->toDateString()) }}">

                            <label for="end_date" class="font-semibold">To:</label>
                            <input type="date" name="end_date" id="end_date" class="border rounded p-2"
                                value="{{ request('end_date', now()->endOfYear()->toDateString()) }}">

                            <!-- Tombol Cari -->
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Cari</button>

                            <label for="find_type">Type</label>
                            <select name="find_type" id="find_type" onchange="this.form.submit()">
                                <option value="">-All-</option>
                                <option value="1" {{ request('find_type') == '1' ? 'selected' : '' }}>Annual
                                </option>
                                <option value="2" {{ request('find_type') == '2' ? 'selected' : '' }}>Sick</option>
                            </select>
                        </form>
                    </div>


                    <!-- Form Pencarian -->
                    <input type="text" id="searchInput" placeholder="Search Everything..."
                        class="px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 mt-4 mb-4">

                    <hr>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white shadow-md rounded-lg text-center">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="py-2 px-4">Leave Number</th>
                                    <th class="py-2 px-4">Type</th>
                                    <th class="py-2 px-4">From</th>
                                    <th class="py-2 px-4">To</th>
                                    <th class="py-2 px-4">Day's</th>
                                    <th class="py-2 px-4">Note</th>
                                    <th class="py-2 px-4">Status</th>
                                </tr>
                            </thead>
                            <tbody id="dataTable">
                                @foreach ($data as $item)
                                    <tr class="border-b hover:bg-gray-100">
                                        <td class="py-2 px-4">{{ $item->leaves_number }}</td>
                                        <td class="py-2 px-4">{{ $item->leaveType->name }}</td>
                                        <td class="py-2 px-4">{{ $item->start_date }}</td>
                                        <td class="py-2 px-4">{{ $item->end_date }}</td>
                                        <td class="py-2 px-4">{{ $item->days }}</td>
                                        <td class="py-2 px-4">{{ $item->note }}</td>
                                        <td class="py-2 px-4">{{ $item->status }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>


            </div>
        </div>
        </div>

    </section>

    <!-- JavaScript untuk Live Search -->
    <script>
        document.getElementById("searchInput").addEventListener("keyup", function() {
            let searchText = this.value.toLowerCase();
            let rows = document.querySelectorAll("#dataTable tr");

            rows.forEach(row => {
                let rowData = row.innerText.toLowerCase();
                row.style.display = rowData.includes(searchText) ? "" : "none";
            });
        });
    </script>


</x-app-layout>

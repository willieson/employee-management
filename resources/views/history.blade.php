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

                    <div class="container py-4">
                        <!-- Tombol Request -->
                        <button id="openModal" type="submit"
                            class="bg-yellow-500 text-white px-4 py-2 rounded">Request</button>
                    </div>




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
                                        <td class="py-2 px-4">
                                            @if ($item->status == 'approved')
                                                <i title="Approved" class="fas fa-check text-green-500"></i>
                                            @elseif ($item->status == 'rejected')
                                                <i title="Rejected" class="fas fa-times text-red-500"></i>
                                            @else
                                                <i title="Pending" class="fas fa-question text-yellow-500"></i>
                                            @endif
                                        </td>
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


    <!-- Modal -->
    <div id="modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
            <h2 id="modalTitle" class="text-xl font-bold mb-4">Request Leaves</h2>
            <form id="requestForm" method="POST" action = "{{ route('leaves.store') }}";>
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700">Type</label>
                    <select name="in_leave_types" id="in_leave_types" class="w-full p-2 border rounded-lg" required>
                        @foreach ($leave_types as $leave_type)
                            <option value="{{ $leave_type->id }}">{{ $leave_type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">From</label>
                    <input type="text" name="in_from" id="fromDate" class="w-full p-2 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">To</label>
                    <input type="text" name="in_to" id="toDate" class="w-full p-2 border rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Days</label>
                    <input required type="text" name="in_days" id="days"
                        class="w-full p-2 border rounded-lg" readonly>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Notes</label>
                    <textarea required type="text" name="in_notes" id="in_notes" class="w-full p-2 border rounded-lg"></textarea>
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

<!-- JavaScript untuk Live Search -->
<script>
    // Ambil hari libur dari database
    const holidays = @json(\App\Models\holiday::pluck('date')->toArray());
    let fromPicker, toPicker;

    // Fungsi untuk parse DD-MM-YYYY ke Date object
    function parseDate(dateStr) {
        const [day, month, year] = dateStr.split('-').map(Number);
        return new Date(year, month - 1, day); // month - 1 karena Januari = 0
    }

    // Hitung hari kerja
    function calculateDays() {
        const fromStr = document.getElementById('fromDate').value;
        const toStr = document.getElementById('toDate').value;
        const daysField = document.getElementById('days');

        if (fromStr && toStr) {
            const from = parseDate(fromStr);
            const to = parseDate(toStr);

            if (from <= to) {
                let workDays = 0;
                let current = new Date(from);

                while (current <= to) {
                    const day = current.getDay();
                    const year = current.getFullYear();
                    const month = String(current.getMonth() + 1).padStart(2, '0');
                    const dayOfMonth = String(current.getDate()).padStart(2, '0');
                    const dateStr = `${year}-${month}-${dayOfMonth}`; // Format YYYY-MM-DD untuk holidays
                    if (day !== 0 && day !== 6 && !holidays.includes(dateStr)) {
                        workDays++;
                    }
                    current.setDate(current.getDate() + 1);
                }
                daysField.value = workDays;
            } else {
                daysField.value = '';
            }
        } else {
            daysField.value = '';
        }
    }
    // Fungsi untuk disable weekend dan holidays
    function disableDates(date) {
        const day = date.getDay();
        // Format tanggal secara manual untuk menghindari pergeseran UTC
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0'); // +1 karena getMonth mulai dari 0
        const dayOfMonth = String(date.getDate()).padStart(2, '0');
        const dateStr = `${year}-${month}-${dayOfMonth}`;
        return day === 0 || day === 6 || holidays.includes(dateStr);
    }

    const openModalBtn = document.getElementById('openModal');
    const closeModalBtn = document.getElementById('closeModal');
    const modal = document.getElementById('modal');

    openModalBtn.addEventListener('click', () => {
        // Inisialisasi Flatpickr saat modal dibuka
        if (!fromPicker) {
            fromPicker = flatpickr('#fromDate', {
                dateFormat: 'd-m-Y',
                disable: [disableDates],
                onChange: function(selectedDates, dateStr) {
                    calculateDays();
                    // Set minDate untuk toDate
                    if (toPicker) {
                        toPicker.set('minDate', dateStr);
                    }
                }
            });
        }
        if (!toPicker) {
            toPicker = flatpickr('#toDate', {
                dateFormat: 'd-m-Y',
                disable: [disableDates],
                onChange: function(selectedDates, dateStr) {
                    calculateDays();
                    // Set maxDate untuk fromDate
                    if (fromPicker) {
                        fromPicker.set('maxDate', dateStr);
                    }
                }
            });
        }
        modal.classList.remove('hidden');
    });

    closeModalBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    // Tutup modal jika klik di luar form
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });
    document.getElementById("searchInput").addEventListener("keyup", function() {
        let searchText = this.value.toLowerCase();
        let rows = document.querySelectorAll("#dataTable tr");

        rows.forEach(row => {
            let rowData = row.innerText.toLowerCase();
            row.style.display = rowData.includes(searchText) ? "" : "none";
        });
    });
</script>

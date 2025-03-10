<?php

namespace App\Http\Controllers;

use App\Models\Annual_leaves;
use App\Models\expired_leaves;
use App\Models\holiday;
use App\Models\Leave_types;
use App\Models\Leaves;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{

    protected $user_id;
    public function __construct()
    {
        $this->user_id = Auth::id(); // Simpan user ID ke properti class
    }

    public function index(Request $request)
    {
        $find_type = $request->input('find_type');


        // Ambil tanggal dari request, default ke awal & akhir tahun
        $start_date = $request->input('start_date', Carbon::now()->startOfYear()->toDateString());
        $end_date = $request->input('end_date', Carbon::now()->endOfYear()->toDateString());

        // Validasi: Pastikan start_date <= end_date
        if ($start_date > $end_date) {
            return redirect()->back()->with('error', 'Invalid Date!!!');
        }

        // Validasi: Maksimal pencarian 2 tahun
        $max_range = Carbon::parse($start_date)->addYears(2);
        if (Carbon::parse($end_date)->greaterThan($max_range)) {
            return redirect()->back()->with('error', 'Max 2 Year.');
        }

        $query = Leaves::with(['user', 'leaveType'])
            ->where('users_id', $this->user_id)
            ->whereBetween('start_date', [$start_date, $end_date]);

        if ($find_type) {
            $query->where('leave_types_id', $find_type);
        }

        $data = $query->get();

        $leave_types = Leave_types::all();

        return view('history', compact('data', 'start_date', 'end_date', 'find_type', 'leave_types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'in_leave_types' => 'required|exists:leave_types,id',
            'in_from' => 'required|date|after_or_equal:today',
            'in_to' => 'required|date|after_or_equal:in_from',
            'in_days' => 'required|integer',
            'in_notes' => 'required|string|max:255',
        ]);

        // Ambil data dari request
        $unformattedDateFrom = Carbon::createFromFormat('d-m-Y', $request->in_from)->format('Y-m-d');
        $unformattedDateTo = Carbon::createFromFormat('d-m-Y', $request->in_to)->format('Y-m-d');
        $in_from = Carbon::parse($unformattedDateFrom);
        $in_to = Carbon::parse($unformattedDateTo);
        $holidays = holiday::pluck('date')->toArray();
        $userId = Auth::id();
        $currentYear = Carbon::now()->year;
        $previousYear = Carbon::now()->subYear()->year; // Tahun sebelumnya

        // Hitung ulang hari kerja untuk verifikasi
        $workDays = 0;
        $current = $in_from->copy();

        while ($current <= $in_to) {
            $day = $current->dayOfWeek; // 0 = Minggu, 6 = Sabtu
            $dateStr = $current->toDateString();
            if ($day !== 0 && $day !== 6 && !in_array($dateStr, $holidays)) {
                $workDays++;
            }
            $current->addDay();
        }

        // Verifikasi bahwa 'days' dari form sesuai dengan perhitungan
        if ($workDays != $request->in_days) {
            return redirect()->back()
                ->withErrors(['days' => 'The number of working days does not match the server calculation.'])
                ->withInput();
        }

        // 1. Cek tanggal yang sudah diambil pada tahun saat ini
        $existingLeave = Leaves::where('users_id', $userId)
            ->whereIn('status', ['approved', 'pending'])
            ->whereYear('start_date', $currentYear) // Hanya cek tahun saat ini
            ->where(function ($query) use ($in_from, $in_to) {
                $query->whereBetween('start_date', [$in_from, $in_to])
                    ->orWhereBetween('end_date', [$in_from, $in_to])
                    ->orWhere(function ($query) use ($in_from, $in_to) {
                        $query->where('start_date', '<=', $in_from)
                            ->where('end_date', '>=', $in_to);
                    });
            })
            ->first();

        if ($existingLeave) {
            return redirect()->back()->with('error', 'there is a date that has been taken');
        }


        //cek jenis cuti
        $isSick = '2';
        if ($isSick != $request->in_leave_types) {

            //cek cuti aktif

            $previousExpLeave = expired_leaves::where('year', $previousYear)->first();
            if ($previousExpLeave) {
                $prevExpAt = Carbon::parse($previousExpLeave->expires_at);
                if ($prevExpAt->isFuture()) {
                    $prev_annual_leaves = Annual_leaves::where('year', $previousYear)->where('users_id', $this->user_id)->first();
                    if ($prev_annual_leaves) {
                        $prev_entitlement = $prev_annual_leaves->total_leave;
                        $prev_remaining = $prev_annual_leaves->remaining_leave;
                    } else {
                        $prev_entitlement = 0;
                        $prev_remaining = 0;
                    }

                    //tanggal belum lewat
                } else {
                    $prev_entitlement = 0;
                    $prev_remaining = 0;
                }
            } else {
                $prev_entitlement = 0;
                $prev_remaining = 0;
            }


            $annualLeave = Annual_leaves::where('users_id', $this->user_id)
                ->where('year', $currentYear)
                ->first();

            if (!$annualLeave) {
                return redirect()->back()->with('error', 'No annual leave allocation found for this year.');
            }

            $totalPendingDays = Leaves::where('users_id', $userId)
                ->where('status', 'pending')
                ->where('leave_types_id', '1')
                ->whereYear('start_date', $currentYear)
                ->sum('days');

            $requestedDays = $request->in_days;
            $remainingLeave = $annualLeave->remaining_leave + $prev_remaining;
            $totalLeave = $annualLeave->total_leave + $prev_entitlement;
            $totalUsedAndPending = $totalPendingDays + $requestedDays;

            if ($remainingLeave - $totalUsedAndPending < 0 || $totalUsedAndPending > $totalLeave) {
                return redirect()->back()->with('error', 'Insufficient remaining leave days. You have ' . $remainingLeave . ' days remaining, with ' . $totalPendingDays . ' days pending approval.');
            }
        }
        // Generate leaves_number
        $lastLeave = Leaves::orderBy('leaves_number', 'desc')->first();
        if ($lastLeave && preg_match('/Lv-(\d{7})/', $lastLeave->leaves_number, $matches)) {
            $lastNumber = (int)$matches[1];
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1; // Mulai dari 1 jika belum ada data
        }
        $leavesNumber = sprintf('Lv-%07d', $newNumber); // Format Lv-0000001

        // Simpan data ke tabel leaves
        Leaves::create([
            'users_id' => Auth::id(),
            'leave_types_id' => $request->in_leave_types,
            'leaves_number' => $leavesNumber,
            'start_date' => $in_from,
            'end_date' => $in_to,
            'days' => $request->in_days,
            'note' => $request->in_notes,
            'status' => 'pending',
        ]);

        return redirect()->route('history')->with('success', 'Leave request submitted successfully');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Annual_leaves;
use App\Models\expired_leaves;
use App\Models\Leaves;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class LeavesController extends Controller
{
    public function dashboard()
    {
        $user_id = Auth::id(); // Ambil user yang sedang login dari database
        $user = User::find($user_id);
        $superior = $user->id_superior ? User::find($user->id_superior) : null;

        //Cek Hak Cuti
        $currentYear = Carbon::now()->year; // Tahun sekarang
        $previousYear = Carbon::now()->subYear()->year; // Tahun sebelumnya

        $previousExpLeave = expired_leaves::where('year', $previousYear)->first();

        if ($previousExpLeave) {
            $prevExpAt = Carbon::parse($previousExpLeave->expires_at);
            if ($prevExpAt->isFuture()) {
                $prev_annual_leaves = Annual_leaves::where('year', $previousYear)->where('users_id', $user_id)->first();
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

        $currentExpLeave = expired_leaves::where('year', $currentYear)->first();

        if ($previousExpLeave) {
            $currExpAt = Carbon::parse($currentExpLeave->expires_at);
            if ($currExpAt->isFuture()) {
                $curr_annual_leaves = Annual_leaves::where('year', $currentYear)->where('users_id', $user_id)->first();
                if ($curr_annual_leaves) {
                    $curr_entitlement = $curr_annual_leaves->total_leave;
                    $curr_remaining = $curr_annual_leaves->remaining_leave;
                } else {
                    $curr_entitlement = 0;
                    $curr_remaining = 0;
                }

                //tanggal belum lewat
            } else {
                $curr_entitlement = 0;
                $curr_remaining = 0;
            }
        } else {
            $curr_entitlement = 0;
            $curr_remaining = 0;
        }

        $total_entitlement = $prev_entitlement + $curr_entitlement;
        $total_remaining = $prev_remaining + $curr_remaining;
        $total_used = $total_entitlement - $total_remaining;
        //Cek Hak Cuti

        //cek jumlah sakit
        $curr_sick = Leaves::where('users_id', $user_id)->where('leave_types_id', 2)->where('status', 'approved')->whereYear('start_date', $currentYear)->sum('days');


        //cari bawahan
        $my_staff_ids = User::where('id_superior', $user_id)->pluck('id');
        $my_staff_request = Leaves::whereIn('users_id', $my_staff_ids)
            ->where('status', 'pending')
            ->with([
                'user:id,name',
                'leaveType:id,name' // Tambahkan relasi leaveType
            ])
            ->get(); // Cari cuti berdasarkan ID staff

        return view('dashboard', [
            'user' => $user,
            'superior_name' => $superior ? $superior->name : 'Tidak Ada Atasan',
            'total_entitlement' => $total_entitlement,
            'total_remaining' => $total_remaining,
            'total_used' => $total_used,
            'total_sick' => $curr_sick,
            'my_staff_request' => $my_staff_request,
        ]);
    }


    public function approve($id)
    {
        $leave = Leaves::findOrFail($id);
        // Jika jenis cuti bukan cuti tahunan, langsung approve tanpa pengurangan cuti
        if ($leave->leave_types_id != 1) { // Misalnya ID 1 adalah cuti tahunan
            $leave->status = 'approved';
            $leave->save();
            return redirect()->back()->with('success', 'Leave request approved successfully.');
        }

        $user_id = $leave->users_id;
        $leave_days = $leave->days;
        $today = Carbon::today();
        // 1. Ambil tahun cuti yang masih berlaku dari expired_leaves
        $active_years = expired_leaves::where('expires_at', '>', $today)
            ->pluck('year')
            ->toArray();

        if (empty($active_years)) {
            return redirect()->back()->withErrors(['error' => 'No valid leave entitlement found.']);
        }

        // 2. Dapatkan cuti yang aktif dari annual_leaves
        $annualLeaves = Annual_leaves::where('users_id', $user_id)
            ->whereIn('year', $active_years)
            ->orderBy('year', 'asc') // Prioritaskan cuti dari tahun terlama
            ->get();

        $remaining_leave_needed = $leave_days;

        foreach ($annualLeaves as $annualLeave) {
            if ($remaining_leave_needed <= 0) break;

            if ($annualLeave->remaining_leave >= $remaining_leave_needed) {
                // Jika cukup, langsung kurangi
                $annualLeave->remaining_leave -= $remaining_leave_needed;
                $annualLeave->save();
                $remaining_leave_needed = 0;
            } else {
                // Jika tidak cukup, habiskan yang ada dan lanjut ke tahun berikutnya
                $remaining_leave_needed -= $annualLeave->remaining_leave;
                $annualLeave->remaining_leave = 0;
                $annualLeave->save();
            }
        }

        // Jika masih ada sisa cuti yang dibutuhkan, berarti cuti tidak mencukupi
        if ($remaining_leave_needed > 0) {
            return redirect()->back()->withErrors(['error' => 'Not enough remaining leave balance to approve.']);
        }

        // 3. Jika validasi lolos, update status menjadi approved
        $leave->status = 'approved';
        $leave->save();

        return redirect()->back()->with('success', 'Leave request approved successfully.');
    }

    public function reject($id)
    {
        $leave = Leaves::findOrFail($id);
        $leave->status = 'rejected';
        $leave->save();

        return redirect()->back()->with('success', 'Leave request rejected Success.');
    }
}

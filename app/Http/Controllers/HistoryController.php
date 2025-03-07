<?php

namespace App\Http\Controllers;

use App\Models\Leaves;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $find_type = $request->input('find_type');
        $user_id = Auth::id();

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
            ->where('users_id', $user_id)
            ->whereBetween('start_date', [$start_date, $end_date]);

        if ($find_type) {
            $query->where('leave_types_id', $find_type);
        }

        $data = $query->get();

        return view('history', compact('data', 'start_date', 'end_date', 'find_type'));
    }
}

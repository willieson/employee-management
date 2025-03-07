<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Annual_leaves;
use App\Models\expired_leaves;
use App\Models\Leave_types;
use App\Models\Leaves;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function dashboard(): View
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




        return view('dashboard', [
            'user' => $user,
            'superior_name' => $superior ? $superior->name : 'Tidak Ada Atasan',
            'total_entitlement' => $total_entitlement,
            'total_remaining' => $total_remaining,
            'total_used' => $total_used,
            'total_sick' => $curr_sick,
        ]);
    }
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

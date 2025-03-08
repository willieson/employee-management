<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index()
    {
        $user_list = User::with('superior')->paginate(5);
        return view('employee', compact('user_list'));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\Civilian;
use App\Enums\UserRoleEnum;
use Illuminate\Support\Arr;
use DB;

class UserController extends Controller
{
    public function getProfile(Request $request)
    {
        return response()->api(auth()->user()->load('civilian', 'staff'), 200, 'ok', 'Berhasil melakukan login');
    }
}

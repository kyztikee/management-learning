<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\Civilian;
use App\Enums\UserRoleEnum;
use App\Enums\CivilianStatusEnum;
use Illuminate\Support\Arr;
use DB;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $data = $request->only(['email', 'password']);

        if (!Auth::attempt($data)) {
            return response()->api([], 200, 'error', 'Email atau password salah');
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;
        $result = [
            'user' => auth()->user()->load('civilian', 'staff'),
            'access_token' => $accessToken
        ];

        return response()->api($result, 200, 'ok', 'Berhasil melakukan login');
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $detail = Arr::pull($data, 'detail');

        $rw = User::with('staff.children')->where(['role' => UserRoleEnum::RW])->whereHas('staff', function($query) use($detail) {
            return $query->where('section_no', $detail['rw']);
        })->first();

        if(!$rw) {
            return response()->api([], 400, 'error', 'Data RW tidak ditemukan');
        }

        $rt = $rw->staff->children->where('section_no', $detail['rt'])->count();
        if(!$rt) {
            return response()->api([], 400, 'error', 'Data RT tidak ditemukan');
        }

        DB::beginTransaction();
        try {
            $user = User::create([...$data, 'role' => UserRoleEnum::CIVILIAN->value]);
            $civilian = Civilian::create([...$detail, 'user_id' => $user->id, 'status' => CivilianStatusEnum::ON_PROGRESS]);

            DB::commit();
            return response()->api([...$user->toArray(), 'civilian' => $civilian], 200, 'ok', 'Berhasil melakukan registrasi');
        } catch (\Exception $e) {
            DB::rollback();
            return response()->api([], 400, 'error', 'Gagal melakukan registrasi, silahkan hubungi admin');
        }
    }
}

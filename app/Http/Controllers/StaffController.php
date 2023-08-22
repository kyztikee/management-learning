<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StaffRegisterRequest;
use App\Models\User;
use App\Models\Staff;
use App\Enums\UserRoleEnum;
use Illuminate\Support\Arr;
use DB;

class StaffController extends Controller
{
    public function register(StaffRegisterRequest $request)
    {
        $data = $request->validated();
        $detail = Arr::pull($data, 'detail');

        DB::beginTransaction();
        try {
            $user = User::create($data);
            $staff = Staff::create([...$detail, 'user_id' => $user->id]);

            DB::commit();
            return response()->api([...$user->toArray(), 'staff' => $staff], 200, 'ok', 'Berhasil melakukan registrasi staff');
        } catch (\Exception $e) {
            DB::rollback();
            return response()->api([], 400, 'error', 'Gagal melakukan registrasi, silahkan hubungi admin');
        }
    }

    public function getStaffList(Request $request)
    {
        $customer = User::whereIn('role', [UserRoleEnum::RT, UserRoleEnum::RW])->search();
        $result = [
            'count' => $customer->count(),
            'users' => $customer->getResult()->load('staff'),
        ];

        return response()->api($result, 200, 'ok', 'Berhasil mendapatkan data staff');
    }

    public function getStaffDetail(Request $request, User $user)
    {
        if($user->role === UserRoleEnum::CIVILIAN) {
            return response()->api([], 400, 'error', 'Fitur ini hanya untuk staff');
        }
        return response()->api($user->load('staff.parent', 'staff.children'), 200, 'ok', 'Berhasil mendapatkan detil staff');
    }

    public function getSectionArea(Request $request) {
        $staff = Staff::whereHas('user', function($query) {
            return $query->where('role', UserRoleEnum::RW);
        })->get()->load('children');

        return response()->api($staff, 200, 'ok', 'Berhasil mendapatkan data RT/RW');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\ProfileApprovalRequest;
use App\Models\User;
use App\Models\Civilian;
use App\Enums\UserRoleEnum;
use App\Enums\CivilianStatusEnum;
use Illuminate\Support\Arr;
use DB;

class UserController extends Controller
{
    public function getProfile(Request $request)
    {
        return response()->api(auth()->user()->load('civilian.approved_by_user', 'staff'), 200, 'ok', 'Berhasil mendapatkan profil');
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        if(auth()->user()->role !== UserRoleEnum::CIVILIAN) {
            return response()->api([], 400, 'error', 'Fitur ini hanya untuk warga');
        }
        if(auth()->user()->civilian->status === CivilianStatusEnum::ACCEPTED) {
            return response()->api([], 400, 'error', 'Profil ini telah disetujui oleh RT setempat');
        }

        $data = $request->validated();
        $detail = Arr::pull($data, 'detail');

        DB::beginTransaction();
        try {
            //code...
            auth()->user()->update($data);
            auth()->user()->civilian()->update($detail);
            DB::commit();

            return response()->api(auth()->user()->load('civilian'), 200, 'ok', 'Berhasil mengubah profil');
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->api([], 400, 'error', 'Gagal mengubah profil');
        }
    }

    public function profileApproval(ProfileApprovalRequest $request, User $user)
    {
        if($user->role !== UserRoleEnum::CIVILIAN) {
            return response()->api([], 400, 'error', 'User ini bukanlah akun tipe warga');
        }

        if($user->civilian->status !== CivilianStatusEnum::ON_PROGRESS) {
            return response()->api([], 400, 'error', 'Profil akun ini telah diterima ataupun diminta revisi kembali');
        }

        $data = $request->validated();

        DB::beginTransaction();
        try {
            $user->civilian()->update([
                'status' => $request->status,
                'approved_by' => auth()->user()->id,
                'approved_at' => date('Y-m-d H:i:s')
            ]);

            DB::commit();
            return response()->api($user->load('civilian'), 200, 'ok', 'Berhasil melakukan approval profil');
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->api([], 400, 'error', 'Gagal melakukan approval');
        }
    }

    public function getCivilianList(Request $request)
    {
        $customer = User::where('role', UserRoleEnum::CIVILIAN)->search();
        $result = [
            'count' => $customer->count(),
            'users' => $customer->getResult()->load('civilian'),
        ];

        return response()->api($result, 200, 'ok', 'Berhasil mendapatkan data warga');
    }

    public function getCivilianDetail(Request $request, User $user)
    {
        if($user->role !== UserRoleEnum::CIVILIAN) {
            return response()->api([], 400, 'error', 'Fitur ini hanya untuk warga');
        }
        return response()->api($user->load('civilian.approved_by_user'), 200, 'ok', 'Berhasil mendapatkan detil warga');
    }
}

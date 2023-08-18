<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class ApiResponseProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('api', function($data = [], $code = 200, $status= "ok", $message =[]){
            return Response::json([
                'status' => $status,
                'code' => $code,
                'message' => $message,
                'data' => $data
            ], $code);
        });
    }
}

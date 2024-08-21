<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
         // Success macro
         Response::macro('success', function ($data = [], $message = 'Operation successful', $status = 200) {
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => $data
            ], $status);
        });

        // Error macro
        Response::macro('error', function ($message = 'Operation failed', $status = 400, $errors = []) {
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'errors' => $errors
            ], $status);
        });
    }
}

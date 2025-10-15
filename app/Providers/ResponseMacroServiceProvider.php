<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('success', function ($data = null, $meta = null, $options = 0) {

            if ($data == NULL)
                $data = collect();

            return Response::json([
                'code' => __('api.codes.success.code'),
                'message' => __('api.codes.success.message'),
                'data' => $data,
                'meta' => $meta,
            ], 200, [], $options);
        });

        Response::macro('error', function ($message = null, $code = 400, $data = null, $meta = null) {

            if ($data == NULL)
                $data = collect();

            return Response::json([
                'code' => __("api.codes.$code.code"),
                'message' => $message ?? __("api.codes.$code.message"),
                'data' => (object) $data,
            ]);
        });
    }
}

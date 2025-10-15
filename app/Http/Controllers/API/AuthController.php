<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AfterLoginRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResendOtpRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\VerifyRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AuthController extends Controller
{

    public function __construct(protected AuthService $authService) {}

    public function login(LoginRequest $request)
    {
        $data = $request->afterValidation();
        $user = $this->authService->login($data);
        return Response::success(UserResource::make($user));
    }

    public function verify(VerifyRequest $request)
    {
        $data = $request->afterValidation();
        $user = $this->authService->verify($data);
        return response()->success(UserResource::make($user));
    }

    public function resend(ResendOtpRequest $request)
    {
        $data = $request->validated();
        $user = $this->authService->resend($data);
        return response()->success(UserResource::make($user));
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->afterValidation();
        $user = $this->authService->register($data);
        return Response::success(UserResource::make($user));
    }

    public function reset_password(ResetPasswordRequest $request)
    {
        $data = $request->validated();
        $user = $this->authService->reset_password($data);
        return response()->success();
    }
}

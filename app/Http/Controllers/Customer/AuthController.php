<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Constructor
     */
    public function __construct(protected AuthService $authService) {}

    /**
     * Register a new user
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->afterValidation();

        // إنشاء مستخدم جديد
        $user = User::create([
            'name' => $data['name'] ?? null,
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'password' => Hash::make($data['password']),
        ]);

        // إنشاء توكن بعد التسجيل
        $token = $user->createToken('customer_token')->plainTextToken;

        return response()->json([
            'error' => false,
            'message' => 'تم التسجيل بنجاح',
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * Login existing user
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'password' => 'required',
        ]);

        // البحث عن المستخدم في جدول users
        $user = User::where('phone', $request->phone)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => true,
                'message' => 'رقم الجوال أو كلمة المرور غير صحيحة'
            ], 401);
        }

        // إنشاء توكن جديد
        $token = $user->createToken('customer_token')->plainTextToken;

        return response()->json([
            'error' => false,
            'message' => 'تم تسجيل الدخول بنجاح',
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * Logout (اختياري)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'error' => false,
            'message' => 'تم تسجيل الخروج بنجاح'
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}

<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    /**
     * @OA\Post(
     * path="/api/login",
     * summary="Login",
     * description="Login by email, password",
     * operationId="authLogin",
     * tags={"Auth"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),     *
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response"
     *    ),
            @OA\Response(
     *    response=200,
     *    description="Successfully auth"
     *    )
     * )
     */

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $user = User::where('email', $fields['email'])->first();
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Bad credentials'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);

    }

    /**
     * @OA\Post(
     * path="/api/register",
     * summary="Register",
     * description="Register by email, password",
     * operationId="authRegister",
     * tags={"Auth"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="name", type="string", format="string", example="TEst"),
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *       @OA\Property(property="password_confirmation", type="string", format="password", example="PassWord12345")
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials",
     *     ),
     *  @OA\Response(
     *    response=200,
     *    description="Successfully auth",     *
     *     )
     * ),
     */

    public function register(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users',
            'password' => 'required|string|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email'=>$request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;
        return response()->json([
            'status' => 'success',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    /**
     * @OA\Post(
     * path="/api/logout",
     * summary="Logout",
     * description="Logout",
     * operationId="authLogout",
     * tags={"Auth"},
     * @OA\Header(
     *    required=true,
     *    description="Pass user token",
     *     ),
     * @OA\Response(
     *    response=200,
     *    description="Logged out",
     *     )
     * )
     */
    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

}

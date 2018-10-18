<?php
namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Auth\Events\Registered;
use Tymon\JWTAuth\Facades\JWTAuth;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = $this->create($request->all());
        $token = JWTAuth::fromUser($user);
        event(new Registered($user));

        return response()->json([
            "token" => $token
        ]);
    }

    private function create(array $data)
    {
        $user = new User;
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'] ?? '';
        $user->email = $data['email'];
        $user->password = app('hash')->make($data['password']);
        $user->save();

        return $user;
    }
}

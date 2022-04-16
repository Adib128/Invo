<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    public function register(UserRequest $request)
    {
        $request['password'] = bcrypt($request->password);

        $user = User::create($request->all());

        $accessToken = $user->createToken('authToken')->accessToken;

        return $this->handleResponse(
            ['user' => $user, 'access_token' => $accessToken],
            'User logged successfully'
        );
    }

    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);
        if (!auth()->attempt($loginData)) {
            return $this->handleError('Invalid Credentials', 401);
        }
        $accessToken = auth()
            ->user()
            ->createToken('authToken')->accessToken;

        return $this->handleResponse(
            ['user' => auth()->user(), 'access_token' => $accessToken],
            'User logged successfully'
        );
    }

    public function profile()
    {
        return $this->handleResponse(auth()->user());
    }
}

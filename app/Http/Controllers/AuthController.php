<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
/**
 * @group User
 */
class AuthController extends BaseController
{
    /**
     * Register
     *
     * User Register
     *
     * @bodyParam name string
     *
     * @bodyParam email string required
     *
     * @bodyParam password string required
     */

    public function register(UserRequest $request)
    {
        // Crypting password
        $request['password'] = bcrypt($request->password);

        $user = User::create($request->all());

        // Creating access token
        $accessToken = $user->createToken('authToken')->accessToken;

        return $this->handleResponse(
            ['user' => $user, 'access_token' => $accessToken],
            'User logged successfully'
        );
    }

    /**
     * Login
     *
     * User login
     *
     * @bodyParam email string required
     *
     * @bodyParam password string required
     */

    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required',
        ]);

        // Check login credentials
        if (!auth()->attempt($loginData)) {
            return $this->handleError('Invalid Credentials', 401);
        }

        // // Creating access token
        $accessToken = auth()
            ->user()
            ->createToken('authToken')->accessToken;

        return $this->handleResponse(
            ['user' => auth()->user(), 'access_token' => $accessToken],
            'User logged successfully'
        );
    }

    /**
     * Profile
     *
     * User profile
     * 
     * @authenticated    
     */
    public function profile()
    {
        return $this->handleResponse(auth()->user());
    }
}

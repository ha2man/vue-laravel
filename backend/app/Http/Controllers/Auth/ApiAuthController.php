<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kyc;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ApiAuthController extends Controller
{
    public function register (Request $request) {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        $request['password'] = Hash::make($request['password']);
        $request['remember_token'] = Str::random(10);
        $user = User::create($request->toArray());

        // generate unique id for search
        $user->uid = $user->first_name . sprintf('%05d', $user->id);
        $user->save();

        // assign user role
        $user->assignRole('User');

        Kyc::create(['user_id' => $user->id]);

        $permissions = $user->getPermissionsViaRoles()->toArray();
        $permissions = array_map(function($permission) {
            return $permission['name'];
        }, $permissions);

        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        $response = [
            'success' => true,
            'token' => $token,
            'user' => $user,
            'roles' => $user->getRoleNames(),
            'permissions' => $permissions
        ];
        return response($response, 200);
    }

    public function login (Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;

                $permissions = $user->getPermissionsViaRoles()->toArray();
                $permissions = array_map(function($permission) {
                    return $permission['name'];
                }, $permissions);

                $response = [
                    'success' => true,
                    'token' => $token,
                    'user' => $user,
                    'roles' => $user->getRoleNames(),
                    'permissions' => $permissions
                ];

                return response($response, 200);
            } else {
                $response = [
                    "message" => "Password mismatch",
                    "success" => false
                ];
                return response($response, 422);
            }
        } else {
            $response = [
                "message" =>'User does not exist',
                "success" => false
            ];
            return response($response, 422);
        }
    }

    public function logout (Request $request) {
        $token = $request->user()->token();
        $token->revoke();
        $response = [
            'message' => 'You have been successfully logged out!',
            'success' => true,
        ];
        return response($response, 200);
    }
}

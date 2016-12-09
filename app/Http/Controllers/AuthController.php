<?php

namespace App\Http\Controllers;

use App\User;
use Validator;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Http\Requests;

class AuthController extends Controller
{
    public function store(Request $request)
    {
        $rules = array(
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:5'
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        $user = new User([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password)
        ]);

        if ($user->save()) {
            $user->signin = [
                'href' => 'v1/user/signin',
                'method' => 'POST',
                'params' => 'email, password'
            ];
            $response = [
                'msg' => 'User created successfully',
                'user' => $user
            ];
            return response()->json($response, 201);
        }

        $response = [
            'msg' => 'An error occurred'
        ];

        return response()->json($response, 404);
    }

    public function signin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $email = $request->input('email');
        //$password = $request->input('password');
        //$api_token= $request->
        $myID =  DB::table('users')->where('email', $email)->pluck('id');
        $user = [
            'email' => $email,
            'userId'=>$myID,
//          'password' => $password
        ];

        $response = [
            'msg' => 'User signed in',
            'user' => $user
        ];

        return response()->json($response, 200);
    }
}

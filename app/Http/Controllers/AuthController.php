<?php

namespace App\Http\Controllers;

use App\User;

use Illuminate\Support\Facades\Auth;
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
            'password' => 'required|min:5',
            'isManager'=>'required'
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
        $response = [
            'msg' => 'User generated',
            'user' => $user
        ];

        if ($user->save()) {
            return $this->_result($response,'1','Success');
//            $user->signin = [
//                'href' => 'v1/user/signin',
//                'method' => 'POST',
//                'params' => 'email, password'
//            ];
//            $response = [
//                'msg' => 'User created successfully',
//                'user' => $user
//            ];
//            return response()->json($response, 201);
        }

        $response = [
            'msg' => 'An error occurred'
        ];

        return $this->_result($response,'0','fail');
    }

    public function signin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required'
        ]);
        $email = $request->input('email');
        $myID =  DB::table('users')->where('email', $email)->pluck('id');

        $data = $request->all();

        $user = [
            'email' => $email,
            'userId'=>$myID,
        ];
        $response = [
            'msg' => 'User signed in',
            'user' => $user
        ];

        if (Auth::attempt(array('email' => $data['email'], 'password' => $data['password'])))
        {
            return $this->_result($response,'1','Success');
        }
        else{
            return $this->_result('fail','0','fail');
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use Validator;

class AuthController extends Controller
{
    /**
     * Create new User.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:3,40',
            'email' => 'required|string|email|max:140|unique:users',
            'password' => 'required|string|without_spaces|confirmed'
        ]);

        if ($validator->fails()){
            return response()->json(['error'=>$validator->errors()], 422);
        }

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        $user->save();
        
        return response()->json(['success' => 'user_created'], 201);
    }

    /**
     * Create fresh token.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json(['error'=>$validator->errors()], 422);
        }

        if(Auth::attempt($request->only('email', 'password')))
        {
            $user = Auth::user();
            $token = $user->createToken('Yummi_Pizza');
            return response()->json(['token' => $token->accessToken], 200);

        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Revoke token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::user()->token()->revoke();
        return response()->json(['success' => 'Logged Out Successfully'], 200);
    }
}

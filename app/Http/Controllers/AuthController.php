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
            'password' => 'required|string|without_spaces|confirmed',
            'cellphone' => 'required|unique:users_contact_data',
            'address' => 'required|string'
        ]);

        if ($validator->fails()){
            return response()->json(['error'=>$validator->errors()], 422);
        }

        $user = auth('api')->user();// Check if pseudo-guest User
        if (!$user) {
            $user = new User();
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();
        if (!$user->contactData) {
            $user->contactData()->create([
                'cellphone' => $request->cellphone,
                'address' => $request->address
            ]);
        } else {
            $user->contactData->cellphone = $request->cellphone;
            $user->contactData->address = $request->address;
            $user->contactData->save();
        }
        
        return response()->json(['success' => 'User created successfully'], 201);
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
        return response()->json(['success' => 'Logged Out successfully'], 200);
    }

    /**
     * Update User data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $user = auth('api')->user();
        $user_id = $user->id;
        $contact_data_id = $user->contactData->id;

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|between:3,40',
            'email' => 'sometimes|string|email|max:140|unique:users,email,'.$user_id,
            'password' => 'sometimes|string|without_spaces|confirmed',
            'cellphone' => 'sometimes|unique:users_contact_data,cellphone,'.$contact_data_id,
            'address' => 'sometimes|string'
        ]);

        if ($validator->fails()){
            return response()->json(['error'=>$validator->errors()], 422);
        }

        $user->fill($request->only('name', 'email'));
        $user->contactData->fill($request->only('cellphone', 'address'));
        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
        }
        $user->update();
        $user->contactData->update();

        if ($user->wasChanged() || $user->contactData->wasChanged()) {
            $changes = array_merge($user->getChanges(), $user->contactData->getChanges());
            unset($changes['updated_at']);
            return response()->json([
                'success' => 'User updated successfully',
                'changes' => $changes
            ], 200);
        } else {
            return response()->json([''], 204);
        }
    }
}

<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\User\UserResource;
use App\Models\User;
use ArgumentCountError;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthApiController extends Controller
{
    /**
     * @desc: API to login through sanctum
     * @type: POST
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request) {
        // Validate the form data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|string',
            'password' => 'required|string',
        ]);     
        
        if($validator->fails()) {
            return response(['success' => false, 'msg' => 'Invalid credentials', 'data' => [
                'errors' => $validator->errors()
            ]], Response::HTTP_BAD_REQUEST);
        }

        $user = User::where('email', $request->email)->first();
        
        if(!$user)
            return response(['success' => false, 'msg' => 'Email does not exist', 'data' => ['errors' => ['email' => ['Email does not exist']]]], Response::HTTP_NOT_FOUND);
            
        if(!Hash::check($request->password, $user->password)) 
            return response(['success' => false, 'msg' => 'Incorrect password', 'data' => ['errors' => ['password' => ['Invalid password']]]], Response::HTTP_FORBIDDEN);
            
        if(!$user->is_active)
            return response(['success' => false, 'msg' => 'Your account is inactive, please contact admin.', 'data' => []], Response::HTTP_FORBIDDEN);
            
        $token = null;
        try {
            // Delete the previous tokens of this user
            $user->tokens()->delete();
            $token = $user->createToken(config('sanctum.sanctum_key'))->plainTextToken;
        } catch(ArgumentCountError $ex) {
            return response(['success' => false, 'msg' => $ex->getMessage(), 'data' => []], Response::HTTP_INTERNAL_SERVER_ERROR);        
        }
        return response(['success' => true, 'msg' => 'Login Successful', 'data' => ['user' => new UserResource($user), 'token' => $token]], Response::HTTP_OK);
    }


    /**
     * @desc: Function to logout from the app
     * @type: POST
     * @return JsonResponse
     */
    public function logout() {
        auth('sanctum')->user()->tokens()->delete();
        return response(['success' => true, 'msg' => 'Successfully Logged Out', 'data' => []], Response::HTTP_OK);
    }

    /**
    * @desc: Function to check the user token and return false if it is invalid. This is for protected routes in the frontend.
    * @type: POST
    * @return JsonResponse
    */
    public function checkUserToken(Request $request) {
        if(auth('sanctum')->check()) {
            $userRole = isset(auth('sanctum')->user()->getRoleNames()[0]) ? auth('sanctum')->user()->getRoleNames()[0] : 'user';
            $userPermissions = auth('sanctum')->user()->getPermissionsViaRoles()->pluck('name');
            return response(['success' => true, 'msg' => 'Authenticated', 'data' => ['role' => $userRole, 'permissions' => $userPermissions]]);
        } else {
            return response(['success' => false, 'msg' => 'Unauthenticated', 'data' => []], Response::HTTP_UNAUTHORIZED);            
        }
    }


}

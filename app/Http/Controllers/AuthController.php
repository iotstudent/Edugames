<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use \Illuminate\Support\Facades\Mail;
use \Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\support\Str;
use App\Http\Requests\ProfileUpdateRequest;

class AuthController extends Controller
{
    
    // resgister user 
    public function register(Request $request)
    {
        // collect all the inputs stored in the request variable , validate them and bundle them in an array
        $fields = $request->validate([
            'name'=>'required|string',
            'email'=>'required|string|unique:users,email',
            'password'=>'required|string|confirmed'
        ]);

        $checkUser = User::where('email', $request->email)->first();

       

      // insert the values in the array into DB extracting them from the fields array
        $user = User::create([
            'name'=>$fields['name'],
            'email'=>$fields['email'],
            'role'=> 'user',
            'password'=>bcrypt($fields['password'])
        ]);

       

        // generate a token for the user
        $token = $user->createToken('myapptoken')->plainTextToken;

        // send user token and user details 
        $response = [
            'Message' => "Registration Successful"
        ];

        return response ($response, 200);
    }


    // update user profile 
    public function update(Request $request)
    {   
        $fields = $request->validate([
            'id' =>'required',
            'name'=>'required|string',
            'email' => 'required|string'
        ]);

        $UpdateDetails = User::where('id', $request->id)->first();

        if (is_null($UpdateDetails)) {
            return response('Error user id missing', 401);
        }

        $UpdateDetails->update([
            'name' => $request->name,
            'email' => $request->email,
            'updated_at' => now()
        ]);

        return response('Profile updated successfully');
    }
   

    //login
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password'=>'required|string'
        ]);
        //check user email
        $user = User::where('email',$fields['email'])->first();
        
        //check password
        if( !$user || !Hash::check($fields['password'],$user->password)){
            return response([
                    'message' => 'Bad creds'
            ],401);
        }


        $token = $user->createToken('myapptoken')->plainTextToken;

        $response =[
            'user' =>$user,
            'token'=>$token
        ];

        return response($response,201);
    }

    //logout
    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return [
            'message'=>'Logged out'
        ];
    }

    // submit forgot password 
    public function ForgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        $token = Str::random(8);

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email, 
            'token' => $token, 
            'created_at' => Carbon::now()
          ]);

        Mail::send('email.forgotpassword', ['token' => $token], function($message) use($request){
            $message->to($request->email);
            $message->subject('Reset Password');
        });

        return response ('We have sent an OTP to your Email',200);
    }

    
 

    // submit reset form
    public function ResetPassword(Request $request)
      {
          $request->validate([
               'token'=> 'required',
              'email' => 'required|email|exists:users',
              'password' => 'required|string|confirmed',
          ]);
  
          $updatePassword = DB::table('password_reset_tokens')
                              ->where([
                                'email' => $request->email, 
                                'token' => $request->token
                              ])
                              ->first();
  
          if(!$updatePassword){
              return response ('Invalid token!',401);
          }
  
          $user = User::where('email', $request->email)
                      ->update(['password' => bcrypt($request->password)]);
 
          DB::table('password_reset_tokens')->where(['email'=> $request->email])->delete();
  
          return response ('Your password has been changed!',200);
      }

       // resgister user 
    public function countUsers()
    {
        
        $Users = User::where('role', 'user');
        $userCount = $Users->count();
        $response = ['Number of Users'=>$userCount];
        return response ($response, 200);
    }


}

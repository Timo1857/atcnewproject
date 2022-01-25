<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Google\Service\CloudHealthcare\Message;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function validateAccountReg($request)
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6'
        ]);
    }

    protected function createAccount($test)
    {
        $this->username = $test['name'];
        return User::create([
            'name' => $test['name'],
            'email' => $test['email'],
            'password' => Hash::make($test['password'])
        ]);
    }



    public function signup(Request $request)
    {
        $test = $this->validateAccountReg($request);
        $user =  $this->createAccount($test);
        $test['email'] = $request['email'];

        $response = [
            'message' => 'Signup Successful',
        ];
        return response($response, 200);

    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6'
        ]);
        $user = User::where('email', $request['email'])->first();
    {
   if (!Hash::check($request->password, $user->password)) {
        $response = ['message' => 'Invalid password'];
        return response()->json($response, 401);}
      else (print "login successful");   }
}



    public function forgotPassword(Request $request)
    {

        $test = $request->validate([ // response(['message' => 'email not found'], 401);
            'email' => 'required|email|exists:users,email',
        ]);
        $token = Str::random(20);
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
        ]);
        $this->resetEmail($request);
        $response = ['message' => 'A link to reset your password has been sent, please check your mailbox.'];
        return response($response, 200);
    }

    public function createNewPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:5|confirmed',
        ]);

        $updatePassword = DB::table('password_resets')
            ->where(['email' => $request->email, 'token' => $request->token])
            ->first();

        if (!$updatePassword) {
            return back()->withInput()->with('error', 'Invalid token!');
        }

        $user = User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_resets')->where(['email' => $request->email])->delete();


        $response = [
            'user' => $user,
            'message' => 'Your Password has been changed, kindly login',

        ];
        return response($response, 200);
    }


public function resetEmail(Request $request)
    { $token = Str::random(20);
        Mail::send("forgotPassword",['token' => $token, 'email'=>$request->email], function ($message) use ($request) {
            $message->to($request->email);
            $message->from(env('MAIL_FROM_ADDRESS'));
            $message->subject('Reset Password Notification');
        });

        return back()->with('message', 'your password reset link has been sent to your mail');
    }


protected function socialPassword(Request $request)
{
    if ($request['tokenId'] !== null) {
        $client = new \Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]); // Specify the CLIENT_ID of the app that accesses the backend
        $payload = $client->verifyIdToken($request->tokenId);
        if ($payload && $request->email == $payload['email']) {
            return $payload;
        }
    } elseif ($request['accessToken'] !== null) {
        $fb = new \Facebook\Facebook([
            'app_id' => '252669133674869',
            'app_secret' => '3d878b522d23a49ddd72fa7e0660f08d'
        ]);
    }
}
}

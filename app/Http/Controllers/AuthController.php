<?php

namespace App\Http\Controllers;

use App\Http\Requests\registerRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;
require_once base_path('vendor/autoload.php');

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
                'phoneNumber'=>'required|digits:8',
                'password'=>'required'
            ]);
        $credentials = $request->only('phoneNumber','password');

            if (Auth::attempt($credentials))
            {
                $user = Auth::user();
                $token = $user->createToken('API Token')->accessToken;

                return response(['token'=>$token,'user'=>$user],200);
            }

            return response()->json(['error'=>'Unauthorized'], status: 401);


    }

    public function register (registerRequest $request):JsonResponse
    {
        try {
            $verificationCode = rand(100000, 999999);

            $user = User::create([
                'name'=> $request->name,
                'email'=> $request->email,
                'password' => Hash::make($request->password),
                'addressee'=> $request->addressee,
                'phoneNumber'=> $request->phoneNumber,
                'role' => $request->role ?? 'user',
                'verification_code' => $verificationCode,
            ]);

            $this->sendVerificationSMS($user->phoneNumber, $verificationCode);

            $token = $user->createToken('API Token')->accessToken;

            return  response()->json(['token'=>$token,'user',$user]);

//            return response()->json([
//                'success' => true,
//                'message' => 'Registration successful. Verification code sent via SMS.',
//                'user' => $user,
//            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'errors' => [$e->getMessage()],
            ], 500);
        }


    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'phoneNumber' => 'required|digits:8',
        ]);

        $user = User::where('phoneNumber', $request->phoneNumber)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        try {
            // Generate a new verification code
            $verificationCode = rand(100000, 999999);

            // Save the verification code to the user
            $user->update([
                'verification_code' => $verificationCode,
            ]);

            // Send the verification code via SMS
//            $this->sendVerificationSMS($user->phoneNumber, $verificationCode);

            return response()->json([
                'success' => true,
                'message' => 'Verification code sent successfully.',
                'verification_code' => $verificationCode, // Include this for testing, but remove in production
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification code.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'phoneNumber' => 'required|digits:8',
            'verification_code' => 'required|digits:6',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('phoneNumber', $request->phoneNumber)
            ->where('verification_code', $request->verification_code)
            ->first();

        if (!$user) {
            return response()->json(['error' => 'Invalid phone number or verification code.'], 400);
        }

        try {
            $user->update([
                'password' => Hash::make($request->new_password),
                'verification_code' => null, // Clear the verification code after successful password reset
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to change password.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $user = User::where('verification_code', $request->code)->first();

        if (!$user) {
            return response()->json(['error' => 'Invalid verification code.'], 400);
        }

        // Mark the user as verified
        $user->is_verified = true;
        $user->verification_code = null; // Clear the code after verification
        $user->save();
        return response()->json(['success' => true, 'message' => 'Phone number verified successfully.'], 200);
    }

    private function sendVerificationSMS(string $phoneNumber, string $verificationCode): void
    {
        try {
            $sid = env('TWILIO_SID');
            $token = env('TWILIO_TOKEN');
//            $from = env('TWILIO_FROM');



            $twilio = new Client($sid, $token);
            $message = $twilio->messages
                ->create("+2169364650011", // to
                    array(
                        "from" => "+17752615526",
                        "body" => "Verification code :$verificationCode"
                    )
                );

            Log::info("Verification SMS sent to $verificationCode");

        } catch (\Twilio\Exceptions\RestException $e) {

            Log::error("Twilio error: " . $e->getMessage());

            throw new \Exception('Failed to send SMS. Please try again.');
        } catch (\Exception $e) {
            // Catch general errors
            Log::error("Error: " . $e->getMessage());
            throw new \Exception('Something went wrong. Please try again.');
        }

    }
}

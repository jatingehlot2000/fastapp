<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AuthController extends \App\Http\Controllers\Controller

{




    public function getUsers()
    {
        $users = User::all();
        $users->each(function($user) {
            if ($user->profile_pic) {
                $user->profile_pic_url = 'https://test.teerthsewanyas.org/' . $user->profile_pic;
            }
        });
        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }



    public function register(Request $request)
    {
        try {
            // Log incoming request for debugging
            Log::info('Register Request:', $request->all());

            // First validate only required fields
            $basicValidator = Validator::make($request->all(), [
                'full_name' => 'required|string|max:255',
                'email' => 'required|email',
                'mobile_no' => 'required|string',
                'role' => 'required|in:owner,broker,user',
                'referred_by' => 'nullable|string',
            ]);

            if ($basicValidator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $basicValidator->errors()
                ], 422);
            }

            // Check if unverified user exists
            $existingUser = User::where(function($query) use ($request) {
                $query->where('email', $request->email)
                      ->orWhere('mobile_no', $request->mobile_no);
            })->where('verified_otp', false)->first();

            // If unverified user exists, resend OTP
            if ($existingUser) {
                $otp = rand(100000, 999999);
                $existingUser->otp = $otp;
                $existingUser->otp_expires_at = Carbon::now()->addMinutes(15);
                $existingUser->save();

                $this->sendOTP($existingUser->mobile_no, $otp);

                return response()->json([
                    'success' => true,
                    'message' => 'OTP resent to existing registration',
                    'data' => [
                        'user_id' => $existingUser->id,
                        'mobile_no' => $existingUser->mobile_no
                    ]
                ], 200);
            }

            // Now check uniqueness for new registration
            $uniqueValidator = Validator::make($request->all(), [
                'email' => 'unique:users,email',
                'mobile_no' => 'unique:users,mobile_no',
            ]);

            if ($uniqueValidator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $uniqueValidator->errors()
                ], 422);
            }

            $otp = rand(100000, 999999);
            $referral_code = strtoupper(Str::random(8));

            $user = User::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'mobile_no' => $request->mobile_no,
                'role' => $request->role,
                'referral_code' => $referral_code,
                'referred_by' => $request->referred_by,
                'otp' => $otp,
                'otp_expires_at' => Carbon::now()->addMinutes(15),
                'verified_otp' => false,
                'login_in' => false,
            ]);

            $this->sendOTP($user->mobile_no, $otp);

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully',
                'data' => [
                    'user_id' => $user->id,
                    'mobile_no' => $user->mobile_no
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error('Register error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'mobile_no' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::where('mobile_no', $request->mobile_no)
                       ->where('verified_otp', true)
                       ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found or not verified'
                ], 404);
            }

            $otp = rand(100000, 999999);
            $user->otp = $otp;
            $user->otp_expires_at = Carbon::now()->addMinutes(15);
            $user->save();

            $this->sendOTP($user->mobile_no, $otp);

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully',
                'data' => [
                    'user_id' => $user->id,
                    'mobile_no' => $user->mobile_no
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'otp' => 'required|string|size:6',
                'type' => 'required|in:register,login'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::find($request->user_id);

            if (!$user->otp || $user->otp != $request->otp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP'
                ], 401);
            }

            if (Carbon::now()->gt($user->otp_expires_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP has expired'
                ], 401);
            }

            // Register verification - Only mark as verified, don't login
            if ($request->type === 'register') {
                $user->verified_otp = true;
                $user->otp = null;
                $user->otp_expires_at = null;
                $user->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Registration successful. Please login to continue.',
                    'data' => [
                        'user' => [
                            'id' => $user->id,
                            'full_name' => $user->full_name,
                            'email' => $user->email,
                            'mobile_no' => $user->mobile_no,
                            'role' => $user->role,
                            'referral_code' => $user->referral_code
                        ]
                    ]
                ]);
            }

            // Login verification - Set login_in and generate token
            $user->login_in = true;
            $user->otp = null;
            $user->otp_expires_at = null;
            $user->save();

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'full_name' => $user->full_name,
                        'email' => $user->email,
                        'mobile_no' => $user->mobile_no,
                        'role' => $user->role,
                        'referral_code' => $user->referral_code,
                        'profile_pic_url' => $user->profile_pic ? 'https://test.teerthsewanyas.org/' . $user->profile_pic : null
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Verify OTP error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'OTP verification failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function resendOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::find($request->user_id);

            $otp = rand(100000, 999999);
            $user->otp = $otp;
            $user->otp_expires_at = Carbon::now()->addMinutes(15);
            $user->save();

            $this->sendOTP($user->mobile_no, $otp);

            return response()->json([
                'success' => true,
                'message' => 'OTP resent successfully',
                'data' => [
                    'user_id' => $user->id,
                    'mobile_no' => $user->mobile_no
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Resend OTP error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend OTP',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $user->login_in = false;
            $user->save();

            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function sendOTP($mobile_number, $otp)
    {
        try {
            $message = "Dear User, {$otp} is Your Login Otp. Otp valid 15 minute. Regards - TEERTH SEWA NYAS";

            $response = Http::get('https://amazesms.in/api/pushsms', [
                'user' => 'Tirth',
                'authkey' => '92SmYf8pxCKI',
                'sender' => 'TRTHSN',
                'mobile' => $mobile_number,
                'text' => $message,
                'entityid' => '1701158047339525963',
                'templateid' => '1007682601829157819'
            ]);

            Log::info('OTP SMS API Response:', ['response' => $response->body()]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Error sending OTP: ' . $e->getMessage());
            return false;
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();

            $validator = Validator::make($request->all(), [
                'full_name' => 'nullable|string|max:255',
                'email' => 'nullable|email|unique:users,email,' . $user->id,
                'mobile_no' => 'nullable|string|unique:users,mobile_no,' . $user->id,
                'role' => 'nullable|in:owner,broker,user',
                'referred_by' => 'nullable|string',
                'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            if ($request->filled('full_name')) {
                $user->full_name = $request->full_name;
            }

            if ($request->filled('email')) {
                $user->email = $request->email;
            }

            if ($request->filled('mobile_no')) {
                $user->mobile_no = $request->mobile_no;
            }

            if ($request->filled('role')) {
                $user->role = $request->role;
            }

            if ($request->filled('referred_by')) {
                $user->referred_by = $request->referred_by;
            }

            if ($request->hasFile('profile_pic')) {
                if ($user->profile_pic && file_exists(public_path($user->profile_pic))) {
                    unlink(public_path($user->profile_pic));
                }

                $uploadPath = public_path('profile_pics');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $file = $request->file('profile_pic');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($uploadPath, $filename);
                $user->profile_pic = 'profile_pics/' . $filename;
            }

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'user' => [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'mobile_no' => $user->mobile_no,
                    'role' => $user->role,
                    'referral_code' => $user->referral_code,
                    'referred_by' => $user->referred_by,
                    'profile_pic_url' => $user->profile_pic ? 'https://test.teerthsewanyas.org/' . $user->profile_pic : null
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Update profile error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

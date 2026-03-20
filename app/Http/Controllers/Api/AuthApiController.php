<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Starrlight\CaregiverProfile;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Workdo\Hrm\Models\Employee;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AuthApiController extends Controller
{
    use ApiResponseTrait;
    public function login(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'email'    => 'required|string|email',
                    'password' => 'required|string',
                ]
            );
            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->errorResponse('The provided credentials are incorrect.');
            }
            $user->tokens()->delete();
            $token = $user->createToken('api-token')->plainTextToken;

            $module_name = $request->module;
            if (!empty($module_name)) {
                if ($module_name == 'Hrm' && in_array($user->type, $user->not_emp_type)) {
                    return $this->errorResponse('Staff members are the only ones allowed to log in to this application.');
                }
                $module_status = Module_is_active($module_name,  $user->created_by);
                if ($module_status != true) {
                    return $this->errorResponse('Your Add-on Is Not Activated!');
                }
            }

            $data = ['user' => $this->getUserArray($user->id), 'token' => $token, 'type' => 'bearer'];

            return $this->successResponse($data, 'User retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('something went wrong');
        }
    }
    public function getUserArray($user_id = null)
    {
        $user = User::find($user_id);
        if (!$user) {
            return $this->errorResponse('User not found.', 404);
        }
        return [
            'id'        => $user->id,
            'name'      => $user->name,
            'email'     => $user->email,
            'mobile_no' => $user->mobile_no,
            'type'      => $user->type,
            'avatar'    => $user->avatar ? getImageUrlPrefix() . '/' . $user->avatar : getImageUrlPrefix() . '/' . 'avatar.png',
            'lang'      => $user->lang ?? 'en',
        ];
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if (!empty($user)) {
            $user->currentAccessToken()->delete();

            return $this->successResponse('Logged out successfully');
        } else {
            return $this->errorResponse('Invalid login details');
        }
    }

    public function refresh(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        $token = $user->createToken('api-token')->plainTextToken;

        $data = ['user' => $this->getUserArray($user->id), 'token' => $token];
        return $this->successResponse($data, 'Token refreshed successfully');
    }

    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'password'         => 'required|confirmed|min:6',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $user = $request->user();

            if (!Hash::check($request->current_password, $user->password)) {
                return $this->errorResponse('The provided current password does not match our records.');
            }
            if (Hash::check($request->password, $user->password)) {
                return $this->errorResponse('The provided password and old password are same.');
            }

            $user->password = Hash::make($request->password);
            $user->save();
            $data = $this->getUserArray($user->id);

            return $this->successResponse($data, 'Password changed successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong');
        }
    }

    public function editProfile(Request $request)
    {
        try {
            if ($request->user_id) {
                $user = User::find($request->user_id);
            } elseif ($request->user()) {
                $user = $request->user();
            }
            if ($user) {
                $validator = Validator::make($request->all(), [
                    'name'      => 'required|string',
                    'mobile_no' => 'required|string',
                    'email'     => [
                        'required',
                        Rule::unique('users')->where(function ($query) use ($user) {
                            return $query->whereNotIn('id', [$user->id])
                                ->where('created_by', creatorId());
                        }),
                    ],
                ]);

                if ($validator->fails()) {
                    return $this->validationErrorResponse($validator->errors());
                }
                // Handle profile image upload
                if ($request->hasFile('profile')) {
                    $filenameWithExt = $request->file('profile')->getClientOriginalName();
                    $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension       = $request->file('profile')->getClientOriginalExtension();
                    $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                    $path = upload_file($request, 'profile', $fileNameToStore, '');

                    if ($path['flag'] == 0) {
                        return $this->errorResponse($path['msg']);
                    }

                    // Delete old avatar if exists
                    if (!empty($user->avatar) && strpos($user->avatar, 'avatar.png') === false && getImageUrlPrefix($user->avatar)) {
                        delete_file($user->avatar);
                    }

                    $user->avatar = ltrim($path['url'], '/');
                }
                //  Update user fields
                $user->name      = $request->name;
                $user->email     = $request->email;
                $user->mobile_no = $request->mobile_no;
                $user->save();

                $data = $this->getUserArray($user->id);

                return $this->successResponse($data, 'Profile updated successfully');
            } else {
                return $this->errorResponse('User not found', 404);
            }
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong');
        }
    }

    public function deleteAccount(Request $request)
    {
        try {
            $user = $request->user();
            $user->delete();

            return $this->successResponse(null, 'Account deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong');
        }
    }

    /**
     * Register a new caregiver user
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
                'firstName' => 'required|string|max:255',
                'lastName' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            // Create user with caregiver type
            $user = User::create([
                'name' => $request->firstName . ' ' . $request->lastName,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'type' => 'caregiver',
                'lang' => 'en',
            ]);

            // Create empty caregiver profile
            $profile = CaregiverProfile::create([
                'user_id' => $user->id,
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'email' => $request->email,
                'status' => 'draft',
            ]);

            // Generate API token
            $token = $user->createToken('api-token')->plainTextToken;

            $data = [
                'user' => $this->getUserArray($user->id),
                'caregiver_profile_id' => $profile->id,
                'token' => $token,
                'type' => 'bearer'
            ];

            return $this->successResponse($data, 'Registration successful.');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Send password reset link
     */
    public function forgotPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return $this->errorResponse('We cannot find a user with that email address.');
            }

            // Generate reset token
            $token = Password::getSender();

            // For API, we'll return a success message
            // In production, you would send an email with the reset link
            return $this->successResponse([
                'message' => 'Password reset link has been sent to your email address.',
                'token' => $token // In production, this would be sent via email, not returned in response
            ], 'Password reset link sent.');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong');
        }
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email',
                'password' => 'required|string|min:6|confirmed',
                'token' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->password = Hash::make($password);
                    $user->save();
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                return $this->successResponse(null, 'Password has been reset successfully.');
            } else {
                return $this->errorResponse('Failed to reset password. Please try again.');
            }
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong');
        }
    }

    /**
     * Get current authenticated user
     */
    public function me(Request $request)
    {
        try {
            $user = $request->user();
            $data = $this->getUserArray($user->id);
            return $this->successResponse($data, 'User retrieved successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong');
        }
    }
}

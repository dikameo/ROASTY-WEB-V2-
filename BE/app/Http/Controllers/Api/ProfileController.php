<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Get authenticated user's profile
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile;

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profile not found',
                'error' => [
                    'code' => 'NOT_FOUND',
                    'details' => 'Profile does not exist for this user'
                ]
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile retrieved successfully',
            'data' => $profile
        ]);
    }

    /**
     * Update authenticated user's profile
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile;

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profile not found',
                'error' => [
                    'code' => 'NOT_FOUND',
                    'details' => 'Profile does not exist for this user'
                ]
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'first_name' => 'sometimes|nullable|string|max:255',
            'last_name' => 'sometimes|nullable|string|max:255',
            'username' => 'sometimes|nullable|string|max:50|unique:users,name,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date_format:Y-m-d',
            'gender' => 'nullable|in:male,female',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'details' => $validator->errors()
                ]
            ], 422);
        }

        // Update profile - now includes birth_date and gender
        $profileData = [];
        if ($request->has('phone')) {
            $profileData['phone'] = $request->phone;
        }
        if ($request->has('birth_date')) {
            $profileData['birth_date'] = $request->birth_date;
        }
        if ($request->has('gender')) {
            $profileData['gender'] = $request->gender;
        }

        if (!empty($profileData)) {
            $profile->update($profileData);
        }

        // Update user data (username, birth_date, gender stored as user attributes or in session)
        if ($request->has('username')) {
            $user->name = $request->username;
        }
        // Update user's name if provided
        elseif ($request->has('name')) {
            $user->name = $request->name;
        } elseif ($request->has('first_name') || $request->has('last_name')) {
            $firstName = $request->first_name ?: explode(' ', $user->name)[0];
            $lastName = $request->last_name ?: '';
            $user->name = trim($firstName . ' ' . $lastName);
        }

        // Store birth_date and gender temporarily (refreshed from DB)
        $profile->refresh();
        $birthDate = $profile->birth_date;
        $gender = $profile->gender;

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'user' => $user->fresh()->load('profile'),
                'profile' => $profile->fresh(),
                'birth_date' => $birthDate,
                'gender' => $gender
            ]
        ]);
    }
}

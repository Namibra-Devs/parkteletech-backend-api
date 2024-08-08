<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\FilesInternal; 
use App\Traits\Upload;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    use Upload;

    /**
     * Register a new user and issue a token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        try {
            $fields = $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|email|unique:users,email',
                'password' => 'required|string|confirmed',
                'role' => 'required|string', // Validate role
                'dob' => 'required|date',
                'phone' => 'required|string|max:20',
                'id_type' => 'required|string',
                'id_no' => 'required|string',
                'employment_status' => 'required|string',
                'address' => 'required|string',
                'department' => 'required|string',
                // 'files.*' => 'file' // Validate files
            ]);

            $user = User::create([
                'name' => $fields['name'],
                'email' => $fields['email'],
                'password' => bcrypt($fields['password']),
                'role' => $fields['role'], // Store role
                'dob' => $fields['dob'],
                'phone' => $fields['phone'],
                'id_type' => $fields['id_type'],
                'id_no' => $fields['id_no'],
                'employment_status' => $fields['employment_status'],
                'address' => $fields['address'],
                'department' => $fields['department'],
            ]);

            // Handle file uploads
            $fileDetails = [];
            if ($request->hasFile('files')) {
                $names = ['cv', 'cert', 'hse_cert']; // Adjust names based on your needs
                $count = 0;
                foreach ($request->file('files') as $file) {
                    $path = $this->uploadFile($file, 'user_documents');
                    $fileDetails[] = ['path' => $path];

                    FilesInternal::create([
                        'staff_detail_id' => $user->id, // Assuming a foreign key 'staff_detail_id' in FilesInternal
                        'name' => $names[$count] ?? 'unknown', // Handle array index out of bounds
                        'path' => $path,
                    ]);

                    $count++;
                }
            }

            return response()->json([
                'message' => 'User successfully registered.',
                'user' => $user,
                'files' => $fileDetails,
                'token' => $user->createToken('myapptoken')->plainTextToken
            ], 201);
        } catch (ValidationException $e) {
            if (isset($e->errors()['email'])) {
                return response()->json([
                    'message' => 'User already exists with this email. Please use a different email address.'
                ], 409);
            }

            return $this->handleException($e, 'Validation error.', 422);
        } catch (Exception $e) {
            return $this->handleException($e, 'An error occurred while processing your request.', 500);
        }
    }

    /**
     * Admin login and issue a token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        return response()->json([
            'message' => 'Admin logged in successfully.',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    /**
     * Logout the user and revoke all tokens.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out'], 200);
    }

    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $users = User::all();
            return response()->json($users, 200);
        } catch (Exception $e) {
            return $this->handleException($e, 'An error occurred while fetching users.', 500);
        }
    }

    /**
     * Display the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            $files = FilesInternal::where('staff_detail_id', $id)->get();

            return response()->json([
                'data' => $user,
                'files' => $files
            ], 200);
        } catch (ModelNotFoundException $e) {
            return $this->handleException($e, 'User not found.', 404);
        } catch (Exception $e) {
            return $this->handleException($e, 'An error occurred while fetching user details.', 500);
        }
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // return $request;
        try {
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email',
                'dob' => 'sometimes|date',
                'phone' => 'sometimes|string|max:20',
                'id_type' => 'sometimes|string',
                'id_no' => 'sometimes|string',
                'employment_status' => 'sometimes|string',
                'address' => 'sometimes|string',
                'department' => 'sometimes|string',
                'role' => 'sometimes|string', // Update role
                // 'files.*' => 'file' // Validate files
            ]);

            $user = User::findOrFail($id);

            // Handle file deletions
            $files = FilesInternal::where('staff_detail_id', $id)->get();
            foreach ($files as $file) {
                $this->deleteFile($file->path);
                $file->delete();
            }

            // Update user details
            $user->update($request->except('files'));


            // Handle file uploads
            $fileDetails = [];
            if ($request->hasFile('files')) {
                $names = ['cv', 'cert', 'hse_cert']; // Adjust names based on your needs
                $count = 0;
                foreach ($request->file('files') as $file) {
                    $path = $this->uploadFile($file, 'user_documents');
                    $fileDetails[] = ['path' => $path];

                    FilesInternal::create([
                        'staff_detail_id' => $user->id,
                        'name' => $names[$count] ?? 'unknown', // Handle array index out of bounds
                        'path' => $path,
                    ]);

                    $count++;
                }
            }

            $updatedFiles = FilesInternal::where('staff_detail_id', $user->id)->get();

            return response()->json([
                'message' => 'User details updated successfully.',
                'data' => $user,
                'files' => $updatedFiles,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return $this->handleException($e, 'User not found.', 404);
        } catch (ValidationException $e) {
            return $this->handleException($e, 'Validation error.', 422);
        } catch (Exception $e) {
            return $this->handleException($e, 'An error occurred while updating user details.', 500);
        }
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            // Check if the user exists
            $user = User::findOrFail($id);

            // Get all files associated with the user
            $files = FilesInternal::where('staff_detail_id', $id)->get();

            foreach ($files as $file) {
                $this->deleteFile($file->path); // Delete from storage
                $file->delete(); // Delete the record from the database
            }

            $user->delete(); // Delete the user

            return response()->json([
                'message' => 'User and associated files deleted successfully.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return $this->handleException($e, 'User not found.', 404);
        } catch (Exception $e) {
            return $this->handleException($e, 'An error occurred while deleting the user.', 500);
        }
    }

    /**
     * Handle exceptions and return a standardized response.
     *
     * @param  \Exception  $e
     * @param  string  $defaultMessage
     * @param  int  $statusCode
     * @return \Illuminate\Http\Response
     */
    private function handleException(Exception $e, $defaultMessage, $statusCode)
    {
        return response()->json([
            'message' => $defaultMessage,
            'error' => $e->getMessage(),
        ], $statusCode);
    }
}

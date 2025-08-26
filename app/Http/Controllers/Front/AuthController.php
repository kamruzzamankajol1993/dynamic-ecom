<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Auth;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash; // For password hashing
use Illuminate\Support\Str; // For generating unique file names
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\File; // For file operations (delete)
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail; // <-- Add Mail facade
use App\Mail\PasswordResetMail; // <-- Add our new Mailable
class AuthController extends Controller
{
    public function loginregisterPage()
    {
        return view('front.auth.loginRegister'); 
}

public function registerUserPost(Request $request)
    {
        // 1. Validate the incoming request data for Customer
        $validatedCustomerData = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('customers', 'email'), // Ensure email is unique in customers table
                Rule::unique('users', 'email'),     // Ensure email is also unique in users table
            ],
            'status' => 'nullable|string|in:active,inactive', // Assuming these are your valid statuses
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // 2MB Max
            'nid_front_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'nid_back_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 2. Handle image uploads directly to the public folder using Intervention Image
        $customerImagePath = null;
        $customerImageDir = 'uploads/customer_images/'; // Directory inside public folder
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $fileName = time() . '_customer_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = public_path($customerImageDir . $fileName);

            // Ensure the directory exists
            if (!File::isDirectory(public_path($customerImageDir))) { // Use File facade
                File::makeDirectory(public_path($customerImageDir), 0777, true, true);
            }

            Image::read($image)->save($path);
            $customerImagePath = $customerImageDir . $fileName; // Store relative path in DB
        }

        $nidFrontImagePath = null;
        $nidImageDir = 'uploads/nid_images/'; // Directory inside public folder for NID images
        if ($request->hasFile('nid_front_image')) {
            $image = $request->file('nid_front_image');
            $fileName = time() . '_nid_front_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = public_path($nidImageDir . $fileName);

            // Ensure the directory exists
            if (!File::isDirectory(public_path($nidImageDir))) { // Use File facade
                File::makeDirectory(public_path($nidImageDir), 0777, true, true);
            }

            Image::read($image)->save($path);
            $nidFrontImagePath = $nidImageDir . $fileName; // Store relative path in DB
        }

        $nidBackImagePath = null;
        if ($request->hasFile('nid_back_image')) {
            $image = $request->file('nid_back_image');
            $fileName = time() . '_nid_back_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = public_path($nidImageDir . $fileName);

            // Ensure the directory exists
            if (!File::isDirectory(public_path($nidImageDir))) { // Use File facade
                File::makeDirectory(public_path($nidImageDir), 0777, true, true);
            }

            Image::read($image)->save($path);
            $nidBackImagePath = $nidImageDir . $fileName; // Store relative path in DB
        }

        // 3. Create a new Customer record
        $customer = Customer::create([
            'name' => $validatedCustomerData['name'],
            'address' => $validatedCustomerData['address'] ?? null, // Use null for nullable fields if not present
            'phone' => $validatedCustomerData['phone'] ?? null,
            'email' => $validatedCustomerData['email'],
            'status' => 'active', // Default status if not provided
            'image' => $customerImagePath,
            'nid_front_image' => $nidFrontImagePath,
            'nid_back_image' => $nidBackImagePath,
        ]);

        // 4. Create a new User record associated with the customer
        $defaultPassword = '12345678';
        User::create([
            'name' => $validatedCustomerData['name'],
            'phone' => $validatedCustomerData['phone'] ?? null,
            'email' => $validatedCustomerData['email'],
            'password' => Hash::make($request->password), // Always hash the password
            'viewpassword' => $request->password,          // Store plain text for viewpassword as per request
            'customer_id' => $customer->id,              // Link to the newly created customer
            'user_type' => 2,                             // As specified, user_type will always be 2
        ]);

        // 5. Redirect back with a success message
        return redirect()->back()->with('success', 'Registered successfully, now login!');
    }

     public function loginUserPost(Request $request): RedirectResponse
    {
        // 1. Validate the incoming request data
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Attempt to authenticate the user
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            
            // 3. Regenerate session
            $request->session()->regenerate();

            // 4. Redirect to the named dashboard route
            //    This is the key update.
            return redirect()->intended(route('front.userDashboard'))
                ->with('success', 'You have successfully logged in!');
        }

        // 5. If authentication fails, redirect back with an error
        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }
    public function userDashboard()
    {
        // 1. Get the authenticated user
        $user = Auth::user();

        // 2. Check if the user is authenticated
        if (!$user && $user->user_type != 2) {
            return redirect()->route('front.loginRegister')->with('error', 'You must be logged in to access the dashboard.');
        }

        // 3. Return the user dashboard view with user data
        return view('front.auth.userDashboard', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        // Define validation rules for password
        $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user = Auth::user();
        
        // Update the password
        $user->password = Hash::make($request->password);
        $user->viewpassword = $request->password;
        $user->save();

        return redirect()->route('front.userDashboard')->with('success', 'Password changed successfully!');
    }


    public function updateProfile(Request $request)
    {
        // 1. Validate the form data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'nid_front_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'nid_back_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            // Start a database transaction
            DB::beginTransaction();

            // 2. Find the authenticated User and their associated Customer record
            $user = Auth::user();
            // Assuming user->customer_id links to the customers table primary key
            $customer = Customer::findOrFail($user->customer_id);

            // 3. Update text-based fields on BOTH models
            $user->name = $validatedData['name'];
            $customer->name = $validatedData['name'];
            
            $user->phone = $validatedData['phone'];
            $customer->phone = $validatedData['phone'];

          

            // 4. Handle Image Uploads, mirroring your existing registration logic
            
            // --- Profile Image ---
            if ($request->hasFile('image')) {
                $path = $this->handleImageUpload($request->file('image'), 'uploads/customer_images/', 'customer', $user->image);
                //$user->image = $path;
                $customer->image = $path;
            }

            // --- NID Front Image ---
            if ($request->hasFile('nid_front_image')) {
                $path = $this->handleImageUpload($request->file('nid_front_image'), 'uploads/nid_images/', 'nid_front', $user->nid_front_image);
                //$user->nid_front_image = $path;
                $customer->nid_front_image = $path;
            }

            // --- NID Back Image ---
            if ($request->hasFile('nid_back_image')) {
                $path = $this->handleImageUpload($request->file('nid_back_image'), 'uploads/nid_images/', 'nid_back', $user->nid_back_image);
                //$user->nid_back_image = $path;
                $customer->nid_back_image = $path;
            }

            // 5. Save both models
            $user->save();
            $customer->save();

            // If everything is successful, commit the transaction
            DB::commit();

            return redirect()->back()->with('success', 'Profile updated successfully!');

        } catch (\Exception $e) {
            // If any error occurs, roll back the transaction
            DB::rollBack();
            // Optionally log the error: Log::error($e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating your profile. Please try again.');
        }
    }

    /**
     * Reusable private method to handle image upload, saving, and old file deletion.
     * This keeps the main method cleaner and follows your established pattern.
     */
    private function handleImageUpload($file, $directory, $prefix, $oldImagePath = null)
    {
        // Delete the old file if it exists
        if ($oldImagePath && File::exists(public_path($oldImagePath))) {
            File::delete(public_path($oldImagePath));
        }

        // Generate a new unique filename
        $fileName = time() . '_' . $prefix . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $path = public_path($directory . $fileName);

        // Ensure the directory exists
        if (!File::isDirectory(public_path($directory))) {
            File::makeDirectory(public_path($directory), 0777, true, true);
        }
        
        // Save the new image using Intervention Image
        Image::read($file)->save($path);

        // Return the relative path to store in the database
        return $directory . $fileName;
    }

   // ---------- NEW FORGOT PASSWORD METHODS ----------

    /**
     * Display the form to request a password reset link.
     */
    public function showForgotPasswordForm(): View
    {
        return view('front.auth.password.email');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'We can\'t find a user with that email address.']);
        }

        //Create Password Reset Token
        $token = Password::broker()->createToken($user);

        try {
            Mail::to($user->email)->send(new PasswordResetMail($token, $user));
            return back()->with('success', 'We have e-mailed your password reset link!');
        } catch (\Exception $e) {
            // You can log the error here if needed: \Log::error($e);
            return back()->withErrors(['email' => 'Unable to send password reset email. Please try again later.']);
        }
    }


    /**
     * Display the password reset view for the given token.
     */
    public function showResetPasswordForm(Request $request, $token): View
    {
        return view('front.auth.password.reset', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Handle an incoming new password request.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'viewpassword' => $password, // Also updating viewpassword as per your existing logic
                ])->save();

                // event(new PasswordReset($user)); // <-- REMOVED AS REQUESTED
            }
        );

        // If the password was successfully reset, we will redirect the user to the
        // login page and display a success message.
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('front.loginRegister')->with('success', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }
}
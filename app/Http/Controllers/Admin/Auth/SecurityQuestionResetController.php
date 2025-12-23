<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SecurityQuestionResetController extends Controller
{
    /**
     * Display the email entry form.
     */
    public function showEmailForm(): View
    {
        return view('admin.auth.security-reset.email');
    }

    /**
     * Verify email and show security question.
     */
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => __('Email is required'),
            'email.email' => __('Please enter a valid email address'),
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            $notification = ['messege' => __('Email does not exist'), 'alert-type' => 'error'];
            return redirect()->back()->with($notification)->withInput();
        }

        if (!$admin->security_question || !$admin->security_answer) {
            $notification = ['messege' => __('No security question set for this account. Please contact administrator.'), 'alert-type' => 'error'];
            return redirect()->back()->with($notification)->withInput();
        }

        // Store email in session for next step
        session(['security_reset_email' => $admin->email]);

        return view('admin.auth.security-reset.question', [
            'security_question' => $admin->security_question,
            'email' => $admin->email,
        ]);
    }

    /**
     * Verify security answer and show password reset form.
     */
    public function verifyAnswer(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'security_answer' => ['required', 'string'],
        ], [
            'security_answer.required' => __('Security answer is required'),
        ]);

        // Verify session email matches
        if (session('security_reset_email') !== $request->email) {
            $notification = ['messege' => __('Invalid session. Please start again.'), 'alert-type' => 'error'];
            return redirect()->route('admin.security-reset')->with($notification);
        }

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            $notification = ['messege' => __('Email does not exist'), 'alert-type' => 'error'];
            return redirect()->route('admin.security-reset')->with($notification);
        }

        if (!Hash::check($request->security_answer, $admin->security_answer)) {
            $notification = ['messege' => __('Security answer is incorrect'), 'alert-type' => 'error'];
            return redirect()->back()->with($notification)->withInput(['email' => $request->email]);
        }

        // Generate a token for the password reset step
        $token = bin2hex(random_bytes(32));
        session(['security_reset_token' => $token]);

        return view('admin.auth.security-reset.reset-password', [
            'email' => $admin->email,
            'token' => $token,
        ]);
    }

    /**
     * Reset the password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:4', 'confirmed'],
        ], [
            'password.required' => __('Password is required'),
            'password.min' => __('Password must be at least 4 characters'),
            'password.confirmed' => __('Password confirmation does not match'),
        ]);

        // Verify session
        if (session('security_reset_email') !== $request->email || session('security_reset_token') !== $request->token) {
            $notification = ['messege' => __('Invalid session. Please start again.'), 'alert-type' => 'error'];
            return redirect()->route('admin.security-reset')->with($notification);
        }

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            $notification = ['messege' => __('Email does not exist'), 'alert-type' => 'error'];
            return redirect()->route('admin.security-reset')->with($notification);
        }

        // Update password
        $admin->password = Hash::make($request->password);
        $admin->forget_password_token = null; // Clear any existing token
        $admin->save();

        // Clear session data
        session()->forget(['security_reset_email', 'security_reset_token']);

        $notification = ['messege' => __('Password reset successfully. Please login with your new password.'), 'alert-type' => 'success'];
        return redirect()->route('admin.login')->with($notification);
    }
}

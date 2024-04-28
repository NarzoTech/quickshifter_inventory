<?php

namespace Modules\NewsLetter\app\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\MailSenderService;
use App\Http\Controllers\Controller;
use Modules\NewsLetter\app\Models\NewsLetter;
use Modules\NewsLetter\app\Jobs\NewsLetterVerifyJob;

class NewsLetterController extends Controller
{
    public function newsletter_request(Request $request)
    {

        $request->validate([
            'email' => 'required|unique:news_letters',
        ], [
            'email.required' => __('Email is required'),
            'email.unique' => __('Email already exist'),
        ]);

        $newsletter = new NewsLetter();
        $newsletter->email = $request->email;
        $newsletter->verify_token = Str::random(100);
        $newsletter->save();

        (new MailSenderService)->sendVerifyMailToNewsletterFromTrait($newsletter);

        return response()->json(['message' => __('A verification link has been send to your email, please verify it and getting our newsletter')]);

    }

    public function newsletter_verification($token)
    {
        $newsletter = NewsLetter::where('verify_token', $token)->first();

        if ($newsletter) {
            $newsletter->verify_token = null;
            $newsletter->status = 'verified';
            $newsletter->save();

            $notification = __('Newsletter verification successfully');
            $notification = ['messege' => $notification, 'alert-type' => 'success'];

            return redirect()->route('home')->with($notification);

        } else {
            $notification = __('Newsletter verification faild for invalid token');
            $notification = ['messege' => $notification, 'alert-type' => 'error'];

            return redirect()->route('home')->with($notification);
        }

    }
}

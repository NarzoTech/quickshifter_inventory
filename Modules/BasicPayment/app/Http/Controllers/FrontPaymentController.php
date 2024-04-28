<?php

namespace Modules\BasicPayment\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\GetGlobalInformationTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Session;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe;

class FrontPaymentController extends Controller
{
    use GetGlobalInformationTrait;

    public function pay_with_stripe(Request $request, $stripe_credentials, $payable_amount, $after_success_url, $after_faild_url, $user)
    {

        $rules = [
            'card_number' => 'required|numeric',
            'year' => 'required|numeric',
            'month' => 'required|numeric',
            'cvc' => 'required|numeric',
        ];

        $customMessages = [
            'card_number.required' => __('Card number is required'),
            'year.required' => __('Year is required'),
            'month.required' => __('Month is required'),
            'cvc.required' => __('Cvc is required'),
        ];

        $request->validate($rules, $customMessages);

        Stripe\Stripe::setApiKey($stripe_credentials->stripe_secret);

        try {
            $token = Stripe\Token::create([
                'card' => [
                    'number' => $request->card_number,
                    'exp_month' => $request->month,
                    'exp_year' => $request->year,
                    'cvc' => $request->cvc,
                ],
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $notification = __('Please provide valid card information');
            $notification = ['messege' => $notification, 'alert-type' => 'error'];

            return redirect()->back()->with($notification);
        }

        if (! isset($token['id'])) {
            $notification = __('Payment faild please try again');
            $notification = ['messege' => $notification, 'alert-type' => 'error'];

            return redirect()->back()->with($notification);
        }

        $calculate_payable_charge = $this->calculate_payable_charge($payable_amount, 'stripe');
        $payable_with_charge = (int) ($calculate_payable_charge->payable_with_charge * 100);

        try {
            $result = Stripe\Charge::create([
                'card' => $token['id'],
                'currency' => $calculate_payable_charge->currency_code,
                'amount' => $payable_with_charge,
                'description' => env('APP_NAME'),
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $notification = __('Payment faild please try again');
            $notification = ['messege' => $notification, 'alert-type' => 'error'];

            return redirect()->back()->with($notification);
        }

        Session::put('after_success_url', $after_success_url);
        Session::put('after_faild_url', $after_faild_url);
        Session::put('payable_amount', $payable_amount);
        Session::put('after_success_gateway', 'Stripe');
        Session::put('after_success_transaction', $result->balance_transaction);

        return redirect($after_success_url);

    }

    public function pay_with_paypal($paypal_credentials, $payable_amount, $after_success_url, $after_faild_url, $user)
    {

        config(['paypal.mode' => $paypal_credentials->paypal_account_mode]);

        if ($paypal_credentials->paypal_account_mode == 'sandbox') {
            config(['paypal.sandbox.client_id' => $paypal_credentials->paypal_client_id]);
            config(['paypal.sandbox.client_secret' => $paypal_credentials->paypal_secret_key]);
        } else {
            config(['paypal.sandbox.client_id' => $paypal_credentials->paypal_client_id]);
            config(['paypal.sandbox.client_secret' => $paypal_credentials->paypal_secret_key]);
            config(['paypal.sandbox.app_id' => 'APP-80W284485P519543T']);
        }

        $calculate_payable_charge = $this->calculate_payable_charge($payable_amount, 'paypal');
        $payable_with_charge = $calculate_payable_charge->payable_with_charge;

        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $paypalToken = $provider->getAccessToken();
            $response = $provider->createOrder([
                'intent' => 'CAPTURE',
                'application_context' => [
                    'return_url' => route('basicpayment.paypal-success-payment'),
                    'cancel_url' => $after_faild_url,
                ],
                'purchase_units' => [
                    0 => [
                        'amount' => [
                            'currency_code' => $calculate_payable_charge->currency_code,
                            'value' => $payable_with_charge,
                        ],
                    ],
                ],
            ]);
        } catch (Exception $ex) {
            $notification = $ex->getMessage();
            $notification = ['messege' => $notification, 'alert-type' => 'error'];

            Log::error($ex->getMessage());
            return redirect()->back()->with($notification);
        }

        if (isset($response['id']) && $response['id'] != null) {

            Session::put('after_success_url', $after_success_url);
            Session::put('after_faild_url', $after_faild_url);
            Session::put('payable_amount', $payable_amount);
            Session::put('paypal_credentials', $paypal_credentials);

            // redirect to approve href
            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    return redirect()->away($links['href']);
                }
            }

            $notification = __('Payment faild, please try again');
            $notification = ['messege' => $notification, 'alert-type' => 'error'];

            return redirect()->back()->with($notification);

        } else {
            $notification = __('Payment faild, please try again');
            $notification = ['messege' => $notification, 'alert-type' => 'error'];

            return redirect()->back()->with($notification);
        }

    }

    public function paypal_success(Request $request)
    {

        $paypal_credentials = Session::get('paypal_credentials');

        config(['paypal.mode' => $paypal_credentials->paypal_account_mode]);

        if ($paypal_credentials->paypal_account_mode == 'sandbox') {
            config(['paypal.sandbox.client_id' => $paypal_credentials->paypal_client_id]);
            config(['paypal.sandbox.client_secret' => $paypal_credentials->paypal_secret_key]);
        } else {
            config(['paypal.sandbox.client_id' => $paypal_credentials->paypal_client_id]);
            config(['paypal.sandbox.client_secret' => $paypal_credentials->paypal_secret_key]);
            config(['paypal.sandbox.app_id' => 'APP-80W284485P519543T']);
        }

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);
        if (isset($response['status']) && $response['status'] == 'COMPLETED') {

            Session::put('after_success_gateway', 'Paypal');
            Session::put('after_success_transaction', $request->PayerID);

            $after_success_url = Session::get('after_success_url');

            return redirect($after_success_url);

        } else {
            $after_faild_url = Session::get('after_faild_url');

            return redirect($after_faild_url);
        }

    }
}

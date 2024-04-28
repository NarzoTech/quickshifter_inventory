<?php

namespace Modules\BasicPayment\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\BasicPayment\app\Models\BasicPayment;
use Modules\Currency\app\Models\MultiCurrency;

class BasicPaymentController extends Controller {
    public function basicpayment() {
        checkAdminHasPermissionAndThrowException( 'basic.payment.view' );
        $payment_info = BasicPayment::get();

        $basic_payment = [];

        foreach ( $payment_info as $payment_item ) {
            $basic_payment[$payment_item->key] = $payment_item->value;
        }

        $basic_payment = (object) $basic_payment;

        $currencies = MultiCurrency::get();

        return view( 'basicpayment::index', compact( 'basic_payment', 'currencies' ) );
    }

    public function update_stripe( Request $request ) {
        checkAdminHasPermissionAndThrowException( 'basic.payment.update' );
        $rules = [
            'stripe_key'         => 'required',
            'stripe_secret'      => 'required',
            'stripe_currency_id' => 'required',
            'stripe_charge'      => 'required|numeric',
        ];
        $customMessages = [
            'stripe_key.required'         => __( 'Stripe key is required' ),
            'stripe_secret.required'      => __( 'Stripe secret is required' ),
            'stripe_currency_id.required' => __( 'Currency is required' ),
            'stripe_charge.required'      => __( 'Gateway charge is required' ),
            'stripe_charge.numeric'       => __( 'Gateway charge should be numeric' ),
        ];

        $request->validate( $rules, $customMessages );

        BasicPayment::where( 'key', 'stripe_key' )->update( ['value' => $request->stripe_key] );
        BasicPayment::where( 'key', 'stripe_secret' )->update( ['value' => $request->stripe_secret] );
        BasicPayment::where( 'key', 'stripe_currency_id' )->update( ['value' => $request->stripe_currency_id] );
        BasicPayment::where( 'key', 'stripe_charge' )->update( ['value' => $request->stripe_charge] );
        BasicPayment::where( 'key', 'stripe_status' )->update( ['value' => $request->stripe_status] );

        if ( $request->file( 'stripe_image' ) ) {
            $stripe_setting = BasicPayment::where( 'key', 'stripe_image' )->first();
            $file_name = file_upload( $request->stripe_image, 'uploads/custom-images/', $stripe_setting->value );
            $stripe_setting->value = $file_name;
            $stripe_setting->save();
        }
        $this->put_basic_payment_cache();

        $notification = __( 'Update Successfully' );
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with( $notification );
    }

    public function update_paypal( Request $request ) {
        checkAdminHasPermissionAndThrowException( 'basic.payment.update' );

        $rules = [
            'paypal_client_id'   => 'required',
            'paypal_secret_key'  => 'required',
            'paypal_currency_id' => 'required',
            'paypal_charge'      => 'required|numeric',
        ];

        $customMessages = [
            'paypal_client_id.required'   => __( 'Client is required' ),
            'paypal_secret_key.required'  => __( 'Secret key is required' ),
            'paypal_currency_id.required' => __( 'Currency is required' ),
            'paypal_charge.required'      => __( 'Gateway charge is required' ),
            'paypal_charge.numeric'       => __( 'Gateway charge should be numeric' ),
        ];

        $request->validate( $rules, $customMessages );

        BasicPayment::where( 'key', 'paypal_client_id' )->update( ['value' => $request->paypal_client_id] );
        BasicPayment::where( 'key', 'paypal_secret_key' )->update( ['value' => $request->paypal_secret_key] );
        BasicPayment::where( 'key', 'paypal_currency_id' )->update( ['value' => $request->paypal_currency_id] );
        BasicPayment::where( 'key', 'paypal_charge' )->update( ['value' => $request->paypal_charge] );
        BasicPayment::where( 'key', 'paypal_status' )->update( ['value' => $request->paypal_status] );
        BasicPayment::where( 'key', 'paypal_account_mode' )->update( ['value' => $request->paypal_account_mode] );

        if ( $request->file( 'paypal_image' ) ) {
            $paypal_setting = BasicPayment::where( 'key', 'paypal_image' )->first();
            $file_name = file_upload( $request->paypal_image, 'uploads/custom-images/', $paypal_setting->value );
            $paypal_setting->value = $file_name;
            $paypal_setting->save();
        }
        $this->put_basic_payment_cache();

        $notification = __( 'Update Successfully' );
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with( $notification );
    }

    public function update_bank_payment( Request $request ) {
        checkAdminHasPermissionAndThrowException( 'basic.payment.update' );

        $rules = [
            'bank_information' => 'required',
            'bank_currency_id' => 'required',
            'bank_charge'      => 'required|numeric',
        ];

        $customMessages = [
            'bank_information.required' => __( 'Bank information is required' ),
            'bank_currency_id.required' => __( 'Currency is required' ),
            'bank_charge.required'      => __( 'Gateway charge is required' ),
            'bank_charge.numeric'       => __( 'Gateway charge should be numeric' ),
        ];

        $request->validate( $rules, $customMessages );

        BasicPayment::where( 'key', 'bank_information' )->update( ['value' => $request->bank_information] );
        BasicPayment::where( 'key', 'bank_currency_id' )->update( ['value' => $request->bank_currency_id] );
        BasicPayment::where( 'key', 'bank_charge' )->update( ['value' => $request->bank_charge] );
        BasicPayment::where( 'key', 'bank_status' )->update( ['value' => $request->bank_status] );

        if ( $request->file( 'bank_image' ) ) {
            $bank_setting = BasicPayment::where( 'key', 'bank_image' )->first();
            $file_name = file_upload( $request->bank_image, 'uploads/custom-images/', $bank_setting->value );
            $bank_setting->value = $file_name;
            $bank_setting->save();
        }

        $this->put_basic_payment_cache();

        $notification = __( 'Update Successfully' );
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with( $notification );

    }

    private function put_basic_payment_cache() {
        $payment_info = BasicPayment::get();
        $basic_payment = [];
        foreach ( $payment_info as $payment_item ) {
            $basic_payment[$payment_item->key] = $payment_item->value;
        }
        $basic_payment = (object) $basic_payment;
        Cache::put( 'basic_payment',$basic_payment );
    }
}

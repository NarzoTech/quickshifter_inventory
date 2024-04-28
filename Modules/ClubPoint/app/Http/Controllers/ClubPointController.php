<?php

namespace Modules\ClubPoint\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\ClubPoint\app\Models\ClubPointHistory;
use Modules\GlobalSetting\app\Models\Setting;
use Modules\Wallet\app\Models\WalletHistory;

class ClubPointController extends Controller
{
    public function index()
    {
        $user = Auth::guard('web')->user();

        $histories = ClubPointHistory::with('order')->where('user_id', $user->id)->latest()->get();

        $wallet_balance = WalletHistory::where('user_id', $user->id)->sum('amount');

        return view('clubpoint::index', ['histories' => $histories, 'wallet_balance' => $wallet_balance]);
    }

    public function clubpoint_convert($id)
    {
        $user = Auth::guard('web')->user();

        $club_point_rate = Setting::where('key', 'club_point_rate')->first();

        $history = ClubPointHistory::findOrFail($id);

        $convert_amount = (int) ($history->club_point / $club_point_rate->value);

        $json_module_data = file_get_contents(base_path('modules_statuses.json'));
        $module_status = json_decode($json_module_data);

        if ($module_status->ClubPoint) {
            $new_wallet = new WalletHistory();
            $new_wallet->user_id = $user->id;
            $new_wallet->amount = $convert_amount;
            $new_wallet->transaction_id = 'club_point_to_wallet';
            $new_wallet->payment_gateway = 'Club Point';
            $new_wallet->payment_status = 'success';
            $new_wallet->save();

            $wallet_balance = $user->wallet_balance;
            $wallet_balance += $convert_amount;
            $user->wallet_balance = $wallet_balance;
            $user->save();
        }

        $history->status = 'success';
        $history->save();

        return back();
    }
}

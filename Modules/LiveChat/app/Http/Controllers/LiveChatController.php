<?php

namespace Modules\LiveChat\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Modules\LiveChat\app\Events\LiveChatEvent;
use Modules\LiveChat\app\Models\Message;

class LiveChatController extends Controller
{
    public function index()
    {
        $auth_user = Auth::guard('web')->user();

        $contact_list = $auth_user->contactUsersWithUnseenMessages();

        $user_list = User::where('id', '!=', $auth_user->id)->get();

        return view('livechat::index')->with(['auth_user' => $auth_user, 'user_list' => $user_list, 'contact_list' => $contact_list]);
    }

    public function send_new_message(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required',
            'message' => 'required',
        ], [
            'receiver_id.required' => __('Please select a user'),
            'message.required' => __('Message field is required'),
        ]);

        $auth_user = Auth::guard('web')->user();

        $new_message = new Message();
        $new_message->sender_id = $auth_user->id;
        $new_message->receiver_id = $request->receiver_id;
        $new_message->message = $request->message;
        $new_message->save();

        event(new LiveChatEvent($new_message));

        $notification = __('Message send Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }

    public function load_message_box($contact_id)
    {

        $auth_user = Auth::guard('web')->user();

        $contact_author = User::select('id', 'name', 'image', 'email')->findOrFail($contact_id);

        $message_list = Message::where(function ($query) use ($auth_user, $contact_id) {
            $query->where('sender_id', $auth_user->id)->where('receiver_id', $contact_id);
        })->orWhere(function ($query) use ($auth_user, $contact_id) {
            $query->where('sender_id', $contact_id)->where('receiver_id', $auth_user->id);
        })->get();

        Message::where('receiver_id', $auth_user->id)->where('sender_id', $contact_id)->update(['seen' => 'yes']);

        return view('livechat::chat_body')->with([
            'contact_author' => $contact_author,
            'message_list' => $message_list,
            'auth_user' => $auth_user,
        ]);
    }

    public function send_message(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required',
            'message' => 'required',
        ], [
            'receiver_id.required' => __('Please select a user'),
            'message.required' => __('Message field is required'),
        ]);

        $auth_user = Auth::guard('web')->user();

        $new_message = new Message();
        $new_message->sender_id = $auth_user->id;
        $new_message->receiver_id = $request->receiver_id;
        $new_message->message = $request->message;
        $new_message->save();

        Message::where('receiver_id', $auth_user->id)->where('sender_id', $request->receiver_id)->update(['seen' => 'yes']);

        $auth_user = Auth::guard('web')->user();

        $contact_author = User::select('id', 'name', 'image', 'email')->findOrFail($request->receiver_id);

        $contact_id = $request->receiver_id;

        $message_list = Message::where(function ($query) use ($auth_user, $contact_id) {
            $query->where('sender_id', $auth_user->id)->where('receiver_id', $contact_id);
        })->orWhere(function ($query) use ($auth_user, $contact_id) {
            $query->where('sender_id', $contact_id)->where('receiver_id', $auth_user->id);
        })->get();

        event(new LiveChatEvent($new_message));

        return view('livechat::chat_message')->with([
            'contact_author' => $contact_author,
            'message_list' => $message_list,
            'auth_user' => $auth_user,
        ]);
    }

    public function load_latest_message($contact_id)
    {
        $auth_user = Auth::guard('web')->user();

        $contact_author = User::select('id', 'name', 'image', 'email')->findOrFail($contact_id);

        $message_list = Message::where(function ($query) use ($auth_user, $contact_id) {
            $query->where('sender_id', $auth_user->id)->where('receiver_id', $contact_id);
        })->orWhere(function ($query) use ($auth_user, $contact_id) {
            $query->where('sender_id', $contact_id)->where('receiver_id', $auth_user->id);
        })->get();

        return view('livechat::chat_message')->with([
            'contact_author' => $contact_author,
            'message_list' => $message_list,
            'auth_user' => $auth_user,
        ]);
    }

    public function get_new_contact_sender($sender_id)
    {
        $contact_author = User::select('id', 'name', 'image', 'email')->findOrFail($sender_id);

        return response()->json(['contact_author' => $contact_author]);
    }
}

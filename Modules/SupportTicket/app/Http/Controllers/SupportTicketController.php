<?php

namespace Modules\SupportTicket\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Modules\SupportTicket\app\Models\MessageDocument;
use Modules\SupportTicket\app\Models\Ticket;
use Modules\SupportTicket\app\Models\TicketMessage;

class SupportTicketController extends Controller
{
    use RedirectHelperTrait;

    public function index()
    {
        checkAdminHasPermissionAndThrowException('support.ticket.view');
        $tickets = Ticket::with('user')->orderBy('id', 'desc')->paginate(15);

        return view('supportticket::ticket', compact('tickets'));
    }

    public function show($id)
    {
        checkAdminHasPermissionAndThrowException('support.ticket.manage');
        $ticket = Ticket::with('user')->where('id', $id)->first();
        TicketMessage::where('ticket_id', $ticket->id)->update(['unseen_admin' => 1]);
        $messages = TicketMessage::where('ticket_id', $ticket->id)->get();

        return view('supportticket::ticket_show', compact('ticket', 'messages'));
    }

    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('support.ticket.delete');
        Ticket::where('id', $id)->delete();
        $messages = TicketMessage::where('ticket_id', $id)->get();
        foreach ($messages as $message) {
            $documents = MessageDocument::where('ticket_message_id', $message->id)->get();
            foreach ($documents as $document) {
                $exist_file_name = $document->file_name;
                if ($exist_file_name) {
                    if (File::exists(public_path('uploads/custom-images').'/'.$exist_file_name)) {
                        unlink(public_path('uploads/custom-images').'/'.$exist_file_name);
                    }
                }

                $document->delete();
            }
            $message->delete();
        }

        return $this->redirectWithMessage(RedirectType::DELETE->value);
    }

    public function closed($id)
    {
        checkAdminHasPermissionAndThrowException('support.ticket.close');
        $ticket = Ticket::where('id', $id)->first();
        $ticket->status = 'closed';
        $ticket->save();

        $notification = __('Closed Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return $this->redirectWithMessage(RedirectType::UPDATE->value, null, [], $notification);
    }

    public function store_message(Request $request)
    {
        checkAdminHasPermissionAndThrowException('support.ticket.manage');
        $rules = [
            'ticket_id' => 'required',
            'message' => 'required',
            'user_id' => 'required',
            'documents' => 'max:2048',
        ];
        $customMessages = [
            'message.required' => __('Message is required'),
            'ticket_id.required' => __('Ticket is required'),
            'user_id.required' => __('User is required'),
        ];
        $this->validate($request, $rules, $customMessages);

        $admin = Auth::guard('admin')->user();
        $message = new TicketMessage();
        $message->ticket_id = $request->ticket_id;
        $message->admin_id = $admin->id;
        $message->user_id = $request->user_id;
        $message->message = $request->message;
        $message->message_from = 'admin';
        $message->unseen_admin = 1;
        $message->save();

        if ($request->hasFile('documents')) {

            foreach ($request->documents as $index => $request_file) {
                $extention = $request_file->getClientOriginalExtension();
                $file_name = 'support-file-'.time().$index.'.'.$extention;
                $destinationPath = public_path('uploads/custom-images/');
                $request_file->move($destinationPath, $file_name);

                $document = new MessageDocument();
                $document->ticket_message_id = $message->id;
                $document->file_name = $file_name;
                $document->save();
            }
        }

        $firstSmsExist = TicketMessage::where('admin_id', '!=', 0)->where('ticket_id', $request->ticket_id)->count();

        if ($firstSmsExist == 1) {
            $ticket = Ticket::where(['id' => $request->ticket_id])->first();
            $ticket->status = 'in_progress';
            $ticket->save();
        }

        $notification = __('Message Send Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return $this->redirectWithMessage(RedirectType::UPDATE->value, null, [], $notification);
    }
}

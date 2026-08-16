<?php

namespace App\Http\Livewire\Chat;

use App\Models\Conversation;
use App\Models\Doctor;
use App\Models\Message;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Chatbox extends Component
{
    public $selected_conversation;
    public $receviverUser;
    public $messages;
    public $auth_email;
    public $auth_id;
    public $event_name;
    public $chat_page;
    public $messagesCount = 0;

    public function mount()
    {
        if (Auth::guard('patient')->check()) {
            $this->auth_email = Auth::guard('patient')->user()->email;
            $this->auth_id = Auth::guard('patient')->user()->id;
        } else {
            $this->auth_email = Auth::guard('doctor')->user()->email;
            $this->auth_id = Auth::guard('doctor')->user()->id;
        }

        $this->messages = collect();
    }

    public function getListeners()
    {
        if (Auth::guard('patient')->check()) {
            $auth_id = Auth::guard('patient')->user()->id;
            $this->event_name = 'MassageSent2';
            $this->chat_page = 'chat2';
        } else {
            $auth_id = Auth::guard('doctor')->user()->id;
            $this->event_name = 'MassageSent';
            $this->chat_page = 'chat';
        }

        return [
            "echo-private:{$this->chat_page}.{$auth_id},{$this->event_name}" => 'broadcastMassage',
            'load_conversationPatient',
            'load_conversationDoctor',
            'pushMessage',
        ];
    }

    public function broadcastMassage($event)
    {
        $broadcastMessage = Message::find($event['message'] ?? null);
        if ($broadcastMessage) {
            $broadcastMessage->read = 1;
            $broadcastMessage->save();
            $this->pushMessage($broadcastMessage->id);
        }
    }

    public function pushMessage($messageId)
    {
        $newMessage = Message::find($messageId);
        if (!$newMessage) {
            return;
        }

        if (!$this->messages) {
            $this->messages = collect();
        }

        if (!$this->messages->contains('id', $newMessage->id)) {
            $this->messages->push($newMessage);
            $this->messagesCount = $this->messages->count();
        }
    }

    public function load_conversationDoctor(Conversation $conversation, Doctor $receiver)
    {
        $receiver->load('image');
        $this->selected_conversation = $conversation;
        $this->receviverUser = $receiver;
        $this->messages = Message::where('conversation_id', $conversation->id)->orderBy('created_at')->get();
        $this->messagesCount = $this->messages->count();
    }

    public function load_conversationPatient(Conversation $conversation, Patient $receiver)
    {
        $this->selected_conversation = $conversation;
        $this->receviverUser = $receiver;
        $this->messages = Message::where('conversation_id', $conversation->id)->orderBy('created_at')->get();
        $this->messagesCount = $this->messages->count();
    }

    /** تحديث فوري بدون اعتماد كامل على Pusher */
    public function refreshMessages()
    {
        if (!$this->selected_conversation) {
            return;
        }

        $fresh = Message::where('conversation_id', $this->selected_conversation->id)
            ->orderBy('created_at')
            ->get();

        if ($fresh->count() !== $this->messagesCount) {
            $this->messages = $fresh;
            $this->messagesCount = $fresh->count();
            $this->emitTo('chat.chatlist', 'refresh');
        }
    }

    public function render()
    {
        return view('livewire.chat.chatbox');
    }
}

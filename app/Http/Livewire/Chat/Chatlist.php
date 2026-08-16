<?php

namespace App\Http\Livewire\Chat;

use App\Models\Conversation;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Chatlist extends Component
{
    public $conversations;
    public $auth_email;
    public $receviverUser;
    public $selected_conversation;
    public $doctorsByEmail;
    public $patientsByEmail;
    protected $listeners = ['chatUserSelected', 'refresh' => '$refresh'];

    public function mount()
    {
        $this->auth_email = auth()->user()->email;
        $this->doctorsByEmail = collect();
        $this->patientsByEmail = collect();
    }

    public function resolveUser(Conversation $conversation)
    {
        $otherEmail = $conversation->sender_email === $this->auth_email
            ? $conversation->receiver_email
            : $conversation->sender_email;

        if ($this->doctorsByEmail && $this->doctorsByEmail->has($otherEmail)) {
            return $this->doctorsByEmail->get($otherEmail);
        }

        if ($this->patientsByEmail && $this->patientsByEmail->has($otherEmail)) {
            return $this->patientsByEmail->get($otherEmail);
        }

        return Doctor::with('image')->where('email', $otherEmail)->first()
            ?: Patient::where('email', $otherEmail)->first();
    }

    public function getUsers(Conversation $conversation, $request)
    {
        $this->receviverUser = $this->resolveUser($conversation);

        if ($this->receviverUser && isset($request)) {
            return $this->receviverUser->$request;
        }

        return null;
    }

    public function chatUserSelected(Conversation $conversation, $receiver_id)
    {
        $this->selected_conversation = $conversation;
        $this->receviverUser = $this->resolveUser($conversation);

        if (!$this->receviverUser) {
            return;
        }

        if (Auth::guard('patient')->check()) {
            $this->emitTo('chat.chatbox', 'load_conversationDoctor', $this->selected_conversation, $this->receviverUser);
            $this->emitTo('chat.send-message', 'updateMessage', $this->selected_conversation, $this->receviverUser);
        } else {
            $this->emitTo('chat.chatbox', 'load_conversationPatient', $this->selected_conversation, $this->receviverUser);
            $this->emitTo('chat.send-message', 'updateMessage2', $this->selected_conversation, $this->receviverUser);
        }
    }

    public function render()
    {
        $this->conversations = Conversation::with(['messages' => function ($q) {
            $q->latest()->limit(1);
        }])
            ->where(function ($q) {
                $q->where('sender_email', $this->auth_email)
                    ->orWhere('receiver_email', $this->auth_email);
            })
            ->orderByDesc('last_time_message')
            ->orderByDesc('created_at')
            ->get();

        $emails = $this->conversations->flatMap(function ($c) {
            return [$c->sender_email, $c->receiver_email];
        })->unique()->filter(function ($email) {
            return $email !== $this->auth_email;
        })->values();

        $this->doctorsByEmail = Doctor::with('image')
            ->whereIn('email', $emails)
            ->get()
            ->keyBy('email');

        $this->patientsByEmail = Patient::whereIn('email', $emails)
            ->get()
            ->keyBy('email');

        return view('livewire.chat.chatlist');
    }
}

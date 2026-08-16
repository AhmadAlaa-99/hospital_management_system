<?php

namespace App\Http\Livewire\Chat;

use App\Models\Conversation;
use App\Models\Doctor;
use App\Models\Message;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Createchat extends Component
{
    public $users;
    public $auth_email;
    public $flashMessage;

    public function mount()
    {
        $this->auth_email = auth()->user()->email;
    }

    public function createConversation($receiver_email)
    {
        $existing = Conversation::chekConversation($this->auth_email, $receiver_email)->first();

        if ($existing) {
            $this->flashMessage = 'المحادثة موجودة مسبقاً، يمكنك فتحها من قائمة المحادثات.';
            return redirect()->to(
                Auth::guard('patient')->check()
                    ? route('chat.doctors')
                    : route('chat.patients')
            );
        }

        DB::beginTransaction();
        try {
            $createConversation = Conversation::create([
                'sender_email' => $this->auth_email,
                'receiver_email' => $receiver_email,
                'last_time_message' => now(),
            ]);

            Message::create([
                'conversation_id' => $createConversation->id,
                'sender_email' => $this->auth_email,
                'receiver_email' => $receiver_email,
                'body' => 'السلام عليكم',
            ]);

            DB::commit();

            return redirect()->to(
                Auth::guard('patient')->check()
                    ? route('chat.doctors')
                    : route('chat.patients')
            );
        } catch (\Exception $e) {
            DB::rollBack();
            $this->flashMessage = 'تعذر إنشاء المحادثة، حاول مرة أخرى.';
        }
    }

    public function render()
    {
        if (Auth::guard('patient')->check()) {
            $this->users = Doctor::where('status', 1)->get();
        } else {
            $this->users = Patient::all();
        }

        return view('livewire.chat.createchat')->extends('Dashboard.layouts.master');
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentConfirmation extends Mailable
{
    use Queueable, SerializesModels;
    public $name;
    public $appointment;
    public $isReminder;

    public function __construct($name, $appointment, $isReminder = false)
    {
        $this->name = $name;
        $this->appointment = $appointment;
        $this->isReminder = $isReminder;
    }

    public function build()
    {
        $subject = $this->isReminder ? 'تذكير بموعدك' : 'تاكيد موعد';
        return $this->markdown('emails.appointments')->subject($subject);
    }
}

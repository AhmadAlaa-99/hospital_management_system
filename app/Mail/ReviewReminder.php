<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReviewReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $patientName;
    public $reviewDate;
    public $doctorName;

    public function __construct(string $patientName, string $reviewDate, ?string $doctorName = null)
    {
        $this->patientName = $patientName;
        $this->reviewDate = $reviewDate;
        $this->doctorName = $doctorName;
    }

    public function build()
    {
        return $this->markdown('emails.review-reminder')->subject('تذكير بموعد المراجعة');
    }
}

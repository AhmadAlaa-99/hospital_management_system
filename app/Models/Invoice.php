<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
   // protected $guarded=[];
    protected $fillable = [
        'invoice_type',
        'invoice_date',
        'patient_id',
        'doctor_id',
        'section_id',
        'appointment_id',
        'Group_id',
        'Service_id',
        'price',
        'discount_value',
        'tax_rate',
        'tax_value',
        'total_with_tax',
        'type',
        'invoice_status',
        'payment_status',
    ];

    public static $paymentStatusLabels = [
        'unpaid' => 'غير مدفوعة',
        'pending_review' => 'جاري مراجعة الدفع',
        'paid' => 'مدفوعة',
        'rejected' => 'مرفوض — أعد رفع الإيصال',
    ];

    public function shamCashPayments()
    {
        return $this->hasMany(ShamCashPayment::class);
    }

    public function latestShamCashPayment()
    {
        return $this->hasOne(ShamCashPayment::class)->latestOfMany();
    }

    public function canPayViaShamCash(): bool
    {
        return in_array($this->payment_status, ['unpaid', 'rejected'], true);
    }

    public function Group()
    {
        return $this->belongsTo(Group::class,'Group_id');
    }

    public function Service()
    {
        return $this->belongsTo(Service::class,'Service_id');
    }

    public function Patient()
    {
        return $this->belongsTo(Patient::class,'patient_id');
    }

    public function Doctor()
    {
        return $this->belongsTo(Doctor::class,'doctor_id');
    }

    public function Section()
    {
        return $this->belongsTo(Section::class,'section_id');
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }
}

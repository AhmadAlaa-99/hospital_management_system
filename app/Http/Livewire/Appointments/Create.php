<?php

namespace App\Http\Livewire\Appointments;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Section;
use Livewire\Component;

class Create extends Component
{
    public $doctors = [];
    public $sections = [];
    public $doctor = '';
    public $section = '';
    public $name;
    public $email;
    public $phone;
    public $notes;
    public $message = false;

    protected $rules = [
        'name' => 'required|string|min:3|max:100',
        'email' => 'required|email|max:150',
        'phone' => 'required|string|min:8|max:20',
        'section' => 'required|exists:sections,id',
        'doctor' => 'required|exists:doctors,id',
        'notes' => 'nullable|string|max:500',
    ];

    protected $messages = [
        'name.required' => 'الاسم مطلوب',
        'email.required' => 'البريد الالكتروني مطلوب',
        'email.email' => 'صيغة البريد الالكتروني غير صحيحة',
        'phone.required' => 'رقم الهاتف مطلوب',
        'section.required' => 'يرجى اختيار القسم',
        'doctor.required' => 'يرجى اختيار الدكتور',
    ];

    public function mount()
    {
        $this->sections = $this->mapSections(Section::with('translations')->get());
        $this->doctors = [];
    }

    public function render()
    {
        return view('livewire.appointments.create');
    }

    public function updatedSection($section_id)
    {
        $this->reset('doctor');
        $this->doctor = '';

        if (!$section_id) {
            $this->doctors = [];
            return;
        }

        $this->doctors = $this->mapDoctors(
            Doctor::with('translations')
                ->where('section_id', $section_id)
                ->where('status', 1)
                ->get()
        );
    }

    public function store()
    {
        $this->validate();

        $doctorBelongsToSection = Doctor::where('id', $this->doctor)
            ->where('section_id', $this->section)
            ->exists();

        if (!$doctorBelongsToSection) {
            $this->addError('doctor', 'الدكتور غير تابع للقسم المختار');
            return;
        }

        Appointment::create([
            'doctor_id' => $this->doctor,
            'section_id' => $this->section,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'notes' => $this->notes,
            'type' => 'غير مؤكد',
        ]);

        \App\Services\NotificationService::notifyDoctor(
            (int) $this->doctor,
            'حجز موعد جديد من المريض: ' . $this->name
        );

        \App\Services\NotificationService::notifyAdmin(
            'طلب موعد جديد: ' . $this->name,
            route('appointments.index')
        );

        \App\Services\NotificationService::notifyPatientByEmail(
            $this->email,
            'تم استلام طلب موعدك وهو قيد المراجعة.'
        );

        $this->reset(['name', 'email', 'phone', 'notes', 'doctor', 'section']);
        $this->doctors = [];
        $this->sections = $this->mapSections(Section::with('translations')->get());
        $this->message = true;
    }

    protected function mapSections($sections): array
    {
        return $sections->map(function ($section) {
            return [
                'id' => $section->id,
                'name' => $this->translatedName($section),
            ];
        })->values()->toArray();
    }

    protected function mapDoctors($doctors): array
    {
        return $doctors->map(function ($doctor) {
            return [
                'id' => $doctor->id,
                'name' => $this->translatedName($doctor),
            ];
        })->values()->toArray();
    }

    protected function translatedName($model): string
    {
        return optional($model->translate(app()->getLocale()))->name
            ?: optional($model->translate('ar'))->name
            ?: optional($model->translate('en'))->name
            ?: ('#' . $model->id);
    }
}

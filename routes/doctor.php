<?php


use App\Http\Controllers\Dashboard_Doctor\DiagnosticController;
use App\Http\Controllers\Dashboard_Doctor\FollowUpPlanController;
use App\Http\Controllers\Dashboard_Doctor\MedicalCertificateController;
use App\Http\Controllers\Dashboard_Doctor\ReferralController;
use App\Http\Controllers\Dashboard_Doctor\LaboratorieController;
use App\Http\Controllers\Dashboard_Doctor\RayController;
use App\Http\Controllers\Dashboard_Doctor\PatientDetailsController;

use App\Http\Controllers\doctor\InvoiceController;
use App\Http\Controllers\Dashboard_Doctor\QueueController as DoctorQueueController;
use App\Http\Controllers\Dashboard_Doctor\AppointmentController as DoctorAppointmentController;
use App\Http\Livewire\Chat\Createchat;
use App\Http\Livewire\Chat\Main;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| doctor Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ], function () {


    //################################ dashboard doctor ########################################

    Route::get('/dashboard/doctor', function () {
        return view('Dashboard.doctor.dashboard');
    })->middleware(['auth:doctor'])->name('dashboard.doctor');

    //################################ end dashboard doctor #####################################

//---------------------------------------------------------------------------------------------------------------


    Route::middleware(['auth:doctor'])->group(function () {

        Route::prefix('doctor')->group(function () {


            //############################# completed_invoices route ##########################################
            Route::get('completed_invoices', [InvoiceController::class,'completedInvoices'])->name('completedInvoices');
            //############################# end invoices route ################################################

            //############################# review_invoices route ##########################################
            Route::get('review_invoices', [InvoiceController::class,'reviewInvoices'])->name('reviewInvoices');
            //############################# end invoices route #############################################

            //############################# invoices route ##########################################
            Route::resource('invoices', InvoiceController::class);
            //############################# end invoices route ######################################


            //############################# review_invoices route ##########################################
            Route::post('add_review', [DiagnosticController::class,'addReview'])->name('add_review');
            //############################# end invoices route #############################################


            //############################# Diagnostics route ##########################################

            Route::resource('Diagnostics', DiagnosticController::class);

            //############################# end Diagnostics route ######################################


            //############################# rays route ##########################################

            Route::resource('rays', RayController::class);

            //############################# end rays route ######################################


            //############################# Laboratories route ##########################################

            Route::resource('Laboratories', LaboratorieController::class);
            Route::get('show_laboratorie/{id}', [InvoiceController::class,'showLaboratorie'])->name('show.laboratorie');

            //############################# end Laboratories route ######################################


            //############################# rays route ##########################################

            Route::get('patient_details/{id}', [PatientDetailsController::class,'index'])->name('patient_details');
            Route::get('medical-record/pdf/{patientId}', [\App\Http\Controllers\MedicalRecordPdfController::class, 'export'])->name('doctor.medical-record.pdf');

            //############################# end rays route ######################################

            //############################# appointments route ##########################################
            Route::get('appointments', [DoctorAppointmentController::class, 'index'])->name('doctor.appointments');
            Route::get('appointments/finished', [DoctorAppointmentController::class, 'finished'])->name('doctor.appointments.finished');
            //############################# end appointments route ######################################

            Route::get('queue', [DoctorQueueController::class, 'index'])->name('doctor.queue.index');
            Route::post('queue/call-next', [DoctorQueueController::class, 'callNext'])->name('doctor.queue.call-next');
            Route::post('queue/{ticket}/recall', [DoctorQueueController::class, 'recall'])->name('doctor.queue.recall');
            Route::post('queue/{ticket}/serving', [DoctorQueueController::class, 'serving'])->name('doctor.queue.serving');
            Route::post('queue/{ticket}/complete', [DoctorQueueController::class, 'complete'])->name('doctor.queue.complete');
            Route::post('queue/{ticket}/no-show', [DoctorQueueController::class, 'noShow'])->name('doctor.queue.no-show');

            Route::get('referrals', [ReferralController::class, 'index'])->name('doctor.referrals.index');
            Route::get('referrals/create', [ReferralController::class, 'create'])->name('doctor.referrals.create');
            Route::post('referrals', [ReferralController::class, 'store'])->name('doctor.referrals.store');
            Route::post('referrals/{referral}/accept', [ReferralController::class, 'accept'])->name('doctor.referrals.accept');
            Route::post('referrals/{referral}/complete', [ReferralController::class, 'complete'])->name('doctor.referrals.complete');
            Route::post('referrals/{referral}/reject', [ReferralController::class, 'reject'])->name('doctor.referrals.reject');

            Route::get('follow-ups', [FollowUpPlanController::class, 'index'])->name('doctor.follow-ups.index');
            Route::post('follow-ups', [FollowUpPlanController::class, 'store'])->name('doctor.follow-ups.store');
            Route::post('follow-ups/{followUp}/complete', [FollowUpPlanController::class, 'complete'])->name('doctor.follow-ups.complete');
            Route::post('follow-ups/{followUp}/appointment', [FollowUpPlanController::class, 'createAppointment'])->name('doctor.follow-ups.appointment');

            Route::get('certificates', [MedicalCertificateController::class, 'index'])->name('doctor.certificates.index');
            Route::get('certificates/create', [MedicalCertificateController::class, 'create'])->name('doctor.certificates.create');
            Route::post('certificates', [MedicalCertificateController::class, 'store'])->name('doctor.certificates.store');
            Route::get('certificates/{certificate}/pdf', [MedicalCertificateController::class, 'pdf'])->name('doctor.certificates.pdf');

            //############################# Chat route ##########################################
            Route::get('list/patients',Createchat::class)->name('list.patients');
            Route::get('chat/patients',Main::class)->name('chat.patients');
            //############################# end Chat route ######################################

        });


        Route::get('/404', function () {
            return view('Dashboard.404');
        })->name('404');


    });
    require __DIR__ . '/auth.php';


});






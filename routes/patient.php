<?php


use App\Http\Controllers\Dashboard_Doctor\DiagnosticController;
use App\Http\Controllers\Dashboard_Doctor\LaboratorieController;
use App\Http\Controllers\Dashboard_Doctor\RayController;
use App\Http\Controllers\Dashboard_Doctor\PatientDetailsController;
use App\Http\Controllers\Dashboard_Patient\DoctorRatingController;
use App\Http\Controllers\Dashboard_Patient\ExternalRecordController;
use App\Http\Controllers\Dashboard_Patient\FollowUpPlanController as PatientFollowUpController;
use App\Http\Controllers\Dashboard_Patient\PatientController;
use App\Http\Controllers\Dashboard_Patient\ShamCashPaymentController;
use App\Http\Controllers\Dashboard_Patient\AppointmentController as PatientAppointmentController;
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

    //################################ dashboard patient ########################################
    Route::get('/dashboard/patient', function () {
        return view('Dashboard.dashboard_patient.dashboard');
    })->middleware(['auth:patient'])->name('dashboard.patient');
    //################################ end dashboard patient #####################################

    Route::middleware(['auth:patient'])->group(function () {

        //############################# patients route ##########################################
        Route::get('invoices', [PatientController::class,'invoices'])->name('invoices.patient');
        Route::get('invoices/{invoice}/pay-sham-cash', [ShamCashPaymentController::class, 'show'])->name('patient.sham-cash.show');
        Route::post('invoices/{invoice}/pay-sham-cash', [ShamCashPaymentController::class, 'store'])->name('patient.sham-cash.store');
        Route::get('laboratories', [PatientController::class,'laboratories'])->name('laboratories.patient');
        Route::get('view_laboratories/{id}', [PatientController::class,'viewLaboratories'])->name('laboratories.view');
        Route::get('rays', [PatientController::class,'rays'])->name('rays.patient');
        Route::get('view_rays/{id}', [PatientController::class,'viewRays'])->name('rays.view');
        Route::get('payments', [PatientController::class,'payments'])->name('payments.patient');
        Route::get('my-appointments', [PatientAppointmentController::class,'index'])->name('patient.appointments');
        Route::get('rate-appointment/{appointment}', [DoctorRatingController::class, 'create'])->name('patient.rate.create');
        Route::post('rate-appointment/{appointment}', [DoctorRatingController::class, 'store'])->name('patient.rate.store');
        Route::get('medical-record/pdf/{patientId}', [\App\Http\Controllers\MedicalRecordPdfController::class, 'export'])->name('patient.medical-record.pdf');
        Route::get('follow-ups', [PatientFollowUpController::class, 'index'])->name('patient.follow-ups.index');
        Route::get('external-records', [ExternalRecordController::class, 'index'])->name('patient.external-records.index');
        Route::post('external-records', [ExternalRecordController::class, 'store'])->name('patient.external-records.store');
        Route::get('external-records/{externalRecord}/download', [ExternalRecordController::class, 'download'])->name('patient.external-records.download');
        Route::delete('external-records/{externalRecord}', [ExternalRecordController::class, 'destroy'])->name('patient.external-records.destroy');
        //############################# end patients route ######################################

        //############################# Chat route ##########################################
         Route::get('list/doctors',Createchat::class)->name('list.doctors');
         Route::get('chat/doctors',Main::class)->name('chat.doctors');
        //############################# end Chat route ######################################



    });


    require __DIR__ . '/auth.php';

});






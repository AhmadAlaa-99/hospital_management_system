<?php

use App\Events\MyEvent;
use App\Http\Controllers\Dashboard\AmbulanceController;
use App\Http\Controllers\Dashboard\AmbulanceRequestController;
use App\Http\Controllers\Dashboard\BlogController;
use App\Http\Controllers\Dashboard\DoctorScheduleController;
use App\Http\Controllers\Dashboard\InsuranceClaimController;
use App\Http\Controllers\Dashboard\QueueController;
use App\Http\Controllers\Dashboard\ReportsController;
use App\Http\Controllers\Dashboard\appointments\AppointmentController;
use App\Http\Controllers\Dashboard\NotificationController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\DoctorController;
use App\Http\Controllers\Dashboard\InsuranceController;
use App\Http\Controllers\Dashboard\LaboratorieEmployeeController;
use App\Http\Controllers\Dashboard\PatientTestimonialController;
use App\Http\Controllers\Dashboard\PatientController;
use App\Http\Controllers\Dashboard\PaymentAccountController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\RayEmployeeController;
use App\Http\Controllers\Dashboard\ReceiptAccountController;
use App\Http\Controllers\Dashboard\SectionController;
use App\Http\Controllers\Dashboard\SingleServiceController;
use App\Http\Controllers\Dashboard\SiteSettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Backend Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get('/Dashboard_Admin', [DashboardController::class, 'index']);


Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath' ]
    ], function(){


   //################################ dashboard user ##########################################
    Route::get('/dashboard/user', function () {
        return view('Dashboard.User.dashboard');
    })->middleware(['auth'])->name('dashboard.user');
    //################################ end dashboard user #####################################



    //################################ dashboard admin ########################################
    Route::get('/dashboard/admin', function () {
        return view('Dashboard.Admin.dashboard');
    })->middleware(['auth:admin'])->name('dashboard.admin');

    //################################ end dashboard admin #####################################



//---------------------------------------------------------------------------------------------------------------


    Route::middleware(['auth:admin'])->group(function () {

    //############################# sections route ##########################################

        Route::resource('Sections', SectionController::class);
        // sectiom.index  

    //############################# end sections route ######################################


     //############################# Doctors route ##########################################

        Route::resource('Doctors', DoctorController::class);  //post get put update delete show
  
        Route::post('update_password', [DoctorController::class, 'update_password'])->name('update_password');
        Route::post('update_status', [DoctorController::class, 'update_status'])->name('update_status');

        //############################# end Doctors route ######################################


        //############################# sections route ##########################################

        Route::resource('Service', SingleServiceController::class);

        //############################# end sections route ######################################

        //############################# GroupServices route ##########################################

        Route::view('Add_GroupServices','livewire.GroupServices.include_create')->name('Add_GroupServices');

        //############################# end GroupServices route ######################################

        //############################# insurance route ##########################################

        Route::resource('insurance', InsuranceController::class);

        //############################# end insurance route ######################################

        //############################# Ambulance route ##########################################

        Route::resource('Ambulance', AmbulanceController::class);

        //############################# end Ambulance route ######################################


        //############################# Patients route ##########################################

        Route::get('patient-testimonials', [PatientTestimonialController::class, 'index'])->name('patient-testimonials.index');
        Route::post('patient-testimonials/{rating}/approve', [PatientTestimonialController::class, 'approve'])->name('patient-testimonials.approve');
        Route::post('patient-testimonials/{rating}/reject', [PatientTestimonialController::class, 'reject'])->name('patient-testimonials.reject');
        Route::post('patient-testimonials/{rating}/unpublish', [PatientTestimonialController::class, 'unpublish'])->name('patient-testimonials.unpublish');

        Route::resource('Patients', PatientController::class);

        //############################# end Patients route ######################################


        //############################# single_invoices route ##########################################

        Route::view('single_invoices','livewire.single_invoices.index')->name('single_invoices');

        Route::get('Print_single_invoices/{invoice}', function (\App\Models\Invoice $invoice) {
            $invoice->load(['Patient', 'Doctor', 'Section', 'Service']);
            return view('livewire.single_invoices.print', compact('invoice'));
        })->name('Print_single_invoices');

        //############################# end single_invoices route ######################################

        //############################# Receipt route ##########################################

        Route::resource('Receipt', ReceiptAccountController::class);

        //############################# end Receipt route ######################################


        //############################# Payment route ##########################################

        Route::resource('Payment', PaymentAccountController::class);

        //############################# end Payment route ######################################


        //############################# RayEmployee route ##########################################

        Route::resource('ray_employee', RayEmployeeController::class);

        //############################# end RayEmployee route ######################################


        //############################# laboratorie_employee route ##########################################

        Route::resource('laboratorie_employee', LaboratorieEmployeeController::class);

        //############################# end laboratorie_employee route ######################################

        //############################# single_invoices route ##########################################

        Route::view('group_invoices','livewire.group_invoices.index')->name('group_invoices');

        Route::get('group_Print_single_invoices/{invoice}', function (\App\Models\Invoice $invoice) {
            $invoice->load(['Group', 'Patient', 'Doctor', 'Section']);
            return view('livewire.group_invoices.print', compact('invoice'));
        })->name('group_Print_single_invoices');

        //############################# end single_invoices route ######################################


        Route::get('appointments',[AppointmentController::class,'index'])->name('appointments.index');
        Route::put('appointments/approval/{id}',[AppointmentController::class,'approval'])->name('appointments.approval');
        Route::post('appointments/refuse/{id}',[AppointmentController::class,'refuse'])->name('appointments.refuse');
        Route::get('appointments/approval',[AppointmentController::class,'index2'])->name('appointments.index2');
        Route::get('appointments/finished',[AppointmentController::class,'index3'])->name('appointments.index3');
        Route::delete('appointments/destroy/{id}',[AppointmentController::class,'destroy'])->name('appointments.destroy');

        Route::get('site-settings', [SiteSettingController::class, 'edit'])->name('site-settings.edit');
        Route::put('site-settings', [SiteSettingController::class, 'update'])->name('site-settings.update');

        Route::get('reports', [ReportsController::class, 'index'])->name('admin.reports');

        Route::resource('admin/blogs', BlogController::class)->names([
            'index' => 'admin.blogs.index',
            'create' => 'admin.blogs.create',
            'store' => 'admin.blogs.store',
            'edit' => 'admin.blogs.edit',
            'update' => 'admin.blogs.update',
            'destroy' => 'admin.blogs.destroy',
        ])->except(['show']);

        Route::get('insurance-claims', [InsuranceClaimController::class, 'index'])->name('insurance-claims.index');
        Route::put('insurance-claims/{claim}/status', [InsuranceClaimController::class, 'updateStatus'])->name('insurance-claims.status');
        Route::get('insurance-claims/report', [InsuranceClaimController::class, 'report'])->name('insurance-claims.report');

        Route::get('ambulance-requests', [AmbulanceRequestController::class, 'index'])->name('ambulance-requests.index');
        Route::post('ambulance-requests/{ambulanceRequest}/dispatch', [AmbulanceRequestController::class, 'assignAmbulance'])->name('ambulance-requests.dispatch');
        Route::post('ambulance-requests/{ambulanceRequest}/complete', [AmbulanceRequestController::class, 'complete'])->name('ambulance-requests.complete');
        Route::post('ambulance-requests/{ambulanceRequest}/cancel', [AmbulanceRequestController::class, 'cancel'])->name('ambulance-requests.cancel');

        Route::get('doctor-schedules', [DoctorScheduleController::class, 'index'])->name('doctor-schedules.index');
        Route::post('doctor-schedules', [DoctorScheduleController::class, 'store'])->name('doctor-schedules.store');
        Route::delete('doctor-schedules/{schedule}', [DoctorScheduleController::class, 'destroy'])->name('doctor-schedules.destroy');

        Route::get('medical-record/pdf/{patientId}', [\App\Http\Controllers\MedicalRecordPdfController::class, 'export'])->name('medical-record.pdf');

        Route::get('queue', [QueueController::class, 'index'])->name('admin.queue.index');
        Route::post('queue', [QueueController::class, 'store'])->name('admin.queue.store');
        Route::post('queue/check-in/{appointment}', [QueueController::class, 'checkIn'])->name('admin.queue.check-in');
        Route::delete('queue/{ticket}', [QueueController::class, 'cancel'])->name('admin.queue.cancel');


    });

    Route::middleware(['auth:admin,doctor,patient,ray_employee,laboratorie_employee'])->group(function () {
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('notifications/read/{id}',[NotificationController::class,'read'])->name('notifications.read');
        Route::get('notifications/read-all',[NotificationController::class,'readAll'])->name('notifications.readAll');

        Route::get('export/insurance-claims', [\App\Http\Controllers\Dashboard\ExportController::class, 'insuranceClaims'])->name('export.insurance-claims');
        Route::get('export/reports', [\App\Http\Controllers\Dashboard\ExportController::class, 'reports'])->name('export.reports');
        Route::get('export/table', [\App\Http\Controllers\Dashboard\ExportController::class, 'table'])->name('export.table');

        Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    });


    require __DIR__.'/auth.php';


});






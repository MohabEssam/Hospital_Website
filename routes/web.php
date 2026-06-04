<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ClinicalRecordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\DoctorDashboardController;
use App\Http\Controllers\LabCenterController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientLookupController;
use App\Http\Controllers\PatientMedicalCardController;
use App\Http\Controllers\PatientPortalController;
use App\Http\Controllers\PharmacyCenterController;
use App\Http\Controllers\ScanCenterController;
use App\Http\Controllers\ServiceBookingController;
use App\Http\Controllers\StaffUserController;
use App\Http\Controllers\Website\BookingController;
use App\Http\Controllers\Website\HomeController;
use App\Http\Controllers\Website\PatientCareController;
use App\Http\Controllers\Website\WebDepartmentController;
use App\Http\Controllers\Website\WebDoctorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Website Routes — Medicare Hospital
|--------------------------------------------------------------------------
*/

Route::get('/', HomeController::class)->name('home');
Route::get('/departments', [WebDepartmentController::class, 'index'])->name('website.departments');
Route::get('/departments/{department}', [WebDepartmentController::class, 'show'])->name('website.departments.show');
Route::get('/doctors', [WebDoctorController::class, 'index'])->name('website.doctors');
Route::get('/doctors/{doctor}', [WebDoctorController::class, 'show'])->name('website.doctors.show');
Route::get('/patient-care', [PatientCareController::class, 'index'])->name('website.patient-care');
Route::get('/patient-care/{service}', [PatientCareController::class, 'show'])->name('website.patient-care.show');
Route::get('/my-bookings', [BookingController::class, 'myBookings'])->name('my-bookings');

/*
|--------------------------------------------------------------------------
| Guest-only Routes (login / register)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('login.store');

    Route::get('/patient/login', [AuthenticatedSessionController::class, 'create'])->name('patient.login');
    Route::post('/patient/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('patient.login.store');

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('register.store');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/patients/lookup/search', [PatientLookupController::class, 'search'])
        ->middleware('throttle:60,1')
        ->name('patients.lookup.search');

    // --- Patient: booking via website ---
    Route::middleware('role:patient')->group(function (): void {
        Route::get('/patient', fn () => redirect()->route('patient.dashboard'))->name('patient.home');
        Route::get('/patient/dashboard', [PatientPortalController::class, 'dashboard'])->name('patient.dashboard');
        Route::get('/patient/results', [PatientPortalController::class, 'results'])->name('patient.results');
        Route::get('/patient/medical-card', [PatientMedicalCardController::class, 'show'])->name('patient.medical-card.show');
        Route::get('/patient/medical-card/download', [PatientMedicalCardController::class, 'download'])->name('patient.medical-card.download');
        Route::get('/patient/lab-results/{labResult}/files/{file}', [PatientPortalController::class, 'labResultFile'])
            ->whereNumber('file')
            ->name('patient.lab-results.files');
        Route::get('/patient/scan-results/{scanResult}/files/{file}', [PatientPortalController::class, 'scanResultFile'])
            ->whereNumber('file')
            ->name('patient.scan-results.files');

        Route::post('/my-bookings/{appointment}/cancel', [BookingController::class, 'cancel'])->name('website.booking.cancel');
        Route::get('/book', [BookingController::class, 'create'])->name('website.book');
        Route::post('/book', [BookingController::class, 'store'])->name('website.book.store');
        Route::get('/booking/{appointment}', [BookingController::class, 'show'])->name('website.booking.status');
        Route::get('/api/doctors/{doctor:id}/slots', [BookingController::class, 'slots'])->name('website.doctor.slots');
        Route::post('/doctors/{doctor}/appointments', [WebDoctorController::class, 'bookAppointment'])
            ->name('website.doctors.appointments.store');

        Route::post('/patient-care/{service}/book', [PatientCareController::class, 'storeBooking'])
            ->name('website.patient-care.book');
    });

    Route::middleware('doctor')->prefix('doctor')->name('doctor.')->group(function (): void {
        Route::get('/dashboard', DoctorDashboardController::class)->name('dashboard');
    });

    Route::middleware('role:admin,lab')->prefix('lab')->name('lab.')->group(function (): void {
        Route::get('/dashboard', [LabCenterController::class, 'index'])->name('dashboard');
        Route::post('/requests/{labRequest}/results', [LabCenterController::class, 'storeResult'])->name('results.store');
    });
    Route::redirect('/lab-center', '/lab/dashboard')->name('lab-center.index');

    Route::middleware('role:admin,scan_center')->prefix('scan-center')->name('scan-center.')->group(function (): void {
        Route::get('/dashboard', [ScanCenterController::class, 'index'])->name('dashboard');
        Route::post('/requests/{scanRequest}/results', [ScanCenterController::class, 'storeResult'])->name('results.store');
    });
    Route::redirect('/scan-center', '/scan-center/dashboard')->name('scan-center.index');

    Route::middleware('role:admin,pharmacy')->prefix('pharmacy-center')->name('pharmacy-center.')->group(function (): void {
        Route::patch('/prescriptions/{prescription}', [PharmacyCenterController::class, 'update'])->name('prescriptions.update');
    });
    Route::middleware('role:admin,pharmacy')->prefix('pharmacy')->name('pharmacy.')->group(function (): void {
        Route::get('/dashboard', [PharmacyCenterController::class, 'index'])->name('dashboard');
    });
    Route::redirect('/pharmacy-center', '/pharmacy/dashboard')->name('pharmacy-center.index');

    Route::middleware('role:admin,reception')->prefix('reception')->name('reception.')->group(function (): void {
        Route::get('/dashboard', [PatientLookupController::class, 'index'])->name('dashboard');
        Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
        Route::get('/patients/{patient}/medical-card', [PatientMedicalCardController::class, 'showForStaff'])->name('patients.medical-card.show');
        Route::get('/patients/{patient}/medical-card/download', [PatientMedicalCardController::class, 'downloadForStaff'])->name('patients.medical-card.download');
    });

    // --- Dashboard routes (admin & doctor only) ---
    Route::middleware('role:admin,doctor')->prefix('dashboard')->group(function (): void {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/patient-overview', [DashboardController::class, 'getPatientOverview'])->name('dashboard.patient-overview');

        Route::resource('appointments', AppointmentController::class);
        Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.status');

        Route::middleware('role:admin')->group(function (): void {
            Route::resource('patients', PatientController::class)->except(['index', 'show']);
            Route::resource('doctors', DoctorController::class)->except(['index', 'show']);
            Route::resource('departments', DepartmentController::class)->except(['index', 'show']);
            Route::resource('staff-users', StaffUserController::class)->only(['index', 'create', 'store']);

            Route::get('/service-bookings', [ServiceBookingController::class, 'index'])->name('service-bookings.index');
            Route::patch('/service-bookings/{serviceBooking}/status', [ServiceBookingController::class, 'updateStatus'])->name('service-bookings.status');
        });

        Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
        Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
        Route::get('/patients/{patient}/medical-card', [PatientMedicalCardController::class, 'showForStaff'])->name('patients.medical-card.show');
        Route::get('/patients/{patient}/medical-card/download', [PatientMedicalCardController::class, 'downloadForStaff'])->name('patients.medical-card.download');
        Route::get('/patients/{patient}/clinical-records/create', [ClinicalRecordController::class, 'create'])->name('patients.clinical-records.create');
        Route::post('/patients/{patient}/clinical-records', [ClinicalRecordController::class, 'store'])->name('patients.clinical-records.store');

        Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');
        Route::get('/doctors/{doctor}', [DoctorController::class, 'show'])->name('doctors.show');
        Route::get('/doctors/{doctor}/schedule', [DoctorController::class, 'schedule'])->name('doctors.schedule');
        Route::post('/doctors/{doctor}/weekly-schedule', [DoctorController::class, 'updateWeeklySchedule'])->name('doctors.weekly-schedule.update');

        Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
        Route::get('/departments/{department}', [DepartmentController::class, 'show'])->name('departments.show');

        Route::post('/availability', [DashboardController::class, 'updateAvailability'])->name('availability.update');
    });
});

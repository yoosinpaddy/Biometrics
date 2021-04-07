<?php

use App\Http\Controllers\DeviceRecordController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\StudentController;
use App\Models\DeviceRecord;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Auth::routes();
Route::group(['middleware' => ['auth']], function () {
    Route::get('/home', [StudentController::class, 'home'])->name('school.home');
    Route::get('/parents', [StudentController::class, 'parents'])->name('school.parents');
    Route::get('/reports', [StudentController::class, 'reports'])->name('school.reports');
    Route::get('/send/sms', [StudentController::class, 'send_sms'])->name('school.send.sms');
    Route::get('/reports/sms', [StudentController::class, 'reports_sms'])->name('school.reports.sms');
    Route::get('/streams', [StudentController::class, 'streams'])->name('school.streams');
    Route::get('/class/{class_name}/{stream_id}', [StudentController::class, 'class'])->name('school.class.data');
});

Route::get('/', function () {
    return view('school.login');
})->name('default');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

Route::get('users/{id}', function ($id) {

});


Route::get('/login', [StudentController::class, 'login'])->name('clogin');
Route::get('/register', [StudentController::class, 'register'])->name('school.register');
Route::get('/logout', [StudentController::class, 'logout'])->name('school.logout');
Route::post('/login', [StudentController::class, 'login'])->name('school.login');
Route::post('/register', [StudentController::class, 'register'])->name('school.register');
Route::post('/recoverPassword', [StudentController::class, 'recoverPassword'])->name('school.recoverPassword');
Route::post('/forgotPassword', [StudentController::class, 'forgotPassword'])->name('school.forgotPassword');
Route::get('/recoverPassword', [StudentController::class, 'recoverPassword'])->name('school.recoverPassword');
Route::get('/forgotPassword', [StudentController::class, 'forgotPassword'])->name('school.forgotPassword');




Route::get('/device/{school_id}/posts', [PostController::class, 'index']);
Route::get('/device/{school_id}/deviceHeartBeat', [DeviceRecordController::class, 'storeg']);
Route::post('/device/{school_id}/deviceHeartBeat', [DeviceRecordController::class, 'store']);

Route::get('/device/{school_id}/recordUpload', [DeviceRecordController::class, 'recordUploadg']);
Route::post('/device/{school_id}/recordUpload', [DeviceRecordController::class, 'recordUpload']);

Route::get('/device/{school_id}/dataPull', [DeviceRecordController::class, 'dataPullg']);
Route::post('/device/{school_id}/dataPull', [DeviceRecordController::class, 'dataPull']);

Route::get('/device/{school_id}/dataPullBack', [DeviceRecordController::class, 'dataPullBackg']);
Route::post('/device/{school_id}/dataPullBack', [DeviceRecordController::class, 'dataPullBack']);

Route::get('store', [PostController::class, 'store']);

<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Mail\ReportReceivedMail;
use http\Client\Request;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;

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
//
//Route::get('/', function () {
//    return to_route('report.index');
//});
//
//Route::get('/dashboard', function () {
//    return Inertia::render('Dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');
//
//



Route::get('/', [ReportController::class, 'index'])->name('report.index');
Route::post('/', [ReportController::class, 'store'])->name('report.store');

//Route::get('/poa', [ReportController::class, 'index'])->name('report.index');
//Route::post('/poa', [ReportController::class, 'store'])->name('report.store');



//Route::get('/attachments/download/{attachment:file_hashed_name}', [AttachmentController::class, 'download'])->name('attachment.download')->where('attachment', '.*');
//Route::get('/report_attachments/{attachment:file_hashed_name}', [AttachmentController::class, 'download'])->name('attachment.download')->where('attachment', '.*');

//Route::get('/test', function (Request $request) {
//    $report = \App\Models\Report::find(1);
//    Mail::to('vadimas.klepko@fltechnics.com')->send(new ReportReceivedMail($report));
//
//    dd('Mail sended');
//});
//

Route::get('/test/report_example', function (Request $request) {
    $report = \App\Models\Report::find(1);

   return view('emails.report-received', compact('report'));
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

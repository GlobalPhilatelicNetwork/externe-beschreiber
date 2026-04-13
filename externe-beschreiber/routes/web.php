<?php
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Describer\ConsignmentController as DescriberConsignmentController;
use Illuminate\Support\Facades\Route;

// Auth
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');
Route::post('/password/change', [LoginController::class, 'changePassword'])->middleware('auth')->name('password.change');

// Locale switching
Route::get('/locale/{locale}', function (string $locale) {
    if (in_array($locale, ['de', 'en'])) {
        session(['locale' => $locale]);
        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }
    }
    return back();
})->name('locale.switch');

// Redirect root
Route::get('/', function () {
    return redirect('/login');
});

Route::middleware('auth')->group(function () {
    Route::get('/consignments', [DescriberConsignmentController::class, 'index'])
        ->name('describer.consignments.index');
    Route::get('/consignments/{consignment}', [DescriberConsignmentController::class, 'show'])
        ->name('describer.consignments.show');
});

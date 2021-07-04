<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstallationController;
use App\Http\Controllers\Admin;

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

// Route::get('/install', [InstallationController::class, 'index'])->name('installation.index');

Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function() {
	Route::resource('educations', Admin\EducationController::class);
	Route::put('educations/{id}/graduated', [Admin\EducationController::class, 'graduated']);
	
	Route::resource('experiences', Admin\ExperienceController::class);
	Route::resource('skills', Admin\SkillController::class);
	Route::resource('certifications', Admin\CertificationController::class);
	
	Route::group(['prefix' => 'me'], function() {
		Route::get('/', [Admin\AboutController::class, 'index'])->name('about.index');
		Route::get('/create', [Admin\AboutController::class, 'create'])->name('about.create');
		Route::get('/edit', [Admin\AboutController::class, 'edit'])->name('about.edit');
		
		Route::post('/', [Admin\AboutController::class, 'store'])->name('about.store');
		Route::put('/', [Admin\AboutController::class, 'update'])->name('about.update');
	});

});

require __DIR__.'/auth.php';

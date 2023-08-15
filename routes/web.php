<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Sas\ShopwareAppLaravelSdk\Http\Controllers\AppRegistrationController;

Route::name('sw-app.auth.')
    ->controller(AppRegistrationController::class)->group(function (): void {
        Route::get(Config::get('sw-app.registration_url'), 'register')->name('registration');
        Route::post(Config::get('sw-app.confirmation_url'), 'confirm')->name('confirmation');
        Route::post(Config::get('sw-app.activate_url'), 'activate')->name('activate');
        Route::post(Config::get('sw-app.deactivate_url'), 'deactivate')->name('deactivate');
        Route::post(Config::get('sw-app.delete_url'), 'delete')->name('delete');
    });

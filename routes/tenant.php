<?php

declare(strict_types=1);

use App\Http\Middleware\CheckIfUserIsActive;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Inertia\Inertia;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\RoleController;
use App\Http\Controllers\Patient\PatientController;
use App\Http\Controllers\Patient\AnamnesisController;
use App\Http\Controllers\Patient\BudgetController;
use App\Http\Controllers\Patient\InvoicesController;
use App\Http\Controllers\Patient\ReferralsController;
use App\Http\Controllers\Patient\TreatmentController;
use App\Http\Controllers\Patient\DocumentController;
use App\Http\Controllers\Patient\ClinicalRecordController;
use App\Http\Controllers\Patient\AppointmentController;
use App\Http\Controllers\Financial\CashAccountController;
use App\Http\Controllers\Financial\CashFlowController;
use App\Http\Controllers\Financial\CurrentAccountController;
use App\Http\Controllers\Financial\CashierSessionController;


/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

$tenantMiddleware = [
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
];

$authMiddleware = [
    'auth',
    'verified',
    CheckIfUserIsActive::class,
];

Route::middleware($tenantMiddleware)->group(function () use ($authMiddleware) {
    Route::get('/', function () {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        } else {
            return redirect()->route('login');
        }
    })->name('home');

    Route::middleware($authMiddleware)->group(function () {
        Route::get('dashboard', function () {
            return Inertia::render('Dashboard');
        })->name('dashboard');

        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index'])
                ->middleware('can:view users')
                ->name('users.index');

            Route::middleware('can:create users')->group(function () {
                Route::get('/create', [UserController::class, 'create'])
                    ->name('users.create');
                Route::post('/', [UserController::class, 'store'])
                    ->name('users.store');
            });

            Route::middleware('can:edit users')->group(function () {
                Route::get('/{user}/edit', [UserController::class, 'edit'])
                    ->name('users.edit');
                Route::put('/{user}', [UserController::class, 'update'])
                    ->name('users.update');
            });

            //TODO: Implementar permissão para ativar/desativar usuários
            Route::middleware('can:edit users')->group(function () {
                Route::post('/{user}/activate', [UserController::class, 'activate'])
                    ->name('users.activate');
                Route::post('/{user}/deactivate', [UserController::class, 'deactivate'])
                    ->name('users.deactivate');
            });

            Route::get('/{user}', [UserController::class, 'show'])
                ->middleware('can:view users')
                ->name('users.show');

            Route::middleware('can:delete users')->group(function () {
                Route::delete('/{user}', [UserController::class, 'destroy'])
                    ->name('users.destroy');
            });
        });

        Route::prefix('roles')->group(function () {
            Route::get('/', [RoleController::class, 'index'])
                ->middleware('can:view roles')
                ->name('roles.index');

            Route::middleware('can:create roles')->group(function () {
                Route::get('/create', [RoleController::class, 'create'])
                    ->name('roles.create');
                Route::post('/', [RoleController::class, 'store'])
                    ->name('roles.store');
            });

            Route::get('/{role}', [RoleController::class, 'show'])
                ->middleware('can:view roles')
                ->name('roles.show');

            Route::middleware('can:edit roles')->group(function () {
                Route::get('/{role}/edit', [RoleController::class, 'edit'])
                    ->name('roles.edit');
                Route::put('/{role}', [RoleController::class, 'update'])
                    ->name('roles.update');
            });

            Route::middleware('can:delete roles')->group(function () {
                Route::delete('/{role}', [RoleController::class, 'destroy'])
                    ->name('roles.destroy');
            });
        });
    });

    Route::prefix('patients')->group(function () {
        Route::get('/', [PatientController::class, 'index'])
            // ->middleware(['auth', 'verified', 'can:view patients'])
            ->name('patients.index');

        Route::get('/create', [PatientController::class, 'create'])
            // ->middleware(['auth', 'verified', 'can:create patients'])
            ->name('patients.create');

        Route::post('/', [PatientController::class, 'store'])
            // ->middleware(['auth', 'verified', 'can:create patients'])
            ->name('patients.store');

        Route::get('/{id}/edit', [PatientController::class, 'edit'])
            // ->middleware(['auth', 'verified', 'can:edit patients'])
            ->name('patients.edit');

        Route::put('/{id}', [PatientController::class, 'update'])
            // ->middleware(['auth', 'verified', 'can:edit patients'])
            ->name('patients.update');

        Route::get('/{id}', [PatientController::class, 'show'])
            // ->middleware(['auth', 'verified', 'can:view patients'])
            ->name('patients.show');

        Route::delete('/{id}', [PatientController::class, 'destroy'])
            // ->middleware(['auth', 'verified', 'can:delete patients'])
            ->name('patients.destroy');

        Route::get('/{id}/anamnesis/history', [AnamnesisController::class, 'show'])
            // ->middleware(['auth', 'verified'])
            ->name('patients.anamnesis.history');

        Route::get('/{id}/appointments', [AppointmentController::class, 'index'])
            // ->middleware(['auth', 'verified'])
            ->name('patients.appointments');

        Route::get('/{id}/clinical-record', [ClinicalRecordController::class, 'index'])
            // ->middleware(['auth', 'verified'])
            ->name('patients.clinicalRecord');

        Route::get('/{id}/anamnesis', [AnamnesisController::class, 'index'])
            // ->middleware(['auth', 'verified'])
            ->name('patients.anamnesis');
        
        Route::get('/{id}/documents', [DocumentController::class, 'index'])
            // ->middleware(['auth', 'verified'])
            ->name('patients.documents');
        
        Route::get('/{id}/invoices', [InvoicesController::class, 'index'])
            // ->middleware(['auth', 'verified'])
            ->name('patients.invoices');

        Route::get('/{id}/invoices/{budget}/checkout', [InvoicesController::class, 'checkout'])
            // ->middleware(['auth', 'verified'])
            ->name('patients.invoices.checkout');

        Route::post('/{id}/invoices/{budget}', [InvoicesController::class, 'storeCheckout'])
            // ->middleware(['auth', 'verified'])
            ->name('patients.invoices.storeCheckout');

        Route::delete('/{id}', [InvoicesController::class, 'destroy'])
            // ->middleware(['auth', 'verified'])
            ->name('patients.invoices.destroy');

        Route::get('/{id}/budgets', [BudgetController::class, 'index'])
            // ->middleware(['auth', 'verified'])
            ->name('patients.budgets');

        Route::get('/{id}/budgets/form', [BudgetController::class, 'create'])
            // ->middleware(['auth', 'verified'])
            ->name('patients.budgets.form');

        Route::post('/{id}/budgets', [BudgetController::class, 'store'])
            // ->middleware(['auth', 'verified'])
            ->name('patients.budgets.store');

        Route::post('/{id}/budgets/approve', [BudgetController::class, 'approve'])
            // ->middleware(['auth', 'verified', 'can:approve budgets'])
            ->name('patients.budgets.approve');

        Route::delete('/{id}', [BudgetController::class, 'destroy'])
            // ->middleware(['auth', 'verified', 'can:delete budgets'])
            ->name('patients.budgets.destroy');

        Route::get('/{id}/referrals', [ReferralsController::class, 'index'])
            // ->middleware(['auth', 'verified'])
            ->name('patients.referrals');

        Route::get('/{id}/treatments', [TreatmentController::class, 'index'])
            // ->middleware(['auth', 'verified'])
            ->name('patients.treatments');

        Route::get('/{id}/edit', [PatientController::class, 'edit'])
            // ->middleware(['auth', 'verified', 'can:edit patients'])
            ->name('patients.edit');
    });

    Route::get('/birthdays', [PatientController::class, 'birthdays'])
        // ->middleware(['auth', 'verified', 'can:view patients'])
        ->name('birthdays');

    Route::prefix('financial')->group(function () {
        Route::get('/', [CashAccountController::class, 'index'])
            // ->middleware(['auth', 'verified', 'can:view cash_accounts'])
            ->name('financial.index');
        
        Route::get('/create', [CashAccountController::class, 'create'])
            // ->middleware(['auth', 'verified', 'can:create cash_accounts'])
            ->name('financial.create');

        Route::post('/', [CashAccountController::class, 'store'])
            // ->middleware(['auth', 'verified', 'can:create cash_accounts'])
            ->name('financial.store');

        Route::get('/{id}', [CashAccountController::class, 'show'])
            // ->middleware(['auth', 'verified', 'can:view cash_accounts'])
            ->name('financial.show');

        Route::get('/{id}/close', [CashAccountController::class, 'close'])
            // ->middleware(['auth', 'verified', 'can:close cash_accounts'])
            ->name('financial.close');

        Route::post('/{id}', [CashAccountController::class, 'disable'])
            // ->middleware(['auth', 'verified', 'can:disable cash_accounts'])
            ->name('financial.disable');

        Route::get('/{id}/edit', [CashAccountController::class, 'edit'])
            // ->middleware(['auth', 'verified', 'can:disable cash_accounts'])
            ->name('financial.edit');

        Route::put('/{id}', [CashAccountController::class, 'update'])
            // ->middleware(['auth', 'verified', 'can:disable cash_accounts'])
            ->name('financial.update');

        Route::get('/{id}/open', [CashierSessionController::class, 'open'])
            // ->middleware(['auth', 'verified', 'can:open cash_accounts'])
            ->name('financial.open');
        
        Route::post('/{id}/sessions', [CashierSessionController::class, 'storeOpen'])
            // ->middleware(['auth', 'verified', 'can:open cash_accounts'])
            ->name('financial.storeOpen');
    });

    Route::get('/current-account', [CurrentAccountController::class, 'index'])
        // ->middleware(['auth', 'verified', 'can:view current_accounts'])
        ->name('current-account');

    Route::get('/cash-flow', [CashFlowController::class, 'index'])
        // ->middleware(['auth', 'verified', 'can:view cash_flows'])
        ->name('cash-flow');
    
});

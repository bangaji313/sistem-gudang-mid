<?php

namespace App\Providers;

use App\Models\PurchaseOrderDetail;
use App\Models\Transaction;
use App\Observers\PurchaseOrderDetailObserver;
use App\Observers\TransactionObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        PurchaseOrderDetail::observe(PurchaseOrderDetailObserver::class);
        Transaction::observe(TransactionObserver::class);
    }
}

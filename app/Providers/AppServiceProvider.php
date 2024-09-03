<?php

namespace App\Providers;

use App\Services\Interfaces\PayslipContract;
use App\Services\PayslipService;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public $bindings = [
        PayslipContract::class => PayslipService::class
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $dateTimeType = Type::getType(Types::DATETIME_MUTABLE);
        Type::addType('timestamp', $dateTimeType::class);
        
    }
}

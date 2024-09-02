<?php

namespace App\Providers;

use App\Service\Interfaces\PayslipContract;
use App\Service\PayslipService;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PayslipService::class, PayslipContract::class);
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

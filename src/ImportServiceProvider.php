<?php

namespace OZiTAG\Tager\Backend\Import;

use Illuminate\Support\ServiceProvider;

class ImportServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/routes.php');
    }
}

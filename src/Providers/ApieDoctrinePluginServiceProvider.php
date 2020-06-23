<?php

namespace W2w\Lib\ApieDoctrinePlugin\Providers;

use Doctrine\Persistence\ManagerRegistry;
use Illuminate\Support\ServiceProvider;
use W2w\Lib\ApieDoctrinePlugin\ApieDoctrinePlugin;

class ApieDoctrinePluginServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ApieDoctrinePlugin::class, function () {
            return ApieDoctrinePlugin::createFromRegistry($this->app->get(ManagerRegistry::class));
        });
    }
}

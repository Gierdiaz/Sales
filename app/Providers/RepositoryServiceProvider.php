<?php

namespace App\Providers;

use App\Contracts\ContactRepositoryInterface;
use App\Repositories\ContactRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ContactRepositoryInterface::class, ContactRepository::class);
    }

    public function boot()
    {
        //
    }
}

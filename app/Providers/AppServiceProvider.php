<?php

namespace App\Providers;

use App\Models\Department;
use App\Models\PatientCareService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
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
        Model::shouldBeStrict(! app()->isProduction());

        View::composer('layouts.website', function (\Illuminate\View\View $view): void {
            $view->with('navDepartments', Department::query()
                ->active()
                ->orderBy('name')
                ->get(['id', 'name', 'slug']));

            $view->with('navPatientCareServices', PatientCareService::query()
                ->active()
                ->orderBy('sort_order')
                ->get(['id', 'name', 'slug', 'icon_class']));
        });
    }
}

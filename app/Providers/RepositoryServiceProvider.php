<?php namespace App\Providers;

use App\Lite\Contracts\ActivityRepositoryInterface;
use App\Lite\Contracts\DocumentLinkRepositoryInterface;
use App\Lite\Contracts\OrganisationRepositoryInterface;
use App\Lite\Contracts\SettingsRepositoryInterface;
use App\Lite\Contracts\UserRepositoryInterface;
use App\Lite\Repositories\Activity\ActivityRepository;
use App\Lite\Repositories\DocumentLinks\DocumentLinksRepository;
use App\Lite\Repositories\Organisation\OrganisationRepository;
use App\Lite\Repositories\Settings\SettingsRepository;
use App\Lite\Repositories\Users\UserRepository;
use App\Services\Settings\Segmentation\SegmentationInterface;
use App\Services\Settings\Segmentation\SegmentationService;
use Illuminate\Support\ServiceProvider;

/**
 * Class RepositoryServiceProvider
 * @package App\Providers
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * This service provider is a great spot to register your various container
     * bindings with the application. As you can see, we are registering our
     * "Registrar" implementation here. You can add your own bindings too!
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'App\SuperAdmin\Repositories\SuperAdminInterfaces\SuperAdmin',
            'App\SuperAdmin\Repositories\SuperAdmin'
        );

        $this->app->bind(
            'App\SuperAdmin\Repositories\SuperAdminInterfaces\OrganizationGroup',
            'App\SuperAdmin\Repositories\OrganizationGroup'
        );

        $this->app->bind(
            SegmentationInterface::class,
            SegmentationService::class
        );

        $this->app->bind(ActivityRepositoryInterface::class, ActivityRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(OrganisationRepositoryInterface::class, OrganisationRepository::class);
        $this->app->bind(SettingsRepositoryInterface::class, SettingsRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(DocumentLinkRepositoryInterface::class, DocumentLinksRepository::class);
    }
}

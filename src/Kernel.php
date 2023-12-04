<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../config/{packages}/*.yaml');
        $container->import('../config/{packages}/'.$this->environment.'/**/*.yaml');
        $container->import('../config/services.yaml');

        // Add the following line to import the security.yaml file explicitly
        $container->import('../config/packages/security.yaml');
    }

    protected function configureRoutes(\Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator $routes): void
    {
        $routes->import('../config/{routes}/'.$this->environment.'/**/*.yaml');
        $routes->import('../config/{routes}/*.yaml');
        $routes->import('../config/routes.yaml');
    }
}

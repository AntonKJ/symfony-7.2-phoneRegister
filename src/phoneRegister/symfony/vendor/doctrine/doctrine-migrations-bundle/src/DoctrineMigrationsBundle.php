<?php

declare(strict_types=1);

namespace Doctrine\Bundle\MigrationsBundle;

use Doctrine\Bundle\MigrationsBundle\DependencyInjection\CompilerPass\ConfigureDependencyFactoryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use function dirname;

final class DoctrineMigrationsBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ConfigureDependencyFactoryPass());
    }

    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}

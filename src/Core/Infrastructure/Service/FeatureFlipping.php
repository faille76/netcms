<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Service;

use App\Core\Domain\ConfigRepositoryInterface;
use App\Core\Domain\FeatureEnum;
use App\Core\Domain\FeatureFlippingInterface;

final class FeatureFlipping implements FeatureFlippingInterface
{
    private ConfigRepositoryInterface $configRepository;

    public function __construct(ConfigRepositoryInterface $configRepository)
    {
        $this->configRepository = $configRepository;
    }

    public function isModuleEnabled(string $moduleName): bool
    {
        return $this->configRepository->get('features')[$moduleName] ?? false;
    }

    public function getModules(): array
    {
        $modules = [];
        foreach (FeatureEnum::getModules() as $module) {
            $modules[$module] = $this->isModuleEnabled($module);
        }

        return $modules;
    }

    public function getModulesEnabled(): array
    {
        $modules = [];
        foreach (FeatureEnum::getModules() as $module) {
            if ($this->isModuleEnabled($module)) {
                $modules[$module] = $this->isModuleEnabled($module);
            }
        }

        return $modules;
    }
}

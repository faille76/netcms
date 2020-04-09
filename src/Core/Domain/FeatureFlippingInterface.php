<?php

declare(strict_types=1);

namespace App\Core\Domain;

interface FeatureFlippingInterface
{
    public function isModuleEnabled(string $moduleName): bool;

    public function getModules(): array;

    public function getModulesEnabled(): array;
}

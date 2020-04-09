<?php

declare(strict_types=1);

namespace App\Shared\Domain;

final class Criteria
{
    private ?int $limit;
    private ?int $offset;
    private ?array $ordersBy;

    public function __construct(
        ?array $ordersBy = null,
        ?int $offset = null,
        ?int $limit = null
    ) {
        $this->ordersBy = $ordersBy;
        $this->offset = $offset;
        $this->limit = $limit;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function getOrdersBy(): ?array
    {
        return $this->ordersBy;
    }
}

<?php

declare(strict_types=1);

namespace App\Shared\Application\Event;

use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractBaseEvent extends Event implements \JsonSerializable
{
    private array $properties = [];

    public function __construct(int $occurredOn)
    {
        $this->properties['occurredOn'] = $occurredOn;
    }

    public function getOccurredOn(): int
    {
        return $this->properties['occurredOn'];
    }

    public function getEventName(): string
    {
        return static::class;
    }

    public function getEventVersion(): string
    {
        return '1.0';
    }

    public function toArray(): array
    {
        return array_merge([
            'eventName' => $this->getEventName(),
            'eventVersion' => $this->getEventVersion(),
        ], $this->properties);
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    protected function addProperties(array $values)
    {
        $this->properties = array_merge($this->properties, $values);
    }

    /**
     * @param mixed|null $value
     */
    protected function addProperty(string $key, $value)
    {
        $this->properties[$key] = $value;
    }

    /**
     * @param mixed|null $default
     * @return mixed|null
     */
    protected function getProperty(string $key, $default = null)
    {
        return $this->properties[$key] ?? $default;
    }
}

<?php

namespace Thinktomorrow\Trader\Testing;

use Psr\Container\ContainerInterface;
use Thinktomorrow\Trader\Domain\Common\Event\EventDispatcher;
use Thinktomorrow\Trader\Infrastructure\Test\EventDispatcherSpy;
use Thinktomorrow\Trader\Infrastructure\Test\TestContainer;
use Thinktomorrow\Trader\Infrastructure\Test\TestTraderConfig;
use Thinktomorrow\Trader\TraderConfig;

abstract class TraderContext
{
    public bool $persist = true;

    /**
     * Release the initial events when creating the aggregate. This way you can test
     * the specific domain events that occur after creation.
     */
    public bool $keepDomainEvents = false;

    protected TraderConfig $config;

    protected ContainerInterface $container;

    protected EventDispatcher $eventDispatcher;

    public function __construct()
    {
        $this->config = new TestTraderConfig;
        $this->container = new TestContainer;
        $this->eventDispatcher = new EventDispatcherSpy;
    }

    public function dontPersist(): self
    {
        $this->persist = false;

        return $this;
    }

    public function persist(): self
    {
        $this->persist = true;

        return $this;
    }

    public function keepDomainEvents(): self
    {
        $this->keepDomainEvents = true;

        return $this;
    }

    public function releaseDomainEvents(): self
    {
        $this->keepDomainEvents = false;

        return $this;
    }

    public function setEventDispatcher(EventDispatcher $eventDispatcher): self
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    public function setConfig(TraderConfig $config): self
    {
        $this->config = $config;

        return $this;
    }

    public function setContainer(ContainerInterface $container): self
    {
        $this->container = $container;

        return $this;
    }

    abstract public static function setUp(): void;

    abstract public static function tearDown(): void;

    abstract public static function drivers(): array;

    // Factory for in memory context - ideal for acceptance tests
    abstract public static function inMemory(): self;

    // Factory for mysql based context - ideal for integration tests
    abstract public static function mysql(): self;

    // Factory for laravel based context - ideal for laravel integration tests or scaffolding
    abstract public static function laravel(): self;
}

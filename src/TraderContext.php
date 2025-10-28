<?php

namespace Thinktomorrow\Trader\Testing;

use Thinktomorrow\Trader\Domain\Common\Event\EventDispatcher;
use Thinktomorrow\Trader\Infrastructure\Test\EventDispatcherSpy;
use Thinktomorrow\Trader\Infrastructure\Test\TestTraderConfig;
use Thinktomorrow\Trader\TraderConfig;

abstract class TraderContext
{
    public bool $persist = true;

    protected TraderConfig $config;

    protected EventDispatcher $eventDispatcher;

    public function __construct()
    {
        $this->config = new TestTraderConfig;
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

    abstract public static function setUp(): void;

    abstract public static function tearDown(): void;

    abstract public static function drivers(): array;

    abstract public static function inMemory(): self;

    abstract public static function mysql(): self;
}

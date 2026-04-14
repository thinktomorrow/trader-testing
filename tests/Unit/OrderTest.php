<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Thinktomorrow\Trader\Testing\Order\OrderApplications;
use Thinktomorrow\Trader\Testing\Order\OrderContext;
use Thinktomorrow\Trader\Testing\Order\Repositories\OrderRepositories;

class OrderTest extends TestCase
{
    public function test_it_can_setup_in_memory_order(): void
    {
        $order = OrderContext::inMemory();

        $this->assertNotNull($order);
    }

    public function test_it_can_call_in_memory_repos(): void
    {
        $order = OrderContext::inMemory();

        $this->assertInstanceOf(OrderRepositories::class, $order->repos());
    }

    public function test_it_can_call_in_memory_apps(): void
    {
        $order = OrderContext::inMemory();

        $this->assertInstanceOf(OrderApplications::class, $order->apps());
    }

    public function test_it_can_setup_mysql_order(): void
    {
        $order = OrderContext::mysql();

        $this->assertNotNull($order);
    }

    public function test_it_can_call_mysql_repos(): void
    {
        $order = OrderContext::mysql();

        $this->assertInstanceOf(OrderRepositories::class, $order->repos());
    }

    public function test_it_can_call_mysql_apps(): void
    {
        $order = OrderContext::mysql();

        $this->assertInstanceOf(OrderApplications::class, $order->apps());
    }
}

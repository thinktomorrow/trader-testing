<?php

class OrderTest extends \PHPUnit\Framework\TestCase
{
    public function test_it_can_setup_in_memory_order(): void
    {
        $order = \Thinktomorrow\Trader\Testing\Order\Order::inMemory();

        $this->assertNotNull($order);
    }

    public function test_it_can_call_in_memory_repos(): void
    {
        $order = \Thinktomorrow\Trader\Testing\Order\Order::inMemory();

        $this->assertInstanceOf(\Thinktomorrow\Trader\Testing\Order\Repositories\OrderRepositories::class, $order->orderRepos());
    }

    public function test_it_can_call_in_memory_apps(): void
    {
        $order = \Thinktomorrow\Trader\Testing\Order\Order::inMemory();

        $this->assertInstanceOf(\Thinktomorrow\Trader\Testing\Order\OrderApplications::class, $order->orderApps());
    }

    public function test_it_can_setup_mysql_order(): void
    {
        $order = \Thinktomorrow\Trader\Testing\Order\Order::mysql();

        $this->assertNotNull($order);
    }

    public function test_it_can_call_mysql_repos(): void
    {
        $order = \Thinktomorrow\Trader\Testing\Order\Order::mysql();

        $this->assertInstanceOf(\Thinktomorrow\Trader\Testing\Order\Repositories\OrderRepositories::class, $order->orderRepos());
    }

    public function test_it_can_call_mysql_apps(): void
    {
        $order = \Thinktomorrow\Trader\Testing\Order\Order::mysql();

        $this->assertInstanceOf(\Thinktomorrow\Trader\Testing\Order\OrderApplications::class, $order->orderApps());
    }
}

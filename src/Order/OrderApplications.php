<?php

namespace Thinktomorrow\Trader\Testing\Order;

use Thinktomorrow\Trader\Application\Cart\CartApplication;
use Thinktomorrow\Trader\Application\Cart\PaymentMethod\UpdatePaymentMethodOnOrder;
use Thinktomorrow\Trader\Application\Cart\PaymentMethod\VerifyPaymentMethodForCart;
use Thinktomorrow\Trader\Application\Cart\RefreshCart\Adjusters\AdjustLine;
use Thinktomorrow\Trader\Application\Cart\RefreshCart\RefreshCartAction;
use Thinktomorrow\Trader\Application\Cart\ShippingProfile\UpdateShippingProfileOnOrder;
use Thinktomorrow\Trader\Application\VatNumber\VatNumberApplication;
use Thinktomorrow\Trader\Application\VatNumber\VatNumberValidator;
use Thinktomorrow\Trader\Application\VatRate\FindVatRateForOrder;
use Thinktomorrow\Trader\Application\VatRate\VatExemptionApplication;
use Thinktomorrow\Trader\Domain\Common\Event\EventDispatcher;
use Thinktomorrow\Trader\Domain\Model\Order\State\OrderStateMachine;
use Thinktomorrow\Trader\Infrastructure\Test\TestContainer;
use Thinktomorrow\Trader\Testing\Order\Repositories\OrderRepositories;
use Thinktomorrow\Trader\TraderConfig;

class OrderApplications
{
    private OrderRepositories $repos;

    private TraderConfig $config;

    private EventDispatcher $eventDispatcher;

    public function __construct(OrderRepositories $repos, TraderConfig $config, EventDispatcher $eventDispatcher)
    {
        $this->repos = $repos;
        $this->config = $config;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function cartApplication(): CartApplication
    {
        return new CartApplication(
            $this->config,
            new TestContainer,
            $this->orderRepos->variantForCartRepository(),
            TestContainer::make(AdjustLine::class),
            $this->orderRepos->orderRepository(),
            TestContainer::make(OrderStateMachine::class),
            new RefreshCartAction,
            $this->orderRepos->shippingProfileRepository(),
            $this->updateShippingProfileOnOrder(),
            $this->updatePaymentMethodOnOrder(),
            $this->shopRepos->customerRepository(),
            $this->eventDispatcher,
            $this->vatNumberApplication(),
            $this->vatExemptionApplication(),
        );
    }

    public function updateShippingProfileOnOrder(): UpdateShippingProfileOnOrder
    {
        return new UpdateShippingProfileOnOrder(
            new TestContainer,
            $this->config,
            $this->orderRepos->orderRepository(),
            $this->orderRepos->shippingProfileRepository(),
            $this->findVatRateForOrder(),
        );
    }

    public function updatePaymentMethodOnOrder(): UpdatePaymentMethodOnOrder
    {
        return new UpdatePaymentMethodOnOrder(
            new TestContainer,
            $this->config,
            $this->orderRepos->orderRepository(),
            TestContainer::make(VerifyPaymentMethodForCart::class),
            $this->orderRepos->paymentMethodRepository(),
            $this->findVatRateForOrder(),
        );
    }

    public function vatNumberApplication(): VatNumberApplication
    {
        return new VatNumberApplication(
            TestContainer::make(VatNumberValidator::class),
        );
    }

    public function vatExemptionApplication(): VatExemptionApplication
    {
        return new VatExemptionApplication(
            $this->config,
        );
    }

    public function FindVatRateForOrder(): FindVatRateForOrder
    {
        return new FindVatRateForOrder(
            $this->config,
            $this->vatExemptionApplication(),
            $this->shopRepos->vatRateRepository()
        );
    }
}

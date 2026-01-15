<?php

namespace Thinktomorrow\Trader\Testing\Order;

use Psr\Container\ContainerInterface;
use Thinktomorrow\Trader\Application\Cart\CartApplication;
use Thinktomorrow\Trader\Application\Cart\PaymentMethod\UpdatePaymentMethodOnOrder;
use Thinktomorrow\Trader\Application\Cart\PaymentMethod\VerifyPaymentMethodForCart;
use Thinktomorrow\Trader\Application\Cart\RefreshCart\Adjusters\AdjustLine;
use Thinktomorrow\Trader\Application\Cart\RefreshCart\RefreshCartAction;
use Thinktomorrow\Trader\Application\Cart\ShippingProfile\UpdateShippingProfileOnOrder;
use Thinktomorrow\Trader\Application\Order\Merchant\MerchantOrderApplication;
use Thinktomorrow\Trader\Application\Order\State\OrderStateApplication;
use Thinktomorrow\Trader\Application\Promo\ApplyPromoToOrder;
use Thinktomorrow\Trader\Application\Promo\Coupon\CouponPromoApplication;
use Thinktomorrow\Trader\Application\Promo\CUD\PromoApplication;
use Thinktomorrow\Trader\Application\VatNumber\VatNumberApplication;
use Thinktomorrow\Trader\Application\VatNumber\VatNumberValidator;
use Thinktomorrow\Trader\Application\VatRate\FindVatRateForOrder;
use Thinktomorrow\Trader\Application\VatRate\VatExemptionApplication;
use Thinktomorrow\Trader\Domain\Common\Event\EventDispatcher;
use Thinktomorrow\Trader\Domain\Model\Order\Payment\PaymentStateMachine;
use Thinktomorrow\Trader\Domain\Model\Order\Shipping\ShippingStateMachine;
use Thinktomorrow\Trader\Domain\Model\Order\State\OrderStateMachine;
use Thinktomorrow\Trader\Testing\Catalog\Repositories\CatalogRepositories;
use Thinktomorrow\Trader\Testing\Order\Repositories\OrderRepositories;
use Thinktomorrow\Trader\TraderConfig;

class OrderApplications
{
    public function __construct(
        private OrderRepositories $repos,
        private CatalogRepositories $catalogRepos,
        private TraderConfig $config,
        private ContainerInterface $container,
        private EventDispatcher $eventDispatcher,
    ) {}

    public function cartApplication(): CartApplication
    {
        return new CartApplication(
            $this->config,
            $this->container,
            $this->repos->variantForCartRepository(),
            $this->container->get(AdjustLine::class),
            $this->repos->orderRepository(),
            $this->container->get(OrderStateMachine::class),
            new RefreshCartAction,
            $this->updateShippingProfileOnOrder(),
            $this->updatePaymentMethodOnOrder(),
            $this->repos->customerRepository(),
            $this->eventDispatcher,
            $this->vatNumberApplication(),
            $this->vatExemptionApplication(),
        );
    }

    public function getEventDispatcher(): EventDispatcher
    {
        return $this->eventDispatcher;
    }

    public function merchantOrderApplication(): MerchantOrderApplication
    {
        return new MerchantOrderApplication(
            $this->repos->orderRepository(),
            $this->eventDispatcher,
            $this->vatNumberApplication()
        );
    }

    public function orderStateApplication(): OrderStateApplication
    {
        return new OrderStateApplication(
            $this->repos->orderRepository(),
            $this->container->get(OrderStateMachine::class),
            $this->container->get(PaymentStateMachine::class),
            $this->container->get(ShippingStateMachine::class),
            $this->eventDispatcher,
        );
    }

    public function updateShippingProfileOnOrder(): UpdateShippingProfileOnOrder
    {
        return new UpdateShippingProfileOnOrder(
            $this->container,
            $this->repos->orderRepository(),
            $this->repos->shippingProfileRepository(),
        );
    }

    public function updatePaymentMethodOnOrder(): UpdatePaymentMethodOnOrder
    {
        return new UpdatePaymentMethodOnOrder(
            $this->container,
            $this->repos->orderRepository(),
            $this->container->get(VerifyPaymentMethodForCart::class),
            $this->repos->paymentMethodRepository(),
        );
    }

    public function vatNumberApplication(): VatNumberApplication
    {
        return new VatNumberApplication(
            $this->container->get(VatNumberValidator::class),
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
            $this->catalogRepos->vatRateRepository()
        );
    }

    public function customerApplication()
    {
        return new \Thinktomorrow\Trader\Application\Customer\CustomerApplication(
            $this->repos->customerRepository(),
            $this->eventDispatcher,
        );
    }

    public function promoApplication(): PromoApplication
    {
        return new PromoApplication(
            $this->config,
            $this->eventDispatcher,
            $this->repos->promoRepository(),
            $this->repos->discountFactory(),
        );
    }

    public function couponPromoApplication(): CouponPromoApplication
    {
        return new CouponPromoApplication(
            $this->repos->orderRepository(),
            $this->eventDispatcher,
            $this->repos->orderPromoRepository(),
            new ApplyPromoToOrder(
                $this->repos->orderRepository(),
            ),
            $this->container,
        );
    }

    public function paymentMethodApplication()
    {
        return new \Thinktomorrow\Trader\Application\PaymentMethod\PaymentMethodApplication(
            $this->eventDispatcher,
            $this->repos->paymentMethodRepository(),
        );
    }

    public function shippingProfileApplication()
    {
        return new \Thinktomorrow\Trader\Application\ShippingProfile\ShippingProfileApplication(
            $this->eventDispatcher,
            $this->repos->shippingProfileRepository(),
        );
    }
}

<?php

namespace Thinktomorrow\Trader\Testing\Order\Repositories;

use Psr\Container\ContainerInterface;
use Thinktomorrow\Trader\Application\Cart\Read\CartRepository;
use Thinktomorrow\Trader\Application\Cart\VariantForCart\VariantForCartRepository;
use Thinktomorrow\Trader\Application\Order\MerchantOrder\MerchantOrderRepository;
use Thinktomorrow\Trader\Application\Promo\OrderPromo\Discounts\FixedAmountOrderDiscount;
use Thinktomorrow\Trader\Application\Promo\OrderPromo\Discounts\PercentageOffOrderDiscount;
use Thinktomorrow\Trader\Application\Promo\OrderPromo\OrderConditionFactory;
use Thinktomorrow\Trader\Application\Promo\OrderPromo\OrderDiscountFactory;
use Thinktomorrow\Trader\Domain\Model\Country\CountryRepository;
use Thinktomorrow\Trader\Domain\Model\Customer\CustomerRepository;
use Thinktomorrow\Trader\Domain\Model\CustomerLogin\CustomerLoginRepository;
use Thinktomorrow\Trader\Domain\Model\Order\OrderRepository;
use Thinktomorrow\Trader\Domain\Model\PaymentMethod\PaymentMethodRepository;
use Thinktomorrow\Trader\Domain\Model\Promo\ConditionFactory;
use Thinktomorrow\Trader\Domain\Model\Promo\Conditions\MinimumLinesQuantity;
use Thinktomorrow\Trader\Domain\Model\Promo\DiscountFactory;
use Thinktomorrow\Trader\Domain\Model\Promo\Discounts\FixedAmountDiscount;
use Thinktomorrow\Trader\Domain\Model\Promo\Discounts\PercentageOffDiscount;
use Thinktomorrow\Trader\Domain\Model\Promo\PromoRepository;
use Thinktomorrow\Trader\Domain\Model\ShippingProfile\ShippingProfileRepository;
use Thinktomorrow\Trader\Domain\Model\VatRate\VatRateRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryCartRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryCountryRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryCustomerLoginRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryCustomerRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryMerchantOrderRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryOrderRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryPaymentMethodRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryPromoRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryShippingProfileRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryVariantRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryVatRateRepository;
use Thinktomorrow\Trader\TraderConfig;

class InMemoryOrderRepositories implements OrderRepositories
{
    private TraderConfig $config;

    private ContainerInterface $container;

    public function __construct(TraderConfig $config, ContainerInterface $container)
    {
        $this->config = $config;
        $this->container = $container;
    }

    public static function clear(): void
    {
        InMemoryOrderRepository::clear();
        InMemoryPromoRepository::clear();
        InMemoryCountryRepository::clear();
        InMemoryVatRateRepository::clear();
    }

    public function countryRepository(): CountryRepository
    {
        return new InMemoryCountryRepository;
    }

    public function customerRepository(): CustomerRepository
    {
        return new InMemoryCustomerRepository;
    }

    public function customerLoginRepository(): CustomerLoginRepository
    {
        return new InMemoryCustomerLoginRepository;
    }

    public function discountFactory(): DiscountFactory
    {
        return new DiscountFactory([
            FixedAmountDiscount::class,
            PercentageOffDiscount::class,
        ], new ConditionFactory([
            MinimumLinesQuantity::class,
        ]));
    }

    public function orderDiscountFactory(): OrderDiscountFactory
    {
        return new OrderDiscountFactory([
            FixedAmountOrderDiscount::class,
            PercentageOffOrderDiscount::class,
        ], new OrderConditionFactory([
            \Thinktomorrow\Trader\Application\Promo\OrderPromo\Conditions\MinimumLinesQuantityOrderCondition::class,
        ]));
    }

    public function promoRepository(): PromoRepository
    {
        $discountFactory = $this->discountFactory();

        $orderDiscountFactory = $this->orderDiscountFactory();

        return new InMemoryPromoRepository($discountFactory, $orderDiscountFactory);
    }

    public function cartRepository(): CartRepository
    {
        return new InMemoryCartRepository;
    }

    public function variantForCartRepository(): VariantForCartRepository
    {
        return new InMemoryVariantRepository;
    }

    public function orderRepository(): OrderRepository
    {
        return new InMemoryOrderRepository;
    }

    public function merchantOrderRepository(): MerchantOrderRepository
    {
        return new InMemoryMerchantOrderRepository;
    }

    public function paymentMethodRepository(): PaymentMethodRepository
    {
        return new InMemoryPaymentMethodRepository;
    }

    public function shippingProfileRepository(): ShippingProfileRepository
    {
        return new InMemoryShippingProfileRepository;
    }

    public function vatRateRepository(): VatRateRepository
    {
        return new InMemoryVatRateRepository($this->config);
    }
}

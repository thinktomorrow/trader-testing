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
use Thinktomorrow\Trader\Domain\Model\Promo\PromoRepository;
use Thinktomorrow\Trader\Domain\Model\ShippingProfile\ShippingProfileRepository;
use Thinktomorrow\Trader\Domain\Model\VatRate\VatRateRepository;
use Thinktomorrow\Trader\Infrastructure\Laravel\Repositories\MysqlCartRepository;
use Thinktomorrow\Trader\Infrastructure\Laravel\Repositories\MysqlCountryRepository;
use Thinktomorrow\Trader\Infrastructure\Laravel\Repositories\MysqlCustomerLoginRepository;
use Thinktomorrow\Trader\Infrastructure\Laravel\Repositories\MysqlCustomerRepository;
use Thinktomorrow\Trader\Infrastructure\Laravel\Repositories\MysqlMerchantOrderRepository;
use Thinktomorrow\Trader\Infrastructure\Laravel\Repositories\MysqlOrderRepository;
use Thinktomorrow\Trader\Infrastructure\Laravel\Repositories\MysqlPaymentMethodRepository;
use Thinktomorrow\Trader\Infrastructure\Laravel\Repositories\MysqlPromoRepository;
use Thinktomorrow\Trader\Infrastructure\Laravel\Repositories\MysqlShippingProfileRepository;
use Thinktomorrow\Trader\Infrastructure\Laravel\Repositories\MysqlVariantRepository;
use Thinktomorrow\Trader\Infrastructure\Laravel\Repositories\MysqlVatRateRepository;
use Thinktomorrow\Trader\TraderConfig;

class MysqlOrderRepositories implements OrderRepositories
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
    }

    public function countryRepository(): CountryRepository
    {
        return new MysqlCountryRepository;
    }

    public function customerRepository(): CustomerRepository
    {
        return new MysqlCustomerRepository($this->container);
    }

    public function customerLoginRepository(): CustomerLoginRepository
    {
        return new MysqlCustomerLoginRepository;
    }

    public function promoRepository(): PromoRepository
    {
        $discountFactory = new DiscountFactory([
            FixedAmountDiscount::class,
            PercentageOffOrderDiscount::class,
        ], new ConditionFactory([
            MinimumLinesQuantity::class,
        ]));

        $orderDiscountFactory = new OrderDiscountFactory([
            FixedAmountOrderDiscount::class,
            PercentageOffOrderDiscount::class,
        ], new OrderConditionFactory([
            \Thinktomorrow\Trader\Application\Promo\OrderPromo\Conditions\MinimumLinesQuantityOrderCondition::class,
        ]));

        return new MysqlPromoRepository($discountFactory, $orderDiscountFactory);
    }

    public function cartRepository(): CartRepository
    {
        return new MysqlCartRepository($this->container, $this->orderRepository());
    }

    public function variantForCartRepository(): VariantForCartRepository
    {
        return new MysqlVariantRepository($this->container);
    }

    public function orderRepository(): OrderRepository
    {
        return new MysqlOrderRepository($this->container, $this->config);
    }

    public function merchantOrderRepository(): MerchantOrderRepository
    {
        return new MysqlMerchantOrderRepository($this->container, $this->orderRepository());
    }

    public function paymentMethodRepository(): PaymentMethodRepository
    {
        return new MysqlPaymentMethodRepository($this->container);
    }

    public function shippingProfileRepository(): ShippingProfileRepository
    {
        return new MysqlShippingProfileRepository($this->container);
    }

    public function vatRateRepository(): VatRateRepository
    {
        return new MysqlVatRateRepository($this->container);
    }
}

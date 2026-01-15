<?php

namespace Thinktomorrow\Trader\Testing\Order\Repositories;

use Psr\Container\ContainerInterface;
use Thinktomorrow\Trader\Application\Cart\Read\CartRepository;
use Thinktomorrow\Trader\Application\Cart\VariantForCart\VariantForCartRepository;
use Thinktomorrow\Trader\Application\Order\MerchantOrder\MerchantOrderRepository;
use Thinktomorrow\Trader\Application\Promo\LinePromo\Discounts\SalePriceLineDiscount;
use Thinktomorrow\Trader\Application\Promo\OrderPromo\Discounts\FixedAmountOrderDiscount;
use Thinktomorrow\Trader\Application\Promo\OrderPromo\Discounts\PercentageOffOrderDiscount;
use Thinktomorrow\Trader\Application\Promo\OrderPromo\OrderConditionFactory;
use Thinktomorrow\Trader\Application\Promo\OrderPromo\OrderDiscountFactory;
use Thinktomorrow\Trader\Application\Promo\OrderPromo\OrderPromoRepository;
use Thinktomorrow\Trader\Domain\Model\Country\CountryRepository;
use Thinktomorrow\Trader\Domain\Model\Customer\CustomerRepository;
use Thinktomorrow\Trader\Domain\Model\CustomerLogin\CustomerLoginRepository;
use Thinktomorrow\Trader\Domain\Model\Order\Invoice\InvoiceRepository;
use Thinktomorrow\Trader\Domain\Model\Order\OrderRepository;
use Thinktomorrow\Trader\Domain\Model\PaymentMethod\PaymentMethodRepository;
use Thinktomorrow\Trader\Domain\Model\Promo\ConditionFactory;
use Thinktomorrow\Trader\Domain\Model\Promo\Conditions\MinimumLinesQuantity;
use Thinktomorrow\Trader\Domain\Model\Promo\DiscountFactory;
use Thinktomorrow\Trader\Domain\Model\Promo\Discounts\FixedAmountDiscount;
use Thinktomorrow\Trader\Domain\Model\Promo\Discounts\PercentageOffDiscount;
use Thinktomorrow\Trader\Domain\Model\Promo\Discounts\SalePriceSystemDiscount;
use Thinktomorrow\Trader\Domain\Model\Promo\PromoRepository;
use Thinktomorrow\Trader\Domain\Model\ShippingProfile\ShippingProfileRepository;
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

    public static function clear(): void {}

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

    public function discountFactory(): DiscountFactory
    {
        return new DiscountFactory([
            SalePriceSystemDiscount::class,
            FixedAmountDiscount::class,
            PercentageOffDiscount::class,
        ], new ConditionFactory([
            MinimumLinesQuantity::class,
        ]));
    }

    public function orderDiscountFactory(): OrderDiscountFactory
    {
        return new OrderDiscountFactory([
            SalePriceLineDiscount::class,
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

        return new MysqlPromoRepository($discountFactory, $orderDiscountFactory);
    }

    public function orderPromoRepository(): OrderPromoRepository
    {
        return $this->promoRepository();
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

    public function invoiceRepository(): InvoiceRepository
    {
        return new MysqlOrderRepository($this->container, $this->config);
    }
}

<?php

namespace Thinktomorrow\Trader\Testing\Order\Repositories;

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

class InMemoryOrderRepositories implements OrderRepositories
{
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
}

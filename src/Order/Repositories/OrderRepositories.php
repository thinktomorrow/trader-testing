<?php

namespace Thinktomorrow\Trader\Testing\Order\Repositories;

use Thinktomorrow\Trader\Application\Cart\Read\CartRepository;
use Thinktomorrow\Trader\Application\Cart\VariantForCart\VariantForCartRepository;
use Thinktomorrow\Trader\Application\Order\MerchantOrder\MerchantOrderRepository;
use Thinktomorrow\Trader\Domain\Model\Country\CountryRepository;
use Thinktomorrow\Trader\Domain\Model\Customer\CustomerRepository;
use Thinktomorrow\Trader\Domain\Model\CustomerLogin\CustomerLoginRepository;
use Thinktomorrow\Trader\Domain\Model\Order\OrderRepository;
use Thinktomorrow\Trader\Domain\Model\PaymentMethod\PaymentMethodRepository;
use Thinktomorrow\Trader\Domain\Model\Promo\PromoRepository;
use Thinktomorrow\Trader\Domain\Model\ShippingProfile\ShippingProfileRepository;

interface OrderRepositories
{
    public function cartRepository(): CartRepository;

    public function variantForCartRepository(): VariantForCartRepository;

    public function orderRepository(): OrderRepository;

    public function merchantOrderRepository(): MerchantOrderRepository;

    public function paymentMethodRepository(): PaymentMethodRepository;

    public function shippingProfileRepository(): ShippingProfileRepository;

    public function countryRepository(): CountryRepository;

    public function customerRepository(): CustomerRepository;

    public function customerLoginRepository(): CustomerLoginRepository;

    public function promoRepository(): PromoRepository;
}

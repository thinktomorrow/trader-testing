<?php

namespace Thinktomorrow\Trader\Testing\Order;

use Thinktomorrow\Trader\Application\Cart\Read\Cart;
use Thinktomorrow\Trader\Application\Cart\Read\CartBillingAddress;
use Thinktomorrow\Trader\Application\Cart\Read\CartDiscount;
use Thinktomorrow\Trader\Application\Cart\Read\CartLine;
use Thinktomorrow\Trader\Application\Cart\Read\CartLinePersonalisation;
use Thinktomorrow\Trader\Application\Cart\Read\CartPayment;
use Thinktomorrow\Trader\Application\Cart\Read\CartShipping;
use Thinktomorrow\Trader\Application\Cart\Read\CartShippingAddress;
use Thinktomorrow\Trader\Application\Cart\Read\CartShopper;
use Thinktomorrow\Trader\Application\Cart\VariantForCart\VariantForCart;
use Thinktomorrow\Trader\Application\Customer\Read\CustomerBillingAddress;
use Thinktomorrow\Trader\Application\Customer\Read\CustomerRead;
use Thinktomorrow\Trader\Application\Customer\Read\CustomerShippingAddress;
use Thinktomorrow\Trader\Application\Order\MerchantOrder\MerchantOrder;
use Thinktomorrow\Trader\Application\Order\MerchantOrder\MerchantOrderBillingAddress;
use Thinktomorrow\Trader\Application\Order\MerchantOrder\MerchantOrderDiscount;
use Thinktomorrow\Trader\Application\Order\MerchantOrder\MerchantOrderEvent;
use Thinktomorrow\Trader\Application\Order\MerchantOrder\MerchantOrderLine;
use Thinktomorrow\Trader\Application\Order\MerchantOrder\MerchantOrderLinePersonalisation;
use Thinktomorrow\Trader\Application\Order\MerchantOrder\MerchantOrderPayment;
use Thinktomorrow\Trader\Application\Order\MerchantOrder\MerchantOrderShipping;
use Thinktomorrow\Trader\Application\Order\MerchantOrder\MerchantOrderShippingAddress;
use Thinktomorrow\Trader\Application\Order\MerchantOrder\MerchantOrderShopper;
use Thinktomorrow\Trader\Domain\Common\Email;
use Thinktomorrow\Trader\Domain\Model\Country\Country;
use Thinktomorrow\Trader\Domain\Model\Country\CountryId;
use Thinktomorrow\Trader\Domain\Model\Customer\Customer;
use Thinktomorrow\Trader\Domain\Model\CustomerLogin\CustomerLogin;
use Thinktomorrow\Trader\Domain\Model\Order\Address\BillingAddress;
use Thinktomorrow\Trader\Domain\Model\Order\Address\ShippingAddress;
use Thinktomorrow\Trader\Domain\Model\Order\Discount\Discount;
use Thinktomorrow\Trader\Domain\Model\Order\Discount\DiscountableType;
use Thinktomorrow\Trader\Domain\Model\Order\Line\Line;
use Thinktomorrow\Trader\Domain\Model\Order\Order as DomainOrder;
use Thinktomorrow\Trader\Domain\Model\Order\Payment\DefaultPaymentState;
use Thinktomorrow\Trader\Domain\Model\Order\Payment\Payment;
use Thinktomorrow\Trader\Domain\Model\Order\Payment\PaymentState;
use Thinktomorrow\Trader\Domain\Model\Order\Shipping\DefaultShippingState;
use Thinktomorrow\Trader\Domain\Model\Order\Shipping\Shipping;
use Thinktomorrow\Trader\Domain\Model\Order\Shipping\ShippingState;
use Thinktomorrow\Trader\Domain\Model\Order\Shopper;
use Thinktomorrow\Trader\Domain\Model\Order\State\DefaultOrderState;
use Thinktomorrow\Trader\Domain\Model\Order\State\OrderState;
use Thinktomorrow\Trader\Domain\Model\PaymentMethod\PaymentMethod;
use Thinktomorrow\Trader\Domain\Model\PaymentMethod\PaymentMethodProviderId;
use Thinktomorrow\Trader\Domain\Model\PaymentMethod\PaymentMethodState;
use Thinktomorrow\Trader\Domain\Model\ShippingProfile\ShippingProfile;
use Thinktomorrow\Trader\Domain\Model\ShippingProfile\ShippingProfileState;
use Thinktomorrow\Trader\Domain\Model\ShippingProfile\Tariff;
use Thinktomorrow\Trader\Domain\Model\VatRate\BaseRate;
use Thinktomorrow\Trader\Domain\Model\VatRate\VatRate;
use Thinktomorrow\Trader\Domain\Model\VatRate\VatRateState;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\Cart\DefaultCart;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\Cart\DefaultCartBillingAddress;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\Cart\DefaultCartDiscount;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\Cart\DefaultCartLine;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\Cart\DefaultCartLinePersonalisation;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\Cart\DefaultCartPayment;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\Cart\DefaultCartShipping;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\Cart\DefaultCartShippingAddress;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\Cart\DefaultCartShopper;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\CustomerRead\DefaultCustomerBillingAddress;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\CustomerRead\DefaultCustomerRead;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\CustomerRead\DefaultCustomerShippingAddress;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\DefaultVariantForCart;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\MerchantOrder\DefaultMerchantOrder;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\MerchantOrder\DefaultMerchantOrderBillingAddress;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\MerchantOrder\DefaultMerchantOrderDiscount;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\MerchantOrder\DefaultMerchantOrderEvent;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\MerchantOrder\DefaultMerchantOrderLine;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\MerchantOrder\DefaultMerchantOrderLinePersonalisation;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\MerchantOrder\DefaultMerchantOrderPayment;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\MerchantOrder\DefaultMerchantOrderShipping;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\MerchantOrder\DefaultMerchantOrderShippingAddress;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\MerchantOrder\DefaultMerchantOrderShopper;
use Thinktomorrow\Trader\Infrastructure\Laravel\Repositories\MysqlOrderRepository;
use Thinktomorrow\Trader\Infrastructure\Test\TestContainer;
use Thinktomorrow\Trader\Infrastructure\Test\TestTraderConfig;
use Thinktomorrow\Trader\Testing\Catalog\Repositories\InMemoryCatalogRepositories;
use Thinktomorrow\Trader\Testing\Order\Repositories\InMemoryOrderRepositories;
use Thinktomorrow\Trader\Testing\Order\Repositories\MysqlOrderRepositories;
use Thinktomorrow\Trader\Testing\Order\Repositories\OrderRepositories;
use Thinktomorrow\Trader\Testing\TraderDomain;

class Order extends TraderDomain
{
    private OrderRepositories $orderRepos;

    public bool $persist = true;

    public function __construct(OrderRepositories $orderRepos)
    {
        parent::__construct();

        $this->orderRepos = $orderRepos;
    }

    public function orderRepos(): OrderRepositories
    {
        return $this->orderRepos;
    }

    public function orderApps(): OrderApplications
    {
        return new OrderApplications($this->orderRepos, $this->config, $this->eventDispatcher);
    }

    public static function setUp(): void
    {
        // States
        (new TestContainer)->add(OrderState::class, DefaultOrderState::class);
        (new TestContainer)->add(ShippingState::class, DefaultShippingState::class);
        (new TestContainer)->add(PaymentState::class, DefaultPaymentState::class);

        // Cart
        (new TestContainer)->add(VariantForCart::class, DefaultVariantForCart::class);
        (new TestContainer)->add(Cart::class, DefaultCart::class);
        (new TestContainer)->add(CartLine::class, DefaultCartLine::class);
        (new TestContainer)->add(CartLinePersonalisation::class, DefaultCartLinePersonalisation::class);
        (new TestContainer)->add(CartShippingAddress::class, DefaultCartShippingAddress::class);
        (new TestContainer)->add(CartBillingAddress::class, DefaultCartBillingAddress::class);
        (new TestContainer)->add(CartShipping::class, DefaultCartShipping::class);
        (new TestContainer)->add(CartPayment::class, DefaultCartPayment::class);
        (new TestContainer)->add(CartShopper::class, DefaultCartShopper::class);
        (new TestContainer)->add(CartDiscount::class, DefaultCartDiscount::class);

        // MerchantOrder
        (new TestContainer)->add(MerchantOrder::class, DefaultMerchantOrder::class);
        (new TestContainer)->add(MerchantOrderLine::class, DefaultMerchantOrderLine::class);
        (new TestContainer)->add(MerchantOrderLinePersonalisation::class, DefaultMerchantOrderLinePersonalisation::class);
        (new TestContainer)->add(MerchantOrderShippingAddress::class, DefaultMerchantOrderShippingAddress::class);
        (new TestContainer)->add(MerchantOrderBillingAddress::class, DefaultMerchantOrderBillingAddress::class);
        (new TestContainer)->add(MerchantOrderShipping::class, DefaultMerchantOrderShipping::class);
        (new TestContainer)->add(MerchantOrderPayment::class, DefaultMerchantOrderPayment::class);
        (new TestContainer)->add(MerchantOrderShopper::class, DefaultMerchantOrderShopper::class);
        (new TestContainer)->add(MerchantOrderDiscount::class, DefaultMerchantOrderDiscount::class);
        (new TestContainer)->add(MerchantOrderEvent::class, DefaultMerchantOrderEvent::class);

        // Customer
        (new TestContainer)->add(CustomerRead::class, DefaultCustomerRead::class);
        (new TestContainer)->add(CustomerShippingAddress::class, DefaultCustomerShippingAddress::class);
        (new TestContainer)->add(CustomerBillingAddress::class, DefaultCustomerBillingAddress::class);

        // Repositories
        (new TestContainer)->add(MysqlOrderRepository::class, new MysqlOrderRepository(new TestContainer, new TestTraderConfig));
    }

    public static function tearDown(): void
    {
        InMemoryCatalogRepositories::clear();
    }

    public static function drivers(): array
    {
        return [
            self::inMemory(),
            self::mysql(),
        ];
    }

    public static function inMemory(): self
    {
        return new self(
            new InMemoryOrderRepositories(new TestTraderConfig, new TestContainer)
        );
    }

    public static function mysql(): self
    {
        return new self(new MysqlOrderRepositories(new TestTraderConfig, new TestContainer));
    }

    public function createDefaultOrder(string $orderId = 'order-aaa'): DomainOrder
    {
        $order = $this->createOrder($orderId);

        // Lines
        $this->addLineToOrder($order, $this->createLine($orderId, 'line-aaa'));
        $this->addLineToOrder($order, $this->createLine($orderId, 'line-bbb'));

        // Shipping, payment
        $this->addShippingToOrder($order, $this->createShipping($orderId, 'shipping-aaa'));
        $this->addPaymentToOrder($order, $this->createPayment($orderId, 'payment-aaa'));

        // Addresses
        $this->addShippingAddressToOrder($order, $this->createShippingAddress($orderId));
        $this->addBillingAddressToOrder($order, $this->createBillingAddress($orderId));

        // Shopper
        $this->addShopperToOrder($order, $this->createShopper($orderId, 'shopper-aaa'));

        // Discount
        $this->addDiscountToOrder($order, $this->createDiscount($orderId, 'discount-aaa'));

        return $order;
    }

    public function createOrder(string $orderId = 'order-aaa', string $state = DefaultOrderState::cart_pending->value): DomainOrder
    {
        $order = DomainOrder::fromMappedData([
            'order_id' => $orderId,
            'order_ref' => $orderId.'-ref',
            'invoice_ref' => $orderId.'-invoice-ref',
            'order_state' => $state,
            'data' => json_encode([]),
        ], []);

        if ($this->persist) {
            $this->saveOrder($order);
        }

        return $order;
    }

    public function createLine(string $orderId = 'order-aaa', string $lineId = 'line-aaa', array $values = []): Line
    {
        return Line::fromMappedData(array_merge([
            'line_id' => $lineId,
            'variant_id' => 'variant-aaa',
            'line_price' => 100,
            'tax_rate' => '21',
            'includes_vat' => true,
            'quantity' => 1,
            'data' => json_encode(['title' => ['nl' => $lineId.' title nl', 'fr' => $lineId.' title fr']]),
        ], $values), [
            'order_id' => $orderId,
        ]);
    }

    public function addLineToOrder(DomainOrder $order, Line $line): DomainOrder
    {
        $order->addOrUpdateLine(
            $line->lineId,
            $line->getVariantId(),
            $line->getLinePrice(),
            $line->getQuantity(),
            $line->getData()
        );

        if ($this->persist) {
            $this->saveOrder($order);
        }

        return $order;
    }

    public function createShipping(string $orderId = 'order-aaa', string $shippingId = 'shipping-aaa', array $values = []): Shipping
    {
        return Shipping::fromMappedData(array_merge([
            'order_id' => $orderId,
            'shipping_id' => $shippingId,
            'shipping_profile_id' => 'shipping-profile-aaa',
            'shipping_state' => DefaultShippingState::none,
            'cost' => 50,
            'tax_rate' => '21',
            'includes_vat' => true,
            'data' => json_encode(['title' => ['nl' => $shippingId.' title nl', 'fr' => $shippingId.' title fr']]),
        ], $values), [
            'order_id' => $orderId,
        ]);
    }

    public function addShippingToOrder(DomainOrder $order, Shipping $shipping): DomainOrder
    {
        $order->addShipping($shipping);

        if ($this->persist) {
            $this->saveOrder($order);
        }

        return $order;
    }

    public function createPayment(string $orderId = 'order-aaa', string $paymentId = 'payment-aaa', array $values = []): Payment
    {
        return Payment::fromMappedData(array_merge([
            'payment_id' => $paymentId,
            'payment_method_id' => 'payment-method-aaa',
            'payment_state' => DefaultPaymentState::initialized,
            'cost' => 20,
            'tax_rate' => '21',
            'includes_vat' => true,
            'data' => json_encode(['title' => ['nl' => $paymentId.' title nl', 'fr' => $paymentId.' title fr']]),
        ], $values), [
            'order_id' => $orderId,
        ]);
    }

    public function addPaymentToOrder(DomainOrder $order, Payment $payment): DomainOrder
    {
        $order->addPayment($payment);

        if ($this->persist) {
            $this->saveOrder($order);
        }

        return $order;
    }

    public function createShippingAddress(string $orderId = 'order-aaa', array $values = []): ShippingAddress
    {
        return ShippingAddress::fromMappedData(array_merge([
            'country_id' => 'BE',
            'line_1' => 'Lierseweg 81',
            'postal_code' => '2200',
            'city' => 'Herentals',
            'data' => '[]',
        ], $values), ['order_id' => $orderId]);
    }

    public function addShippingAddressToOrder(DomainOrder $order, ShippingAddress $shippingAddress): DomainOrder
    {
        $order->updateShippingAddress($shippingAddress);

        if ($this->persist) {
            $this->saveOrder($order);
        }

        return $order;
    }

    public function createBillingAddress(string $orderId = 'order-aaa', array $values = []): BillingAddress
    {
        return BillingAddress::fromMappedData(array_merge([
            'country_id' => 'NL',
            'line_1' => 'Example 12',
            'postal_code' => '1000',
            'city' => 'Amsterdam',
            'data' => '[]',
        ], $values), ['order_id' => $orderId]);
    }

    public function addBillingAddressToOrder(DomainOrder $order, BillingAddress $billingAddress): DomainOrder
    {
        $order->updateBillingAddress($billingAddress);

        if ($this->persist) {
            $this->saveOrder($order);
        }

        return $order;
    }

    public function createShopper(string $orderId = 'order-aaa', string $shopperId = 'shopper-aaa', array $values = []): Shopper
    {
        return Shopper::fromMappedData(array_merge([
            'shopper_id' => $shopperId,
            'email' => 'ben@thinktomorrow.be',
            'is_business' => false,
            'locale' => 'nl_BE',
            'data' => json_encode([]),
        ], $values), ['order_id' => $orderId]);
    }

    public function addShopperToOrder(DomainOrder $order, Shopper $shopper): DomainOrder
    {
        $order->updateShopper($shopper);

        if ($this->persist) {
            $this->saveOrder($order);
        }

        return $order;
    }

    public function createDiscount(string $orderId = 'order-aaa', string $discountId = 'discount-aaa', array $values = []): Discount
    {
        return Discount::fromMappedData(array_merge([
            'discount_id' => $discountId,
            'discountable_type' => DiscountableType::order->value,
            'discountable_id' => $orderId,
            'promo_id' => 'promo-aaa',
            'promo_discount_id' => 'promo-disc-aaa',
            'total' => '15',
            'tax_rate' => '21',
            'includes_vat' => true,
            'data' => json_encode([]),
        ], $values), ['order_id' => $orderId]);
    }

    public function addDiscountToOrder(DomainOrder $order, Discount $discount): DomainOrder
    {
        $order->addDiscount($discount);

        if ($this->persist) {
            $this->saveOrder($order);
        }

        return $order;
    }

    public function createPaymentMethod(string $paymentMethodId = 'payment-method-aaa', array $values = []): PaymentMethod
    {
        $model = PaymentMethod::fromMappedData(array_merge([
            'payment_method_id' => $paymentMethodId,
            'provider_id' => PaymentMethodProviderId::fromString('mollie')->get(),
            'state' => PaymentMethodState::online->value,
            'rate' => '123',
            'data' => json_encode([]),
        ], $values), [CountryId::class => []]);

        if ($this->persist) {
            $this->orderRepos->paymentMethodRepository()->save($model);
        }

        return $model;
    }

    public function createShippingProfile(string $shippingProfileId = 'shipping-profile-aaa', array $values = []): ShippingProfile
    {
        $model = ShippingProfile::fromMappedData(array_merge([
            'shipping_profile_id' => $shippingProfileId,
            'provider_id' => 'postnl',
            'state' => ShippingProfileState::online->value,
            'requires_address' => true,
            'data' => json_encode([]),
        ], $values), [Tariff::class => [], CountryId::class => []]);

        if ($this->persist) {
            $this->orderRepos->shippingProfileRepository()->save($model);
        }

        return $model;
    }

    public function createVatRate(string $vatRateId = 'vatrate-aaa', array $values = [], array $baseRateValues = []): VatRate
    {
        $model = VatRate::fromMappedData(array_merge([
            'vat_rate_id' => $vatRateId,
            'country_id' => 'BE',
            'rate' => '21',
            'is_standard' => true,
            'state' => VatRateState::online->value,
            'data' => json_encode([]),
        ], $values), [
            BaseRate::class => [
                array_merge([
                    'base_rate_id' => 'baserate-aaa',
                    'origin_vat_rate_id' => 'origin-aaa',
                    'target_vat_rate_id' => 'target-aaa',
                    'rate' => '21',
                ], $baseRateValues),
            ],
        ]);

        if ($this->persist) {
            $this->orderRepos->vatRateRepository()->save($model);
        }

        return $model;
    }

    public function createCountry(string $countryId = 'BE', array $values = []): Country
    {
        $model = Country::fromMappedData(array_merge([
            'country_id' => $countryId,
            'data' => json_encode([]),
        ], $values));

        if ($this->persist) {
            $this->orderRepos->countryRepository()->save($model);
        }

        return $model;
    }

    public function saveOrder(DomainOrder $order): void
    {
        $this->orderRepos->orderRepository()->save($order);
    }

    public function createCustomer(string $customerId = 'customer-aaa', array $values = []): Customer
    {
        $model = Customer::fromMappedData(array_merge([
            'customer_id' => $customerId,
            'email' => 'ben@thinktomorrow.be',
            'is_business' => false,
            'locale' => 'nl_BE',
            'data' => json_encode([]),
        ], $values), []);

        if ($this->persist) {
            $this->orderRepos->customerRepository()->save($model);
        }

        return $model;
    }

    public function createCustomerLogin(Customer $customer, string $password = '123456'): CustomerLogin
    {
        $model = CustomerLogin::create(
            $customer->customerId,
            Email::fromString($customer->getEmail()->get()),
            bcrypt($password)
        );

        if ($this->persist) {
            $this->orderRepos->customerLoginRepository()->save($model);
        }

        return $model;
    }

    // --------------------------------------------------------------------------
    // Data provider for tests
    // --------------------------------------------------------------------------

    public function orders(): array
    {
        $orderWithLine = $this->createOrder('order-bbb');
        $line = $this->createLine('order-bbb', 'line-bbb');
        $this->addLineToOrder($orderWithLine, $line);

        return [
            $this->createOrder(),
            $orderWithLine,
        ];
    }
}

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
use Thinktomorrow\Trader\Domain\Model\Order\Line\Personalisations\LinePersonalisation;
use Thinktomorrow\Trader\Domain\Model\Order\Order;
use Thinktomorrow\Trader\Domain\Model\Order\Order as DomainOrder;
use Thinktomorrow\Trader\Domain\Model\Order\OrderEvent\OrderEvent;
use Thinktomorrow\Trader\Domain\Model\Order\OrderId;
use Thinktomorrow\Trader\Domain\Model\Order\OrderReference;
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
use Thinktomorrow\Trader\Domain\Model\Promo\Condition;
use Thinktomorrow\Trader\Domain\Model\Promo\Discounts\PercentageOffDiscount;
use Thinktomorrow\Trader\Domain\Model\Promo\Promo;
use Thinktomorrow\Trader\Domain\Model\Promo\PromoState;
use Thinktomorrow\Trader\Domain\Model\ShippingProfile\ShippingProfile;
use Thinktomorrow\Trader\Domain\Model\ShippingProfile\ShippingProfileState;
use Thinktomorrow\Trader\Domain\Model\ShippingProfile\Tariff;
use Thinktomorrow\Trader\Domain\Model\VatRate\BaseRate;
use Thinktomorrow\Trader\Domain\Model\VatRate\VatRate;
use Thinktomorrow\Trader\Domain\Model\VatRate\VatRateState;
use Thinktomorrow\Trader\Infrastructure\Laravel\config\TraderConfig;
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
use Thinktomorrow\Trader\Testing\TraderContext;

class OrderContext extends TraderContext
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
            self::laravel(),
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

    public static function laravel(): self
    {
        $config = app(TraderConfig::class);
        $container = app();

        $context = new self(new MysqlOrderRepositories($config, $container));

        $context->setConfig($config);
        $context->setContainer($container);

        return $context;
    }

    public function createDefaultOrder(string $orderId = 'order-aaa'): DomainOrder
    {
        $this->createShippingProfile();
        $this->createPaymentMethod();
        $this->createCountry();
        $this->createPromo();

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

        // Clear all these default events
        $order->releaseEvents();

        return $order;
    }

    public function createOrder(string $orderId = 'order-aaa', string $state = DefaultOrderState::cart_pending->value): DomainOrder
    {
        $order = DomainOrder::fromMappedData([
            'order_id' => $orderId,
            'order_ref' => $orderId.'-ref',
            'invoice_ref' => $orderId.'-invoice-ref',
            'order_state' => $this->container->get(OrderState::class)::fromString($state),
            'total_excl' => 82500,
            'total_incl' => 100000,
            'total_vat' => 17500,
            'vat_lines' => json_encode([]),
            'subtotal_excl' => 82500,
            'subtotal_incl' => 100000,
            'discount_total_excl' => 5785,
            'discount_total_incl' => 7000,
            'shipping_cost_excl' => 4132,
            'shipping_cost_incl' => 5000,
            'payment_cost_excl' => 1653,
            'payment_cost_incl' => 2000,
            'data' => json_encode([]),
        ], [
            Discount::class => [],
            Line::class => [],
            Shipping::class => [],
            Payment::class => [],
            ShippingAddress::class => null,
            BillingAddress::class => null,
            Shopper::class => null,
            OrderEvent::class => [],
        ]);

        if ($this->persist) {
            $this->saveOrder($order);
        }

        return $order;
    }

    public function createEmptyOrder(string $orderId = 'order-aaa'): Order
    {
        return Order::create(
            OrderId::fromString($orderId),
            OrderReference::fromString($orderId.'-ref'),
            DefaultOrderState::getDefaultState()
        );
    }

    public function createLine(string $orderId = 'order-aaa', string $lineId = 'line-aaa', array $values = []): Line
    {
        return Line::fromMappedData(array_merge([
            'line_id' => $orderId.':'.$lineId,
            'variant_id' => 'variant-aaa',
            'unit_price_incl' => '83',
            'unit_price_excl' => '100',
            'total_excl' => '83',
            'total_incl' => '100',
            'total_vat' => '17',
            'discount_excl' => '0',
            'tax_rate' => '21',
            'includes_vat' => false,
            'quantity' => 1,
            'reduced_from_stock' => false,
            'data' => json_encode([
                'product_id' => 'product-aaa',
                'unit_price_excluding_vat' => '83',
                'unit_price_including_vat' => '100',
                'title' => ['nl' => $lineId.' title nl', 'fr' => $lineId.' title fr'],
            ]),
        ], $values), [
            'order_id' => $orderId,
        ], [
            Discount::class => [],
            LinePersonalisation::class => [],
        ]);
    }

    public function addLineToOrder(DomainOrder $order, Line $line): DomainOrder
    {
        $order->addOrUpdateLine($line);

        if ($this->persist) {
            $this->saveOrder($order);
        }

        return $order;
    }

    public function createShipping(string $orderId = 'order-aaa', string $shippingId = 'shipping-aaa', array $values = []): Shipping
    {
        return Shipping::fromMappedData(array_merge([
            'order_id' => $orderId,
            'shipping_id' => $orderId.':'.$shippingId,
            'shipping_profile_id' => 'shipping-profile-aaa',
            'shipping_state' => DefaultShippingState::none,
            'cost' => 50,
            'data' => json_encode(['title' => ['nl' => $shippingId.' title nl', 'fr' => $shippingId.' title fr']]),
        ], $values), [
            'order_id' => $orderId,
        ], [
            Discount::class => [],
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
            'payment_id' => $orderId.':'.$paymentId,
            'payment_method_id' => 'payment-method-aaa',
            'payment_state' => DefaultPaymentState::initialized,
            'cost' => 20,
            'data' => json_encode(['title' => ['nl' => $paymentId.' title nl', 'fr' => $paymentId.' title fr']]),
        ], $values), [
            'order_id' => $orderId,
        ], [
            Discount::class => [],
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
            'line_2' => '',
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
            'line_2' => '',
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
            'shopper_id' => $orderId.':'.$shopperId,
            'customer_id' => null,
            'email' => 'ben@thinktomorrow.be',
            'is_business' => false,
            'locale' => 'nl_BE',
            'register_after_checkout' => false,
            'data' => json_encode([
                'firstname' => 'Ben',
                'lastname' => 'Cavens',
                'company' => 'Think Tomorrow',
                'phone' => '',
            ]),
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
            'discount_id' => $orderId.':'.$discountId,
            'discountable_type' => DiscountableType::order->value,
            'discountable_id' => $orderId,
            'promo_id' => 'promo-aaa',
            'promo_discount_id' => 'promo-discount-aaa',
            'total' => '15',
            'data' => json_encode([]),
        ], $values), ['order_id' => $orderId]);
    }

    public function createLineDiscount(string $orderId = 'order-aaa', string $lineId = 'order-aaa:line-aaa', string $discountId = 'discount-aaa', array $values = []): Discount
    {
        return $this->createDiscount(
            $orderId,
            $discountId,
            array_merge([
                'discountable_type' => DiscountableType::line->value,
                'discountable_id' => $lineId,
            ], $values)
        );
    }

    public function createShippingDiscount(string $orderId = 'order-aaa', string $shippingId = 'order-aaa:shipping-aaa', string $discountId = 'discount-aaa', array $values = []): Discount
    {
        return $this->createDiscount(
            $orderId,
            $discountId,
            array_merge([
                'discountable_type' => DiscountableType::shipping->value,
                'discountable_id' => $shippingId,
            ], $values)
        );
    }

    public function createPaymentDiscount(string $orderId = 'order-aaa', string $paymentId = 'order-aaa:payment-aaa', string $discountId = 'discount-aaa', array $values = []): Discount
    {
        return $this->createDiscount(
            $orderId,
            $discountId,
            array_merge([
                'discountable_type' => DiscountableType::payment->value,
                'discountable_id' => $paymentId,
            ], $values)
        );
    }

    public function createPromo(string $promoId = 'promo-aaa', array $values = [], array $discountValues = []): Promo
    {
        $model = Promo::fromMappedData(array_merge([
            'promo_id' => $promoId,
            'coupon_code' => 'PROMO123',
            'state' => PromoState::online->value,
            'is_combinable' => false,
            'start_at' => null,
            'end_at' => null,
            'data' => json_encode([]),
        ], $values), [
            \Thinktomorrow\Trader\Domain\Model\Promo\Discount::class => [
                PercentageOffDiscount::fromMappedData(array_merge([
                    'discount_id' => 'promo-discount-aaa',
                    'description' => '15% off',
                    'discount_type' => 'percentage',
                    'data' => json_encode([
                        'percentage' => '15',
                    ]),
                ], $discountValues), [
                    'promo_id' => $promoId,
                ], [
                    Condition::class => [],
                ]),
            ],
        ]);

        if ($this->persist) {
            $this->orderRepos->promoRepository()->save($model);
        }

        return $model;
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
            'data' => json_encode([
                'firstname' => 'Ben',
                'lastname' => 'Cavens',
                'company' => 'Think Tomorrow',
                'phone' => '01293494',
                'vat' => 'BE0123456789',
            ]),
        ], $values), [
            \Thinktomorrow\Trader\Domain\Model\Customer\Address\BillingAddress::class => null,
            \Thinktomorrow\Trader\Domain\Model\Customer\Address\ShippingAddress::class => null,
        ]);

        if ($this->persist) {
            $this->orderRepos->customerRepository()->save($model);
        }

        return $model;
    }

    public function createCustomerBillingAddress(string $customerId = 'customer-aaa', array $values = []): \Thinktomorrow\Trader\Domain\Model\Customer\Address\BillingAddress
    {
        return \Thinktomorrow\Trader\Domain\Model\Customer\Address\BillingAddress::fromMappedData(array_merge([
            'country_id' => 'NL',
            'line_1' => 'Example 12',
            'line_2' => '',
            'postal_code' => '1000',
            'city' => 'Amsterdam',
            'data' => '[]',
        ], $values), ['customer_id' => $customerId]);
    }

    public function createCustomerShippingAddress(string $customerId = 'customer-aaa', array $values = []): \Thinktomorrow\Trader\Domain\Model\Customer\Address\ShippingAddress
    {
        return \Thinktomorrow\Trader\Domain\Model\Customer\Address\ShippingAddress::fromMappedData(array_merge([
            'country_id' => 'BE',
            'line_1' => 'Lierseweg 81',
            'line_2' => '',
            'postal_code' => '2200',
            'city' => 'Herentals',
            'data' => '[]',
        ], $values), ['customer_id' => $customerId]);
    }

    public function addBillingAddressToCustomer(Customer $customer, \Thinktomorrow\Trader\Domain\Model\Customer\Address\BillingAddress $billingAddress): Customer
    {
        $customer->updateBillingAddress($billingAddress);

        if ($this->persist) {
            $this->orderRepos->customerRepository()->save($customer);
        }

        return $customer;
    }

    public function addShippingAddressToCustomer(Customer $customer, \Thinktomorrow\Trader\Domain\Model\Customer\Address\ShippingAddress $shippingAddress): Customer
    {
        $customer->updateShippingAddress($shippingAddress);

        if ($this->persist) {
            $this->orderRepos->customerRepository()->save($customer);
        }

        return $customer;
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

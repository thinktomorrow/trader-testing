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
use Thinktomorrow\Trader\Domain\Common\Locale;
use Thinktomorrow\Trader\Domain\Model\Order\Payment\DefaultPaymentState;
use Thinktomorrow\Trader\Domain\Model\Order\Payment\PaymentState;
use Thinktomorrow\Trader\Domain\Model\Order\Shipping\DefaultShippingState;
use Thinktomorrow\Trader\Domain\Model\Order\Shipping\ShippingState;
use Thinktomorrow\Trader\Domain\Model\Order\State\DefaultOrderState;
use Thinktomorrow\Trader\Domain\Model\Order\State\OrderState;
use Thinktomorrow\Trader\Domain\Model\Product\Personalisation\Personalisation;
use Thinktomorrow\Trader\Domain\Model\Product\Personalisation\PersonalisationId;
use Thinktomorrow\Trader\Domain\Model\Product\Personalisation\PersonalisationType;
use Thinktomorrow\Trader\Domain\Model\Product\Product;
use Thinktomorrow\Trader\Domain\Model\Product\ProductId;
use Thinktomorrow\Trader\Domain\Model\Product\ProductState;
use Thinktomorrow\Trader\Domain\Model\Product\ProductTaxa\ProductTaxon;
use Thinktomorrow\Trader\Domain\Model\Product\Variant\Variant;
use Thinktomorrow\Trader\Domain\Model\Product\Variant\VariantId;
use Thinktomorrow\Trader\Domain\Model\Product\Variant\VariantSalePrice;
use Thinktomorrow\Trader\Domain\Model\Product\Variant\VariantUnitPrice;
use Thinktomorrow\Trader\Domain\Model\Product\VariantTaxa\VariantTaxon;
use Thinktomorrow\Trader\Domain\Model\Taxon\Taxon;
use Thinktomorrow\Trader\Domain\Model\Taxon\TaxonId;
use Thinktomorrow\Trader\Domain\Model\Taxon\TaxonKey;
use Thinktomorrow\Trader\Domain\Model\Taxon\TaxonKeyId;
use Thinktomorrow\Trader\Domain\Model\Taxonomy\Taxonomy;
use Thinktomorrow\Trader\Domain\Model\Taxonomy\TaxonomyId;
use Thinktomorrow\Trader\Domain\Model\Taxonomy\TaxonomyType;
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
            new InMemoryOrderRepositories,
        );
    }

    public static function mysql(): self
    {
        return new self(new MysqlOrderRepositories(new TestTraderConfig, new TestContainer));
    }

    public function createTaxonomy(string $taxonomyId = 'taxonomy-aaa', string $type = TaxonomyType::category->value): Taxonomy
    {
        $taxonomy = Taxonomy::create(TaxonomyId::fromString($taxonomyId), TaxonomyType::from($type));
        $taxonomy->showAsGridFilter();
        $taxonomy->addData(['title' => ['nl' => $taxonomyId.' title nl', 'fr' => $taxonomyId.' title fr']]);

        $this->saveTaxonomy($taxonomy);

        return $taxonomy;
    }

    public function createTaxon(string $taxonId = 'taxon-aaa', string $taxonomyId = 'taxonomy-aaa', ?string $parentId = null): Taxon
    {
        $taxon = Taxon::create(TaxonId::fromString($taxonId), TaxonomyId::fromString($taxonomyId), $parentId ? TaxonId::fromString($parentId) : null);
        $taxon->addData(['title' => ['nl' => $taxonId.' title nl', 'fr' => $taxonId.' title fr']]);

        $taxon->updateTaxonKeys([
            TaxonKey::create($taxon->taxonId, TaxonKeyId::fromString($taxonId.'-key-nl'), Locale::fromString('nl')),
            TaxonKey::create($taxon->taxonId, TaxonKeyId::fromString($taxonId.'-key-fr'), Locale::fromString('fr')),
        ]);

        $this->saveTaxon($taxon);

        return $taxon;
    }

    public function createProduct(string $productId = 'product-aaa', string $variantId = 'variant-aaa'): Product
    {
        $product = Product::create(ProductId::fromString($productId));
        $product->updateState(ProductState::online);

        if ($this->persist) {
            $this->saveProduct($product);
        }

        $this->createVariant($product, $variantId);

        return $product;
    }

    public function createVariant(string|Product $productId = 'product-aaa', string $variantId = 'variant-aaa'): Variant
    {
        $product = $productId instanceof Product ? $productId : $this->repos->productRepository()->find(ProductId::fromString($productId));

        $variant = Variant::create(
            $product->productId,
            VariantId::fromString($variantId),
            VariantUnitPrice::fromScalars(100, '20', false),
            VariantSalePrice::fromScalars(80, '20', false),
            'sku-'.$variantId
        );

        $variant->showInGrid();

        $product->createVariant($variant);

        if ($this->persist) {
            $this->saveProduct($product);
        }

        return $variant;
    }

    public function saveTaxonomy(Taxonomy $taxonomy): void
    {
        $this->repos->taxonomyRepository()->save($taxonomy);
    }

    public function saveTaxon(Taxon $taxon): void
    {
        $this->repos->taxonRepository()->save($taxon);
    }

    public function saveProduct(Product $product): void
    {
        $this->repos->productRepository()->save($product);
    }

    public function linkProductToTaxon(string|Product $productId, string|Taxon $taxonId): Product
    {
        $product = $productId instanceof Product ? $productId : $this->repos->productRepository()->find(ProductId::fromString($productId));
        $taxon = $taxonId instanceof Taxon ? $taxonId : $this->repos->taxonRepository()->find(TaxonId::fromString($taxonId));

        $productTaxon = ProductTaxon::create($product->productId, $taxon->taxonId);

        // If taxonomy is of type variant_property, we need to set the relation as a VariantTaxon instead of default ProductTaxon
        $taxonomy = $this->repos->taxonomyRepository()->find($taxon->taxonomyId);
        if ($taxonomy->getType() == TaxonomyType::variant_property) {
            $productTaxon = $productTaxon->toVariantProperty();
        }

        $product->updateProductTaxa([
            ...$product->getProductTaxa(),
            $productTaxon,
        ]);

        if ($this->persist) {
            $this->repos->productRepository()->save($product);
        }

        return $product;
    }

    public function linkVariantToTaxon(string $productId, string $variantId, string $taxonId): Product
    {
        $product = $this->repos->productRepository()->find(ProductId::fromString($productId));
        $variant = $product->findVariant(VariantId::fromString($variantId));

        $variant->updateVariantTaxa([
            ...$variant->getVariantTaxa(),
            VariantTaxon::create(VariantId::fromString($variantId), TaxonId::fromString($taxonId)),
        ]);

        if ($this->persist) {
            $this->repos->productRepository()->save($product);
        }

        return $product;
    }

    public function makePersonalisation(string $productId = 'product-aaa', string $personalisationId = 'personalisation-aaa'): Personalisation
    {
        return Personalisation::create(
            ProductId::fromString($productId),
            PersonalisationId::fromString($personalisationId),
            PersonalisationType::fromString(PersonalisationType::TEXT),
            ['foo' => 'bar']
        );
    }

    public function addPersonalisationToProduct(Product $product, Personalisation $personalisation): Product
    {
        $product->updatePersonalisations([
            $personalisation,
        ]);

        return $product;
    }

    /**
     * Data provider for tests, providing different product setups.
     *
     * 1. Product with variant
     * 2. Product without variant
     * 3. Product with personalisation
     * 4. Product linked to taxon
     * 5. Product linked to multiple taxons (from different taxonomies)
     */
    public function products(): array
    {
        // For product with taxon
        $this->createTaxonomy();
        $taxon = $this->createTaxon();

        $taxonomy2 = $this->createTaxonomy('taxonomy-bbb', TaxonomyType::variant_property->value);
        $taxon2 = $this->createTaxon('taxon-bbb', $taxonomy2->taxonomyId->get());

        return [
            $this->createProduct(),
            Product::create(ProductId::fromString('product-aaa')), // Without variant
            $this->addPersonalisationToProduct($this->createProduct(), $this->makePersonalisation()),
            $this->linkProductToTaxon($this->createProduct(), $taxon),
            $this->linkProductToTaxon($this->linkProductToTaxon($this->createProduct(), $taxon2), $taxon),
        ];
    }
}

<?php

namespace Thinktomorrow\Trader\Testing\Catalog;

use Thinktomorrow\Trader\Application\Product\ProductDetail\ProductDetail;
use Thinktomorrow\Trader\Application\Product\Taxa\ProductTaxonItem;
use Thinktomorrow\Trader\Application\Product\Taxa\VariantTaxonItem;
use Thinktomorrow\Trader\Application\Product\VariantLinks\VariantLink;
use Thinktomorrow\Trader\Application\Taxon\Tree\TaxonNode;
use Thinktomorrow\Trader\Application\Taxonomy\TaxonomyItem;
use Thinktomorrow\Trader\Domain\Common\Locale;
use Thinktomorrow\Trader\Domain\Model\Product\Personalisation\Personalisation;
use Thinktomorrow\Trader\Domain\Model\Product\Personalisation\PersonalisationId;
use Thinktomorrow\Trader\Domain\Model\Product\Personalisation\PersonalisationType;
use Thinktomorrow\Trader\Domain\Model\Product\Product;
use Thinktomorrow\Trader\Domain\Model\Product\ProductId;
use Thinktomorrow\Trader\Domain\Model\Product\ProductState;
use Thinktomorrow\Trader\Domain\Model\Product\ProductTaxa\ProductTaxon;
use Thinktomorrow\Trader\Domain\Model\Product\Variant\Variant;
use Thinktomorrow\Trader\Domain\Model\Product\Variant\VariantId;
use Thinktomorrow\Trader\Domain\Model\Product\Variant\VariantState;
use Thinktomorrow\Trader\Domain\Model\Product\VariantTaxa\VariantTaxon;
use Thinktomorrow\Trader\Domain\Model\Taxon\Taxon;
use Thinktomorrow\Trader\Domain\Model\Taxon\TaxonId;
use Thinktomorrow\Trader\Domain\Model\Taxon\TaxonKey;
use Thinktomorrow\Trader\Domain\Model\Taxon\TaxonKeyId;
use Thinktomorrow\Trader\Domain\Model\Taxon\TaxonState;
use Thinktomorrow\Trader\Domain\Model\Taxonomy\Taxonomy;
use Thinktomorrow\Trader\Domain\Model\Taxonomy\TaxonomyState;
use Thinktomorrow\Trader\Domain\Model\Taxonomy\TaxonomyType;
use Thinktomorrow\Trader\Infrastructure\Laravel\config\TraderConfig;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\DefaultProductDetail;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\DefaultProductTaxonItem;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\DefaultTaxonNode;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\DefaultTaxonomyItem;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\DefaultVariantLink;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\DefaultVariantTaxonItem;
use Thinktomorrow\Trader\Infrastructure\Test\TestContainer;
use Thinktomorrow\Trader\Infrastructure\Test\TestTraderConfig;
use Thinktomorrow\Trader\Testing\Catalog\Repositories\CatalogRepositories;
use Thinktomorrow\Trader\Testing\Catalog\Repositories\InMemoryCatalogRepositories;
use Thinktomorrow\Trader\Testing\Catalog\Repositories\MysqlCatalogRepositories;
use Thinktomorrow\Trader\Testing\TraderContext;

class CatalogContext extends TraderContext
{
    private CatalogRepositories $catalogRepos;

    public function __construct(CatalogRepositories $catalogRepos)
    {
        parent::__construct();

        $this->catalogRepos = $catalogRepos;
    }

    public function repos(): CatalogRepositories
    {
        return $this->catalogRepos;
    }

    public function apps(): CatalogApplications
    {
        return new CatalogApplications($this->catalogRepos, $this->config, $this->eventDispatcher);
    }

    public static function setUp(): void
    {
        $container = new TestContainer;

        $container->add(ProductDetail::class, DefaultProductDetail::class);
        $container->add(ProductTaxonItem::class, DefaultProductTaxonItem::class);
        $container->add(VariantTaxonItem::class, DefaultVariantTaxonItem::class);
        $container->add(TaxonomyItem::class, DefaultTaxonomyItem::class);
        $container->add(TaxonNode::class, DefaultTaxonNode::class);
        $container->add(VariantLink::class, DefaultVariantLink::class);
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
        return new self(new InMemoryCatalogRepositories(new TestTraderConfig, new TestContainer));
    }

    public static function mysql(): self
    {
        return new self(new MysqlCatalogRepositories(new TestTraderConfig, new TestContainer));
    }

    public static function laravel(): self
    {
        $config = app(TraderConfig::class);
        $container = app();

        $context = new self(new MysqlCatalogRepositories($config, $container));

        $context->setConfig($config);
        $context->setContainer($container);

        return $context;
    }

    public function createTaxonomy(string $taxonomyId = 'taxonomy-aaa', string $type = TaxonomyType::category->value, array $values = []): Taxonomy
    {
        $taxonomy = Taxonomy::fromMappedData(array_merge([
            'taxonomy_id' => $taxonomyId,
            'type' => $type,
            'state' => TaxonomyState::online->value,
            'shows_as_grid_filter' => true,
            'shows_in_grid' => true,
            'allows_multiple_values' => false,
            'allows_nestable_values' => false,
            'order' => 0,
            'data' => json_encode(['title' => ['nl' => $taxonomyId.' title nl', 'fr' => $taxonomyId.' title fr']]),
        ], $values));

        $this->saveTaxonomy($taxonomy);

        // Clear all these default events
        if (! $this->keepDomainEvents) {
            $taxonomy->releaseEvents();
        }

        return $taxonomy;
    }

    public function createTaxon(string $taxonId = 'taxon-aaa', string $taxonomyId = 'taxonomy-aaa', ?string $parentId = null, array $values = []): Taxon
    {
        $taxon = Taxon::fromMappedData(array_merge([
            'taxon_id' => $taxonId,
            'taxonomy_id' => $taxonomyId,
            'parent_id' => $parentId,
            'state' => TaxonState::online->value,
            'data' => json_encode([]),
        ], $values), [
            TaxonKey::class => [],
        ]);

        $taxon->addData(['title' => ['nl' => $taxonId.' title nl', 'fr' => $taxonId.' title fr']]);

        $taxon->updateTaxonKeys([
            TaxonKey::create($taxon->taxonId, TaxonKeyId::fromString($taxonId.'-key-nl'), Locale::fromString('nl')),
            TaxonKey::create($taxon->taxonId, TaxonKeyId::fromString($taxonId.'-key-fr'), Locale::fromString('fr')),
        ]);

        $this->saveTaxon($taxon);

        // Clear all these default events
        if (! $this->keepDomainEvents) {
            $taxon->releaseEvents();
        }

        return $taxon;
    }

    public function createProduct(string $productId = 'product-aaa', ?string $variantId = 'variant-aaa', array $values = [], array $data = []): Product
    {
        $product = Product::fromMappedData(array_merge([
            'product_id' => $productId,
            'state' => ProductState::online->value,
            'data' => json_encode(array_merge([
                'title' => ['nl' => $productId.' title nl', 'fr' => $productId.' title fr'],
            ], $data)),
        ], $values), [
            ProductTaxon::class => [],
            Personalisation::class => [],
        ]);

        if ($this->persist) {
            $this->saveProduct($product);
        }

        if ($variantId) {
            $this->createVariant($product, $variantId);
        }

        // Clear all these default events
        if (! $this->keepDomainEvents) {
            $product->releaseEvents();
        }

        return $product;
    }

    public function createVariant(string|Product $productId = 'product-aaa', string $variantId = 'variant-aaa', array $values = [], array $data = []): Variant
    {
        $product = $productId instanceof Product ? $productId : $this->catalogRepos->productRepository()->find(ProductId::fromString($productId));

        $variant = Variant::fromMappedData(array_merge([
            'product_id' => $product->productId->get(),
            'variant_id' => $variantId,
            'unit_price' => 100,
            'sale_price' => 80,
            'tax_rate' => '20',
            'includes_vat' => false,
            'sku' => 'sku-'.$variantId,
            'state' => VariantState::available->value,
            'show_in_grid' => true,
            'data' => json_encode(array_merge([
                'title' => ['nl' => $variantId.' title nl', 'fr' => $variantId.' title fr'],
                'option_title' => ['nl' => $variantId.' option title nl', 'fr' => $variantId.' option title fr'],
            ], $data)),
        ], $values), [
            'product_id' => $product->productId->get(),
        ], [
            VariantTaxon::class => [],
        ]);

        $product->createVariant($variant);

        if ($this->persist) {
            $this->saveProduct($product);
        }

        // Clear all these default events
        if (! $this->keepDomainEvents) {
            $product->releaseEvents();
        }

        return $variant;
    }

    public function saveTaxonomy(Taxonomy $taxonomy): void
    {
        $this->catalogRepos->taxonomyRepository()->save($taxonomy);
    }

    public function saveTaxon(Taxon $taxon): void
    {
        $this->catalogRepos->taxonRepository()->save($taxon);
    }

    public function saveProduct(Product $product): void
    {
        $this->catalogRepos->productRepository()->save($product);
    }

    public function linkProductToTaxon(string|Product $productId, string|Taxon $taxonId, array $data = []): Product
    {
        $product = $productId instanceof Product ? $productId : $this->catalogRepos->productRepository()->find(ProductId::fromString($productId));
        $taxon = $taxonId instanceof Taxon ? $taxonId : $this->catalogRepos->taxonRepository()->find(TaxonId::fromString($taxonId));

        $productTaxon = ProductTaxon::create($product->productId, $taxon->taxonId);
        $productTaxon->addData($data);

        // If taxonomy is of type variant_property, we need to set the relation as a VariantTaxon instead of default ProductTaxon
        $taxonomy = $this->catalogRepos->taxonomyRepository()->find($taxon->taxonomyId);
        if ($taxonomy->getType() == TaxonomyType::variant_property) {
            $productTaxon = $productTaxon->toVariantProperty();
        }

        $product->updateProductTaxa([
            ...$product->getProductTaxa(),
            $productTaxon,
        ]);

        if ($this->persist) {
            $this->catalogRepos->productRepository()->save($product);
        }

        return $product;
    }

    public function linkVariantToTaxon(string $productId, string $variantId, string $taxonId, array $data = []): Product
    {
        $product = $this->catalogRepos->productRepository()->find(ProductId::fromString($productId));
        $variant = $product->findVariant(VariantId::fromString($variantId));

        $variantTaxon = VariantTaxon::create(VariantId::fromString($variantId), TaxonId::fromString($taxonId));
        $variantTaxon->addData($data);

        $variant->updateVariantTaxa([
            ...$variant->getVariantTaxa(),
            $variantTaxon,
        ]);

        if ($this->persist) {
            $this->catalogRepos->productRepository()->save($product);
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

    public function findProduct(string|ProductId $productId): Product
    {
        $productId = is_string($productId) ? ProductId::fromString($productId) : $productId;

        return $this->catalogRepos->productRepository()->find($productId);
    }

    public function findProductDetail(VariantId $variantId): ProductDetail
    {
        return $this->catalogRepos->productDetailRepository()->findProductDetail($variantId);
    }
}

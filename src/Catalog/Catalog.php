<?php

namespace Thinktomorrow\Trader\Testing\Catalog;

use Thinktomorrow\Trader\Application\Product\ProductDetail\ProductDetail;
use Thinktomorrow\Trader\Application\Product\Taxa\ProductTaxonItem;
use Thinktomorrow\Trader\Application\Product\Taxa\VariantTaxonItem;
use Thinktomorrow\Trader\Application\Taxon\Tree\TaxonNode;
use Thinktomorrow\Trader\Application\Taxonomy\TaxonomyItem;
use Thinktomorrow\Trader\Domain\Common\Locale;
use Thinktomorrow\Trader\Domain\Common\Vat\VatPercentage;
use Thinktomorrow\Trader\Domain\Model\Order\Discount\DiscountPriceDefaults;
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
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\DefaultProductDetail;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\DefaultProductTaxonItem;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\DefaultTaxonNode;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\DefaultTaxonomyItem;
use Thinktomorrow\Trader\Infrastructure\Laravel\Models\DefaultVariantTaxonItem;
use Thinktomorrow\Trader\Infrastructure\Test\TestContainer;
use Thinktomorrow\Trader\Infrastructure\Test\TestTraderConfig;
use Thinktomorrow\Trader\Testing\Catalog\Repositories\CatalogRepositories;
use Thinktomorrow\Trader\Testing\Catalog\Repositories\InMemoryCatalogRepositories;
use Thinktomorrow\Trader\Testing\Catalog\Repositories\MysqlCatalogRepositories;
use Thinktomorrow\Trader\Testing\TraderDomain;

class Catalog extends TraderDomain
{
    private CatalogRepositories $catalogRepos;

    public function __construct(CatalogRepositories $catalogRepos)
    {
        parent::__construct();

        $this->catalogRepos = $catalogRepos;
    }

    public function catalogRepos(): CatalogRepositories
    {
        return $this->catalogRepos;
    }

    public function catalogApps(): CatalogApplications
    {
        return new CatalogApplications($this->catalogRepos, $this->config, $this->eventDispatcher);
    }

    public static function setUp(): void
    {
        (new TestContainer)->add(ProductDetail::class, DefaultProductDetail::class);
        (new TestContainer)->add(ProductTaxonItem::class, DefaultProductTaxonItem::class);
        (new TestContainer)->add(VariantTaxonItem::class, DefaultVariantTaxonItem::class);
        (new TestContainer)->add(TaxonomyItem::class, DefaultTaxonomyItem::class);
        (new TestContainer)->add(TaxonNode::class, DefaultTaxonNode::class);

        DiscountPriceDefaults::setDiscountTaxRate(VatPercentage::fromString('21'));
        DiscountPriceDefaults::setDiscountIncludeTax(true);
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
        return new self(new InMemoryCatalogRepositories(new TestTraderConfig));
    }

    public static function mysql(): self
    {
        return new self(new MysqlCatalogRepositories(new TestContainer));
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
        $product = $productId instanceof Product ? $productId : $this->catalogRepos->productRepository()->find(ProductId::fromString($productId));

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

    public function linkProductToTaxon(string|Product $productId, string|Taxon $taxonId): Product
    {
        $product = $productId instanceof Product ? $productId : $this->catalogRepos->productRepository()->find(ProductId::fromString($productId));
        $taxon = $taxonId instanceof Taxon ? $taxonId : $this->catalogRepos->taxonRepository()->find(TaxonId::fromString($taxonId));

        $productTaxon = ProductTaxon::create($product->productId, $taxon->taxonId);

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

    public function linkVariantToTaxon(string $productId, string $variantId, string $taxonId): Product
    {
        $product = $this->catalogRepos->productRepository()->find(ProductId::fromString($productId));
        $variant = $product->findVariant(VariantId::fromString($variantId));

        $variant->updateVariantTaxa([
            ...$variant->getVariantTaxa(),
            VariantTaxon::create(VariantId::fromString($variantId), TaxonId::fromString($taxonId)),
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
}

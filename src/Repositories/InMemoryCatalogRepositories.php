<?php

namespace Thinktomorrow\Trader\Testing\Repositories;

use Thinktomorrow\Trader\Application\Taxon\Redirect\TaxonRedirectRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryTaxonRedirectRepository;
use Thinktomorrow\Trader\Testing\Support\CatalogRepositories;
use Thinktomorrow\Trader\Application\Cart\VariantForCart\VariantForCartRepository;
use Thinktomorrow\Trader\Application\Product\Grid\FlattenedTaxonIds;
use Thinktomorrow\Trader\Application\Product\ProductDetail\ProductDetailRepository;
use Thinktomorrow\Trader\Application\Taxon\Queries\TaxaSelectOptions;
use Thinktomorrow\Trader\Application\Taxon\Queries\TaxonFilters;
use Thinktomorrow\Trader\Application\Taxon\Tree\TaxonTreeRepository;
use Thinktomorrow\Trader\Domain\Model\Product\ProductRepository;
use Thinktomorrow\Trader\Domain\Model\Product\VariantRepository;
use Thinktomorrow\Trader\Domain\Model\Taxon\TaxonRepository;
use Thinktomorrow\Trader\Domain\Model\Taxonomy\TaxonomyRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryCountryRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryOrderRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryProductDetailRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryProductRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryPromoRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryTaxonomyRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryTaxonRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryTaxonTreeRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryVariantRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryVatRateRepository;
use Thinktomorrow\Trader\Infrastructure\Test\TestContainer;
use Thinktomorrow\Trader\Infrastructure\Test\TestTraderConfig;
use Thinktomorrow\Trader\Infrastructure\Vine\VineFlattenedTaxonIds;
use Thinktomorrow\Trader\Infrastructure\Vine\VineTaxaSelectOptions;
use Thinktomorrow\Trader\Infrastructure\Vine\VineTaxonFilters;

class InMemoryCatalogRepositories implements CatalogRepositories
{
    public static function clear(): void
    {
        InMemoryTaxonomyRepository::clear();
        InMemoryTaxonRepository::clear();
        InMemoryProductRepository::clear();

        InMemoryOrderRepository::clear();
        InMemoryProductRepository::clear();
        InMemoryVariantRepository::clear();
        InMemoryTaxonRepository::clear();
        InMemoryPromoRepository::clear();
        InMemoryCountryRepository::clear();
        InMemoryVatRateRepository::clear();
    }

    public function taxonomyRepository(): TaxonomyRepository
    {
        return new InMemoryTaxonomyRepository();
    }

    public function taxonRepository(): TaxonRepository
    {
        return new InMemoryTaxonRepository();
    }

    public function taxonTreeRepository(): TaxonTreeRepository
    {
        return new InMemoryTaxonTreeRepository(new TestContainer(), new TestTraderConfig());
    }

    public function productRepository(): ProductRepository
    {
        return new InMemoryProductRepository();
    }

    public function productDetailRepository(): ProductDetailRepository
    {
        return new InMemoryProductDetailRepository();
    }

    public function variantRepository(): VariantRepository
    {
        return new InMemoryVariantRepository();
    }

    public function variantForCartRepository(): VariantForCartRepository
    {
        return new InMemoryVariantRepository();
    }

    public function taxonRedirectRepository(): TaxonRedirectRepository
    {
        return new InMemoryTaxonRedirectRepository();
    }

    public function taxonFilters(): TaxonFilters
    {
        return new VineTaxonFilters(new TestTraderConfig(), $this->taxonTreeRepository(), $this->taxonomyRepository());
    }

    public function flattenedTaxonIds(): FlattenedTaxonIds
    {
        return new VineFlattenedTaxonIds($this->taxonTreeRepository());
    }

    public function taxaSelectOptions(): TaxaSelectOptions
    {
        return new VineTaxaSelectOptions($this->taxonomyRepository(), $this->taxonTreeRepository());
    }
}

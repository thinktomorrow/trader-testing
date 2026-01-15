<?php

namespace Thinktomorrow\Trader\Testing\Catalog\Repositories;

use Psr\Container\ContainerInterface;
use Thinktomorrow\Trader\Application\Cart\VariantForCart\VariantForCartRepository;
use Thinktomorrow\Trader\Application\Product\Grid\FlattenedTaxonIds;
use Thinktomorrow\Trader\Application\Product\Grid\GridRepository;
use Thinktomorrow\Trader\Application\Product\ProductDetail\ProductDetailRepository;
use Thinktomorrow\Trader\Application\Product\VariantLinks\VariantLinksComposer;
use Thinktomorrow\Trader\Application\Taxon\Queries\TaxaSelectOptions;
use Thinktomorrow\Trader\Application\Taxon\Queries\TaxonFilters;
use Thinktomorrow\Trader\Application\Taxon\Redirect\TaxonRedirectRepository;
use Thinktomorrow\Trader\Application\Taxon\Tree\TaxonTreeRepository;
use Thinktomorrow\Trader\Domain\Model\Product\ProductRepository;
use Thinktomorrow\Trader\Domain\Model\Product\VariantRepository;
use Thinktomorrow\Trader\Domain\Model\Taxon\TaxonRepository;
use Thinktomorrow\Trader\Domain\Model\Taxonomy\TaxonomyRepository;
use Thinktomorrow\Trader\Domain\Model\VatRate\VatRateRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryCountryRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryOrderRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryProductDetailRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryProductRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryPromoRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryTaxonomyRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryTaxonRedirectRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryTaxonRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryTaxonTreeRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryVariantRepository;
use Thinktomorrow\Trader\Infrastructure\Test\Repositories\InMemoryVatRateRepository;
use Thinktomorrow\Trader\Infrastructure\Test\TestContainer;
use Thinktomorrow\Trader\Infrastructure\Test\TestTraderConfig;
use Thinktomorrow\Trader\Infrastructure\Vine\VineFlattenedTaxonIds;
use Thinktomorrow\Trader\Infrastructure\Vine\VineTaxaSelectOptions;
use Thinktomorrow\Trader\Infrastructure\Vine\VineTaxonFilters;
use Thinktomorrow\Trader\TraderConfig;

class InMemoryCatalogRepositories implements CatalogRepositories
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
        return new InMemoryTaxonomyRepository;
    }

    public function taxonRepository(): TaxonRepository
    {
        return new InMemoryTaxonRepository;
    }

    public function taxonTreeRepository(): TaxonTreeRepository
    {
        return new InMemoryTaxonTreeRepository(new TestContainer, new TestTraderConfig);
    }

    public function gridRepository(): GridRepository
    {
        throw new \Exception('Not implemented in in-memory repositories');
    }

    public function productRepository(): ProductRepository
    {
        return new InMemoryProductRepository;
    }

    public function productDetailRepository(): ProductDetailRepository
    {
        return new InMemoryProductDetailRepository;
    }

    public function variantRepository(): VariantRepository
    {
        return new InMemoryVariantRepository;
    }

    public function variantForCartRepository(): VariantForCartRepository
    {
        return new InMemoryVariantRepository;
    }

    public function variantLinksComposer(): VariantLinksComposer
    {
        return new VariantLinksComposer(
            $this->productRepository(),
            $this->container,
        );
    }

    public function taxonRedirectRepository(): TaxonRedirectRepository
    {
        return new InMemoryTaxonRedirectRepository;
    }

    public function taxonFilters(): TaxonFilters
    {
        return new VineTaxonFilters(new TestTraderConfig, $this->taxonTreeRepository(), $this->taxonomyRepository());
    }

    public function flattenedTaxonIds(): FlattenedTaxonIds
    {
        return new VineFlattenedTaxonIds($this->taxonTreeRepository());
    }

    public function taxaSelectOptions(): TaxaSelectOptions
    {
        return new VineTaxaSelectOptions($this->taxonomyRepository(), $this->taxonTreeRepository());
    }

    public function vatRateRepository(): VatRateRepository
    {
        return new InMemoryVatRateRepository($this->config);
    }
}

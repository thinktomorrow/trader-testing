<?php

namespace Thinktomorrow\Trader\Testing\Catalog\Repositories;

use Psr\Container\ContainerInterface;
use Thinktomorrow\Trader\Application\Cart\VariantForCart\VariantForCartRepository;
use Thinktomorrow\Trader\Application\Product\Grid\FlattenedTaxonIds;
use Thinktomorrow\Trader\Application\Product\ProductDetail\ProductDetailRepository;
use Thinktomorrow\Trader\Application\Taxon\Queries\TaxaSelectOptions;
use Thinktomorrow\Trader\Application\Taxon\Queries\TaxonFilters;
use Thinktomorrow\Trader\Application\Taxon\Redirect\TaxonRedirectRepository;
use Thinktomorrow\Trader\Application\Taxon\Tree\TaxonTreeRepository;
use Thinktomorrow\Trader\Domain\Model\Product\ProductRepository;
use Thinktomorrow\Trader\Domain\Model\Product\VariantRepository;
use Thinktomorrow\Trader\Domain\Model\Taxon\TaxonRepository;
use Thinktomorrow\Trader\Domain\Model\Taxonomy\TaxonomyRepository;
use Thinktomorrow\Trader\Domain\Model\VatRate\VatRateRepository;
use Thinktomorrow\Trader\Infrastructure\Laravel\Repositories\MysqlProductDetailRepository;
use Thinktomorrow\Trader\Infrastructure\Laravel\Repositories\MysqlProductRepository;
use Thinktomorrow\Trader\Infrastructure\Laravel\Repositories\MysqlTaxonomyRepository;
use Thinktomorrow\Trader\Infrastructure\Laravel\Repositories\MysqlTaxonRedirectRepository;
use Thinktomorrow\Trader\Infrastructure\Laravel\Repositories\MysqlTaxonRepository;
use Thinktomorrow\Trader\Infrastructure\Laravel\Repositories\MysqlTaxonTreeRepository;
use Thinktomorrow\Trader\Infrastructure\Laravel\Repositories\MysqlVariantRepository;
use Thinktomorrow\Trader\Infrastructure\Laravel\Repositories\MysqlVatRateRepository;
use Thinktomorrow\Trader\Infrastructure\Test\TestContainer;
use Thinktomorrow\Trader\Infrastructure\Test\TestTraderConfig;
use Thinktomorrow\Trader\Infrastructure\Vine\VineFlattenedTaxonIds;
use Thinktomorrow\Trader\Infrastructure\Vine\VineTaxaSelectOptions;
use Thinktomorrow\Trader\Infrastructure\Vine\VineTaxonFilters;

class MysqlCatalogRepositories implements CatalogRepositories
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function taxonomyRepository(): TaxonomyRepository
    {
        return new MysqlTaxonomyRepository($this->container);
    }

    public function taxonRepository(): TaxonRepository
    {
        return new MysqlTaxonRepository;
    }

    public function taxonTreeRepository(): TaxonTreeRepository
    {
        return new MysqlTaxonTreeRepository(new TestContainer, new TestTraderConfig);
    }

    public function productRepository(): ProductRepository
    {
        return new MysqlProductRepository(new MysqlVariantRepository(new TestContainer));
    }

    public function productDetailRepository(): ProductDetailRepository
    {
        return new MysqlProductDetailRepository(new TestContainer);
    }

    public function variantRepository(): VariantRepository
    {
        return new MysqlVariantRepository(new TestContainer);
    }

    public function variantForCartRepository(): VariantForCartRepository
    {
        return new MysqlVariantRepository(new TestContainer);
    }

    public function taxonRedirectRepository(): TaxonRedirectRepository
    {
        return new MysqlTaxonRedirectRepository;
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
        return new MysqlVatRateRepository($this->container);
    }
}

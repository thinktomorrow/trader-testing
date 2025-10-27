<?php

namespace Thinktomorrow\Trader\Testing\Catalog\Repositories;

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

interface CatalogRepositories
{
    public function taxonomyRepository(): TaxonomyRepository;

    public function taxonRepository(): TaxonRepository;

    public function taxonTreeRepository(): TaxonTreeRepository;

    public function productRepository(): ProductRepository;

    public function productDetailRepository(): ProductDetailRepository;

    public function variantRepository(): VariantRepository;

    public function variantForCartRepository(): VariantForCartRepository;

    public function taxonRedirectRepository(): TaxonRedirectRepository;

    public function taxonFilters(): TaxonFilters;

    public function flattenedTaxonIds(): FlattenedTaxonIds;

    public function taxaSelectOptions(): TaxaSelectOptions;

    public function vatRateRepository(): VatRateRepository;
}

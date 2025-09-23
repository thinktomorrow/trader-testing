<?php

namespace Thinktomorrow\Trader\Tests\Support;

use Thinktomorrow\Trader\Application\Product\ProductApplication;
use Thinktomorrow\Trader\Application\Taxon\TaxonApplication;
use Thinktomorrow\Trader\Application\Taxonomy\TaxonomyApplication;
use Thinktomorrow\Trader\Infrastructure\Test\EventDispatcherSpy;
use Thinktomorrow\Trader\Infrastructure\Test\TestTraderConfig;

class CatalogApplications
{
    public readonly CatalogRepositories $repos;

    public function __construct(CatalogRepositories $repos)
    {
        $this->repos = $repos;
    }

    public function taxonomyApplication(): TaxonomyApplication
    {
        return new TaxonomyApplication(
            new TestTraderConfig(),
            new EventDispatcherSpy(),
            $this->repos->taxonomyRepository()
        );
    }

    public function taxonApplication(): TaxonApplication
    {
        return new TaxonApplication(
            new TestTraderConfig(),
            new EventDispatcherSpy(),
            $this->repos->taxonRepository(),
        );
    }

    public function productApplication(): ProductApplication
    {
        return new ProductApplication(
            new TestTraderConfig(),
            new EventDispatcherSpy(),
            $this->repos->productRepository(),
            $this->repos->variantRepository(),
            $this->repos->taxonTreeRepository(),
            $this->repos->taxonomyRepository(),
        );
    }
}

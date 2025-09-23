<?php

namespace Thinktomorrow\Trader\Testing\Support;

use Thinktomorrow\Trader\Application\Product\ProductApplication;
use Thinktomorrow\Trader\Application\Taxon\TaxonApplication;
use Thinktomorrow\Trader\Application\Taxonomy\TaxonomyApplication;
use Thinktomorrow\Trader\Domain\Common\Event\EventDispatcher;
use Thinktomorrow\Trader\TraderConfig;

class CatalogApplications
{
    private CatalogRepositories $repos;
    private TraderConfig $config;
    private EventDispatcher $eventDispatcher;

    public function __construct(CatalogRepositories $repos, TraderConfig $config, EventDispatcher $eventDispatcher)
    {
        $this->repos = $repos;
        $this->config = $config;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function taxonomyApplication(): TaxonomyApplication
    {
        return new TaxonomyApplication(
            $this->config,
            $this->eventDispatcher,
            $this->repos->taxonomyRepository()
        );
    }

    public function taxonApplication(): TaxonApplication
    {
        return new TaxonApplication(
            $this->config,
            $this->eventDispatcher,
            $this->repos->taxonRepository(),
        );
    }

    public function productApplication(): ProductApplication
    {
        return new ProductApplication(
            $this->config,
            $this->eventDispatcher,
            $this->repos->productRepository(),
            $this->repos->variantRepository(),
            $this->repos->taxonTreeRepository(),
            $this->repos->taxonomyRepository(),
        );
    }
}

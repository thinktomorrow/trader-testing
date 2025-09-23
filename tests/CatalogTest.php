<?php

class CatalogTest extends \PHPUnit\Framework\TestCase
{
    public function test_it_can_setup_catalog(): void
    {
        $catalog = \Thinktomorrow\Trader\Testing\Support\Catalog::inMemory();

        $this->assertNotNull($catalog);
    }

    public function test_it_can_call_repos(): void
    {
        $catalog = \Thinktomorrow\Trader\Testing\Support\Catalog::inMemory();

        $this->assertInstanceOf(\Thinktomorrow\Trader\Testing\Support\CatalogRepositories::class, $catalog->repos());
    }

    public function test_it_can_call_apps(): void
    {
        $catalog = \Thinktomorrow\Trader\Testing\Support\Catalog::inMemory();

        $this->assertInstanceOf(\Thinktomorrow\Trader\Testing\Support\CatalogApplications::class, $catalog->apps());
    }
}

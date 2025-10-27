<?php

class CatalogTest extends \PHPUnit\Framework\TestCase
{
    public function test_it_can_setup_in_memory_catalog(): void
    {
        $catalog = \Thinktomorrow\Trader\Testing\Catalog\Catalog::inMemory();

        $this->assertNotNull($catalog);
    }

    public function test_it_can_call_in_memory_repos(): void
    {
        $catalog = \Thinktomorrow\Trader\Testing\Catalog\Catalog::inMemory();

        $this->assertInstanceOf(\Thinktomorrow\Trader\Testing\Catalog\Repositories\CatalogRepositories::class, $catalog->catalogRepos());
    }

    public function test_it_can_call_in_memory_apps(): void
    {
        $catalog = \Thinktomorrow\Trader\Testing\Catalog\Catalog::inMemory();

        $this->assertInstanceOf(\Thinktomorrow\Trader\Testing\Catalog\CatalogApplications::class, $catalog->catalogApps());
    }

    public function test_it_can_setup_mysql_catalog(): void
    {
        $catalog = \Thinktomorrow\Trader\Testing\Catalog\Catalog::mysql();

        $this->assertNotNull($catalog);
    }

    public function test_it_can_call_mysql_repos(): void
    {
        $catalog = \Thinktomorrow\Trader\Testing\Catalog\Catalog::mysql();

        $this->assertInstanceOf(\Thinktomorrow\Trader\Testing\Catalog\Repositories\CatalogRepositories::class, $catalog->catalogRepos());
    }

    public function test_it_can_call_mysql_apps(): void
    {
        $catalog = \Thinktomorrow\Trader\Testing\Catalog\Catalog::mysql();

        $this->assertInstanceOf(\Thinktomorrow\Trader\Testing\Catalog\CatalogApplications::class, $catalog->catalogApps());
    }
}

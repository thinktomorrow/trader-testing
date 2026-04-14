<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Thinktomorrow\Trader\Testing\Catalog\CatalogApplications;
use Thinktomorrow\Trader\Testing\Catalog\CatalogContext;
use Thinktomorrow\Trader\Testing\Catalog\Repositories\CatalogRepositories;

class CatalogTest extends TestCase
{
    public function test_it_can_setup_in_memory_catalog(): void
    {
        $catalog = CatalogContext::inMemory();

        $this->assertNotNull($catalog);
    }

    public function test_it_can_call_in_memory_repos(): void
    {
        $catalog = CatalogContext::inMemory();

        $this->assertInstanceOf(CatalogRepositories::class, $catalog->repos());
    }

    public function test_it_can_call_in_memory_apps(): void
    {
        $catalog = CatalogContext::inMemory();

        $this->assertInstanceOf(CatalogApplications::class, $catalog->apps());
    }

    public function test_it_can_setup_mysql_catalog(): void
    {
        $catalog = CatalogContext::mysql();

        $this->assertNotNull($catalog);
    }

    public function test_it_can_call_mysql_repos(): void
    {
        $catalog = CatalogContext::mysql();

        $this->assertInstanceOf(CatalogRepositories::class, $catalog->repos());
    }

    public function test_it_can_call_mysql_apps(): void
    {
        $catalog = CatalogContext::mysql();

        $this->assertInstanceOf(CatalogApplications::class, $catalog->apps());
    }
}

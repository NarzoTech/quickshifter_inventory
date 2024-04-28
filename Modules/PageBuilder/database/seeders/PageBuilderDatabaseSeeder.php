<?php

namespace Modules\PageBuilder\database\seeders;

use Illuminate\Database\Seeder;

class PageBuilderDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PageItemComponentsSeeder::class,
            CustomizeablePageSeeder::class,
            CustomizeablePageItemSeeder::class,
        ]);
    }
}

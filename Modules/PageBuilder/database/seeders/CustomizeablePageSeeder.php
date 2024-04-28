<?php

namespace Modules\PageBuilder\database\seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\PageBuilder\app\Models\CustomizeablePage;

class CustomizeablePageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();
        $page = new CustomizeablePage();
        $page->title = 'Home Page';
        $page->slug = 'homepage';
        $page->save();
    }
}

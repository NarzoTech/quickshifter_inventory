<?php

namespace Modules\PageBuilder\database\seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\PageBuilder\app\Models\PageItemComponents;

class PageItemComponentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        $component = new PageItemComponents();
        $component->name = 'Home Page Item';
        $component->file = 'component-1';
        $component->save();

        $component = new PageItemComponents();
        $component->name = 'Home Page Item 2';
        $component->file = 'component-2';
        $component->save();

        $component = new PageItemComponents();
        $component->name = 'Home Page Item 3';
        $component->file = 'component-3';
        $component->save();
    }
}

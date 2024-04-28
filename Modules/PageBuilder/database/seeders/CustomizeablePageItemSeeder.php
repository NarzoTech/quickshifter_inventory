<?php

namespace Modules\PageBuilder\database\seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\PageBuilder\app\Models\CustomizeablePage;
use Modules\PageBuilder\app\Models\CustomizeablePageItem;
use Modules\PageBuilder\app\Models\PageItemComponents;

class CustomizeablePageItemSeeder extends Seeder
{
    public function run(): void
    {
        Model::unguard();

        $item = new CustomizeablePageItem();
        $item->customizeable_page_id = CustomizeablePage::first()->id;
        $item->title = PageItemComponents::find(1)->name;
        $item->component_name = PageItemComponents::find(1)->file;
        $item->position = 0;
        $item->save();

        $item = new CustomizeablePageItem();
        $item->customizeable_page_id = CustomizeablePage::first()->id;
        $item->title = PageItemComponents::find(2)->name;
        $item->component_name = PageItemComponents::find(2)->file;
        $item->position = 1;
        $item->save();
    }
}

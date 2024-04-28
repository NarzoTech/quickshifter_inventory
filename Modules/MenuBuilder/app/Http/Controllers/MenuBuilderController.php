<?php

namespace Modules\MenuBuilder\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Modules\Language\app\Enums\TranslationModels;
use Modules\Language\app\Traits\GenerateTranslationTrait;
use Modules\MenuBuilder\app\Http\Requests\MenuRequest;
use Modules\MenuBuilder\app\Models\Menu;
use Modules\MenuBuilder\app\Models\MenuTranslation;

class MenuBuilderController extends Controller
{
    use GenerateTranslationTrait, RedirectHelperTrait;

    public function index($code)
    {
        checkAdminHasPermissionAndThrowException('menu.view');
        $items = Menu::whereNull('parent_id')->orderBy('order', 'asc')->get();

        return view('menubuilder::index', [
            'items' => $items,
            'code' => $code,
        ]);
    }

    public function store(MenuRequest $request)
    {
        if (checkAdminHasPermission('menu.store')) {
            if (! $request->title) {
                return response()->json([
                    'status' => false,
                    'data' => null,
                    'message' => __('Operation Failed'),
                ]);
            }

            $highestOrder = Menu::max('order');

            if ($request->parent_id) {
                $highestOrder = Menu::where('parent_id', $request->parent_id)->max('order');
            }

            $menu = new Menu();
            $menu->link = $request->link;
            if ($request->parent_id) {
                $menu->parent_id = $request->parent_id;
            }
            $menu->status = 1;
            $menu->order = $highestOrder + 1;

            if ($menu->save()) {
                $this->generateTranslations(
                    TranslationModels::MenuBuilder,
                    $menu,
                    'menu_id',
                    $request,
                    true,
                    [
                        'title' => $request->title,
                    ]
                );
            }

            return response()->json([
                'id' => $menu->id,
                'title' => $request->title,
                'link' => $menu->link,
                'parent_id' => $request->parent_id ?? 0,
            ]);
        }

        return response()->json([
            'message' => __('Permission Denied'),
        ], 403);
    }

    public function update(MenuRequest $request)
    {
        if (checkAdminHasPermission('menu.update')) {
            if (! $request->id && ! $request->title) {
                return response()->json([
                    'status' => false,
                    'data' => null,
                    'message' => __('Operation Failed'),
                ]);
            }

            $validatedData = $request->validated();

            $menu = Menu::find($request->id);
            if ($menu) {
                $menu->link = $request->link;
                $this->updateTranslations(
                    $menu,
                    $request,
                    $validatedData
                );
                $menu->save();
            }

            return $menu ? response()->json([
                'status' => true,
                'data' => $menu,
                'message' => __('Update Successfully'),
            ]) : response()->json([
                'status' => false,
                'data' => null,
                'message' => __('Operation Failed'),
            ]);
        }

        return response()->json([
            'message' => __('Permission Denied'),
        ], 403);
    }

    public function destroy(Request $request)
    {
        if (checkAdminHasPermission('menu.delete')) {
            if (! $request->id) {
                return response()->json([
                    'status' => false,
                    'data' => null,
                    'message' => __('Operation Failed'),
                ]);
            }
            $menu = Menu::find($request->id);

            if ($menu) {
                MenuTranslation::where('menu_id', $request->id)->delete();
                Menu::where('parent_id', $menu->id)->delete();
            }

            $status = $menu->delete();

            return $menu ? response()->json([
                'status' => $status,
                'data' => $menu,
                'message' => __('Deleted Successfully'),
            ]) : response()->json([
                'status' => $status,
                'data' => null,
                'message' => __('Operation Failed'),
            ]);
        }

        return response()->json([
            'message' => __('Permission Denied'),
        ], 403);
    }

    public function sortmenu(Request $request)
    {
        $ids = $request->id;
        foreach ($ids as $order => $id) {
            $menu = Menu::find($id);
            $menu->order = $order;
            $menu->save();
        }

        return $menu ? response()->json([
            'status' => true,
            'data' => $menu,
            'message' => __('Updated Successfully'),
        ]) : response()->json([
            'status' => false,
            'data' => null,
            'message' => __('Operation Failed'),
        ]);
    }
}

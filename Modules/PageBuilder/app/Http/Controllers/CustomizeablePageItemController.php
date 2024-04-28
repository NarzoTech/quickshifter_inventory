<?php

namespace Modules\PageBuilder\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Modules\PageBuilder\app\Models\CustomizeablePageItem;
use Modules\PageBuilder\app\Models\PageItemComponents;

class CustomizeablePageItemController extends Controller
{
    use RedirectHelperTrait;

    public function index()
    {
        return view('pagebuilder::index');
    }

    public function store(Request $request)
    {
        checkAdminHasPermissionAndThrowException('page.component.add');

        $maxPosition = CustomizeablePageItem::where('customizeable_page_id', $request->page_id)->max('position');
        $component = PageItemComponents::whereFile($request->component_name)->first();

        $item = new CustomizeablePageItem();
        $item->customizeable_page_id = $request->page_id;
        $item->title = $component->name;
        $item->component_name = $component->file;
        $item->position = $maxPosition++;
        $item->save();

        return $this->redirectWithMessage(RedirectType::CREATE->value);
    }

    public function destroy($id)
    {
        return $this->redirectWithMessage(RedirectType::ERROR->value);
    }

    public function statusUpdate($id)
    {
        $pageItem = CustomizeablePageItem::find($id);
        $status = $pageItem->status == 1 ? 0 : 1;
        $pageItem->update(['status' => $status]);

        $notification = __('Updated Successfully');

        return response()->json([
            'success' => true,
            'message' => $notification,
        ]);
    }

    public function sortPageItems(Request $request)
    {
        if (checkAdminHasPermission('page.update')) {
            $ids = $request->id;

            foreach ($ids as $position => $id) {
                $item = CustomizeablePageItem::find($id);
                $item->position = $position;
                $item->save();
            }

            return $item ? response()->json([
                'status' => true,
                'data' => $item,
                'message' => __('Updated Successfully'),
            ]) : response()->json([
                'status' => false,
                'data' => null,
                'message' => __('Operation Failed'),
            ]);
        }

        return response()->json([
            'success' => false,
        ], 403);
    }
}

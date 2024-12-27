<?php

namespace Modules\Service\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Service\app\Http\Requests\ServiceRequest;
use Modules\Service\app\Services\ServiceCategoryService;
use Modules\Service\app\Services\ServicesService;

class ServiceController extends Controller
{
    use RedirectHelperTrait;
    public function __construct(private ServiceCategoryService $category, private ServicesService $service)
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = $this->category->all()->get();
        $services = $this->service->all();

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $services = $services->get();
        } else {
            $services = $services->paginate($parpage);
            $services->appends(request()->query());
        }

        return view('service::service', compact('categories', 'services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('service::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceRequest $request): RedirectResponse
    {
        $this->service->store($request);
        return $this->redirectWithMessage(RedirectType::CREATE->value, null, [], ['messege' => 'Service created successfully', 'alert-type' => 'success']);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('service::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('service::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ServiceRequest $request, $id): RedirectResponse
    {
        $this->service->update($id, $request);
        return $this->redirectWithMessage(RedirectType::UPDATE->value, null, [], ['messege' => 'Service updated successfully', 'alert-type' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->service->destroy($id);
        return $this->redirectWithMessage(RedirectType::DELETE->value, null, [], ['messege' => 'Service deleted successfully', 'alert-type' => 'success']);
    }
}

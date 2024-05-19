<?php

namespace Modules\Accounts\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Accounts\app\Services\BankService;

class BankController extends Controller
{
    use RedirectHelperTrait;
    public function __construct(private BankService $bankService)
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banks = $this->bankService->all()->paginate(20);
        return view('accounts::bank.index', compact('banks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $this->bankService->create($request->only('name'));
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.bank.index', [], ['messege' => 'Bank created successfully', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.bank.index', [], ['messege' => 'Something went wrong', 'alert-type' => 'error']);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('accounts::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('accounts::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}

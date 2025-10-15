<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCardRequest;
use App\Http\Requests\UpdateCardRequest;
use App\Models\Card;
use App\Services\CardService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CardController extends Controller
{

    public function __construct(protected CardService $cardService)
    {
        // $this->middleware('can:read_card')->only('index', 'fetch', 'show');
        // $this->middleware('can:create_card')->only('store', 'create');
        // $this->middleware('can:update_card')->only('update', 'edit');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.card.index');
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            Card::withTrashed()->with([
                'cardable'
            ])->onlyBranches()->latest()
        )->toJson();
        return $data;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCardRequest $request)
    {
        $data = $request->afterValidation();
        $card = $this->cardService->store($data);
        session()->flash('message', _t("Success"));
        return redirect()->route('card.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function edit($id)
    {
        $card = $this->cardService->show($id);
        return view('dashboard.card.edit', compact('card'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCardRequest $request, string $id)
    {
        $data = $request->validated();
        $card = $this->cardService->update($data, $id);
        session()->flash('message', _t("Success"));
        return redirect()->route('card.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

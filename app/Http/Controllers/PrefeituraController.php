<?php

namespace App\Http\Controllers;

use App\Models\Prefeitura;
use App\Http\Requests\StorePrefeituraRequest;
use App\Http\Requests\UpdatePrefeituraRequest;

class PrefeituraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $prefeitura = Prefeitura::with('user')->get();
        return response()->json($prefeitura);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePrefeituraRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Prefeitura $prefeitura)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Prefeitura $prefeitura)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePrefeituraRequest $request, Prefeitura $prefeitura)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prefeitura $prefeitura)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCaracteristicaRequest;
use App\Http\Requests\UpdatePresentacioneRequest;
use App\Models\Caracteristica;
use App\Models\Presentacione;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PresentacioneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $presentaciones = Presentacione::with('caracteristica')->latest()->get();
        return view('presentacione.index', compact('presentaciones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('presentacione.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCaracteristicaRequest $request)
    {
        try {
            DB::beginTransaction();
            $caracteristica = Caracteristica::create($request->validated());
            $caracteristica->presentacione()->create(['sigla' => $request->sigla]);
            DB::commit();
        }catch (Exception $e){
            DB::rollBack();
        }

        return redirect()->route('presentaciones.index')->with('success', 'Presentación registrada');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('presentacione.edit', compact('presentacione'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePresentacioneRequest $request, Presentacione $presentacione)
    {
        $presentacione->caracteristica->update($request->validated());
        $presentacione->update($request->validated());

        return redirect()->route('presentaciones.index')->with('success', 'Presentación editada');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $message = '';
        $presentacione = Presentacione::find($id);
        if ($presentacione->caracteristica->estado == 1) {
            Caracteristica::where('id', $presentacione->caracteristica->id)
            ->update([
                'estado' => 0
            ]);
            $message = 'Presentación eliminada';
        } else {
            Caracteristica::where('id', $presentacione->caracteristica->id)
            ->update([
                'estado' => 1
            ]);
            $message = 'Presentación restaurada';
        }

        return redirect()->route('presentaciones.index')->with('success', $message);
    }
}

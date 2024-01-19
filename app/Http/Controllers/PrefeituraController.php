<?php

namespace App\Http\Controllers;

use App\Models\Prefeitura;
use App\Http\Requests\StorePrefeituraRequest;
use App\Http\Requests\UpdatePrefeituraRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PrefeituraController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }
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
        try {
            // Tratar CNPJ, inscrição estadual e telefone
            $cnpj = preg_replace('/[^0-9]/', '', $request->cnpj);
            $telefone = preg_replace('/[^0-9]/', '', $request->telefone);

            DB::beginTransaction();

            // Criar um novo usuário
            $user = User::create([
                'email' => $request->email,
                'perfils_id' => '2'
            ]);

            // Criar uma nova Prefeitura associada ao usuário
            $prefeitura = new Prefeitura([
                'razao_social' => $request->razao_social,
                'cnpj' => $cnpj,
                'telefone' => $telefone,
                'rua' => $request->rua,
                'numero' => $request->numero,
                'bairro' => $request->bairro,
                'complemento' => $request->complemento,
                'cidade' => $request->cidade,
                'uf' => $request->uf,
            ]);

            // Associar o prefeitura ao usuário
            $user->prefeitura()->save($prefeitura);

            // Commit da transação se todas as operações forem bem-sucedidas
            DB::commit();

            // Retorna uma resposta de sucesso
            return response()->json(['message' => 'Prefeitura criada com sucesso'], 201);
        } catch (\Exception $e) {
            // Rollback em caso de falha
            DB::rollBack();

            // Retorna uma resposta de erro em caso de falha
            return response()->json(['message' => 'Erro ao criar prefeitura', 'error' => $e->getMessage()], 500);
        }
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

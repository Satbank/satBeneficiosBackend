<?php

namespace App\Http\Controllers;

use App\Models\Comercio;
use App\Http\Requests\StoreComercioRequest;
use App\Http\Requests\UpdateComercioRequest;
use App\Models\User;

class ComercioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      
        try {
            // Obter todos os registros da tabela 'comercios' com o relacionamento 'users'
            $comercios = Comercio::with('users')->get();
         
            // Montar a resposta JSON com os detalhes necessários
            $responseData = [];
            foreach ($comercios as $comercio) {
                $responseData[] = [
                    'razao_social' => $comercio->razao_social,
                    'nome_fantasia' => $comercio->nome_fantasia,
                    'cnpj' => $comercio->cnpj,
                    'inscricao_estadual' => $comercio->inscricao_estadual,
                    'telefone' => $comercio->telefone,
                    'rua' => $comercio->rua,
                    'numero' => $comercio->numero,
                    'bairro' => $comercio->bairro,
                    'complemento' => $comercio->complemento,
                    'cidade' => $comercio->cidade,
                    'uf' => $comercio->uf,
                    'prefeitura_id' => $comercio->prefeitura_id,
                    'users_id' => $comercio->users_id,
                    'email' => $comercio->users->email,
                ];
            }
    
            // Retornar a resposta JSON
            return response()->json(['comercios' => $responseData], 200);
        } catch (\Exception $e) {
            // Se houver um erro, retornar uma resposta JSON com a mensagem de erro
            return response()->json(['error' => 'Erro ao buscar comercios: ' . $e->getMessage()], 500);
        }
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
    public function store(StoreComercioRequest $request)
    {

        try {

            // Tratar CNPJ, inscrição estadual e telefone
            $cnpj = preg_replace('/[^0-9]/', '', $request->cnpj);
            $inscricaoEstadual = preg_replace('/[^0-9]/', '', $request->inscricao_estadual);
            $telefone = preg_replace('/[^0-9]/', '', $request->telefone);

            // Criar um novo usuário
            $user = User::create([
                'email' => $request->email,
                'password' => bcrypt($request->senha),
                'perfils_id' => '3'
            ]);
         
            // Criar um novo Comercio associado ao usuário
            $comercio = new Comercio([
                'razao_social' => $request->razao_social,
                'nome_fantasia' => $request->nome_fantasia,
                'cnpj' => $cnpj,
                'inscricao_estadual' => $inscricaoEstadual,
                'telefone' => $telefone,
                'rua' => $request->rua,
                'numero' => $request->numero,
                'bairro' => $request->bairro,
                'complemento' => $request->complemento,
                'cidade' => $request->cidade,
                'uf' => $request->uf,
                'prefeitura_id' => $request->prefeitura_id,
            ]);

            // Associar o Comercio ao usuário
            $user->comercio()->save($comercio);

            // Retornar uma resposta JSON com os detalhes do Comercio recém-criado
            return response()->json(['success' => 'Comercio cadastrado com sucesso '], 201);
        } catch (\Exception $e) {
            // Se houver um erro durante a criação, retornar uma resposta JSON com a mensagem de erro
            return response()->json(['error' => 'Erro ao criar Comercio: ' . $e->getMessage()], 500);
        }
    }

    public function show(Comercio $comercio)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comercio $comercio)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateComercioRequest $request, Comercio $comercio)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comercio $comercio)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // Obter o parâmetro de pesquisa da solicitação
            $search = $request->input('search');
    
            // Definir o número de itens por página
            $perPage = $request->input('pageSize', 10);
    
            // Consultar clientes com base no parâmetro de pesquisa
            $clientesQuery = Cliente::query();
    
            if ($search) {
                $clientesQuery->where('nome', 'LIKE', "%$search%")
                               ->orWhere('cpf', 'LIKE', "%$search%")
                               ->orWhere('telefone', 'LIKE', "%$search%")
                               // Adicione outros campos que você deseja pesquisar
                               ->orWhereHas('users', function ($query) use ($search) {
                                   $query->where('email', 'LIKE', "%$search%");
                               });
            }
    
            // Paginar os resultados
            $clientes = $clientesQuery->with('users')->paginate($perPage);
    
    
            // Montar a resposta JSON com os detalhes necessários
            $responseData = [];
            foreach ($clientes as $cliente) {
                $responseData[] = [
                    'users_id'=>$cliente->users_id,
                    'nome' => $cliente->nome,
                    'cpf' => $cliente->cpf,
                    'telefone' => $cliente->telefone,
                    'rua' => $cliente->rua,
                    'numero' => $cliente->numero,
                    'bairro' => $cliente->bairro,
                    'complemento' => $cliente->complemento,
                    'cidade' => $cliente->cidade,
                    'uf' => $cliente->uf,
                    'prefeitura_id' => $cliente->prefeitura_id,
                    'email' => $cliente->users->email,
                    
                ];
            }
    
            // Retornar a resposta JSON
            return response()->json([
                'clientes' => $responseData,
                'totalPages' => $clientes->lastPage(),
            ], 200);
        } catch (\Exception $e) {
            // Se houver um erro, retornar uma resposta JSON com a mensagem de erro
            return response()->json(['error' => 'Erro ao buscar clientes: ' . $e->getMessage()], 500);
        }
    }
    

    
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClienteRequest $request)
    {
    
        try {
            // Iniciar uma transação
            DB::beginTransaction();

            // Tratar CNPJ,  e telefone
            $cpf = preg_replace('/[^0-9]/', '', $request->cpf);
            $telefone = preg_replace('/[^0-9]/', '', $request->telefone);

            // Criar um novo usuário
            $user = User::create([
                'email' => $request->email,
                'password' => bcrypt($request->senha),
                'perfils_id' => '4'
            ]);

            // Criar um novo Cliente associado ao usuário
            $cliente = new Cliente([
                'nome' => $request->nome,
                'cpf' => $cpf,
                'telefone' => $telefone,
                'rua' => $request->rua,
                'numero' => $request->numero,
                'bairro' => $request->bairro,
                'complemento' => $request->complemento,
                'cidade' => $request->cidade,
                'uf' => $request->uf,
                'prefeitura_id' => $request->prefeitura_id,
            ]);

            // Associar o cliente ao usuário
            $user->cliente()->save($cliente);

            // Commit da transação se todas as operações foram bem-sucedidas
            DB::commit();

            // Retornar uma resposta JSON com os detalhes do cliente recém-criado
            return response()->json(['success' => 'Cliente cadastrado com sucesso'], 201);
        } catch (\Exception $e) {
            // Se houver um erro durante a criação, realizar rollback da transação
            DB::rollBack();
            // Retornar uma resposta JSON com a mensagem de erro
            return response()->json(['error' => 'Erro ao criar cliente: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClienteRequest $request, Cliente $cliente)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Cartao;
use App\Http\Requests\StoreCartaoRequest;
use App\Http\Requests\UpdateCartaoRequest;
use Illuminate\Http\Request;

class CartaoController extends Controller
{
    // Restante do seu código...

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
    
            // Consultar cartões com base no parâmetro de pesquisa
            $cartoesQuery = Cartao::query();
    
            if ($search) {
                $cartoesQuery->where('tipo_cartao', 'LIKE', "%$search%")
                    ->orWhere('numero_cartao', 'LIKE', "%$search%")
                    ->orWhere('status', 'LIKE', "%$search%")
                    ->orWhereHas('cliente', function ($query) use ($search) {
                        $query->where('nome', 'LIKE', "%$search%")
                            ->orWhere('cpf', 'LIKE', "%$search%");
                    });
            }
    
            // Paginar os resultados e carregar a relação 'cliente'
            $cartoes = $cartoesQuery->with('cliente')->paginate($perPage);
    
            // Montar a resposta JSON com os detalhes necessários
            $responseData = [];
            foreach ($cartoes as $cartao) {
                $responseData[] = [
                    'users_id'        => $cartao->users_id,
                    'nome'    => $cartao->cliente ? $cartao->cliente->nome : null,
                    'tipo_cartao'     => $cartao->tipo_cartao,
                    'numero_cartao'   => $cartao->numero_cartao,
                    'saldo_disponivel' => $cartao->saldo_disponivel,
                    'data_emissao'    => $cartao->data_emissao,
                    'status'          => $cartao->status,
                    'data_validade'   => $cartao->data_validade,
                    'valor_alocado'   => $cartao->valor_alocado,
                    'saldo_atual'     => $cartao->saldo_atual,
                ];
            }
    
            // Obtém a quantidade total de páginas
            $totalPages = ceil($cartoes->total() / $cartoes->perPage());
    
            return response()->json(['success' => true, 'data' => $responseData, 'totalPages' => $totalPages], 200);
        } catch (\Exception $e) {
            // Lidar com exceções (erros)
            return response()->json(['error' => 'Erro ao obter os cartões: ' . $e->getMessage()], 500);
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
    public function store(StoreCartaoRequest $request)
    {
        try {
            // Remover máscaras dos campos
            $numero_cartao = preg_replace('/\D/', '', $request->numero_cartao); // Remove não dígitos
            $data_validade = \DateTime::createFromFormat('d/m/Y', $request->data_validade)->format('Y-m-d'); // Converte formato da data
            $valorAlocado = str_replace(',', '.', $request->valor_alocado);

            // Incluindo a data_emissao com o valor da data atual
            $data_emissao = now()->format('Y-m-d');

            // Verificar se o número do cartão já existe
            if (Cartao::where('numero_cartao', $numero_cartao)->exists()) {
                return response()->json(['error' => 'Já existe um cartão com esse número cadastrado.', ], 422);
            }

            $cartao = new Cartao([
                'users_id'      => $request->users_id,
                'numero_cartao' => $numero_cartao,
                'tipo_cartao'   => $request->tipo_cartao,
                'status'        => $request->status,
                'valor_alocado' => $valorAlocado,
                'data_validade' => $data_validade,
                'data_emissao'  => $data_emissao,
            ]);

            $cartao->save();

            return response()->json(['success' => true, 'message' => 'Cartão criado com sucesso', 'data' => $cartao], 201);
        } catch (\Exception $e) {
            // Lidar com exceções (erros)
            return response()->json(['error' => 'Erro ao criar o cartão: ' . $e->getMessage()], 500);
        }
    }




    /**
     * Display the specified resource.
     */
    public function show(Cartao $cartao)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cartao $cartao)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCartaoRequest $request, Cartao $cartao)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cartao $cartao)
    {
        //
    }

   
}

<?php

namespace App\Http\Controllers;

use App\Models\Cartao;
use App\Models\Comercio;
use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Models\Recebimentos_sat_bank;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Movimentacao_cliente_comercio;
use App\Http\Requests\StoreMovimentacao_cliente_comercioRequest;
use App\Http\Requests\UpdateMovimentacao_cliente_comercioRequest;

class MovimentacaoClienteComercioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    }


    public function create()
    {
        //
    }
    public function getRelatorios(Request $request)
    {
        $quantidadeDias = $request->query('dias', 1);

        // Obter o ID do usuário logado
        $userId = Auth::id();

        // Obter o ID do Comercio associado ao usuário
        $comercioId = Comercio::where('users_id', $userId)->value('id');

        // Buscar movimentações com base no ID do Comercio e na quantidade de dias
        $movimentacoes = Movimentacao_cliente_comercio::where('comercios_id', $comercioId)
            ->where('created_at', '>=', now()->subDays($quantidadeDias))
            ->get();

        // Mapear os dados para o formato desejado
        $relatorioFormatado = $movimentacoes->map(function ($movimentacao) {
            // Obter o users_id associado ao cartao_id
            $usersId = Cartao::where('id', $movimentacao->cartoes_id)->value('users_id');

            // Obter dados do cliente associado ao users_id
            $cliente = Cliente::where('users_id', $usersId)->first();

            // Verificar se o cliente existe antes de acessar a propriedade 'nome'
            if ($cliente) {
                return [
                    'id' => $movimentacao->id,
                    'nome' => $cliente->nome,
                    'valor' => $movimentacao->valor,
                    'valor_original' => $movimentacao->valor_original,
                    'status' => $movimentacao->status,
                    'data' => $movimentacao->created_at,
                ];
            }

            return null; // ou um valor padrão, dependendo dos requisitos
        })
            ->filter(); // Remover valores nulos do array

        // Retornar o relatório formatado como JSON
        return response()->json(['relatorios' => $relatorioFormatado]);
    }

    public function store(StoreMovimentacao_cliente_comercioRequest $request)
    {

        try {

            $valor =  str_replace(",", ".", $request->valor);
            $numero_cartao = preg_replace('/\D/', '', $request->numero_cartao);

            //verifica se existe o cartão
            $cartao = Cartao::where('numero_cartao', $numero_cartao)->first();

            if (!$cartao) {
                return response()->json(['error' => 'Cartão não encontrado.'], 422);
            }
            //verifica se esta ativo
            if ($cartao->status !== 'ativo') {
                return response()->json(['error' => 'Cartão bloqueado ou inativo.'], 422);
            }
            //verifica a senha 
            if (!Hash::check($request->senha, $cartao->senha)) {
                // Incrementa o contador de tentativas
                $cartao->tentativas += 1;
                if ($cartao->tentativas >= 3) {
                    // Bloqueia o cartão se exceder as tentativas permitidas
                    $cartao->status = 'bloqueado';
                }

                $cartao->save();

                // Retorna a resposta incluindo o número de tentativas restantes
                $mensagem = 'Senha incorreta. Restam ' . (3 - $cartao->tentativas) . ' tentativas.';
                return response()->json(['error' => $mensagem], 422);
            }
            // Reinicia as tentativas após senha correta
            $cartao->tentativas = 0;
            $cartao->save();


            DB::beginTransaction();

            // Calcular descontos
            $descontoEstabelecimento = $valor * 0.03;  // 3% para o estabelecimento
            $descontoCliente = $valor * 0.02;  // 2% para o cliente

            // Atualizar o valor com desconto e o saldo do cliente
            $valorComDesconto = $valor - $descontoEstabelecimento;
            $cartao->saldo -= $valor + $descontoCliente;


            // Verificar se o saldo é suficiente
            if ($cartao->saldo < 0) {
                DB::rollBack();
                return response()->json(['error' => 'Saldo insuficiente'], 422);
            }

            // Salvar as alterações no cartão
            $cartao->save();

            // 3. Salva a movimentação na tabela movimentacao_cliente_comercios
            $usuarioLogadoId = auth()->user()->id;
            $comercio = Comercio::where('users_id', $usuarioLogadoId)->first();

            if (!$comercio) {
                DB::rollBack();
                return response()->json(['error' => 'Usuário logado não associado a um comércio.'], 422);
            }


            // Salvar a movimentação na tabela movimentacao_cliente_comercios
            $movimentacaoClienteComercio = Movimentacao_cliente_comercio::create([
                'cartoes_id' => $cartao->id,
                'comercios_id' => $comercio->id,
                'valor' => $valorComDesconto,
                'valor_original' => $valor,
                'status' => 'ativo',
            ]);

            // Salvar os 5% descontados na tabela recebimentos_sat_bank         
            Recebimentos_sat_bank::create([
                'movimentacao_cliente_comercios_id' => $movimentacaoClienteComercio->id,
                'valor' => $valor - $valorComDesconto,
                'taxas_clientes' => $descontoCliente,
                'taxas_comercios' => $descontoEstabelecimento,
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Movimentação realizada com sucesso.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erro ao realizar a movimentação: ' . $e->getMessage()], 500);
        }
    }

    public function estornar(Request $request)
    {
        try {
            // 1. Obter movimentação a ser estornada
            $movimentacao = Movimentacao_cliente_comercio::find($request->id);
            if (!$movimentacao || $movimentacao->status !== 'ativo') {
                return response()->json(['error' => 'Movimentação não encontrada ou não ativa.'], 422);
            }

            // 2. Obter cartão associado à movimentação
            $cartao = Cartao::find($movimentacao->cartoes_id);
            if (!$cartao || $cartao->status !== 'ativo') {
                return response()->json(['error' => 'Cartão não encontrado ou bloqueado. Entre em contato com suporte.'], 422);
            }
            //começar transação de inserção no banco de dados 
            DB::beginTransaction();

            // 3. Devolver saldo ao cartão
            $valorOriginal = $movimentacao->valor_original; // Valor da transação original
            // Obter o valor de taxas_clientes da tabela recebimentos_sat_banks
            $taxasClientes = Recebimentos_sat_bank::where('movimentacao_cliente_comercios_id', $movimentacao->id)->value('taxas_clientes');
            // Adicionar as taxas do cliente ao valor de estorno
            $valorEstorno = $valorOriginal + $taxasClientes;
            $cartao->saldo += $valorEstorno;
            $cartao->save();

            // 4. Atualizar status na movimentacao_cliente_comercios
            $movimentacao->status = 'estornado';
            $movimentacao->save();

            // 5. Atualizar status na recebimentos_sat_bank
            $recebimentoSatBank = Recebimentos_sat_bank::where('movimentacao_cliente_comercios_id', $movimentacao->id)->first();

            if ($recebimentoSatBank) {
                $recebimentoSatBank->status = 'estornado';
                $recebimentoSatBank->save();
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Estorno realizado com sucesso.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erro ao realizar o estorno: ' . $e->getMessage()], 500);
        }
    }

    public function show(Movimentacao_cliente_comercio $movimentacao_cliente_comercio)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Movimentacao_cliente_comercio $movimentacao_cliente_comercio)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMovimentacao_cliente_comercioRequest $request, Movimentacao_cliente_comercio $movimentacao_cliente_comercio)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movimentacao_cliente_comercio $movimentacao_cliente_comercio)
    {
        //
    }
}

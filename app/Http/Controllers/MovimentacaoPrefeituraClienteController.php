<?php

namespace App\Http\Controllers;

use App\Models\Movimentacao_prefeitura_cliente;
use App\Http\Requests\StoreMovimentacao_prefeitura_clienteRequest;
use App\Http\Requests\UpdateMovimentacao_prefeitura_clienteRequest;
use App\Models\Cartao;
use App\Models\movimentacao_prefeitura;
use App\Models\Prefeitura;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MovimentacaoPrefeituraClienteController extends Controller
{
   
    public function index()
    {
        //
    }

   
    public function create()
    {
        //
    }

    public function alocarValorIndividual(StoreMovimentacao_prefeitura_clienteRequest $request){
         // 1. Pegar os dados do request        
         $tipoMovimentacao = $request->tipo;
         $saldo =  str_replace(",", ".", $request->saldo);
       

             // 2. Verificar o tipo de movimentação e atualizar o saldo da prefeitura
        $prefeiturasId = $request->input('prefeituras_id');
        $prefeitura = Prefeitura::find($prefeiturasId);

        if (!$prefeitura) {
            return response()->json(['error' => 'Prefeitura não encontrada.'], 404);
        }

        // Iniciar a transação
        DB::beginTransaction();

        try {
            // Atualizar o saldo na tabela movimentacao_prefeitura
            $movimentacaoRecente = movimentacao_prefeitura::where('prefeituras_id', $prefeiturasId)
                ->latest()
                ->first();

            if (!$movimentacaoRecente) {
                return response()->json(['error' => 'Não foi encontrada nenhuma movimentação recente.'], 400);
            }

            $saldoPrefeitura = $movimentacaoRecente->saldo;
            $valorMovimentadoTotal =   $saldo;

            if ($tipoMovimentacao === 'entrada') {
                $saldoAtualizado = $saldoPrefeitura - $valorMovimentadoTotal;
                $tipoMovimentacaoRegistrado = 'saida';
            } elseif ($tipoMovimentacao === 'saida') {
                $saldoAtualizado = $saldoPrefeitura + $valorMovimentadoTotal;
                $tipoMovimentacaoRegistrado = 'entrada';
            }
            //se o saldo for ficar negativo retorna o erro!
            if ($saldoAtualizado < 0) {
                return response()->json(['error' => 'Saldo da prefeitura insuficiente.'], 400);
            }
            movimentacao_prefeitura::create([
                'prefeituras_id' => $prefeitura->id,
                'tipo' => $tipoMovimentacaoRegistrado,
                'valor_alocado' => $valorMovimentadoTotal,
                'saldo' => $saldoAtualizado,
            ]);
            
            
            //atualizar o saldo na tabela cartoes

            $numeroCartao = $request->input('numero_cartao');
            $cartao = Cartao::where('numero_cartao', $numeroCartao)->first();
           
       
            
            $saldoCartaoAtual = $cartao->saldo;
            
            if ($tipoMovimentacao === 'entrada') {
                $saldoCartaoAtualizado = $saldoCartaoAtual + $saldo;
            } elseif ($tipoMovimentacao === 'saida') {
                // Verifica se há saldo suficiente para a movimentação de saída
                if ($valorMovimentadoTotal > $saldoCartaoAtual) {
                    return response()->json(['error' => 'Saldo insuficiente para a movimentação de saída.'], 400);
                }
            
                $saldoCartaoAtualizado = $saldoCartaoAtual - $saldo;
            } else {
                return response()->json(['error' => 'Tipo de movimentação inválido.'], 400);
            }
         
            // Atualizar o saldo na tabela de cartoes
            $cartao->update(['saldo' => $saldoCartaoAtualizado]);
            DB::commit();
            return response()->json(['message' => 'Movimentação registrada com sucesso'], 200);
        } catch (\Exception $e) {
            // Reverter a transação em caso de erro
            DB::rollback();       
           
            return response()->json(['error' => 'Erro ao processar a movimentação.'], 500);
        }
    }
 
    public function store(StoreMovimentacao_prefeitura_clienteRequest $request)
    {
        // 1. Pegar os dados do request
        $tipoCartao = $request->tipo_cartao;
        $tipoMovimentacao = $request->tipo;
        $valorMovimentadoIndividual =  str_replace(",", ".", $request->valor_movimentado_individual);

        // 2. Obter todos os cartões do tipo especificado e ativos
        $cartoes = Cartao::where('tipo_cartao', $tipoCartao)
            ->where('status', 'ativo')
            ->get();
        
        // 3. Calcular o valor total movimentado para distribuir entre os cartões
        $quantidadeCartoes = $cartoes->count();
        $valorMovimentadoTotal = $valorMovimentadoIndividual * $quantidadeCartoes;

        // 4. Verificar o tipo de movimentação e atualizar o saldo da prefeitura
        $prefeiturasId = $request->input('prefeituras_id');
        $prefeitura = Prefeitura::find($prefeiturasId);

        if (!$prefeitura) {
            return response()->json(['error' => 'Prefeitura não encontrada.'], 404);
        }

        // Iniciar a transação
        DB::beginTransaction();

        try {
            // 5. Atualizar o saldo na tabela movimentacao_prefeitura
            $movimentacaoRecente = movimentacao_prefeitura::where('prefeituras_id', $prefeiturasId)
                ->latest()
                ->first();

            if (!$movimentacaoRecente) {
                return response()->json(['error' => 'Não foi encontrada nenhuma movimentação recente.'], 400);
            }

            $saldoPrefeitura = $movimentacaoRecente->saldo;

            if ($tipoMovimentacao === 'entrada') {
                $saldoAtualizado = $saldoPrefeitura - $valorMovimentadoTotal;
                $tipoMovimentacaoRegistrado = 'saida';
            } elseif ($tipoMovimentacao === 'saida') {
                $saldoAtualizado = $saldoPrefeitura + $valorMovimentadoTotal;
                $tipoMovimentacaoRegistrado = 'entrada';
            }
            //se o saldo for ficar negativo retorna o erro!
            if ($saldoAtualizado < 0) {
                return response()->json(['error' => 'Saldo da prefeitura insuficiente.'], 400);
            }
            movimentacao_prefeitura::create([
                'prefeituras_id' => $prefeitura->id,
                'tipo' => $tipoMovimentacaoRegistrado,
                'valor_alocado' => $valorMovimentadoTotal,
                'saldo' => $saldoAtualizado,
            ]);
            // 6. Distribuir o valor entre os cartões
            $valorPorCartao = $valorMovimentadoTotal / $quantidadeCartoes;

            // Obtém o ID do usuário autenticado
            $userId = Auth::id();

            foreach ($cartoes as $cartao) {
                // Atualizar o saldo do cartão
                if ($tipoMovimentacao === 'entrada') {
                    $cartao->update(['saldo' => $cartao->saldo + $valorPorCartao]);
                } elseif ($tipoMovimentacao === 'saida') {
                    $cartao->update(['saldo' => $cartao->saldo - $valorPorCartao]);
                }
            }
            
            // Registrar a movimentação na tabela movimentacao_prefeitura_clientes       
            Movimentacao_prefeitura_cliente::create([
                'users_id' => $userId,
                'prefeituras_id' => $prefeitura->id,
                'tipo' => $tipoMovimentacao,
                'valor_movimentado_individual' => $valorPorCartao,
                'valor_movimentado' => $valorMovimentadoTotal
            ]);


            // 7. Atualizar o status para concluída na tabela movimentacao_prefeitura_cliente
            Movimentacao_prefeitura_cliente::where('tipo', $tipoMovimentacao)
                ->where('users_id', $userId)
                ->where('prefeituras_id', $prefeitura->id)
                ->update(['status' => 'concluida']);

            // Commit da transação se tudo estiver bem
            DB::commit();

            // 8. Retornar uma resposta de sucesso
            return response()->json(['message' => 'Movimentação registrada com sucesso'], 200);
        } catch (\Exception $e) {
            // Reverter a transação em caso de erro
            DB::rollback();
          
            return response()->json(['error' => 'Erro ao processar a movimentação.'], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Movimentacao_prefeitura_cliente $movimentacao_prefeitura_cliente)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Movimentacao_prefeitura_cliente $movimentacao_prefeitura_cliente)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMovimentacao_prefeitura_clienteRequest $request, Movimentacao_prefeitura_cliente $movimentacao_prefeitura_cliente)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movimentacao_prefeitura_cliente $movimentacao_prefeitura_cliente)
    {
        //
    }
}

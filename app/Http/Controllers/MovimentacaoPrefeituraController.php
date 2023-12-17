<?php

namespace App\Http\Controllers;

use App\Models\movimentacao_prefeitura;
use App\Http\Requests\Storemovimentacao_prefeituraRequest;
use App\Http\Requests\Updatemovimentacao_prefeituraRequest;
use App\Models\Prefeitura;


class MovimentacaoPrefeituraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $prefeituras = Prefeitura::all();
        $data = [];

        foreach ($prefeituras as $prefeitura) {
            $ultimoSaldo = Movimentacao_prefeitura::where('prefeituras_id', $prefeitura->id)
                ->orderBy('created_at', 'desc')
                ->value('saldo');

            if ($ultimoSaldo !== null) {
                $data[] = [
                    'razao_social' => $prefeitura->razao_social,
                    'saldo' => $ultimoSaldo,
                ];
            }
        }

        return $data;
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    private function formatarValorAlocado($valor)
    {
        // Remover máscara de valor
        return str_replace(',', '.', $valor);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Storemovimentacao_prefeituraRequest $request)
    {
        // Obter os dados do request
        $data = $request->all();
        // Tratar o campo valor_alocado no array de entrada
        $data['valor_alocado'] = $this->formatarValorAlocado($data['valor_alocado']);        

        // Buscar a prefeitura
        $prefeitura = Prefeitura::find($data['prefeituras_id']);

        // Verificar se a prefeitura foi encontrada
        if (!$prefeitura) {
            return response()->json(['error' => 'Prefeitura não encontrada'], 404);
        }
        //faz a busca para acessar saldo na tabela movimentaca_prefeitura
        $saldoAtual = movimentacao_prefeitura::where('prefeituras_id', $data['prefeituras_id'])
            ->orderBy('created_at', 'desc') // Ordena pela data de criação descrescente
            ->first();


        // Calcular o novo saldo com base no tipo de movimentação
        $novoSaldo = $data['tipo'] === 'entrada'
            ? $saldoAtual->saldo + floatval($data['valor_alocado'])
            : $saldoAtual->saldo - floatval($data['valor_alocado']);

        // Verificar se o novo saldo é menor que 0 em caso de saída
        if ($data['tipo'] === 'saida' && $novoSaldo < 0) {
            return response()->json(['error' => 'Saldo insuficiente para alocar saída'], 400);
        }
        $prefeituraId = $prefeitura->id;

        // Criar a movimentação
        $movimentacao = new movimentacao_prefeitura([
            'prefeituras_id' => $prefeituraId,
            'tipo' => $data['tipo'],
            'valor_alocado' => $data['valor_alocado'],
            'saldo' => $novoSaldo,
        ]);

        // Salvar a movimentação
        $movimentacao->save();



        return response()->json(['success' => 'Movimentação registrada com sucesso'], 201);
    }




    /**
     * Display the specified resource.
     */
    public function show(movimentacao_prefeitura $movimentacao_prefeitura)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(movimentacao_prefeitura $movimentacao_prefeitura)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Updatemovimentacao_prefeituraRequest $request, movimentacao_prefeitura $movimentacao_prefeitura)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(movimentacao_prefeitura $movimentacao_prefeitura)
    {
        //
    }
}

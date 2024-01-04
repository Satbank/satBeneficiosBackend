<?php

namespace App\Http\Controllers;

use App\Models\recebimentos_sat_bank;
use App\Http\Requests\Storerecebimentos_sat_bankRequest;
use App\Http\Requests\Updaterecebimentos_sat_bankRequest;
use Illuminate\Support\Facades\DB;

class RecebimentosSatBankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtém o mês em vigor
        $mesEmVigor = date('m');

        // Consulta para obter a soma das taxas de clientes ativas para o mês em vigor
        $taxasClientes = DB::table('recebimentos_sat_banks')
            ->whereMonth('created_at', $mesEmVigor)
            ->where('status', 'ativo') // Adiciona a condição de status ativo
            ->sum('taxas_clientes');

        // Consulta para obter a soma das taxas de comercios ativas para o mês em vigor
        $taxasComercios = DB::table('recebimentos_sat_banks')
            ->whereMonth('created_at', $mesEmVigor)
            ->where('status', 'ativo') // Adiciona a condição de status ativo
            ->sum('taxas_comercios');

        // Soma os valores das taxas de clientes e comercios
        $totalTaxas = $taxasClientes + $taxasComercios;

        // Retorna o resultado
        return response()->json(['total_taxas' => $totalTaxas]);
    }

    public function totalAno(){
        // Obtém o ano em vigor
        $anoEmVigor = date('Y');
    
        // Cria um array representando todos os meses do ano
        $todosMeses = range(1, 12);
    
        // Consulta para obter a soma das taxas de clientes ativas para cada mês do ano
        $totalTaxasPorMes = DB::table('recebimentos_sat_banks')
            ->select(DB::raw('MONTH(created_at) as mes'), DB::raw('SUM(taxas_clientes + taxas_comercios) as total_taxas'))
            ->whereYear('created_at', $anoEmVigor)
            ->where('status', 'ativo') // Adiciona a condição de status ativo
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();
    
        // Preenche os valores para todos os meses
        $totalTaxasPorMes = $totalTaxasPorMes->keyBy('mes')->map(function ($item) {
            return $item->total_taxas;
        })->toArray();
    
        // Preenche os valores para os meses sem registros
        foreach ($todosMeses as $mes) {
            if (!isset($totalTaxasPorMes[$mes])) {
                $totalTaxasPorMes[$mes] = 0;
            }
        }
    
        // Ordena os resultados pelos meses
        ksort($totalTaxasPorMes);
    
        // Retorna o resultado
        return response()->json(['total_taxas_por_mes' => $totalTaxasPorMes]);
    }
    
    public function totalCartoes(){
        $totalCartoesAtivos = DB::table('cartoes')
                                ->where('status', '=', 'ativo')
                                ->count();
    
        return $totalCartoesAtivos;
    }

    public function totalClienteBase(){
        $totalClienteBase = DB::table('clientes')                              
                                ->count();    
        return $totalClienteBase;
    }

    public function totalComerciosBase(){
        $totalComerciosBase = DB::table('comercios')                              
                                ->count();    
        return $totalComerciosBase;
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
    public function store(Storerecebimentos_sat_bankRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(recebimentos_sat_bank $recebimentos_sat_bank)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(recebimentos_sat_bank $recebimentos_sat_bank)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Updaterecebimentos_sat_bankRequest $request, recebimentos_sat_bank $recebimentos_sat_bank)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(recebimentos_sat_bank $recebimentos_sat_bank)
    {
        //
    }
}

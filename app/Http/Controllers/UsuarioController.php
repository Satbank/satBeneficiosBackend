<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use Illuminate\Http\Request;
use App\Models\User;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $user = $request->user();

        if ($user) {
            $perfil_id = $user->perfil_id;

            switch ($perfil_id) {
                case 1:
                    $userWithAdminData = User::with(['admin' => function ($query) {
                        $query->select('users_id', 'nome', 'telefone');
                    }])->find($user->id);

                    // Se houver um administrador associado, extrai o nome e telefone
                    $adminNome = $userWithAdminData->admin ? $userWithAdminData->admin->nome : null;
                    $adminTelefone = $userWithAdminData->admin ? $userWithAdminData->admin->telefone : null;

                    // Remove a relação 'admin' do objeto principal
                    unset($userWithAdminData->admin);

                    // Adiciona o nome e telefone do administrador diretamente no objeto principal
                    $userWithAdminData->nome = $adminNome;
                    $userWithAdminData->telefone = $adminTelefone;

                    return response()->json($userWithAdminData);
                    break;

                case 2:
                    $userWithContratanteData = User::with(['prefeitura' => function ($query) {
                        $query->select('users_id', 'razao_social', 'telefone');
                    }])->find($user->id);

                    // Se houver um contratante associado, extrai a razão social e o telefone
                    $razaoSocial = $userWithContratanteData->contratante ? $userWithContratanteData->contratante->razao_social : null;
                    $telefoneContratante = $userWithContratanteData->contratante ? $userWithContratanteData->contratante->telefone : null;

                    // Remove a relação 'contratante' do objeto principal
                    unset($userWithContratanteData->contratante);

                    // Adiciona a razão social e o telefone do contratante diretamente no objeto principal
                    $userWithContratanteData->razao_social = $razaoSocial;
                    $userWithContratanteData->telefone_contratante = $telefoneContratante;

                    return response()->json($userWithContratanteData);
                    break;

                case 3:
                    $userWithComercioData = User::with(['comercio' => function ($query) {
                        $query->select('users_id', 'razao_social', 'telefone');
                    }])->find($user->id);

                    // Se houver um comércio associado, extrai a razão social e o telefone
                    $razaoSocialComercio = $userWithComercioData->comercio ? $userWithComercioData->comercio->razao_social : null;
                    $telefoneComercio = $userWithComercioData->comercio ? $userWithComercioData->comercio->telefone : null;

                    // Remove a relação 'comercio' do objeto principal
                    unset($userWithComercioData->comercio);

                    // Adiciona a razão social e o telefone do comércio diretamente no objeto principal
                    $userWithComercioData->razao_social = $razaoSocialComercio;
                    $userWithComercioData->telefone = $telefoneComercio;

                    return response()->json($userWithComercioData);
                    break;


                case 4:
                    $userWithClienteData = User::with(['cliente' => function ($query) {
                        $query->select('users_id', 'nome', 'telefone');
                    }])->find($user->id);

                    // Se houver um cliente associado, extrai o nome e o telefone
                    $nomeCliente = $userWithClienteData->cliente ? $userWithClienteData->cliente->nome : null;
                    $telefoneCliente = $userWithClienteData->cliente ? $userWithClienteData->cliente->telefone : null;

                    // Remove a relação 'cliente' do objeto principal
                    unset($userWithClienteData->cliente);

                    // Adiciona o nome e o telefone do cliente diretamente no objeto principal
                    $userWithClienteData->nome = $nomeCliente;
                    $userWithClienteData->telefone = $telefoneCliente;

                    return response()->json($userWithClienteData);
                    break;





                default:
                    return response()->json(['message' => 'Perfil desconhecido'], 400);
                    break;
            }
        } else {
            return response()->json(['message' => 'Usuário não autenticado'], 401);
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
    public function store(StoreUsuarioRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Usuario $usuario)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Usuario $usuario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUsuarioRequest $request, Usuario $usuario)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Usuario $usuario)
    {
        //
    }
}

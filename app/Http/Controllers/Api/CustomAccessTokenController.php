<?php

namespace App\Http\Controllers\Api;

use Laravel\Passport\Http\Controllers\AccessTokenController as BaseAccessTokenController;
use Psr\Http\Message\ServerRequestInterface;


class CustomAccessTokenController extends BaseAccessTokenController
{
    /**
     * Issue an access token.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface  $request
     * @return \Illuminate\Http\Response
     */
    public function issueToken(ServerRequestInterface $request)
    {
        // Chame o método da classe pai para obter a resposta padrão
        $response = parent::issueToken($request);

        // Personalize a resposta adicionando os dados do usuário, se disponíveis
        $user = $request->getAttribute('oauth_user_id');

        if ($user) {
            $responseData = json_decode($response->getContent(), true);
            $responseData['user'] = $user;
            
            // Atualize a resposta com os dados do usuário
            $response->setContent(json_encode($responseData));
        }

        return $response;
    }
}

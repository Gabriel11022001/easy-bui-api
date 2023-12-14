<?php

namespace App\Utils;

class Resposta
{

    public static function resposta($mensagem, $dados = null, $codigoHttp = 200, $ok = true) {

        return response()
            ->json([
                'mensagem' => $mensagem,
                'dados' => $dados,
                'ok' => $ok
            ], $codigoHttp);
    }
}
<?php

namespace App\Utils;

class ValidaSexoUtils
{
    private static $sexos = [
        'Masculino',
        'Feminino',
        'Outro',
        'Não desejo informar'
    ];

    public static function validarSexo($sexoInformado) {
        $sexosTodosMaiusculos = [];

        foreach (self::$sexos as $sexo) {
            $sexoMaiusculo = mb_strtoupper($sexo);
            $sexosTodosMaiusculos[] = $sexoMaiusculo;
        }
        
        $sexoInformado = mb_strtoupper($sexoInformado);

        return in_array($sexoInformado, $sexosTodosMaiusculos);
    }
}
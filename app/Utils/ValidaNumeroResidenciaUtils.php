<?php

namespace App\Utils;

class ValidaNumeroResidenciaUtils
{

    public static function validarNumeroResidencia($numero) {

        if (!empty($numero)) {
            
            if (is_numeric($numero)) {

                if ($numero <= 0) {
    
                    return 'Número de residência inválido!';
                }
    
            } else {
                $numero = mb_strtoupper($numero);
    
                if ($numero != 'S/N') {
    
                    return 'Número de residência inválido!';
                }
    
            }

        }

        return '';
    }
}
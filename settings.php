<?php

$dblite = new SQLite3(__DIR__ . '../db.db');
#$dblite = new SQLite3('db.db');
/* Teste conexão
if (!$dblite) {
    echo "Erro ao conectar ao banco de dados: " . $dblite->lastErrorMsg();
    echo "Código do erro: " . $dblite->lastErrorCode();
} else {
    echo "Conexão bem-sucedida!";
}
*/




#if($dblite->sqlite3_system_errno)
#    echo "Falha na conexão: (".$dblite->connect_errno.") ".$dblite->connect_error;


    function validaCPF($cpf) {
 
        // Extrai somente os números
        $cpf = preg_replace( '/[^0-9]/is', '', $cpf );
         
        // Verifica se foi informado todos os digitos corretamente
        if (strlen($cpf) != 11) {
            return false;
        }
    
        // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
    
        // Faz o calculo para validar o CPF
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }

    function validaData($data, $formato = 'd-m-Y') {

    $d = DateTime::createFromFormat($formato, $data);

    return $d && $d->format($formato) == $data;

}
?>
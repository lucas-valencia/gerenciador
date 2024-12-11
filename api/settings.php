<?php

//$dblite = new SQLite3(__DIR__ . '../db.db');
$dblite = new SQLite3('sqlite:api/db.db');
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
    global $dblite; // Torna a conexão com o banco disponível dentro da função

    // Extrai somente os números
    $cpf = preg_replace('/[^0-9]/is', '', $cpf);

    // Verifica se o CPF tem 11 dígitos
    if (strlen($cpf) != 11) {
        return "CPF inválido"; // Retorna mensagem específica
    }

    // Verifica se todos os dígitos são iguais, como "111.111.111-11"
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return "CPF inválido"; // Retorna mensagem específica
    }

    // Verifica no banco se o CPF já está cadastrado
    $stmt = $dblite->prepare("SELECT COUNT(*) as count FROM clientes WHERE cpf = :cpf");
    $stmt->bindValue(':cpf', $cpf, SQLITE3_TEXT);
    $result = $stmt->execute();
    $row = $result->fetchArray(SQLITE3_ASSOC);

    if ($row['count'] > 0) {
        return "CPF já cadastrado"; // Retorna mensagem específica
    }

    // Valida o CPF com o cálculo de verificação
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return "CPF inválido"; // Retorna mensagem específica
        }
    }

    return true; // CPF válido
}

function validaData($data, $formato = 'd-m-Y')
{

    $d = DateTime::createFromFormat($formato, $data);

    return $d && $d->format($formato) == $data;
}

function validaCEP($cep) {

    $cep = preg_replace('/[^0-9]/', '', $_POST['cep']);

    if (empty($cep) || strlen($cep) != 8) {
        return "CPF inválido";
    } elseif (preg_match('/(\d)\1{10}/', $cep)) {
        return "CEP inválido!";
    }
    return true; //CEP válido

}
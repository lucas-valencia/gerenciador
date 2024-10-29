<?php

include(__DIR__ . "/../settings.php");

$cli_codigo = intval($_GET['cliente']);

$sqlite_code = "DELETE FROM clientes WHERE codigo = '$cli_codigo'";
#$sqlite_query = $dblite->query($sqlite_code) or die($dblite->lastErrorMsg());
$statement = $dblite->prepare($sqlite_code);
$statement->bindValue(':codigo', $cli_codigo, SQLITE3_INTEGER);

    if ($statement->execute()) {
        echo "
            <script>
                alert('Usuário deletado com sucesso.'); 
                location.href='../index.php?p=consulta'; 
            </script>";
    }else{
        echo "
            <script> 
                alert('Não foi possivel deletar o usuário'); 
                location.href='../index.php?p=consulta'; 
            </script>";

    }




?>
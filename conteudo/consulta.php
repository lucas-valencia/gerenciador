<?php

include(__DIR__ . "/../settings.php");

$genero[1] = "Masculino";
$genero[2] = "Feminino";
$genero[3] = "Outro";

/*echo "<h2>Colunas da tabela 'clientes':</h2>";

$columns = $dblite->query("PRAGMA table_info(clientes)");
if ($columns) {
    echo "<ul>";
    while ($column = $columns->fetchArray(SQLITE3_ASSOC)) {
        echo "<li>" . htmlspecialchars($column['name']) . "</li>";
    }
    echo "</ul>";
} else {
    echo "Erro ao obter informações das colunas: " . $dblite->lastErrorMsg();
}
*/

$sqlite_query = $dblite->query("SELECT * FROM clientes") or die($dblite->lastErrorMsg());
#$linha = $sqlite_query->fetchArray();
#var_dump($linha);
#var_dump($sqlite_query);

?>

<h1>CLIENTES</h1>
<div class="menu">
        <a class="menu-item" href="../index.php?p=cadastro">CADASTRO</a>

        <a class="menu-item" href="../index.php?p=consulta">CONSULTA</a>

        <a class="menu-item" href="../index.php?p=inicial">VOLTAR</a>
    </div>
<p class="espaco"></p>
<div class="tabela-container"> <!-- Contêiner para rolagem -->
    <table border="1" cellpadding="10">
        <tr class="titulo">
            <td>ID</td>
            <td>Nome</td>
            <td>Sobrenome</td>
            <td>CPF</td>
            <td>Nascimento</td>
            <td>Gênero</td>
            <td>CEP</td>
            <td>Logradouro</td>
            <td>Bairro</td>
            <td>Cidade</td>
            <td>Estado</td>
            <td>Celular</td>
            <td>E-mail</td>
            <td>Ação</td>
        </tr>

        <?php
        if ($sqlite_query && $linha = $sqlite_query->fetchArray(SQLITE3_ASSOC)) {
            do {
        ?>
                <tr>
                    <td><?php echo $linha['codigo']; ?></td>
                    <td><?php echo $linha['nome']; ?></td>
                    <td><?php echo $linha['sobrenome']; ?></td>
                    <td><?php echo $linha['cpf']; ?></td>
                    <td><?php
                        $data = explode("-", $linha['data_nascimento']);

                        echo "$data[2]/$data[1]/$data[0]";
                        ?></td>
                    <td><?php echo $genero[$linha['genero']]; ?></td>
                    <td><?php echo $linha['cep']; ?></td>
                    <td><?php echo $linha['logradouro']; ?></td>
                    <td><?php echo $linha['bairro']; ?></td>
                    <td><?php echo $linha['cidade']; ?></td>
                    <td><?php echo $linha['estado']; ?></td>
                    <td><?php echo $linha['celular']; ?></td>
                    <td><?php echo $linha['email']; ?></td>
                    <td>
                        <a href="../index.php?p=editar&cliente=<?php echo $linha['codigo']; ?>">Editar</a>|
                        <a href="javascript: if(confirm('Deletar usuário <?php echo $linha['nome']; ?>'))
                    location.href='../index.php?p=deletar&cliente=<?php echo $linha['codigo']; ?>?';">Deletar</a>
                    </td>
                </tr>
        <?php } while ($linha = $sqlite_query->fetchArray());
        } else {
            echo "<tr><td colspan='13' text-align:center>Nenhum cliente encontrado.</td></tr>";
        }
        ?>
    </table>
</div>
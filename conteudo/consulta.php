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
<?php if (isset($error_message)): ?>
    <div class="error"><?php echo $error_message; ?></div>
<?php endif; ?>

<h1>CLIENTES</h1>

<div class="menu_consulta">
    <a class="menu_consulta-item" href="../index.php?p=cadastro">NOVO CADASTRO</a>

    <a class="menu_consulta-item" href="../index.php?p=inicial">VOLTAR</a>
</div>
<p class="espaco"></p>
<!-- Barra de Pesquisa -->
<form method="get" action="../index.php" class="formulario_pesquisa">
    <input type="hidden" name="p" value="consulta"> <!-- Adiciona o parâmetro para redirecionar para consulta.php -->
    <input type="text" name="search" placeholder="Buscar cliente..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" class="barra_pesquisa">
    <input type="submit" value="Pesquisar">
</form>
<?php
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Consulta SQL com filtro condicional para a pesquisa
$query = "SELECT * FROM clientes";
if (!empty($search)) {
    $query .= " WHERE nome LIKE '%$search%' OR sobrenome LIKE '%$search%' OR cpf LIKE '%$search%' 
                OR genero LIKE '%$search%' OR cep LIKE '%$search%' OR logradouro LIKE '%$search%' 
                OR bairro LIKE '%$search%' OR cidade LIKE '%$search%' OR estado LIKE '%$search%' 
                OR celular LIKE '%$search%' OR email LIKE '%$search%'";
}

$sqlite_query = $dblite->query($query) or die($dblite->lastErrorMsg());
?>

<div class="tabela-container"> <!-- Contêiner para rolagem -->
    <table border="1" cellpadding="10">
        <tr class="titulo">
            <td>Ação</td>
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
        </tr>

        <?php
        if ($sqlite_query && $linha = $sqlite_query->fetchArray(SQLITE3_ASSOC)) {
            do {
        ?>
                <tr>
                    <td>
                        <a href="../index.php?p=editar&cliente=<?php echo $linha['codigo']; ?>">Editar</a><br>
                        <a href="javascript: if(confirm('Deletar usuário <?php echo $linha['nome']; ?>?'))
                    location.href='../index.php?p=deletar&cliente=<?php echo $linha['codigo']; ?>?';">Deletar</a>
                    </td>
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
                </tr>
        <?php } while ($linha = $sqlite_query->fetchArray());
        } else {
            echo "<tr><td colspan='13' text-align:center>Nenhum cliente encontrado.</td></tr>";
        }
        ?>
    </table>
</div>
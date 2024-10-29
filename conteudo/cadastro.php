<?php

include(__DIR__ . "/../settings.php");

/* Listagem colunas
echo "<h2>Colunas da tabela 'clientes':</h2>";
#var_dump($dblite->query("SELECT * FROM clientes"));

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

$erro = [];

if (isset($_POST['confirmar'])) {

    // 1- Registro dos dados
    if (!isset($_SESSION)) {
        session_start();
    }

    if (isset($_POST['confirmar'])) {
        foreach ($_POST as $chave => $valor)
            $_SESSION[$chave] = $valor;
    }

    // 2 - Validação dos dados

    if (strlen($_SESSION['nome']) == 0)
        $erro[] = "Preecha o nome!";

    if (strlen($_SESSION['sobrenome']) == 0)
        $erro[] = "Preecha o sobrenome!";

    if (validaCPF($_SESSION['cpf']) == false)
        $erro[] = "Informe um CPF válido!";

    if (validaData($_SESSION['data_nascimento']) == true)
        $erro[] = "Informe uma data válida!";

    if (strlen($_SESSION['genero']) == "")
        $erro[] = "Preecha o gênero!";

    if (strlen($_SESSION['logradouro']) == 0)
        $erro[] = "Preecha o logradouro!";

    if (strlen($_SESSION['bairro']) == 0)
        $erro[] = "Preecha o bairro!";

    if (strlen($_SESSION['cidade']) == 0)
        $erro[] = "Preecha a cidade!";

    if (strlen($_SESSION['estado']) == 0)
        $erro[] = "Preecha o estado!";

    if (strlen($_SESSION['celular']) != 11)
        $erro[] = "Preecha o número de celular com 11 dígitos: DD000000000";

    if (substr_count($_SESSION['email'], '@') != 1 || substr_count($_SESSION['email'], '.') < 1)
        $erro[] = "Preencha o e-mail corretamente!";

    // 3 - Inserção no banco e redirecionamento
    /* Inserção com query
        if(count($erro ) == 0) {
            
            $sql_code = "INSERT INTO clientes (
            nome, 
            sobrenome, 
            cpf, 
            data_nascimento, 
            genero, 
            cep, 
            logradouro, 
            bairro, 
            cidade, 
            estado, 
            celular, 
            email)
            VALUES(
            '$_SESSION[nome]',
            '$_SESSION[sobrenome]',
            '$_SESSION[cpf]',
            '$_SESSION[data_nascimento]',
            '$_SESSION[genero]',
            '$_SESSION[cep]',
            '$_SESSION[logradouro]',
            '$_SESSION[bairro]',
            '$_SESSION[cidade]',
            '$_SESSION[estado]',
            '$_SESSION[celular]',
            '$_SESSION[email]'
            )";

            $confirma = $dblite->query($sql_code) or die($dblite->lastErrorCode());

            if ($confirma){
                unset($_SESSION['nome'],
                $_SESSION['sobrenome'],
                $_SESSION['cpf'],
                $_SESSION['data_nascimento'],
                $_SESSION['genero'],
                $_SESSION['cep'],
                $_SESSION['logradouro'],
                $_SESSION['bairro'],
                $_SESSION['cidade'],
                $_SESSION['estado'],
                $_SESSION['celular'],
                $_SESSION['email']
                );

                echo "<script> location.href='index.php?p=cadastro'; </script>";
            }else
                $erro[] = $confirma;

        }

    */
    #Inserção com statement
    if (count($erro) == 0) {

        $statement = $dblite->prepare("INSERT INTO clientes (
            nome,
            sobrenome,
            cpf,
            data_nascimento,
            genero,
            cep,
            logradouro,
            bairro,
            cidade,
            estado,
            celular,
            email
            ) VALUES (
                :nome,
                :sobrenome,
                :cpf,
                :data_nascimento,
                :genero,
                :cep,
                :logradouro,
                :bairro,
                :cidade,
                :estado,
                :celular,
                :email
            )");

        $statement->bindValue(':nome', $_SESSION['nome']);
        $statement->bindValue(':sobrenome', $_SESSION['sobrenome']);
        $statement->bindValue(':cpf', $_SESSION['cpf']);
        $statement->bindValue(':data_nascimento', $_SESSION['data_nascimento']);
        $statement->bindValue(':genero', $_SESSION['genero']);
        $statement->bindValue(':cep', $_SESSION['cep']);
        $statement->bindValue(':logradouro', $_SESSION['logradouro']);
        $statement->bindValue(':bairro', $_SESSION['bairro']);
        $statement->bindValue(':cidade', $_SESSION['cidade']);
        $statement->bindValue(':estado', $_SESSION['estado']);
        $statement->bindValue(':celular', $_SESSION['celular']);
        $statement->bindValue(':email', $_SESSION['email']);

        $confirma = $statement->execute();

        if ($confirma) {

            unset(
                $_SESSION['nome'],
                $_SESSION['sobrenome'],
                $_SESSION['cpf'],
                $_SESSION['data_nascimento'],
                $_SESSION['cep'],
                $_SESSION['logradouro'],
                $_SESSION['bairro'],
                $_SESSION['cidade'],
                $_SESSION['estado'],
                $_SESSION['celular'],
                $_SESSION['email']
            );

            echo "<script> location.href='index.php?p=cadastro'; </script>";
        } else {
            $erro[] = "Erro ao inserir os dados no banco de dados";
        }

        $statement->close();
    }
}

?>

<h1>CADASTRO DE CLIENTE</h1>

<?php

if (count($erro) > 0) {
    echo "<div class='erro'>
        <p>Verifique os seguintes erros:</P>";
    foreach ($erro as $valor)
        echo "$valor <br>";
    echo "<br></div>";
}
?>

<form action="../index.php?p=cadastro" method="POST" class="formulario">
    <div class="linha_form">
        <label for="nome">Nome:</label>
        <input name="nome" value="<?php echo isset($_SESSION['nome']) ? htmlspecialchars($_SESSION['nome']) : ''; ?>" required type="text">
        
        <label for="sobrenome">Sobrenome:</label>
        <input name="sobrenome" value="<?php echo isset($_SESSION['sobrenome']) ? htmlspecialchars($_SESSION['sobrenome']) : ''; ?>" required type="text">
    </div>
    <p class="espaco"></p>
    <div class="linha_form">
        <label for="cpf">CPF:</label>
        <input name="cpf" value="<?php echo isset($_SESSION['cpf']) ? htmlspecialchars($_SESSION['cpf']) : ''; ?>" required type="number">

        <label for="data_nascimento">Data de Nascimento:</label>
        <input name="data_nascimento" value="<?php echo isset($_SESSION['data_nascimento']) ? htmlspecialchars($_SESSION['data_nascimento']) : ''; ?>" required type="date">
    </div>
    <p class="espaco"></p>
    <div class="linha_form">
        <label for="genero">Gênero:</label>
        <select name="genero">
            <option value="">Selecione</option>
            <option value="1" <?php echo (isset($_SESSION['genero']) && $_SESSION['genero'] == '1') ? 'selected' : ''; ?>>Masculino</option>
            <option value="2" <?php echo (isset($_SESSION['genero']) && $_SESSION['genero'] == '2') ? 'selected' : ''; ?>>Feminino</option>
            <option value="3" <?php echo (isset($_SESSION['genero']) && $_SESSION['genero'] == '3') ? 'selected' : ''; ?>>Outro</option>
        </select>

        <label for="cep">CEP:</label>
        <input name="cep" value="<?php echo isset($_SESSION['cep']) ? htmlspecialchars($_SESSION['cep']) : ''; ?>" required type="number">
    </div>
    <p class="espaco"></p>
    <div class="linha_form">
        <label for="logradouro">Logradouro:</label>
        <input name="logradouro" value="<?php echo isset($_SESSION['logradouro']) ? htmlspecialchars($_SESSION['logradouro']) : ''; ?>" required type="text">

        <label for="bairro">Bairro:</label>
        <input name="bairro" value="<?php echo isset($_SESSION['bairro']) ? htmlspecialchars($_SESSION['bairro']) : ''; ?>" required type="text">
    </div>
    <p class="espaco"></p>
    <div class="linha_form">
        <label for="cidade">Cidade:</label>
        <input name="cidade" value="<?php echo isset($_SESSION['cidade']) ? htmlspecialchars($_SESSION['cidade']) : ''; ?>" required type="text">
        <p class="espaco"></p>

        <label for="estado">Estado:</label>
        <input name="estado" value="<?php echo isset($_SESSION['estado']) ? htmlspecialchars($_SESSION['estado']) : ''; ?>" required type="text">
    </div>
    <p class="espaco"></p>

    <div class="linha_form">
        <label for="celular">Celular:</label>
        <input name="celular" value="<?php echo isset($_SESSION['celular']) ? htmlspecialchars($_SESSION['celular']) : ''; ?>" required type="number">

        <label for="email">E-mail:</label>
        <input name="email" value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>" required type="email">
    </div>
        <p class="espaco"></p>

    <input value="Salvar" name="confirmar" type="submit">
    <a href="../index.php" class="botao_voltar_cadastro">Voltar</a>


</form>
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
$cep = isset($_SESSION['cep']) ? $_SESSION['cep'] : '';
$logradouro = isset($_SESSION['logradouro']) ? $_SESSION['logradouro'] : '';
$bairro = isset($_SESSION['bairro']) ? $_SESSION['bairro'] : '';
$cidade = isset($_SESSION['cidade']) ? $_SESSION['cidade'] : '';
$estado = isset($_SESSION['estado']) ? $_SESSION['estado'] : '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['pesquisar_cep'])) {
        if (!isset($_SESSION)) {
            session_start();
        }
// Carrega os valores já preenchidos nos campos
        if (isset($_POST['pesquisar_cep'])) {
            foreach ($_POST as $chave => $valor)
                $_SESSION[$chave] = $valor;
        }

        $cep = preg_replace('/[^0-9]/', '', $_POST['cep']); // Remove caracteres não numéricos

        // Verifica se o CEP é vazio ou não tem 8 dígitos
        if (empty($cep) || strlen($cep) !== 8) {
            $error_message = "Por favor, insira um CEP válido com 8 dígitos.";
            $erro[] = $error_message;
        } else {
            $url = "https://viacep.com.br/ws/{$cep}/json/";

            // Obter os dados do CEP
            $response = @file_get_contents($url);

            if ($response === FALSE) {
                $error_message = "Erro ao buscar informações para o CEP fornecido.";
                $erro[] = $error_message;
            } else {
                $data = json_decode($response, true);
                if (isset($data['erro'])) {
                    $error_message = "CEP não encontrado.";
                    $erro[] = $error_message;
                } else {

                    // Preenche automaticamente os campos com os dados retornados
                    $_SESSION['logradouro'] = $data['logradouro'];
                    $_SESSION['bairro'] = $data['bairro'];
                    $_SESSION['cidade'] = $data['localidade'];
                    $_SESSION['estado'] = $data['uf'];
                }
            }
        }
    } else {
        // Caso o formulário seja enviado, armazena os valores do CEP
        $_SESSION['cep'] = $_POST['cep'];
    }
}

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
        $erro[] = "Preencha o nome!";

    if (strlen($_SESSION['sobrenome']) == 0)
        $erro[] = "Preencha o sobrenome!";

    $cpfValidacao = validaCPF($_SESSION['cpf']);
    if ($cpfValidacao !== true) {
        $erro[] = $cpfValidacao; // Adiciona a mensagem específica de erro ao array de erros
    }

    if (validaData($_SESSION['data_nascimento']) == true)
        $erro[] = "Informe uma data válida!";

    if (strlen($_SESSION['genero']) == "")
        $erro[] = "Preecha o gênero!";

    $cepValidacao = validaCEP($_SESSION['cep']);
    if ($cepValidacao !== true) {
        $erro[] = $cepValidacao;
    }

    if (strlen($_SESSION['logradouro']) == 0)
        $erro[] = "Preecha o logradouro!";

    if (strlen($_SESSION['bairro']) == 0)
        $erro[] = "Preecha o bairro!";

    if (strlen($_SESSION['cidade']) == 0)
        $erro[] = "Preecha a cidade!";

    if (strlen($_SESSION['estado']) == 0)
        $erro[] = "Preecha o estado!";

    if (strlen($_SESSION['celular']) != 14)
        $erro[] = "Preecha o número de celular conforme o requisito: (DD)XXXXX-XXXX";

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
        $_SESSION['cpf'] = preg_replace('/[^0-9]/', '', $_POST['cpf']);
        $_SESSION['cep'] = preg_replace('/[^0-9]/', '', $_POST['cep']);
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
        <input placeholder="Ex.: Maria" name="nome" value="<?php echo isset($_SESSION['nome']) ? htmlspecialchars($_SESSION['nome']) : ''; ?>" type="text">

        <label for="sobrenome">Sobrenome:</label>
        <input placeholder="Ex.: Silva" name="sobrenome" value="<?php echo isset($_SESSION['sobrenome']) ? htmlspecialchars($_SESSION['sobrenome']) : ''; ?>" type="text">
    </div>
    <p class="espaco"></p>
    <div class="linha_form">
        <label for="cpf">CPF:</label>
        <input placeholder="Apenas números" name="cpf" value="<?php echo isset($_SESSION['cpf']) ? htmlspecialchars($_SESSION['cpf']) : ''; ?>" type="">

        <label for="data_nascimento">Data de Nascimento:</label>
        <input name="data_nascimento" value="<?php echo isset($_SESSION['data_nascimento']) ? htmlspecialchars($_SESSION['data_nascimento']) : ''; ?>" type="date">
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
        <input placeholder="Apenas números" name="cep" value="<?php echo isset($_SESSION['cep']) ? htmlspecialchars($_SESSION['cep']) : ''; ?>" id="cep" type="text" pattern="\d{8}" title="Digite 8 dígitos">
        <button type="submit" name="pesquisar_cep" class="botao_pesquisar" onclick="this.innerHTML='Pesquisando...';">Pesquisar</button>
    </div>
    <p class="espaco"></p>
    <div class="linha_form">
        <label for="logradouro">Logradouro:</label>
        <input id="logradouro" name="logradouro" value="<?php echo isset($_SESSION['logradouro']) ? htmlspecialchars($_SESSION['logradouro']) : ''; ?>" type="text">

        <label for="numero">N°:</label>
        <input id="numero" name="numero" value="<?php echo isset($_SESSION['numero']) ? htmlspecialchars($_SESSION['numero']) : ''; ?>" type="number">

        <label for="bairro">Bairro:</label>
        <input id="bairro" name="bairro" value="<?php echo isset($_SESSION['bairro']) ? htmlspecialchars($_SESSION['bairro']) : ''; ?>" type="text">
    </div>
    <p class="espaco"></p>
    <div class="linha_form">
        <label for="cidade">Cidade:</label>
        <input id="cidade" name="cidade" value="<?php echo isset($_SESSION['cidade']) ? htmlspecialchars($_SESSION['cidade']) : ''; ?>" type="text">
        <p class="espaco"></p>

        <label for="estado">Estado:</label>
        <input id="estado" name="estado" value="<?php echo isset($_SESSION['estado']) ? htmlspecialchars($_SESSION['estado']) : ''; ?>" type="text">
    </div>
    <p class="espaco"></p>

    <div class="linha_form">
        <label for="celular">Celular:</label>
        <input placeholder="(DD)XXXX-XXXX" name="celular" value="<?php echo isset($_SESSION['celular']) ? htmlspecialchars($_SESSION['celular']) : ''; ?>" type="tel" pattern="[()][0-9]{2}-[0-9]{5}-[-][0-9]{4}">

        <label for="email">E-mail:</label>
        <input name="email" value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>" type="email">
    </div>
    <p class="espaco"></p>
    <div class="botoes_cadastro">
        <input value="Salvar" name="confirmar" type="submit">
        <a href="../index.php" class="botao_voltar_cadastro">Voltar</a>
    </div>

</form>
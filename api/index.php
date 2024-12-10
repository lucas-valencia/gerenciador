<?php
phpinfo();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de usuários</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <div class=principal>

    <?php
        if(isset($_GET['p'])){
            $pagina = $_GET['p'].".php"; 
            #var_dump($pagina);
            if(is_file("conteudo/$pagina"))
                include("conteudo/$pagina");
            else
                include("conteudo/404.php");

        }else
            include("conteudo/inicial.php");
    ?>


    </div>
    
</body>
</html>
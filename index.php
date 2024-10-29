<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de usuários</title>
    <link rel="stylesheet" href="estilo.css">
    <!--Estilo dev
    <style>
        .principal{
            width: 100%;
            margin: 0 auto;
            background-color:#FFF;
            border: 1px solid #e3e3e3;
            border-radius: 5px;
        }
        body{
            background:#eaeaea;
            padding: 20px;
            font-family: Arial;
            font-size: 18px;
        }
        label{
            display: block;
            font-weight: bold;
        }
        /*.espaco{
            height: 5px; display: block;
        }*/
        input{
            font-size: 16px;
            padding: 5px;
        }
        .titulo{
            font-weight: bold;
            text-align: center;
        }
        .menu{
            width: 300px;
            margin: 20px auto;
            text-align: center;
        }
        .menu-item {
        display: block;
        padding: 10px;
        background-color: #f0f0f0;
        margin: 5px 0;
        text-decoration: none;
        color: #000;
        border-radius: 5px;
        transition: background-color 0.3s;
        }
        .menu-item:hover {
        background-color: #ddd;
        }
    </style>
    -->

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
<?php
$pedidoAjax = true;

require_once "../config/config.php";

if(isset($_POST['metodo'])){
    require_once "../controllers/MainController.php";
    $controllerObj = new MainController();
    switch ($_POST['metodo']){
        case "adicionaFilho":
            echo $controllerObj->cadastraPessoas($_POST);
            break;

        case "lerDados":
            echo $controllerObj->lerDados();
            break;
    }
}
?>

<?php

require_once("../config/db_connect.php");
session_start();

/* Check et envoi de la requete */

function data_check($data) {
    if (strncmp($data, "data:image/png;base64", 21)) {?>
        <script type="text/javascript">console.log("invalid data provided, please try again");</script>;<?php
        header('refresh:0;url=../index.php');
        return 0;
    }
    return 1;
}

 if (isset($_POST['img']) && isset($_SESSION['id']) && $_SESSION['id'] !== "" && data_check(($_POST['img']))) {
    $sid = $_SESSION['id'];
    $img = str_replace(' ', '+', $_POST['img']);
    $sql = "INSERT INTO `gallery`(`owner_id`, `data`, `likes`) VALUES (:sid, :img, '0')";
    $res = $db->prepare($sql);
    $res->bindValue(':img', $img, PDO::PARAM_STR);
    $res->bindValue(':sid', $sid, PDO::PARAM_INT);
    $res->execute();
}

else {
    header('refresh:0;url=../pages/404.php', null, 404);
}

?>

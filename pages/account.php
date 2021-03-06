<?php
    session_start();
    require_once('../config/db_connect.php');


    function isNotified($db, $usr) {
        $sql = "SELECT notif FROM `users` WHERE id = :user";
        $res = $db->prepare($sql);
        $res->bindParam(':user', $usr);
        $res->execute();
        try {
            $obj = $res->fetchAll(PDO::FETCH_OBJ);
        }
        catch (Exception $e) {
            return 0;
        }
        return ($obj[0]->notif);
    }

    function imgCount($db, $user) {
        $sql = "SELECT * FROM gallery WHERE owner_id = :owner";
        $res = $db->prepare($sql);
        $res->bindParam(':owner', $user);
        $res->execute();
        try {
            $obj = $res->fetchAll(PDO::FETCH_OBJ);
        }
        catch (Exception $e) {
            return 0;
        }
        return (count($obj));
    }


    function imgOwner($db, $img_id) {
        $sql = "SELECT `owner_id` FROM `gallery` WHERE `id` = :id";
        $res = $db->prepare($sql);
        $res->bindParam(':id', $img_id);
        $res->execute();
        try {
            $obj = $res->fetchAll(PDO::FETCH_OBJ);
        }
        catch (Exception $e) {
            return 0;
        }
        return($obj[0]->owner_id);
    }

    function retMail($db, $uid) {
        $sql = "SELECT `mail` FROM `users` WHERE `id` = :id";
        $res = $db->prepare($sql);
        $res->bindParam(':id', $uid);
        $res->execute();
        try {
            $obj = $res->fetchAll(PDO::FETCH_OBJ);
        }
        catch (Exception $e) {
            return 0;
        }
        return($obj[0]->mail);
    }

    function displayPics($db, $user) {
        $sql = "SELECT * FROM gallery WHERE owner_id = :owner";
        $res = $db->prepare($sql);
        $res->bindParam(':owner', $user);
        $res->execute();
        try {
            $obj = $res->fetchAll(PDO::FETCH_OBJ);
        }
        catch (Exception $e) {
            return 0;
        }
        $i = 0;
        echo '<script>';
        echo '
                function sleep(milliseconds) {
                        var start = new Date().getTime();
                        for (var i = 0; i < 1e7; i++) {
                            if ((new Date().getTime() - start) > milliseconds){
                                break;
                            }
                        }
                    }

                function getXMLHttpRequest() {
                var xhr = null;
                if (window.XMLHttpRequest || window.ActiveXObject) {
                    if (window.ActiveXObject) {
                        try {
                            xhr = new ActiveXObject("Msxml2.XMLHTTP");
                        } catch(e) {
                            xhr = new ActiveXObject("Microsoft.XMLHTTP");
                        }
                    } else { xhr = new XMLHttpRequest(); }
                } else {
                    alert("Votre navigateur ne supporte pas l\'objet XMLHTTPRequest...");
                    return null;
                }
                return xhr;
            }

            function dlt(img) {
                var xhr = getXMLHttpRequest();
                xhr.open("POST", "../scripts/comment.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.send("delete=" + img);
                sleep(50);
                location.reload(\'true\');
                

            }';
        echo '</script>';
        while ($i < count($obj))
            echo '<img src="' . $obj[$i]->data . '" />' . '<input value="Supprimer" type="button" id="btn" onclick="dlt(' . $obj[$i++]->id . ');">';
    }

    if (isset($_SESSION['id']))
    {
        echo "Bienvenue, " . $_SESSION['surname'] . PHP_EOL;
?>      <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta content="Mon compte" name="title">
            <link rel="stylesheet" type="text/css" href="../css/account.css">
            <title>Mon compte</title>
        </head>
        <body>
        <div>
            <form action="../scripts/userchange.php" method="POST">
                <input type="text" autocomplete="mail" placeholder="Changer de mail..." name="mail" required>
                <input type="submit" value="Valider">
            </form>
        </div>
        <?php if (!isNotified($db, $_SESSION['id'])) {?>
        <div>
            <form action="../scripts/notify.php" method="POST">
                <button class="delog" type="submit" value="submit" name="on">
                    Activer notifs
                </button>
            </form>
        </div>
        <?php
        } else { ?>
            <div>
                <form action="../scripts/notify.php" method="POST">
                    <button class="delog" type="submit" value="submit" name="off">
                        Désactiver notifs
                    </button>
                </form>
            </div>
        <?php } ?>
        <div>
            <form action="../index.php">
                <button class="delog" type="submit" value="submit">
                    Retour
                </button>
            </form>
        </div>
        <div>
            <form action="../scripts/logout.php">
            <button class="delog" type="submit" value="submit">
            Deconnexion
            </button>
            </form>
        </div>
        <div>
            Vous avez <?php echo imgCount($db, $_SESSION['id']);?> montage(s) enregistré(s) : <br/><br/><?php displayPics($db, $_SESSION['id']); ?>
        </div>
        </body>
    </html>
        <?php


    }

    else {
        echo "Vous n'êtes pas connecté. Redirection vers le menu principal...\n";
        header('refresh:3;url=../index.php', TRUE, 401); }
?>
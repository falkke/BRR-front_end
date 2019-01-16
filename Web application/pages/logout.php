<?php
    if(is_logged() == 0) {
        header('Location:index.php?page=home');
    }

    session_destroy();
    header("Location:index.php?page=home");
?>

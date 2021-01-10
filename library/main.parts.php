<?php


function getHeader(){
ob_start();
?><!doctype html>
<html lang="ru-RU">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        <?php include_once "bootstrap-css.php"; ?>
    </style>
    <title>PARSER</title>
</head>
<body><?php
return ob_get_clean();
}

function getFooter(){
ob_start();
?>
</body>
<script>
    <?php include_once "bootstrap-js-1.php"; ?>
</script>
<script>
    <?php include_once "bootstrap-js-2.php"; ?>
</script>
<script>
    <?php include_once "bootstrap-js-3.php"; ?>
</script>
</html>
<?php
return ob_get_clean();
}

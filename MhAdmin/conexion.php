<?php
    $host = getenv('DB_HOST') ?: "localhost";
    $user = getenv('DB_USER') ?: "root";
    $clave = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : "";
    $bd = getenv('DB_NAME_USERS') ?: (getenv('DB_NAME') ?: "usuarios");
    $port = getenv('DB_PORT') ? (int)getenv('DB_PORT') : 3306;

    try{
         $conexion = new PDO("mysql:host=$host;port=$port;dbname=$bd;charset=utf8", $user, $clave);
         $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }catch(PDOException $prueba_error){
        echo "Error: " . $prueba_error->getMessage();
    }
?>
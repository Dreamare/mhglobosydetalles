<?php
    $host = getenv('DB_HOST') ?: "localhost";
    $user = getenv('DB_USER') ?: "root";
    $clave = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : "";
    $bd = getenv('DB_NAME') ?: "card";
    $port = getenv('DB_PORT') ? (int)getenv('DB_PORT') : 3306;

    $conexion = mysqli_connect($host, $user, $clave, $bd, $port);
    if (mysqli_connect_errno()){
        echo "No se pudo conectar a la base de datos: " . mysqli_connect_error();
        exit();
    }
    mysqli_select_db($conexion, $bd) or die("No se encuentra la base de datos");
    mysqli_set_charset($conexion, "utf8");

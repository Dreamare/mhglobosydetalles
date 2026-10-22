<?php
include_once '../bd/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$cedula = (isset($_POST['cedula'])) ? $_POST['cedula'] : '';
$nombre = (isset($_POST['nombre'])) ? $_POST['nombre'] : '';
$apellido = (isset($_POST['apellido'])) ? $_POST['apellido'] : '';
$correo = (isset($_POST['correo'])) ? $_POST['correo'] : '';
$contacto = (isset($_POST['contacto'])) ? $_POST['contacto'] : '';
$direccion = (isset($_POST['direccion'])) ? $_POST['direccion'] : '';
$ciudad = (isset($_POST['ciudad'])) ? $_POST['ciudad'] : '';
$contraseña = (isset($_POST['contraseña'])) ? $_POST['contraseña'] : '';


$opcion = (isset($_POST['opcion'])) ? $_POST['opcion'] : '';
$user_id = (isset($_POST['user_id'])) ? $_POST['user_id'] : '';


switch($opcion){
    case 1:
        $consulta = "INSERT INTO usuarios (cedula, nombre, apellido, correo, contacto, direccion, ciudad,contraseña) VALUES('$nombre', '$apellido', '$correo', '$contacto', '$direccion', '$ciudad','$contraseña') ";			
        $resultado = $conexion->prepare($consulta);
        $resultado->execute(); 
        
        $consulta = "SELECT * FROM usuarios ORDER BY user_id DESC LIMIT 1";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);       
        break;    
    case 2:        
        $consulta = "UPDATE usuarios SET cedula='$cedula', nombre='$nombre', apellido='$apellido', correo='$correo', contacto='$contacto', direccion='$direccion', ciudad='$ciudad', contraseña='$contraseña' WHERE user_id='$user_id' ";		
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();        
        
        $consulta = "SELECT * FROM usuarios WHERE user_id='$user_id' ";       
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;
    case 3:        
        $consulta = "DELETE FROM usuarios WHERE user_id='$user_id' ";		
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();                           
        break;
    case 4:    
        $consulta = "SELECT * FROM usuarios";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();        
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;
}

print json_encode($data, JSON_UNESCAPED_UNICODE);//envio el array final el formato json a AJAX
$conexion=null;
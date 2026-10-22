<?php
include_once '../bd/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$nombre = (isset($_POST['nombre'])) ? $_POST['nombre'] : '';
$categoria = (isset($_POST['categoria'])) ? $_POST['categoria'] : '';
$descripcion = (isset($_POST['descripcion'])) ? $_POST['descripcion'] : '';
$precio = (isset($_POST['precio'])) ? $_POST['precio'] : '';
$stock = (isset($_POST['stock'])) ? $_POST['stock'] : '';


$opcion = (isset($_POST['opcion'])) ? $_POST['opcion'] : '';
$prod_id = (isset($_POST['prod_id'])) ? $_POST['prod_id'] : '';


switch($opcion){
    case 1:
        $consulta = "INSERT INTO productos (nombre, categoria, descripcion, precio, stock) VALUES('$nombre', '$categoria', '$descripcion', '$precio', '$stock') ";			
        $resultado = $conexion->prepare($consulta);
        $resultado->execute(); 
        
        $consulta = "SELECT * FROM productos ORDER BY prod_id DESC LIMIT 1";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);       
        break;    
    case 2:        
        $consulta = "UPDATE productos SET nombre='$nombre', categoria='$categoria', descripcion='$descripcion', precio='$precio', stock='$stock' WHERE prod_id='$prod_id' ";		
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();        
        
        $consulta = "SELECT * FROM productos WHERE prod_id='$prod_id' ";       
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;
    case 3:        
        $consulta = "DELETE FROM productos WHERE prod_id='$prod_id' ";		
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();                           
        break;
    case 4:    
        $consulta = "SELECT * FROM productos";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();        
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;
}

print json_encode($data, JSON_UNESCAPED_UNICODE);//envio el array final el formato json a AJAX
$conexion=null;
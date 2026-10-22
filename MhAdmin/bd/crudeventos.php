<?php
include_once '../bd/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$cliente = (isset($_POST['cliente'])) ? $_POST['cliente'] : '';
$barrio = (isset($_POST['barrio'])) ? $_POST['barrio'] : '';
$direccion = (isset($_POST['direccion'])) ? $_POST['direccion'] : '';
$fecha = (isset($_POST['fecha'])) ? $_POST['fecha'] : '';
$descripcion = (isset($_POST['descripcion'])) ? $_POST['descripcion'] : '';
$precioevento = (isset($_POST['precioevento'])) ? $_POST['precioevento'] : '';


$opcion = (isset($_POST['opcion'])) ? $_POST['opcion'] : '';
$codigo = (isset($_POST['codigo'])) ? $_POST['codigo'] : '';


switch($opcion){
    case 1:
        $consulta = "INSERT INTO eventos (cliente, barrio, direccion, fecha, descripcion, precioevento) VALUES('$cliente', '$barrio', '$direccion', '$fecha', '$descripcion', '$precioevento') ";			
        $resultado = $conexion->prepare($consulta);
        $resultado->execute(); 
        
        $consulta = "SELECT * FROM eventos ORDER BY codigo DESC LIMIT 1";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);       
        break;    
    case 2:        
        $consulta = "UPDATE eventos SET cliente='$cliente', barrio='$barrio', direccion='$direccion', fecha='$fecha', descripcion='$descripcion', precioevento='$precioevento' WHERE codigo='$codigo' ";		
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();        
        
        $consulta = "SELECT * FROM eventos WHERE codigo='$codigo' ";       
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;
    case 3:        
        $consulta = "DELETE FROM eventos WHERE codigo='$codigo' ";		
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();                           
        break;
    case 4:    
        $consulta = "SELECT * FROM eventos";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();        
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;
}

print json_encode($data, JSON_UNESCAPED_UNICODE);//envio el array final el formato json a AJAX
$conexion=null;
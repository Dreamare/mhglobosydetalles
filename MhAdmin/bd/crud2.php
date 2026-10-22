<?php
include_once '../bd/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$nombre = (isset($_POST['nombre'])) ? $_POST['nombre'] : '';
$nombreC = (isset($_POST['nombreC'])) ? $_POST['nombreC'] : '';
$apellidoC = (isset($_POST['apellidoC'])) ? $_POST['apellidoC'] : '';
$NITCC = (isset($_POST['NITCC'])) ? $_POST['NITCC'] : '';
$telefono = (isset($_POST['telefono'])) ? $_POST['telefono'] : '';


$opcion = (isset($_POST['opcion'])) ? $_POST['opcion'] : '';
$prov_id = (isset($_POST['prov_id'])) ? $_POST['prov_id'] : '';


switch($opcion){
    case 1:
        $consulta = "INSERT INTO proveedores (nombre, nombreC, apellidoC, NITCC, telefono) VALUES('$nombre', '$nombreC', '$apellidoC', '$NITCC', '$telefono') ";			
        $resultado = $conexion->prepare($consulta);
        $resultado->execute(); 
        
        $consulta = "SELECT * FROM proveedores ORDER BY prov_id DESC LIMIT 1";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);       
        break;    
    case 2:        
        $consulta = "UPDATE proveedores SET nombre='$nombre', nombreC='$nombreC', apellidoC='$apellidoC', NITCC='$NITCC', telefono='$telefono' WHERE prov_id='$prov_id' ";		
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();        
        
        $consulta = "SELECT * FROM proveedores WHERE prov_id='$prov_id' ";       
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;
    case 3:        
        $consulta = "DELETE FROM proveedores WHERE prov_id='$prov_id' ";		
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();                           
        break;
    case 4:    
        $consulta = "SELECT * FROM proveedores";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();        
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;
}

print json_encode($data, JSON_UNESCAPED_UNICODE);//envio el array final el formato json a AJAX
$conexion=null;
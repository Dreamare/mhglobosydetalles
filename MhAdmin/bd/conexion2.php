<?php 
class Conexion{	  
    public static function Conectar() {        
        $servidor  = getenv('DB_HOST') ?: 'localhost';
        $nombre_bd = getenv('DB_NAME_USERS') ?: (getenv('DB_NAME') ?: 'usuarios');
        $usuario   = getenv('DB_USER') ?: 'root';
        $password  = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '';
        $port      = getenv('DB_PORT') ? (int)getenv('DB_PORT') : 3306;
        $opciones  = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');			
        try{
            $conexion2 = new PDO("mysql:host=$servidor;port=$port;dbname=$nombre_bd", $usuario, $password, $opciones);			
            return $conexion2;
        }catch (Exception $e){
            die("El error de Conexión es: ". $e->getMessage());
        }
    }
}
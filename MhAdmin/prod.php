
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="#" />  
    <title>DataTables</title>
      
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <!-- CSS personalizado --> 
    <link rel="stylesheet" href="main.css">  
    <!-- <link rel="stylesheet" href="css/style.css"> -->
      
      
    <!--datables CSS básico-->
    <link rel="stylesheet" type="text/css" href="assets/datatables/datatables.min.css"/>
    <!--datables estilo bootstrap 4 CSS-->  
    <link rel="stylesheet"  type="text/css" href="assets/datatables/DataTables-1.10.18/css/dataTables.bootstrap4.min.css">    
      
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet">  
  </head>
    
  <body> 

   <!-- Navbar -->
   <nav class="navbar navbar-expand-lg navbar-dark bg-dark text-uppercase">
   <a class="navbar-brand" href="index.php">MH Globos u Detalles</a>
        <div class="container">
           
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                    </li>
                        <a class="nav-link" aria-current="page" href="index.php">Clientes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="prod.php">Productos</a>
                    </li> 
                    <li class="nav-item">
                        <a class="nav-link" href="prov.php">Proveedores</a>
                    <li class="nav-item">
                        <a class="nav-link" href="eventos.php">Eventos</a> 
                </ul>
               
            </div>
            <a href="cerrar.php">Cerrar sesion</a>
            <div class="welcome">
    <!-- <a href="cerrar.php">Cerrar sesion</a> -->
</div> 
        </div>
    </nav>
    <div class="container">
        <div class="row my-2">
            <div class="col-md">
                <h3 class="text-center fw-bold text-uppercase">Lista Productos</h3>
                <h3 class="text-center fw-bold text-uppercase"><img src="image/producto.png" alt=""></h3>
                <hr>
            </div>
        </div>
     <header>
     <h3 class='text-center'></h3>
     </header>    
      
    <div class="container">
        <div class="row">
            <div class="col-lg-12">            
            <button id="btnNuevo" type="button" class="btn btn-info" data-toggle="modal">Registrar<img src="image/carrito-de-compras.png"></i></button>    
            </div>    
        </div>    
    </div>    
    <br>  

    <div class="container caja">
        <div class="row">
            <div class="col-lg-12">
            <div class="table-responsive">        
                <table id="tablaProductos" class="table table-striped table-bordered table-condensed" style="width:100%" >
                    <thead class="text-center">
                        <tr>
                            <th>CÓDIGO</th>
                            <th>NOMBRE</th>
                            <th>CATEGORÍA</th>                                
                            <th>DESCRIPCIÓN</th>  
                            <th>PRECIO</th>
                            <th>STOCK</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>                           
                    </tbody>        
                </table>               
            </div>
            </div>
        </div>  
    </div>   

<!--Modal para CRUD-->
<div class="modal fade" id="modalCRUD" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
        <form id="formProductos">    
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                    <div class="form-group">
                    <label for="" class="col-form-label">Nombre:</label>
                    <input type="text" class="form-control" id="nombre">
                    </div>
                    </div>
                    <div class="col-lg-6">
                    <div class="form-group">
                    <label for="" class="col-form-label">Categoría</label>
                    <input type="text" class="form-control" id="categoria">
                    </div> 
                    </div>    
                </div>
                <div class="row"> 
                    <div class="col-lg-6">
                    <div class="form-group">
                    <label for="" class="col-form-label"> Descripcion</label>
                    <input type="text" class="form-control" id="descripcion">
                    </div>               
                    </div>
                    <div class="col-lg-6">
                    <div class="form-group">
                    <label for="" class="col-form-label">Precio</label>
                    <input type="text" class="form-control" id="precio">
                    </div>
                    </div>  
                </div>
                <div class="row">
                    <div class="col-lg-9">
                        <div class="form-group">
                        <label for="" class="col-form-label">Stock</label>
                        <input type="text" class="form-control" id="stock">
                        </div>
                    </div>    
                       
                </div>                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                <button type="submit" id="btnGuardar" class="btn btn-dark">Guardar</button>
            </div>
        </form>    
        </div>
    </div>
</div>  
      
    <!-- jQuery, Popper.js, Bootstrap JS -->
    <script src="assets/jquery/jquery-3.3.1.min.js"></script>
    <script src="assets/popper/popper.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
      
    <!-- datatables JS -->
    <script type="text/javascript" src="assets/datatables/datatables.min.js"></script>    
     
    <script type="text/javascript" src="main3.js"></script>  
    
    
  </body>
</html>

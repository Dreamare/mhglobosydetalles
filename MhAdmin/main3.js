$(document).ready(function() {
var prod_id, opcion;
opcion = 4;
    
tablaProductos = $('#tablaProductos').DataTable({  
    "ajax":{            
        "url": "bd/crud3.php", 
        "method": 'POST', //usamos el metodo POST
        "data":{opcion:opcion}, //enviamos opcion 4 para que haga un SELECT
        "dataSrc":""
    },
    "columns":[
        {"data": "prod_id"},
        {"data": "nombre"},
        {"data": "categoria"},
        {"data": "descripcion"},
        {"data": "precio"},
        {"data": "stock"},
        {"defaultContent": "<div class='text-center'><div class='btn-group'><button class='btn btn-primary btn-sm btnEditar'>Editar</i></button><button class='btn btn-danger btn-sm btnBorrar'>Eliminar</i></button></div></div>"}
    ]
});     

var fila; //captura la fila, para editar o eliminar
//submit para el Alta y Actualización
$('#formProductos').submit(function(e){                         
    e.preventDefault(); //evita el comportambiento normal del submit, es decir, recarga total de la página
    nombre = $.trim($('#nombre').val());    
    categoria = $.trim($('#categoria').val());
    descripcion = $.trim($('#descripcion').val());    
    precio = $.trim($('#precio').val());    
    stock = $.trim($('#stock').val());
        $.ajax({
          url: "bd/crud3.php",
          type: "POST",
          datatype:"json",    
          data:  {prod_id:prod_id, nombre:nombre, categoria:categoria, descripcion:descripcion, precio:precio, stock:stock ,opcion:opcion},    
          success: function(data) {
            tablaProductos.ajax.reload(null, false);
           }
        });			        
    $('#modalCRUD').modal('hide');											     			
});
        
 

//para limpiar los campos antes de dar de Alta una Persona
$("#btnNuevo").click(function(){
    opcion = 1; //alta           
    prod_id=null;
    $("#formProductos").trigger("reset");
    $(".modal-header").css( "background-color", "#52be80");
    $(".modal-header").css( "color", "white" );
    $(".modal-title").text("Registro productos");
    $('#modalCRUD').modal('show');	    
});

//Editar        
$(document).on("click", ".btnEditar", function(){		        
    opcion = 2;//editar
    fila = $(this).closest("tr");	        
    prod_id = parseInt(fila.find('td:eq(0)').text()); //capturo el ID		            
    nombre = fila.find('td:eq(1)').text();
    categoria = fila.find('td:eq(2)').text();
    descripcion = fila.find('td:eq(3)').text();
    precio = fila.find('td:eq(4)').text();
    stock = fila.find('td:eq(5)').text();
    $("#nombre").val(nombre);
    $("#categoria").val(categoria);
    $("#descripcion").val(descripcion);
    $("#precio").val(precio);
    $("#stock").val(stock);
    $(".modal-header").css("background-color", "#52be80");
    $(".modal-header").css("color", "white" );
    $(".modal-title").text("Editar Producto");		
    $('#modalCRUD').modal('show');		   
});

//Borrar
$(document).on("click", ".btnBorrar", function(){
    fila = $(this);           
    prod_id = parseInt($(this).closest('tr').find('td:eq(0)').text()) ;		
    opcion = 3; //eliminar        
    var respuesta = confirm("¿Está seguro de borrar el registro "+prod_id+"?");                
    if (respuesta) {            
        $.ajax({
          url: "bd/crud3.php",
          type: "POST",
          datatype:"json",    
          data:  {opcion:opcion, prod_id:prod_id},    
          success: function() {
              tablaProductos.row(fila.parents('tr')).remove().draw();                  
           }
        });	
    }
 });
     
});    
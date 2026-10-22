$(document).ready(function() {
    var codigo, opcion;
    opcion = 4;
        
    tablaEventos = $('#tablaEventos').DataTable({  
        "ajax":{            
            "url": "bd/crudeventos.php", 
            "method": 'POST', //usamos el metodo POST
            "data":{opcion:opcion}, //enviamos opcion 4 para que haga un SELECT
            "dataSrc":""
        },
        "columns":[
            {"data": "codigo"},
            {"data": "cliente"},
            {"data": "barrio"},
            {"data": "direccion"},
            {"data": "fecha"},
            {"data": "descripcion"},
            {"data": "precioevento"},
            {"defaultContent": "<div class='text-center'><div class='btn-group'><button class='btn btn-primary btn-sm btnEditar'>Editar</i></button><button class='btn btn-danger btn-sm btnBorrar'>Eliminar</i></button></div></div>"}
        ]
    });     
    
    var fila; //captura la fila, para editar o eliminar
    //submit para el Alta y Actualización
    $('#formEventos').submit(function(e){                         
        e.preventDefault(); //evita el comportambiento normal del submit, es decir, recarga total de la página
        cliente = $.trim($('#cliente').val());    
        barrio = $.trim($('#barrio').val());
        direccion = $.trim($('#direccion').val());    
        fecha = $.trim($('#fecha').val());    
        descripcion = $.trim($('#descripcion').val());
        precioevento = $.trim($('#precioevento').val());
            $.ajax({
              url: "bd/crudeventos.php",
              type: "POST",
              datatype:"json",    
              data:  {codigo:codigo, cliente:cliente, barrio:barrio, direccion:direccion, fecha:fecha, descripcion:descripcion, precioevento:precioevento, opcion:opcion},    
              success: function(data) {
                tablaEventos.ajax.reload(null, false);
               }
            });			        
        $('#modalCRUD').modal('hide');											     			
    });
            
     
    
    //para limpiar los campos antes de dar de Alta 
    $("#btnNuevo").click(function(){
        opcion = 1; //alta           
        codigo=null;
        $("#formEventos").trigger("reset");
        $(".modal-header").css( "background-color", "#52be80");
        $(".modal-header").css( "color", "white" );
        $(".modal-title").text("Registrar evento");
        $('#modalCRUD').modal('show');	    
    });
    
    //Editar        
    $(document).on("click", ".btnEditar", function(){		        
        opcion = 2;//editar
        fila = $(this).closest("tr");	        
        codigo = parseInt(fila.find('td:eq(0)').text()); //capturo el ID		            
        cliente = fila.find('td:eq(1)').text();
        barrio = fila.find('td:eq(2)').text();
        direccion = fila.find('td:eq(3)').text();
        fecha = fila.find('td:eq(4)').text();
        descripcion = fila.find('td:eq(5)').text();
        precioevento = fila.find('td:eq(6)').text();

        $("#cliente").val(cliente);
        $("#barrio").val(barrio);
        $("#direccion").val(direccion);
        $("#fecha").val(fecha);
        $("#descripcion").val(descripcion);
        $("#precioevento").val(precioevento);
        $(".modal-header").css("background-color", "#52be80");
        $(".modal-header").css("color", "white" );
        $(".modal-title").text("Editar evento");		
        $('#modalCRUD').modal('show');		   
    });
    
    //Borrar
    $(document).on("click", ".btnBorrar", function(){
        fila = $(this);           
        codigo = parseInt($(this).closest('tr').find('td:eq(0)').text()) ;		
        opcion = 3; //eliminar        
        var respuesta = confirm("¿Está seguro de borrar el evento "+codigo+" ?");                
        if (respuesta) {            
            $.ajax({
              url: "bd/crudeventos.php",
              type: "POST",
              datatype:"json",    
              data:  {opcion:opcion, codigo:codigo},    
              success: function() {
                  tablaEventos.row(fila.parents('tr')).remove().draw();                  
               }
            });	
        }
     });
         
    });       

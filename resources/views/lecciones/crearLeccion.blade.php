@extends('layouts.admin.principal')


@section('css')
<style>
td, th {
	text-align: center;
}
</style>
@endsection

@section('content')
<div class="page-title">
	<div class="title_left">
		<h3>Crear lección</h3>
	</div>
</div>

<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title">Agregar palabras</h3>		
	</div>
	<div class="panel-body">
		<div class="row">
			{!!Form::open()!!}	
				<div class="form-group">
					{!!Form::label('Nombre de la lección: ')!!}
				<div class="form-group">
					{!!Form::text('nombreleccion', null,['class'=>'form-control', 'id' => 'nombreleccion', 'required', 'placeholder' => 'Asigne un nombre a la lección actual ...'])!!}
				</div>
				<div class="form-group">
					{!!Form::label('Seleccione categoria: ')!!}
				<div class="form-group">
					{!!Form::select('categorias', $listaCategorias ,null,['class'=>'form-control', 'id' => 'categ', 'required', 'placeholder' => 'Seleccione ...'])!!}
				</div>
				</div>				
				<div class="form-group">
					<div class="form-group">
					{!!Form::label('Buscar palabra: ')!!}		
					</div>		
					<div class="row">	
						<div class="form-group col-md-10">	
							{!!Form::select('Nombre', [], null, ['class'=>'form-control','readonly','id'=>'name', 'required','placeholder' => 'Escriba la palabra a buscar'])!!}
						</div>	
						<div class="form-group col-md-2 align-right">	
							{!! Form::button('Agregar', array('class' => 'btn btn-primary', 'id'=>'agregarPalabra')) !!}
						</div>	
					</div>	
				</div>								 			
			{!!Form::close()!!}			
		</div>
	</div>
</div>

<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title">
			Palabras en la lección actual
		</h3>	
	</div>
	<div class="panel-body">
		<table id="palabrasAgregadas" class="table table-striped table-bordered no-footer" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Id</th>			
					<th>Palabra</th>						
					<th>Eliminar</th>
				</tr>
			</thead>
			<tbody>
				
			</tbody>
		</table>
	</div>
	<div class="panel-footer" style="text-align:right">
		{!! Form::button('Guardar', array('class' => 'btn btn-success', 'id'=>'guardar')) !!}
	</div>
</div>
@endsection

@section('scripts')

<script type="text/javascript">

$(function(){

	table[0] = $('#palabrasAgregadas').DataTable( {
		"language": {
			"url": "{!!route('espanol')!!}"
		},
		columns: [ ],
		"columnDefs": [
		{
			"targets": [0],
			"visible": false,
			"searchable": false
		},
		{
			"targets": [2],
			"data": null,
			"defaultContent":  "<button class='btn btn-danger' onclick='eliminarPalabra(event)'>Eliminar</button>" 
		}
		],
		"scrollX": true
	} );

});

/*****************************************************************************************************************/
// Función que carga las palabras de una categoría seleccionada
/*****************************************************************************************************************/
$('#categ').on('change', function ( ) {
	var categoria = $('#categ').val();
	if(categoria){		
		$('#name').attr('readonly',false);
		$.post("{!!route('lecciones.categorias')!!}",{"id_categoria": categoria}, function(result){
			var data;		
			data = $.map(result, function (result) {
                    return {
                        text: result.palabra,
                        id: result.id
                    }
                });			
			cargarPalabrasBusqueda(data);
		});
	}
		
	else
		$('#name').	attr('readonly',true);
});



/*****************************************************************************************************************/
// Función que agrega las palabras de manera temporal a la tabla de leccion
/*****************************************************************************************************************/
var palabras = [];
$('#guardar').attr('disabled',true);

$('#agregarPalabra').on('click', function () {
	var t = $('#palabrasAgregadas').DataTable();    
	var esta = false;
	var validator = $("form").kendoValidator().data("kendoValidator");
                    if (validator.validate()) {     
						esta = buscarpalabra($('#name').val());
						
						if(esta){
							if (esta == false){								
								agregarpalabras(t);			
								$('#guardar').attr('disabled',false);
							}
									
						else
							$.msgbox("La palabra " + $('#name option:selected').text() + " ya se encuentra en la lección actual.", { type: 'error' });
						}
						else{
							agregarpalabras(t);
							$('#guardar').attr('disabled',false);
						}
							
				}	
});

/*****************************************************************************************************************/
// Función que guarda la lección en la base de datos
/*****************************************************************************************************************/

$('#guardar').on('click', function () {

$.ajax({
			type : "POST",
			url : "{!!route('lecciones.store')!!}",
			async: false,
			data: {"nombre": $('#nombreleccion').val(), "usuario_documento": "86074808"},
			success: function(respuesta){
				if(respuesta.id > 0){
				var dataenvio = [];
				var aux = [];
				
				for(i=0; i< palabras.length; i++){
						aux[i] = {"palabra_id": palabras[i],"leccion_id": respuesta.id};
						dataenvio.push(aux[i]);
				}				
				$.post("{!!route('lecciones.guardardetalle')!!}",{"datos": dataenvio}, function(result){ 					
					if(result.status == 0){
						$.msgbox("Se registró la lección de manera exitosa.",{type:'success'},function (){
							window.location = "{!!route('lecciones.index')!!}";
						});
						
					}
						
					else
						$.msgbox("Se presentó un error durante el registro.",{type:'error'});
				});

				}
				else{
					$.msgbox("La lección con nombre " + $('#nombreleccion').val() + " ya se encuentra registrada. Asigne un nombre diferente.",{type:'error'});
				}
				
			}
		});
});


function buscarpalabra(id){
	for(i=0;i< palabras.length;i++) {		
		if(id == palabras[i])
			{return true;}
	}
}

function agregarpalabras(t){
	t.row.add( [
		$('#name').val(),
		$('#name option:selected').text(),
		null
	] ).draw( false );   
palabras.push( $('#name').val());  
}

function eliminarPalabra(event){
	
	var element = event.target;
	var data = table[0].row( $(element).parents('tr') ).data();	
	var id_palabra = data[0];
	for(i=0; i< palabras.length; i++){
						if(id_palabra == palabras[i]){
							palabras.splice(i,1);
							
							if( palabras.length==0)
								{$('#guardar').attr('disabled',true);
						}
						break;
						}
							
				}		
	
	table[0].row( $(element).parents('tr') ).remove().draw();
		
	//});
	
};

function cargarPalabrasBusqueda(datos){
  $('#name').empty();
  $('#name').select2({
	 	data: datos,
	    language: "es",
	});
}



</script>

@endsection
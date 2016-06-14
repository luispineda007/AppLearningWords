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
		<h3>
			Crear lección
		</h3>
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
						{!!Form::label('Buscar palabra: ')!!}
						{!!Form::text('Nombre',null,['class'=>'form-control', 'required'])!!}
					</div>			
					 <div class="form-group">
						{!!Form::label('Seleccione categoria: ')!!}
						<div class="form-group">
							{!!Form::select('categorias', array('L' => 'Large', 'S' => 'Small'),null,['class'=>'form-control', 'required'])!!}
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
</div>
@endsection

@section('scripts')

<script type="text/javascript">

$(function(){

	table[0] = $('#palabrasAgregadas').DataTable( {
		"language": {
			"url": "{!!route('espanol')!!}"
		},
		ajax: {
			url: "{!!route('lecciones.cargar')!!}",
			"type": "POST"
		},
		columns: [  { data: 'id' },
					{ data: 'nombre'}
				 ],
		"columnDefs": [
		{
			"targets": [0],
			"visible": false,
			"searchable": false
		},
		{
			"targets": [2],
			"data": null,
			"defaultContent":  "<button class='btn btn-danger' onclick='eliminarLeccion(event)'>Eliminar</button>" 
		}
		],
		"scrollX": true
	} );

});

function eliminarLeccion(event){
	var element = event.target;
	$.msgbox("¿Esta seguro que desea elimnar esta lección?", { type: 'confirm' }, function(result){
		if(result == 'Aceptar')
			var data = table[0].row( $(element).parents('tr') ).remove().draw( false );
	});
	
}

</script>

@endsection
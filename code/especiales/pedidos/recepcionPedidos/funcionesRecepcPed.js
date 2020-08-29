var antes="",temporal="",input_tmp="",foco=0,id_orden_compra,id_proveedor="";

/*Implemntación Oscar 11.02.2019 para buscar folios existentes*/
//función que busca el folio
	function busca_folio(e,obj){
		if(e.keyCode==13 || e.keyCode==40){

		}
	//obtenemos el valor de la caja de texto del folio
		var busca_txt=$(obj).val().trim();
		if(busca_txt.length<=2){
			$("#res_busc_folio").val("");
			$("#res_busc_folio").css("display","none");
			return false;
		} 
		if(id_proveedor==""){
			id_proveedor=$("#id_prov").val();
		}
	//enviamos datos por ajax
		$.ajax({
			type:'post',
			url:'recPedBD.php',
			cache:false,
			data:{flag:'busca_folios',txt:busca_txt,id_pro:id_proveedor},
			success: function(dat){
				var aux=dat.split("|");
				//cargamos lo valores en el resultado de búsqueda
					$("#res_busc_folio").html(aux[1]);
					$("#res_busc_folio").css("display","block");
				if(aux[0]!='ok'){
					return false;
				}else{
				//cargamos lo valores en el resultado de búsqueda
					$("#res_busc_folio").html(aux[1]);
					if(aux[1]!='sin coincidencias'){
						$("#res_busc_folio").css("display","block");
					}else{
						$("#res_busc_folio").css("display","none");
					}
				}
			}
		});
	}
//función que carga el folio de la recepción
	function carga_folio_recepcion(id_rec,folio,monto,pzas_rem,pzas_rec){
		$("#ref_nota_1").val(folio);//asignamos el folio
		$("#monto_nota").val(monto);//asignamos el monto de la nota del proveedor
		$("#id_recepcion").val(id_rec);//asignamos el id de recpción en el campo oculto
		$("#res_busc_folio").css("display","none");//ocultamos los resultados del folio
		$("#pzas_remision").val(pzas_rem);//asignamos las piezas en remision
		$("#pzas_recibidas").val(pzas_rec);//asignamos las piezas recibidas
	}
/*fin de cambio oscar 11.02.2019*/

/*función que guarda la recepción de OC*/
function guarda_recepcion(){
//validamos que este lleno el campo de referencia de nota
	var referencia=$("#id_recepcion").val();
	if(referencia==null||referencia==""||referencia==0){
		alert("Debe de escoger una remisión antes de guardar la recepción!!!");
		$("#ref_nota_1").select();
		return false;
	}
/*implementacion Oscar 11.02.2019 para campo de monito de la nota*/
//validamos que este lleno el campo de monto de nota
	var monto_nota=$("#monto_nota").val();
	if(referencia==null||referencia==""){
		alert("El campo de referencia de nota no puede ir vacío");
		$("#ref_nota_1").select();
		return false;
	}
/*fin de cambio Oscar 11.02.2019*/

	id_orden_compra=$("#id_oc").val();//sacamos el id de la orden de compra

	var tope=$("#filas_totales").val();
	var datos="";//declaramos la variable que guardará los datos
	var proveedor=$("#id_prov").val();//capturamos el id del proveedor
//recorremos la tabla
	for(var i=0;i<=tope;i++){
		if(document.getElementById('fila_'+i)){//si existe la fila
			if(document.getElementById('10_'+i).checked==true){
				datos+="invalida~";
				datos+=$("#1_"+i).html()+"~";//extraemos el id de producto
				datos+=parseInt($("#3_"+i).html().trim());//extraemos la cantidad  pendiente
			}else{
			//extraemos datos
				datos+=$("#1_"+i).html()+"~";//extraemos el id de producto
			//piezas
				var tmp=0;
				tmp=parseInt($("#5_"+i).html().trim()*$("#4_"+i).html().trim());//extraemos cajas * presentación
				tmp+=parseInt($("#6_"+i).html());//extraemos la cantidad de piezas y le sumamos la cajas
				datos+=tmp+"~";

				datos+=$("#7_"+i).html().trim()+"~";//extraemos precio por pieza
				datos+=$("#8_"+i).html().trim()+"~";//extraemos el monto por producto
				datos+=$("#4_"+i).html().trim()+"~";//extraemos presentación por caja
			}//fin de else

			datos+=$("#11_"+i).html().trim()+"~";//extraemos el descuento
			datos+=$("#12_"+i).html();//proveedor_producto
			if(i<tope){
			//concatenamos el separador
				datos+="|";
			}
		}
	}//fin de for i
	//alert(datos);return false;
//extraemos el valor de la recepción de orden de compra
	var id_recepcion_oc=$("#id_recepcion").val();
//enviamos datos por ajax
	$.ajax({
		type:'post',
		url:'recPedBD.php',
		cache:false,
		data:{flag:2,oc:id_orden_compra,datos:datos,ref:referencia,id_prov:proveedor,id:id_recepcion_oc,mt_nota:monto_nota},
		success: function(dat){
			var aux=dat.split("|");
			if(aux[0]!='ok'){
				alert("Error al guardar la orden de compra!!!\n\n"+dat);
				return false;
			}else{
				alert("Recepción guardada satisfactoriamente");
				location.reload();
			}
		}
	});
}

/*Función que edita celda*/
function editaCelda(flag,num){
	if(foco==1){//validamos que no sea la misma celda
		return false;
	}
//formamos la caja de texto
	input_tmp='<input type="text" id="entrada_temporal" onkeyup="valida_acc(event,'+flag+','+num+');" onblur="deseditaCelda('+flag+','+num+');">';
//extraemos el valor de la celda
	antes=$("#"+flag+"_"+num).html();
	$("#"+flag+"_"+num).html(input_tmp);
	$("#entrada_temporal").val(antes);
	$("#entrada_temporal").select();
	foco=1;
}

/*Función que desedita celda*/
function deseditaCelda(flag,num){
	temporal=$("#entrada_temporal").val();
	$("#"+flag+"_"+num).html(temporal);
	foco=0;
//realizamos acciones dependiendo la caja de texto
	var subtotal=0,porcentaje_desc=0,subtotal_desc=0,total=0;
	if(flag==4||flag==5||flag==6||flag==7||flag==11){
	//cajas recibidas
		porcentaje_desc=$("#11_"+num).html();
		subtotal=parseFloat(($("#5_"+num).html().trim()*$("#4_"+num).html().trim())+parseFloat($("#6_"+num).html().trim()));
		subtotal_desc=subtotal*porcentaje_desc;
		total=subtotal-subtotal_desc;
	
		$("#9_"+num).html(subtotal);
//		$("#8_"+num).html(Math.round(total*parseFloat($("#7_"+num).html().trim()),2) );
		$("#8_"+num).html(Number((total*parseFloat($("#7_"+num).html().trim())).toFixed(2)));		
	}
	
	/*implementacion Oscar 06.09.2019 para no dejar recibir mas piezas de las pendientes spor recibir*/
		//alert($("#3_"+num).html()+"|"+$("#9_"+num).html());
		if( parseFloat($("#3_"+num).html())<parseFloat($("#9_"+num).html()) ){
			alert("No se pueden recibir mas piezas de las pendientes por recibir!!!");//\nPendientes:"+$("#3_"+num).html()+"\nRecibidas"+$("#9_"+num).html()
			$("#"+flag+"_"+num).html(0);
			$("#"+flag+"_"+num).click();
			return false;
		}
	/*Fin de cambio Oscar 06.09.2019*/
 
//mandamos el cambio por ajax
	if(flag==-1||flag==11){
		var fl_tmp='';
		
		if(flag==-1){fl_tmp='ubicacion';}
		if(flag==11){fl_tmp='descuento';}

		var val_id=$("#1_"+num).html();
		
		$.ajax({
			type:'post',
			url:'recPedBD.php',
			cache:false,
			data:{flag:fl_tmp,valor:temporal,id:val_id},
			success:function(dat){
				if(dat!='ok'){
					alert("Error al modificar la ubicacion del almacen en Matriz!!!"+dat);
					return false;
				}
			}
		});
	}
}

/*función que quita fila*/
function quitar_fila(num){
//marcamos el check correspondiente
	document.getElementById("10_"+num).checked=true;
//ocultamos la fila
	$("#fila_"+num).css("display","none");
	foco=0;//reseteamos el enfoque
	return true;
}

/*Función que valida acción en la celda*/
function valida_acc(e,flag,num){
	var tca=e.keyCode;
	var tope=$("#filas_totales").val();//sacamos el tamaño del grid

//si es tecla abajo o intro
	if(tca==40||tca==13){
		if(num==tope){
			$("#"+flag+"_"+num).select();
			return false;
		}
		$("#input_buscador").focus();
		$("#"+flag+"_"+parseInt(num+1)).click();
	}
//si es tecla arriba 
	if(tca==38){
		if(num==1){
			$("#"+flag+"_"+num).select();
			return false;
		}
		$("#input_buscador").focus();
		$("#"+flag+"_"+parseInt(num-1)).click();
	}
//si es tecla derecha
	if(tca==39){
		if(flag==11){
			$("#"+flag+"_"+num).select();
			return false;
		}
		$("#input_buscador").focus();
		if(flag<7){
			$("#"+parseInt(flag+1)+"_"+num).click();
		}else if(flag==7){
			$("#11_"+num).click();
		}
	}
//si es tecla izquierda 
	if(tca==37){
		if(flag==4){
			$("#"+flag+"_"+num).select();
			return false;
		}
		$("#input_buscador").focus();
		if(flag==11){
			$("#7_"+num).click();
		}else{
			$("#"+parseInt(flag-1)+"_"+num).click();
		}
	}

}


/**********************************************************FUNCIONES DEL BUSCADOR*************************************************************/
var opc_res=0;
/*Función que aciva el buscador*/
function busca_txt(e){
	orden_compra=$("#id_oc").val();
	var texto=$("#input_buscador").val();
	if(texto.length<=2){
		$("#res_busc").css("display","none");
		return false;
	}
	if(e.keyCode==13||e.keyCode==40){
	//enfocamos la primera opción
		resalta_opc(1);
		return false;
	}
//enviamos datos por ajax
	$.ajax({
		type:'post',
		url:'recPedBD.php',
		cache:false,
		data:{flag:1,oc:orden_compra,txt:texto},
		success: function(dat){
			var aux=dat.split("|");
			if(aux[0]!='ok'){
				alert("Error!!\n\n"+dat);
				return false;
			}else{
			//cargamos lo valores en el resultado de búsqueda
				$("#res_busc").html(aux[1]);
				$("#res_busc").css("display","block");
			}
		}
	});
}

/*función que valida tecla de buscador*/
function valida_opc(e,num){
	var tca=e.keyCode;
	if(tca==40){
		if(num<$("#opc_totales").val()){
		//recorremos hacia a abajo
			resalta_opc(parseInt(num+1));
		}
		return false;
	}
	if(tca==38){
		//recorremos hacia arriba
		if(num>1){
			resalta_opc(parseInt(num-1));
		}else{
			$("#input_buscador").select();
		}
		return false;
	}

	if(tca==13||e=='click'){
	//extraaemos el id del productro en la opción
		var valor_opc=$("#val_opc_"+num).html();
	//recorremos la tabla en busca del ´roducto
		var tope=$("#filas_totales").val();
		for(var i=1;i<=tope;i++){
			if($("#1_"+i).html().trim()==valor_opc){
				$("#res_busc").css("display","none");
				$("#input_buscador").val("");
				$("#fila_"+i).focus();
				$("#5_"+i).click();
				return true;
			}
		}
		alert("Este producto ya fue recibido completamente o cancelado!!!");
		$("#res_busc").css("display","none");
		$("#input_buscador").select();
		return true;
	}
}

/*función que resalta opciones del buscador*/
function resalta_opc(num){
	if(opc_res!=0){
	//regresamos las propiedades de la opción resaltada
		$("#opc_"+opc_res).css("background","white");
		$("#opc_"+opc_res).css("color","black");
	}
//resaltamos la nueva opción
	$("#opc_"+num).css("background","rgba(92, 124, 14,.7)");
	$("#opc_"+num).css("color","white");
	$("#opc_"+num).focus();
//marcamos la nueva opción resaltada
	opc_res=num;
}

/************************************************************************************************************************************************/

/*functionamiento de teclas del grid*/
	var fila_resaltada=0;
	function valida_tca(e,num){
		var tca=e.keyCode;

		if(tca==38){//tecla arriba
			if(num==1){
				$("#busc").focus();
				return true;
			}
			$("#fila_"+parseInt(num-1)).focus();
			$("#4_"+parseInt(num-1)).click();
			return true;
		}
		
		if(tca==40||tca==13){//si es tecla abajo o intro
			if(tca==$("#filasTotales").val()){
				return false;
			}
			$("#fila_"+parseInt(num+1)).focus();
			$("#4_"+parseInt(num+1)).click();
			return true;
		}
	}

	function resalta_fila(num){
		if(fila_resaltada!=0){
			var color='';
			if(num%2==0){
				color="#E6E8AB";
			}else{
				color="#BAD8E6";
			}
			$("#fila_"+fila_resaltada).css("background",color);
		}
	//asignamos la nueva fila resaltada
		fila_resaltada=num;
		$("#fila_"+fila_resaltada).css("background","rgba(0,225,0,.6)");
	}


/*funcionamiento de teclas en buscador*/
	var res_seleccionado=0;
	function valida_mov_resultados(e,num,id_pr){
		var tca=e.keyCode;
		if(tca==38){//tecla arriba
			if(num==1){
				$("#busc").select();return true;
			}
			enfoca_resultado(parseInt(num-1));
			return true;
		}
		if(tca==40){//tecla abajo
			enfoca_resultado(parseInt(num+1));
			return true;
		}
		if(tca==13){//tecla intro
			validaProducto(id_pr);
			return true;
		}
	}

	function enfoca_resultado(num){
		if(res_seleccionado!=0){
		//regresamos el color blanco
			$("#resultado_"+res_seleccionado).css("background","white");
		}
		res_seleccionado=num;
	//resaltamos el sigueinte resultado
		$("#resultado_"+res_seleccionado).css("background","rgba(0,225,0,.5)");
		$("#resultado_"+res_seleccionado).focus();
	}

/*funcion para quitar producto de exclusión*/
	function elimina(num){
	//recolectamos el id del registro
		if(!confirm("Realmente desea quitar este producto de la exclusión de transferencias???")){
			return false;
		}
		var id_reg=$("#0_"+num).html();
	//enviamos datos por ajax
		$.ajax({
			type:'post',
			url:'bd.php',
			cache:false,
			data:{fl:'eliminar',id:id_reg},
			success:function(dat){
				if(dat!='ok'){
					alert("Error al eliminar el registro de exclusión!!!\n"+dat);
				}else{
					location.reload();//recargamos
				}
			}
		});
	}

	function busca(e){
		var tca=e.keyCode;
		if(tca==40){
			if(document.getElementById("resultado_1")){//document.getElementById("#resultado_1")
				enfoca_resultado(1);
			}else{
				$("#fila_1").focus();
				$("#4_1").click();
			}
			return true;
		}
	//validamos el texto
		var texto=$("#busc").val();
		if(texto.length<=2){
			$("#res_busc").html();
			$("#res_busc").css("display","none");
			return true;
		}
	//enviamos datos por ajax
		$.ajax({
			type:'post',
			url:'bd.php',
			cache:false,
			data:{fl:'busca',clave:texto},
			success:function(dat){
				var ax=dat.split("|");
				if(ax[0]!='ok'){
					alert("Error al buscar productos!!!\n"+dat);
				}else{
					$("#res_busc").html(ax[1]);
					$("#res_busc").css("display","block");
				}
			}
		});
	}

	function validaProducto(id_pr){
		var tam=$("#filasTotales").val();
	//recorremos la tabla en busqueda de ¿l producto
		for(var i=1;i<=tam;i++){
			if($("#fila_"+i)){
				if($("#1_"+i).html()==id_pr){
					$("#fila_"+i).focus();//enfocamos la fila
					$("#4_"+i).click();//damos click en el elemento
					$("#busc").val('');//limpiamos la búsqeda
					$("#res_busc").html('');//limpiamos los resultados de búsqueda
					$("#res_busc").css("display","none");//ocultamos resultados de búsqueda
					return true;
				}
			}
		}
		if(confirm("Este producto no esta excluido, desea agregarlo a la exclusión?")){
			$("#busc").val('');//limpiamos la búsqeda
			$("#res_busc").html('');//limpiamos los resultados de búsqueda
			$("#res_busc").css("display","none");//ocultamos resultados de búsqueda
			agregaFila(id_pr);
		}
	}

	function agregaFila(id_pr){
		var cont_nvo=parseInt($("#filasTotales").val())+1;
	//enviamos datos por ajax
		$.ajax({
			type:'post',
			url:'bd.php',
			cache:false,
			data:{fl:'agrega',id:id_pr,contador:cont_nvo},
			success:function(dat){
				var ax=dat.split("|");
				if(ax[0]!='ok'){
					alert("Error al excluir producto!!!\n"+dat);
				}else{
					$("#tabla_exclusion").append(ax[1]);
					$("#filasTotales").val(cont_nvo);//reasignamos el valor del contador
					$("#fila_"+cont_nvo).focus();
					$("#4_"+cont_nvo).click();
				}
			}
		});
	}

var auxiliar='',ocupado=0;
	function edita_celda(num){
		if(ocupado!=0){
			return false;
		}
	//obtenemos el dato anterior
		auxiliar=$("#4_"+num).html();//sacamos el valor del registro
		var cda_tmp='<input type="text" id="celda_tmp" value="'+auxiliar+'" style="width:99%;height:35px;" onkeyup="valida_tca(event,'+num+');" ';
		cda_tmp+='onblur="desedita_celda('+num+');">';
		$("#4_"+num).html(cda_tmp);
		$("#celda_tmp").select();
		ocupado=1;
	}
	function desedita_celda(num){
		var nvo_val=$("#celda_tmp").val();
		if(nvo_val!=auxiliar){
			var id_reg=$("#0_"+num).html();
		//enviamos datos por ajax
			$.ajax({
				type:'post',
				url:'bd.php',
				cache:false,
				data:{fl:'modifica',id:id_reg,dato:nvo_val},
				success:function(dat){
					var ax=dat.split("|");
		
					if(ax[0]!='ok'){
						alert("Error al modificar la observación!!!\n"+dat);
					}else{
					}
				}
			});
		}
		$("#4_"+num).html(nvo_val);
		//setTimeout(,500);
		ocupado=0;
	}
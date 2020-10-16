<?php
/*
  Creado por: AF
  Fecha: 2020-10-10
  Funcionalidad: Proceso para para actualizar cantidad de producto en Magento
*/
  error_log('Log CL - Job Init: Actualización productos en Magento');
  // Instancia database and object files
  require "../classes/database.php";
  $db = new db();
  $db = $db->conectDB();

  //Actualiza producto: Procesando
  $updateProducto="update ec_sync_magento set estatus=2 where tipo='Producto' and estatus=1;";
  $updateStmt = $db->prepare($updateProducto);
  try {
    $updateStmt->execute();
  }catch (PDOException $e) {
    error_log('Error integación Magento: ' . $e->getMessage());
  }

  //Recupera productos: Por procesar
  $consultaProductoMagento="select distinct id_registro, detalle from ec_sync_magento where tipo='Producto' and estatus=2;";
  $productosProcesar = $db->prepare($consultaProductoMagento);
  $productosProcesar->execute();
  if($productosProcesar->rowCount()){
    //Recupera token
    $token = getToken();
    error_log('token: '. $token['token']);
    //Itera productos
    if(!empty($token['token'])) {
      foreach ($productosProcesar as $row) {
        error_log($row['id_registro']);
        updateMagento($token,$row);
      }
    }
  }
  error_log('Log CL - Job End: Actualización productos en Magento');

  function getToken() {
    try {
      //Declara variables de api_config
      $db = new db();
      $db = $db->conectDB();
      $result = '';
      $hostMagento = '';
      $usuarioMagento = '';
      $contrasenaMagento = '';
      $tokenMagento = '';
      $sqlAPIConfig="select c.name, c.value FROM api_config c WHERE c.key='magento' and c.value is not null";
      //Recupera variables
      $resultadoAPIConf = $db->prepare($sqlAPIConfig);
      $resultadoAPIConf->execute();
      foreach ($resultadoAPIConf as $row) {
        $hostMagento= ($row['name'] == 'url') ? $row['value'] : $hostMagento;
        $usuarioMagento=($row['name'] == 'usuario') ? $row['value'] : $usuarioMagento;
        $contrasenaMagento=($row['name'] == 'contrasena') ? $row['value'] : $contrasenaMagento;
      }
      //Recupera token Magento
      try {
        if (!empty($hostMagento) && !empty($usuarioMagento) && !empty($contrasenaMagento)) {
            //Prepar petición
            $data = array(
                'username' => $usuarioMagento,
                'password' => $contrasenaMagento
            );
            $post_data = json_encode($data);
            // Inicializa curl request
            $crl = curl_init($hostMagento.'/rest/V1/integration/admin/token');
            //error_log('CL - Log Magento token request: ' .$hostMagento.'/rest/V1/integration/admin/token');
            //error_log($post_data);
            curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($crl, CURLINFO_HEADER_OUT, true);
            curl_setopt($crl, CURLOPT_POST, true);
            curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($crl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
              )
            );
            // Ejecuta petición
            $tokenMagento = curl_exec($crl);
            $tokenMagento = str_replace('"','',$tokenMagento);
            //error_log('CL - LOG Magento token result: '.$tokenMagento);
            //Estructura de resultado
            $result = array(
                'token' => $tokenMagento,
                'host' => $hostMagento
            );
            // Cierra curl sesión
            curl_close($crl);
        }
      } catch (Exception $e) {
        error_log('CL - LOG Respuesta token: Error - ' . $e->getMessage());
      }
    } catch (Exception $e) {
      error_log('CL - LOG Respuesta token: Error - ' . $e->getMessage());
    }
    return $result;
  }

  function updateMagento($magento, $row) {
    //Genera petición para actualizar producto en Magento
    $tokenMagento = $magento['token'];
    $hostMagento = $magento['host'];
    if (!empty($tokenMagento)) {
      $db = new db();
      $db = $db->conectDB();
      try{
        //Prepar petición
        $data = array(
            'product' => array(
              'extension_attributes' => array(
                'stock_item' => array(
                  'qty'=>$row['detalle'],
                  'is_in_stock'=>true
                )
              )
            )
        );
        $post_data = json_encode($data);
        // Inicializa curl request
        error_log('CL - Log Magento update request: ' .$hostMagento.'/rest/V1/products/'.$row['id_registro']);
        error_log($post_data);
        $crl = curl_init();
        curl_setopt($crl, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($crl, CURLOPT_URL, $hostMagento.'/rest/V1/products/'.$row['id_registro']);
        curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($crl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Bearer '.$tokenMagento)
        );
        $result = curl_exec($crl);
        $curl_info = curl_getinfo($crl);
        $http_status = curl_getinfo($crl, CURLINFO_HTTP_CODE);
        //error_log('CL - LOG Magento update result: '. print_r($result,true));
        // Cierra curl sesión
        curl_close($crl);
        //actualiza estatus realizado
        try {
          $updateProducto="update ec_sync_magento set estatus=3 where tipo='Producto' and id_registro='".$row['id_registro']."';";
          $updateStmt = $db->prepare($updateProducto);
          $updateStmt->execute();
        }catch (PDOException $e) {
          error_log('Error actualización BD: ' . $e->getMessage());
        }
      } catch (Exception $e) {
        error_log('CL - LOG Respuesta Actualiza Prod: Error - ' . $e->getMessage());
        try {
          $updateProducto="update ec_sync_magento set estatus=4 where tipo='Producto' and id_registro='".$row['id_registro']."';";
          $updateStmt = $db->prepare($updateProducto);
          $updateStmt->execute();
        }catch (PDOException $e) {
          error_log('Error actualización BD: ' . $e->getMessage());
        }
      }
    }else{
        error_log('CL - LOG No token');
    }
  }

?>

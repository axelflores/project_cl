<?php
class manageResponse{
  function errorMessage($response, $error, $description, $code, $inserts = null){
      $resultadoError = [];
      $resultadoError['erorr']=$error;
      $resultadoError['description']=$description;
      return $response->withStatus($code)
             ->withHeader('Content-Type', 'application/json')
             ->write(json_encode($resultadoError));
  }

  function successMessage($response, $dataResult){
      $resultado = [];
      $resultado['status']='OK';
      $resultado['result']=$dataResult;
      return $response->withStatus(200)
             ->withHeader('Content-Type', 'application/json')
             ->write(json_encode($resultado));
  }
}
?>

<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

//$app = new \Slim\App;

//Recupera productos
$app->get('/productos', function (Request $request, Response $response){
    //Valida token
    $token =  (empty($request->getHeader('Token'))) ? '' : implode(" ",$request->getHeader('Token'));
    if (empty($token) || strlen($token)<36 ) {
      //Define estructura de salida: Token requerido
      $resultadoError = [];
      $resultadoError['erorr']='Token_Requerido';
      $resultadoError['description']='Se requiere el uso de un token';
      return $response->withStatus(400)
             ->withHeader('Content-Type', 'application/json')
             ->write(json_encode($resultadoError));
    }else{
      //Consulta vigencia
      try{
        $db = new db();
        $db = $db->conectDB();
        $sqlToken = "SELECT token FROM api_token WHERE token='{$token}' AND expired_in>now();";
        $resultadoToken = $db->query($sqlToken);
        if ($resultadoToken->rowCount()==0) {
            $resultadoError = [];
            $resultadoError['erorr']='Token_Invalido';
            $resultadoError['description']='El token proporcionado no es válido';
            return $response->withStatus(400)
                   ->withHeader('Content-Type', 'application/json')
                   ->write(json_encode($resultadoError));
        }
      }catch (PDOException $e) {
        return '{"error":"'.$e->getMessage().'"}';

      }
    }

    // echo $request->getHeader('Authorization');
    // return json_encode($request->getHeader('Authorization'));
    // return json_encode($request->getHeaders());
    // return json_encode($request->getMethod());
    // return json_encode($request->getParams());

    //Define estructura salida
    $resultado = [];
    $resultado['records']=[];

    //echo "Todos los productos";
    $sql = "SELECT * FROM ec_productos LIMIT 20";
    try {
      $resultadoConsulta = $db->query($sql);
      if ($resultadoConsulta->rowCount()>0) {
         $productos = $resultadoConsulta->fetchAll(PDO::FETCH_OBJ);
         $resultado['records']=$productos;

         return $response->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode($resultado));
      }else{
        return json_encode("No existen productos");
      }
      $resultado = null;
      $db = null;
    } catch (PDOException $e) {
      return '{"error":"'.$e->getMessage().'"}';
    }
});

//Recupera producto por id
$app->get('/productos/{id}', function (Request $request, Response $response, array $args){
    //echo "Todos los productos";
    $idProducto = $args['id'];

    $sql = "select * from ec_productos where id_productos='{$idProducto}'";
    try {
      $db = new db();
      $db = $db->conectDB();
      $resultado = $db->query($sql);
      if ($resultado->rowCount()>0) {
         $productos = $resultado->fetchAll(PDO::FETCH_OBJ);
         return json_encode($productos);
      }else{
        return json_encode("No existen productos");
      }
      $resultado = null;
      $db = null;
    } catch (PDOException $e) {
      return '{"error":"'.$e->getMessage().'"}';
    }

});


//POST - Nuevo producto
$app->post('/productos/nuevo', function (Request $request, Response $response){
    //recupera parámetros
    $nombre = $request->getParam('nombre');
    $clave = $request->getParam('clave');
    $id_categoria = $request->getParam('id_categoria');
    $observaciones = $request->getParam('observaciones');

    //Genera sentencia para insert
    $sql = "insert into ec_productos (nombre, clave, id_categoria, observaciones) values (:nombre, :clave, :id_categoria, :observaciones)";

    try {
      $db = new db();
      $db = $db->conectDB();
      $resultado = $db->prepare($sql);
      $resultado->bindParam(':nombre',$nombre);
      $resultado->bindParam(':clave',$clave);
      $resultado->bindParam(':id_categoria',$id_categoria);
      $resultado->bindParam(':observaciones',$observaciones);
      $resultado->execute();
      return json_encode("Producto insertado");
      $resultado = null;
      $db = null;
    } catch (PDOException $e) {
      return '{"error":"'.$e->getMessage().'"}';
    }

});

?>

<?php

namespace Controllers;
use MVC\Router;
use Model\Vendedor;

class VendedorController {
   public static function crear( Router $router) {
    
      $errores = Vendedor::getErrores();

      $vendedor = new Vendedor;

      if($_SERVER['REQUEST_METHOD'] === 'POST') {

         /** Crea una nueva instancia */
         $vendedor = new Vendedor($_POST['vendedor']);

         // Validar
         $errores = $vendedor->validar();


         if(empty($errores)) {

             // Guarda en la base de datos
              $vendedor->guardar();
              header('Location: /admin');
         }
     }
      
      
      $router->render('vendedores/crear ', [
        'errores' => $errores ,
        'vendedor' => $vendedor
      ]);
   } 

    public static function actualizar( router $router) {

     $errores = Vendedor::getErrores();  
     //Redirecciona a la clase principal
     $id = validarORedireccionar('/admin');

     //Obtener datos de lvendedor al actualizar
     $vendedor = Vendedor::find($id);

     if($_SERVER['REQUEST_METHOD'] === 'POST') {

      // Asignar los atributos
      $args = $_POST['vendedor'];
      $vendedor->sincronizar($args);

      // Validación
      $errores = $vendedor->validar();
      
      if(empty($errores)) {

          // Guarda en la base de datos
          $resultado = $vendedor->guardar();

          if($resultado) {
              header('location: /admin');
          }
      }
}

     $router->render('vendedores/actualizar' , [
       'errores' => $errores ,
       'vendedor' => $vendedor
     ]);

    } 

     public static function eliminar() {
      
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
         //validar el id
         $id = $_POST['id'];
         $id = filter_var($id , FILTER_VALIDATE_INT);

         if ($id) {
         //valida el tipo de elemento que va a eliminar
         $tipo = $_POST['tipo'];

         if (validarTipoContenido($tipo)) {
            $vendedor = Vendedor::find($id);
            $vendedor->eliminar();

            header('Location: /admin');
            exit;
         }
         }
      }
     } 
}
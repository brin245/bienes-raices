<?php
namespace Controllers;

use MVC\Router;
use Model\Propiedad;
use Model\Vendedor;
use Intervention\Image\ImageManagerStatic as Image;

class PropiedadController {
  public static function index(Router $router){
 
    $propiedades = Propiedad::all();
    $vendedores = Vendedor::all();

    //Muestra mensaje condicional
    $resultado = $_GET['resultado'] ?? null;

    $router->render('propiedades/admin' , [
      'propiedades' => $propiedades ,
      'vendedores' => $vendedores,
      'resultado' => $resultado
    ]);
  }  
  public static function crear(Router $router){
     //Se crea una nueva instancia
    $propiedad = new Propiedad;
    $vendedores = Vendedor::all();
    // Arreglo con mensajes de errores
    $errores = Propiedad::getErrores();
   
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

      // Crea una nueva instancia con los datos recibidos
      $propiedad = new Propiedad($_POST['propiedad']);
  
      // Generar un nombre único para la imagen
      $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";
  
      // Si hay una imagen, se setea y se procesa
      if ($_FILES['propiedad']['tmp_name']['imagen']) {
          $image = Image::make($_FILES['propiedad']['tmp_name']['imagen'])->fit(800, 600);
          $propiedad->setImagen($nombreImagen);
      }
  
      // Validar
      $errores = $propiedad->validar();
  
      if (empty($errores)) {
  
          // Crear la carpeta para subir imágenes si no existe
          if (!is_dir(CARPETA_IMAGENES)) {
              mkdir(CARPETA_IMAGENES);
          }
  
          // Subir la imagen
          $image->save(CARPETA_IMAGENES . $nombreImagen);
  
          // Guardar en la base de datos (un solo llamado a guardar)
          $resultado = $propiedad->guardar();
  
          // Redirigir al usuario.
          if ($resultado) {
              header('Location: /admin?resultado=1');
              exit;
          }
      }
  }
  

    $router -> render('propiedades/crear' , [
      //Se pasa la instancia hacia la vista
      'propiedad' => $propiedad ,
      'vendedores' => $vendedores ,
      'errores' =>$errores
    ]);
  }
  public static function actualizar(Router $router){
    
    $id = validarORedireccionar('/admin');
    // Obtener los datos de la propiedad
    $propiedad = Propiedad::find($id);

    // Consultar para obtener los vendedores
    $vendedores = Vendedor::all();
    // Arreglo con mensajes de errores
    $errores = Propiedad::getErrores();

    //Metodo post para Actualizar
    if($_SERVER['REQUEST_METHOD'] === 'POST'){

      //Asignar los atributos
      $args = $_POST['propiedad'];
  
      $propiedad ->sincronizar($args);
  
      //Validacion
      $errores = $propiedad ->validar();
  
      //Subida de Archivos
      //Generar un nnombre unico
      $nombreImagen = md5( uniqid ( rand() , true )) . ".jpg";
  
      if($_FILES['propiedad']['tmp_name']['imagen']) {
        $image = Image::make($_FILES['propiedad']['tmp_name']['imagen'])->fit(800,600);
        $propiedad->setImagen($nombreImagen);
    }
    
    if(empty($errores)) {
        // Almacenar la imagen
        if($_FILES['propiedad']['tmp_name']['imagen']) {
            $image->save(CARPETA_IMAGENES . $nombreImagen);
        }
  
          $propiedad ->guardar();
          // Redirigir a la página de administrador con el mensaje de resultado
          header('Location: /admin?resultado=3');
          exit; // Termina la ejecución del script después de la redirección
      }
  }

    $router ->render('propiedades/actualizar' , [
      'propiedad' => $propiedad ,
      'errores' => $errores ,
      'vendedores' => $vendedores
    ]);

  }

  public static function eliminar(){
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      
      //Validar id
      $id = $_POST['id'];
      $id = filter_var($id , FILTER_VALIDATE_INT);

      if ($id) {
        $tipo = $_POST['tipo'];
       if (validarTipoContenido($tipo)) {
          $propiedad = Propiedad::find($id);
          $propiedad->eliminar();

          // Redirigir a la página de administrador con el mensaje de resultado
          header('Location: /admin?resultado=3');
          exit; // Termina la ejecución del script después de la redirección
       }
      }
    }
  }
 }
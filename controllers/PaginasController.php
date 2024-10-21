<?php

namespace Controllers;

use MVC\Router;
use Model\Propiedad;
use PHPMailer\PHPMailer\PHPMailer;

class PaginasController{
public static function index(Router $router){
  $propiedades = Propiedad::get(3);
  $inicio = true;

  $router ->render('paginas/index' , [
     'propiedades' => $propiedades ,
     'inicio' => $inicio
  ]);
}
public static function nosotros( Router $router){
 $router -> render('paginas/nosotros');
}
public static function propiedades( Router $router){

$propiedades = Propiedad::all(); 
    
 $router -> render('paginas/propiedades' , [
    'propiedades' =>$propiedades
 ]);
 }
 public static function propiedad( Router $router){
//PARA HACER DINAMICA UNA CLASE DEBE SER COMO ESTA CLASE
  $id = validarORedireccionar('/propiedades');

  //Buscar la propiedad por su id
  $propiedad = Propiedad::find($id);

  $router -> render('paginas/propiedad' , [
    'propiedad' => $propiedad
    ]);
}
public static function blog( Router $router){

   $router->render('paginas/blog');
}
public static function entrada( Router $router){
 $router-> render('paginas/entrada');
}
public static function contacto( Router $router){

    $mensaje = null;

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
   $respuestas = $_POST['contacto'];

   //Crear una instancia de PhpMailer
   $mail = new PHPMailer();

   //Configurar SMTP
   $mail->isSMTP();
   $mail->Host = 'smtp.mailtrap.io';
   $mail->SMTPAuth= true;
   $mail->Username= '09a62c3f48b1f6';
   $mail->Password = 'a4b4a775e089ac';
   $mail->SMTPSecure = 'tls';
   $mail->Port = 2525;

   //Configurar el contenido del Mail
   $mail ->setFrom('admin@bienesraices.com');
   $mail->addAddress('admin@bienesraices.com' , 'BienesRaices.com');
   $mail->Subject = ' Tienes un nuevo mensaje';

   //Habilitar HTML
   $mail -> isHtml(true);
   $mail->CharSet = 'UTF-8';

   //Definir el contenido (Se concatena con el punto , Este codigo es lo que se envia al correo)
   $contenido = '<html>';
   $contenido .= "<p><strong>Has Recibido un email:</strong></p>";
   $contenido .= "<p>Nombre: " . $respuestas['nombre'] . "</p>";
   $contenido .= "<p>Mensaje: " . $respuestas['mensaje'] . "</p>";
   $contenido .= "<p>Vende o Compra: " . $respuestas['opciones'] . "</p>";
   $contenido .= "<p>Presupuesto o Precio: $" . $respuestas['presupuesto'] . "</p>";

   if($respuestas['contacto'] === 'telefono') {
       $contenido .= "<p>Eligió ser Contactado por Teléfono</p>";
       $contenido .= "<p>Su teléfono es: " .  $respuestas['telefono'] ." </p>";
       $contenido .= "<p>Fecha Contacto: " . $respuestas['fecha'] . '</p>';
       $contenido .= '<p>Hora:' .$respuestas['hora'] . '</p>';
   } else {
       $contenido .= "<p>Eligio ser Contactado por Email</p>";
       $contenido .= "<p>Su Email  es: " .  $respuestas['email'] ." </p>";
   }


   $mail -> Body = $contenido;
   $mail ->AltBody = "Esto es un texto alternativo sin html";
   //Enviar el email
   if ($mail->send()) {
     $mensaje ="Mensaje enviado Correctamente";
   }else{
    $mensaje ="El mensaje no se pudo enviar";
   }
  }
  $router-> render('paginas/contacto' , [
     'mensaje' => $mensaje
  ]);
}

}
<?php
namespace MVC ;

class Router {

    public $rutasGET = [];
    public $rutasPOST = [];

       public function get($url , $fn){
        $this->rutasGET[$url] = $fn;
       }

       public function post($url , $fn){
        $this->rutasPOST[$url] = $fn;
       }
        public function comprobarRutas(){
           session_start();
           //Esto va a existir recien cuando el usuario y contraseña sean validadas
           $auth = $_SESSION['login'] ?? null;

          
           //Arreglo de rutas protegidas...
       $rutas_protegidas = [ '/admin' , '/propiedades/crear' , '/propiedades/actualizar' , '/propiedades/eliminar',
      '/vendedores/crear' , '/vendedores/actualizar' , '/vendedores/eliminar'];

           $urlActual = $_SERVER['PATH_INFO'] ?? '/';
           $metodo = $_SERVER['REQUEST_METHOD'];

           if ($metodo === 'GET') {
            
             $fn= $this-> rutasGET[$urlActual] ?? null;
           } else {
            
            $fn = $this->rutasPOST[$urlActual] ?? null;
           }

           //Proteger las rutas
           if (in_array($urlActual , $rutas_protegidas) && !$auth ){
            //Si alguien intenta entrar a la ruta protegida /admin y no 
            //esta autenticado lo reedireccionara a la pestaña de inicio
             header('Location: /');
           
          }

           if ($fn) {
            # La url existe y hay una funcion asociada 
           call_user_func($fn , $this);

           }else{
            echo "Pagina no encontrada";
           }
        }

        //Muestra una vista
        public function render($view , $datos =[]){
         
       foreach($datos as $key => $value){
         //Esta sintaxis se llama variable de variable,
         // mantiene el nombre pero que no pierda el valor(FUNDAMENTAL)
         $$key = $value;
       }
        //sirve para almacenar en memoria algun dato conciso.
        ob_start();

          // Incluir la vista, aplicando trim a $view
    include_once __DIR__ . "/views/" . trim($view) . ".php";
         $contenido = ob_get_clean();//limpia el Buffer
         include_once __DIR__ . "/views/layout.php";
         
        }
    }

<?php
/**
* Holding a instance of CHanady to enable use of $this in subclasses.
*
* @package HandyCore
*/
class CObject {

   public $config;
   public $request;
   public $data;
   public $db;
   public $views;
   public $session;
   public $user;
   
   /**
    * Constructor
    */
   protected function __construct($ha=null) {//nytt konstruktorargument
   	if(!$ha) {
   	   $ha = CHandy::Instance();
   	}
    $this->config   = &$ha->config;
    $this->request  = &$ha->request;
    $this->data     = &$ha->data;
    $this->db 		= &$ha->db;
    $this->views 	= &$ha->views;
    $this->session	= &$ha->session;
    $this->user     = &$ha->user;//uppdaterat
  }
  
/**
    * Redirect to another url and store the session
    * called from CGuestbook->handler()
    */
    protected function RedirectTo($urlOrController=null, $method=null) {//9/11 MOs har 
    //$ha = CHandy::Instance(); //9/11
    if(isset($ha->config['debug']['db-num-queries']) && $ha->config['debug']['db-num-queries'] && isset($this->db)) {
      $this->session->SetFlash('database_numQueries', $this->db->GetNumQueries());
    }
    if(isset($ha->config['debug']['db-queries']) && $this->config['debug']['db-queries'] && isset($this->db)) {
      $this->session->SetFlash('database_queries', $this->db->GetQueries());
    }
    if(isset($ha->config['debug']['timer']) && $ha->config['debug']['timer']) {
         $this->session->SetFlash('timer', $ha->timer);
    }
    $this->session->StoreInSession();
    header('Location: ' . $this->request->CreateUrl($urlOrController, $method));// 9/11 test istället för $url
  }
//-----------------------------------------------------------------------------
/**
  * Redirect to a method within the current controller. Defaults to index-method. Uses RedirectTo().
  *
  * @param string method name the method, default is index method.
  */
         
   protected function RedirectToController($method=null) {
   	   $this->RedirectTo($this->request->controller, $method);
  }
//-----------------------------------------------------------------------------

        /**
         * Redirect to a controller and method. Uses RedirectTo().
         *
         * @param string controller name the controller or null for current controller.
         * @param string method name the method, default is current method.
         */
        protected function RedirectToControllerMethod($controller=null, $method=null) {
         $controller = is_null($controller) ? $this->request->controller : null;
         $method = is_null($method) ? $this->request->method : null;        
         $this->RedirectTo($this->request->CreateUrl($controller, $method));
  }
 //-----------------------------------------------------------------------------
 //-----------------------------------------------------------------------------

  // 9/11 från MOS 9/11 test för att se om det hjälper.
   /**
         * Save a message in the session. Uses $this->session->AddMessage()
         *
         * @param $type string the type of message, for example: notice, info, success, warning, error.
         * @param $message string the message.
         */
         protected function AddMessage($type, $message) {
         	 $this->session->AddMessage($type, $message);
         }
 //-----------------------------------------------------------------------------
        
       /**
         * Create an url. Uses $this->request->CreateUrl()
         *
         * @param $urlOrController string the relative url or the controller
         * @param $method string the method to use, $url is then the controller or empty for current
         * @param $arguments string the extra arguments to send to the method
         */
      protected function CreateUrl($urlOrController=null, $method=null, $arguments=null) {
      	  $this->request->CreateUrl($urlOrController, $method, $arguments);
  }
}

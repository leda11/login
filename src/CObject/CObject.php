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
    protected function RedirectTo($url) {
    $ha = CHandy::Instance();
    if(isset($ha->config['debug']['db-num-queries']) && $ha->config['debug']['db-num-queries'] && isset($ha->db)) {
      $this->session->SetFlash('database_numQueries', $this->db->GetNumQueries());
    }
    if(isset($ha->config['debug']['db-queries']) && $ha->config['debug']['db-queries'] && isset($ha->db)) {
      $this->session->SetFlash('database_queries', $this->db->GetQueries());
    }
    if(isset($ha->config['debug']['timer']) && $ha->config['debug']['timer']) {
         $this->session->SetFlash('timer', $ha->timer);
    }
    $this->session->StoreInSession();
    header('Location: ' . $this->request->CreateUrl($url));
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
}

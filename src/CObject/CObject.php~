<?php
/**
* Holding a instance of CLydia to enable use of $this in subclasses.
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
  // public $model:
   
   /**
    * Constructor
    */
   protected function __construct() {
    $ha = CHandy::Instance();
    $this->config   = &$ha->config;
    $this->request  = &$ha->request;
    $this->data     = &$ha->data;
    $this->db 		= &$ha->db;
    $this->views 	= &$ha->views;
    $this->session	=&$ha->session;
   // $this->model = &$ha->model;
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


}

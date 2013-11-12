<?php

/**
* Main class for Handy, holds everything.
*
* @package HandyCore
*/
class CHandy implements ISingleton {

   private static $instance = null;
   // all new
  
   public $config = array();
   public $request;
   public $data;
   public $db;
   public $views;
   public $session;
   public $user;
   public $timer = array();
   //public $timer = array();

   /**
    * Constructor
    */
   protected function __construct() {
   	  //time the page generation. added 11/11
   	   $this->timer['first'] = microtime(true); 
   	   
      // include the site specific config.php and create a ref to $ha to be used by site/config.php
      $ha = &$this;
      require(HANDY_SITE_PATH.'/config.php');
      
     //Start Session
      session_name($this->config['session_name']);
      session_start();
      $this->session = new CSession($this->config['session_key']);
      $this->session->PopulateFromSession();
      
      //Set default date/time-zone
      date_default_timezone_set($this->config['timezone']);
      
      // Create a container for all views and theme data
     $this->views = new CViewContainer();
     
     // create data base 
      if(isset($this->config['database'][0]['dsn'])) {
        $this->db = new CMDatabase($this->config['database'][0]['dsn']);
     }
     
     // Create a object for the user
     $this->user = new CMUser($this);
    
      
  }
   
//..............................................................................
   
   /**
    * Singleton pattern. Get the instance of the latest created object or create a new one.
    * @return CHandy The instance of this class.
    */
   public static function Instance() {
      if(self::$instance == null) {
         self::$instance = new CHandy();
      }
      return self::$instance;
   }
   
//..............................................................................
      
   /**
    * Frontcontroller, check url and route to controllers.
    */
  public function FrontControllerRoute() {

     
    // Take current url and divide it in controller, method and parameters
    // part in fixing base_url mom05
    $this->request = new CRequest();
    //add 11/11 testa sen
    // $this->request = new CRequest($this->config['url_type']);
     
    $this->request->Init($this->config['base_url']);
          
    $controller = $this->request->controller;
    $method     = $this->request->method;
    $arguments  = $this->request->arguments;                       

    // Is the controller enabled in config.php?
    $controllerExists    = isset($this->config['controllers'][$controller]);
    $controllerEnabled    = false;
    $className             = false;
    $classExists           = false;

    if($controllerExists) {
      $controllerEnabled    = ($this->config['controllers'][$controller]['enabled'] == true);
      $className            = $this->config['controllers'][$controller]['class'];
      $classExists          = class_exists($className);
    }
    
    // Step 3
    // Check if there is a callable method in the controller class, if then call it
    // Anropa med PHP reflection
    if($controllerExists && $controllerEnabled && $classExists) {
      $rc = new ReflectionClass($className);
      $formattedMethod = str_replace(array('_', '-'), '', $method);//test kommentera bort 9/11
      
      if($rc->implementsInterface('IController')) {
        if($rc->hasMethod($formattedMethod)) {
          $controllerObj = $rc->newInstance();
          $methodObj = $rc->getMethod($formattedMethod);
         
          if($methodObj->isPublic()) {//9/11 la till denn a if-elsesats
            $methodObj->invokeArgs($controllerObj, $arguments);
          } else {
            die("404. " . get_class() . ' error: Controller method not public.');
          }
        } else {
          die("404. " . get_class() . ' error: Controller does not contain method.');
        }
      } else {
        die('404. ' . get_class() . ' error: Controller does not implement interface IController.');
      }
    }
    else {
      die('404. Page is not found.');
    }

  }

//..............................................................................
   
    /**
    * ThemeEngineRender, renders the reply of the request.
    */
  public function ThemeEngineRender() {
  	   // Save to session before output anything
    $this->session->StoreInSession();// 9/11 skall det var amed här finnns också i CObject 
  	 // Is theme enabled? 9/11 test tillägg från 2.0.5 MOS
    if(!isset($this->config['theme'])) {
      return;
    } 
  	  
    // Get the paths and settings for the theme
    $themeName    = $this->config['theme']['name'];   
    $themePath    = HANDY_INSTALL_PATH . "/themes/{$themeName}";
    //$themeUrl      = "themes/{$themeName}";
    $themeUrl = $this->request->base_url . "themes/{$themeName}";
    
    // Add stylesheet path to the $ha->data array
    $this->data['stylesheet'] = "{$themeUrl}/style.css";

    // Include the global functions.php and the functions.php that are part of the theme
    $ha = &$this;
    include(HANDY_INSTALL_PATH . '/themes/functions.php');
    $functionsPath = "{$themePath}/functions.php";
    if(is_file($functionsPath)) {
      include $functionsPath;
    }

    // Extract $ha->data to own variables and handover to the template file
    extract($this->data);
    extract($this->views->GetData());   
    include("{$themePath}/default.tpl.php");    
  
  }
       
}

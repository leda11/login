<?php

/**
* Main class for Handy, holds everything.
*
* @package HandyCore
*/
class CHandy implements ISingleton {

   private static $instance = null;

   /**
    * Constructor
    */
   protected function __construct() {
      // include the site specific config.php and create a ref to $ha to be used by site/config.php
      $ha = &$this;
      require(HANDY_SITE_PATH.'/config.php');
      
      //Start Session
      session_name($this->config['session_name']);
      session_start();
  }
   
//..............................................................................
   
   /**
    * Singleton pattern. Get the instance of the latest created object or create a new one.
    * @return CLydia The instance of this class.
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
    $this->data['debug']  = "REQUEST_URI - {$_SERVER['REQUEST_URI']}\n";
    $this->data['debug'] .= "SCRIPT_NAME - {$_SERVER['SCRIPT_NAME']}\n";
     
    // Take current url and divide it in controller, method and parameters
    // part in fixing base_url mom05
    $this->request = new CRequest();
    $this->request->Init($this->config['base_url']);
          
    $controller = $this->request->controller;
    $method     = $this->request->method;
    $formattedMethod = str_replace(array('_', '-'), '', $method);//test funkar det mom03
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
      
      if($rc->implementsInterface('IController')) {
        if($rc->hasMethod($formattedMethod)) {
          $controllerObj = $rc->newInstance();
          $methodObj = $rc->getMethod($formattedMethod);
         
          $methodObj->invokeArgs($controllerObj, $arguments);
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
    include("{$themePath}/default.tpl.php");
  }
  
}

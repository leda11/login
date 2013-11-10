<?php
/**
* Parse the request and identify controller, method and arguments.
*
* @package HandyCore
*/
class CRequest {
   
   /**
   * variables
   */
   public $cleanUrl;
   public $querystringUrl;
  
  /**
   * Contstruct that takes a urlType as an argument
   * @param boolean $urlType integer
   */   
   public function __construct($urlType=0) {
		$this->cleanUrl = $urlType= 1 ? true : false;
		$this->querystringUrl = $urlType= 2 ? true : false;
    }
  
  /**
   * Init the object by parsing the current url request.
   */
  public function Init($baseUrl = null) {
    // Take current url and divide it in controller, method and arguments
    // hanteraar föjande länkar : 
    // 1./controller/method/arg1/arg2/arg3
    // 2. ToDo - index.php/controller/method/arg1/arg2/arg3
    // 3. ToDo - index.php?q=/controller/method/arg1/arg2/arg3
    $requestUri  = $_SERVER['REQUEST_URI']; //ny 05
    $scriptPart = $scriptName  = $_SERVER['SCRIPT_NAME']; //ny 05

    // (06)  Check if url is in format controller/method/arg1/arg2/arg3
    if(substr_compare($requestUri, $scriptName, 0, strlen($scriptName))) {
      $scriptPart = dirname($scriptName);
    }
    //$query = trim(substr($requestUri, strlen(rtrim($scriptPart, '/'))), '/');  
    
    
    //NY mom03 session - query after base_url, except if optional querystring
  /*  $pos = strpos($query, '?');// felstavat på strpos 9/11 lagt till en med MOS namn
    if($pos){
    	$query = substr($query, 0 , $pos);
    }*/
    // 9/11 
    // Compare REQUEST_URI and SCRIPT_NAME as long they match, leave the rest as current request.
    $i=0;
    $len = min(strlen($requestUri), strlen($scriptName));
    while($i<$len && $requestUri[$i] == $scriptName[$i]) {
      $i++;
    }
    $request = trim(substr($requestUri, $i), '/');
    
    // Check if this looks like a querystring approach link
  /*  if(substr($query, 0, 1) === '?' && isset($_GET['q'])) {
      $query = trim($_GET['q']);
    }
    */
    //LD eget kanske inte behövs
    /*if(substr($query, 0, 1) === '?' && isset($_POST['q'])) {
      $query = trim($_POST['q']);
    }
    */

    //9/11
    // Remove the ?-part from the query when analysing controller/metod/arg1/arg2
    $queryPos = strpos($request, '?');
    if($queryPos !== false) {
      $request = substr($request, 0, $queryPos);
    }
    // 9/11 test tillaggt
    // Check if request is empty and querystring link is set 
    if(empty($request) && isset($_GET['q'])) {
      $request = trim($_GET['q']);
    }
    $splits = explode('/', $request);
        
   // $query =substr($request_uri, strlen(rtrim(dirname($scriptName), '/')));//ändrad (05)
    // 05 05 $splits = explode('/', trim($query, '/'));
  
    // Set controller, method and arguments
    $controller =  !empty($splits[0]) ? $splits[0] : 'index';
    $method       =  !empty($splits[1]) ? $splits[1] : 'index';
    $arguments = $splits;
    unset($arguments[0], $arguments[1]); // remove controller & method part from argument list
    
    // Prepare to create current_url and base_url (05)
    $currentUrl = $this->GetCurrentUrl();
    $parts        = parse_url($currentUrl);
    $baseUrl       = !empty($baseUrl) ? $baseUrl : "{$parts['scheme']}://{$parts['host']}" . (isset($parts['port']) ? ":{$parts['port']}" : '') . rtrim(dirname($scriptName), '/');
      
    // Store it (05)
    $this->base_url      = rtrim($baseUrl, '/') . '/';
    $this->current_url  = $currentUrl;
    $this->request_uri = $requestUri; // (06)
    $this->script_name = $scriptName;
    $this->request         = $request;
    $this->splits         = $splits;
    $this->controller     = $controller;
    $this->method         = $method;
    $this->arguments    = $arguments;
    // ....
  }
  
   /**
   * Get the url to the current page. (05 fixa base_url)
   * Handle querys mom03 part 2
   */
  public function GetCurrentUrl() {
    $url = "http";
    $url .= (@$_SERVER["HTTPS"] == "on") ? 's' : '';
    $url .= "://";
    $serverPort = ($_SERVER["SERVER_PORT"] == "80") ? '' :
    (($_SERVER["SERVER_PORT"] == 443 && @$_SERVER["HTTPS"] == "on") ? '' : ":{$_SERVER['SERVER_PORT']}");
    $url .= $_SERVER["SERVER_NAME"] . $serverPort . htmlspecialchars($_SERVER["REQUEST_URI"]);
    return $url;
  }
//----------------------------------------------------------------------------

/**
  * Create a url in the way it should be created.
  * updated with Sessions between pages implemented
  *
  * @param $url string the relative url or the controller
  * @param $method string the method to use, $url is then the controller or empty for current
  */
  public function CreateUrl($url=null, $method=null, $arguments=null) {
    // If fully qualified just leave it.
    
    if(!empty($url) && (strpos($url, '://') || $url[0] == '/')) {
        $url;
       }
    
    // Get current controller if empty and method choosen. arguments tillagd user 2.1
    if(empty($url) && (!empty($method) || !empty($arguments)) ) {
      $url = $this->controller;
      }
    
    // Get current method if empty and arguments choosen-nytt user.1
    if(empty($method) && !empty($arguments)) {
      $method = $this->method;
    }
    
    // Create url according to configured style
    $prepend = $this->base_url;
    if($this->cleanUrl) {
      ;
    } elseif ($this->querystringUrl) {
      $prepend .= 'index.php?q=';
    } else {
      $prepend .= 'index.php/';
    }
    //nytt
    $url = trim($url, '/');
    $method = empty($method) ? null : '/' . trim($method, '/');
    $arguments = empty($arguments) ? null : '/' . trim($arguments, '/');
    return $prepend . rtrim("$url$method$arguments", '/');
    
  }

//----------------------------------------------------------------------------  
    /**
   * Create a url in the way it should be created. (06)
   *
   */
/*  public function CreateUrl($url=null) {
    $prepend = $this->base_url;
    if($this->cleanUrl) {
      ;
    } elseif ($this->querystringUrl) {
      $prepend .= 'index.php?q=';
    } else {
      $prepend .= 'index.php/';
    }
    return $prepend . rtrim($url, '/');
  }
  */
}

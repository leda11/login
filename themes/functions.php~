<?php
/**
* Helpers for theming, available for all themes in their template files and functions.php.
* This file is included right before the themes own functions.php
*/

function login_menu(){
	$ha = CHandy::instance();
	if( $ha->user->IsAuthenticated()){
		// skapa gravatar
	
		//skapa en länk som triggar en utloggning -lagra i en variabel
		$logstatus = "<a href='" . create_url('user/profile') . "'><img class='gravatar' src='" . get_gravatar(20) . "' alt=''> " . $ha->user['acronym'] . "</a> ";
		
		if($ha->user->IsAdministrator()){
			$logstatus.= "<a href='" . create_url('acp') . "'>acp</a> ";	
		}	
		$logstatus .= "Du är inloggad <a href='" . create_url('user/logout') . "'>Logout</a>" ;
		
	}else{
		$logstatus = "Logga in <a href='" . create_url('user/login') . "'>login</a> ";

	}
	return $logstatus;
}

 
//-----------------------------------------------------------------------------

/**
* Create a url by prepending the base_url.
*/
function base_url($url=null) {
  return CHandy::Instance()->request->base_url . trim($url, '/');
}
//-----------------------------------------------------------------------------
/**
* Create a url to an internal resource.
* new in user mom04.1
* @param string the whole url or the controller. Leave empty for current controller.
* @param string the method when specifying controller as first argument, else leave empty.
* @param string the extra arguments to the method, leave empty if not using method.
*/

function create_url($urlOrController=null, $method=null, $arguments=null) {
  return CHandy::Instance()->request->CreateUrl($urlOrController, $method, $arguments);
}

//-----------------------------------------------------------------------------

/**
* Prepend the theme_url, which is the url to the current theme directory.
*/

	function theme_url($url) {
		$Ha = CHandy::Instance();
		return "{$ha->request->base_url}themes/{$ha->config['theme']['name']}/{$url}";
	}
//-----------------------------------------------------------------------------  
  
/**
* Return the current url.
*/

	function current_url() {
		return CHandy::Instance()->request->current_url;
	}    
 
//-----------------------------------------------------------------------------    

/**
    * Render all views.
    */
    function render_views() {
    	
      return CHandy::Instance()->views->Render();
      
    }
//-----------------------------------------------------------------------------    

/**
    * Print debuginformation from the framework.
    */
    function get_debug() {
      $ha = CHandy::Instance(); 
      if(empty($ha->config['debug'])) {
      	  return;
      }
      $html = null;
      if(isset($ha->config['debug']['db-num-queries']) && $ha->config['debug']['db-num-queries'] && isset($ha->db)) {
      	  $flash = $ha->session->GetFlash('database_numQueries');
      	  $flash = $flash ? "$flash + " : null;
      	  $html .= "<p>Database (db-num-queries) made $flash" . $ha->db->GetNumQueries() . " queries.</p>";
      	  //$html .= "<p>Database made " . $ha->db->GetNumQueries() . " queries.</p>"; 
      }   	  
       if(isset($ha->config['debug']['db-queries']) && $ha->config['debug']['db-queries'] && isset($ha->db)) {
       	   
       	   $flash = $ha->session->GetFlash('database_queries');
       	   $queries = $ha->db->GetQueries();
       	   if($flash) {
       	   	   $queries = array_merge($flash, $queries);
       	   }
       	$html .= "<p>Database made the following queries.</p><pre>" . implode('<br/><br/>', $queries) . "</pre>";
       }
      if(isset($ha->config['debug']['timer']) && $ha->config['debug']['timer']) {
      	  $html .= "<p>Page was loaded in " . round(microtime(true) - $ha->timer['first'], 5)*1000 . " msecs.</p>";
      } 
      if(isset($ha->config['debug']['handy']) && $ha->config['debug']['handy']) {
        $html .= "<hr><h3>Debuginformation</h3><p>The content of CHandy:</p><pre>" . htmlent(print_r($ha, true)) . "</pre>";
      }  
      if(isset($ha->config['debug']['session']) && $ha->config['debug']['session']){
      	$html .= "<hr><h3>SESSION</h3><p>The content of CHandy->session:</p><pre>" . htmlent(print_r($ha->session, true)) . "</pre>";
      	$html .= "<p>The content of \$_SESSION:</p><pre>" . htmlent(print_r($_SESSION, true)) . "</pre>";	  
     
      }      
      return $html;
    }
//-----------------------------------------------------------------------------    

    /**
    * Get messages stored in flash-session.
    */
    function get_messages_from_session() {
      $messages = CHandy::Instance()->session->GetMessages();
      
      $html = null;
      if(!empty($messages)) {
      	  
        foreach($messages as $val) {
          $valid = array('info', 'notice', 'success', 'warning', 'error', 'alert');
          $class = (in_array($val['type'], $valid)) ? $val['type'] : 'info';
          $html .= "<div class='$class'>{$val['message']}</div>\n";
        }
      }
      return $html;
    }
    
//--------------------------------------------------------------------------
        /**
    * Get a gravatar based on the user's email.
    */
    function get_gravatar($size=null) {
      return 'http://www.gravatar.com/avatar/' . md5(strtolower(trim(CHandy::Instance()->user['email']))) . '.jpg?' . ($size ? "s=$size" : null);
    }

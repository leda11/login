<?php
/**
* Site configuration, this file is changed by user per site.
*
*/

/**
* Set level of error reporting
*/
error_reporting(-1);
ini_set('display_errors', 1);

// handle outgoing links (06)
/**
* What type of urls should be used? 
*
* default      = 0      => index.php/controller/method/arg1/arg2/arg3
* clean        = 1      => controller/method/arg1/arg2/arg3
* querystring  = 2      => index.php?q=controller/method/arg1/arg2/arg3
*/
$ha->config['url_type'] = 1;

/**
* Set a base_url to use another than the default calculated
*/
$ha->config['base_url'] = null;

/**
* Define session name
*/
//$ha->config['session_name'] = preg_replace('/[:\.\/-_]/', '', $_SERVER["SERVER_NAME"]);
$ha->config['session_name'] = preg_replace('/[:\.\/-_]/', '', __DIR__);

$ha->config['session_key']  = 'handy';

/**
 * Create user ok
 */
$ha->config['create_new_users']= true;;

/**
* Set database(s).
*/
$ha->config['database'][0]['dsn'] = 'sqlite:' . HANDY_SITE_PATH . '/data/.ht.sqlite';

/**
* How to hash password of new users, choose from: plain, md5salt, md5, sha1salt, sha1.
*/
// choose between md5 plain, md5, md5salt, sha1, sha1salt
$ha->config['hashing_algorithm'] = 'sha1salt';
    
/**
* Define server timezone
*/
$ha->config['timezone'] = 'Europe/Stockholm';

/**
* Define internal character encoding
*/
$ha->config['character_encoding'] = 'UTF-8';

/**
* Define language
*/
$ha->config['language'] = 'en';


/**
 * Set what to show as debug or developer information in the get_debug() theme helper.
*/
    $ha->config['debug']['handy'] = false;
    $ha->config['debug']['db-num-queries'] = true;
    $ha->config['debug']['db-queries'] = true;
    $ha->config['debug']['session'] = false;
    $ha->config['debug']['timer'] = true;
    //$ha->config['debug']= 0;



/**
* Define the controllers, their classname and enable/disable them.
*
* The array-key is matched against the url, for example:
* the url 'developer/dump' would instantiate the controller with the key "developer", that is
* CCDeveloper and call the method "dump" in that class. This process is managed in:
* $ha->FrontControllerRoute();
* which is called in the frontcontroller phase from index.php.
*/
$ha->config['controllers'] = array(
  'index'     => array('enabled' => true,'class' => 'CCIndex'),
  'developer' => array('enabled' => true, 'class' => 'CCDeveloper'),
  'guestbook' => array('enabled' => true, 'class' => 'CCGuestbook'),
  'user'	  => array('enabled' => true, 'class' => 'CCUser' ),
  'acp'       => array('enabled' => true, 'class' => 'CCAdminControlPanel' ), 
);



/**
* Settings for the theme.
*/
$ha->config['theme'] = array(
  // The name of the theme in the theme directory
  'name'    => 'core',
);



<?php
/**
* Standard controller layout.
*
* @package HandyCore
*/
class CCIndex implements IController {

   /**
    * Implementing interface IController. All controllers must have an index action.
    */
   public function Index() {   
      global $ha;
      $ha->data['title'] = "The Index Controller";
      $ha->data['main'] = "<h1>The Index Controller</h1><p>This is what you can do for now:</p>";      
   }

} 

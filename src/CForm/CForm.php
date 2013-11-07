<?php

/**
 * En klass som skapar ett formulär
 *
 */

class CForm {
	

  /**
* Properties
*/
  public $form; // array with settings for the form
  public $elements; // array with all form elements
	
	public function  __constuct($form_array, $elements_array){
		$this->$form = $form_array; //array with settings for the form
		$this->elements= $elements_array; // array with the form elements 
	}
	
//-----------------------------------------------------------------------------	
	/**
   * Add a form elements
   */
  public function AddElement($key, $element) {
    $this->elements[$key] = $element;
    $this->elements[$key]['name'] = $key;
  }
  
//-----------------------------------------------------------------------------
  	/**
  	* Return HTML for the form
  	*/
  	public function GetHTML() {
  		$id      = isset($this->form['id'])      ? " id='{$this->form['id']}'" : null;
  		$class    = isset($this->form['class'])   ? " class='{$this->form['class']}'" : null;
  		$name    = isset($this->form['name'])    ? " name='{$this->form['name']}'" : null;
  		$action = isset($this->form['action'])  ? " action='{$this->form['action']}'" : null;
  		$method = " method='post'";
  		$elements = $this->GetHTMLForElements();
  		$html = <<< EOD
  			\n<form{$id}{$class}{$name}{$action}{$method}>
  			<fieldset>
  			{$elements}
  			</fieldset>
  			</form>
EOD;
    return $html;
  }	
  
//-----------------------------------------------------------------------------
    /**
   * Return HTML for the elements
   */
   public function GetHTMLForElements() {
    $html = null;
    $i = 0;
    foreach($this->elements as $key => $val) {
      $defaultId = "form-input-" . $i++;
      $id = isset($val['id']) ? $val['id'] : $defaultId;
      $class = isset($val['class']) ? " class='{$val['class']}'" : null;
      $name = " name='" . (isset($val['name']) ? $val['name'] : $key) . "'";
      $label = isset($val['label']) ? ($val['label'] . (isset($val['mandatory']) && $val['mandatory'] ? "<span class='form-element-mandatory'> *</span>" : null)) : null;
      $autofocus = isset($val['autofocus']) && $val['autofocus'] ? " autofocus='autofocus'" : null;
     
      $type    = isset($val['type']) ? " type='{$val['type']}'" : null;
      $value    = isset($val['value']) ? " value='{$val['value']}'" : null;
      $html .= "<p><label for='$id'>$label</label><br><input id='$id'{$type}{$class}{$name}{$value}{$autofocus} /></p>\n";         
    }
    return $html;
  }
//-----------------------------------------------------------------------------
        /**
       * Get the value of a element
       */
      public function GetValue($key) {
        return (isset($_POST[$key])) ? $_POST[$key] : null;
      }
//-----------------------------------------------------------------------------
         /**
       * Check if a form was submitted and perform validation and call callbacks
       */
      public function CheckIfSubmitted() {
        $submitted = false;
        if(!empty($_POST)) {
          $submitted = true;
          foreach($this->elements as $key => $val) {
            if(isset($_POST[$key]) && isset($val['callback'])) {
              call_user_func($val['callback'], $this);
            }
          }
        }
        return $submitted;
      }
}
	

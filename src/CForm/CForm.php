<?php
    /**
    * A utility class to easy creating and handling of forms
    *
    * @package HandyCore
    */
    class CFormElement implements ArrayAccess{

      /**
       * Properties
       */
      public $attributes;
     

      /**
       * Constructor
       *
       * @param string name of the element.
       * @param array attributes to set to the element. Default is an empty array.
       */
      public function __construct($name, $attributes=array()) {
        $this->attributes = $attributes;   
        $this['name'] = $name;
      }
  //-------------------------------------------------------------------------------    
     
     
      /**
       * Implementing ArrayAccess for this->attributes
       */
      public function offsetSet($offset, $value) { if (is_null($offset)) { $this->attributes[] = $value; } else { $this->attributes[$offset] = $value; }}
      public function offsetExists($offset) { return isset($this->attributes[$offset]); }
      public function offsetUnset($offset) { unset($this->attributes[$offset]); }
      public function offsetGet($offset) { return isset($this->attributes[$offset]) ? $this->attributes[$offset] : null; }

//-------------------------------------------------------------------------------
 public function Validate($rules) {
        $tests = array(
          'fail' => array(
            'message' => 'Will always fail.',
            'test' => 'return false;',
          ),
          'pass' => array(
            'message' => 'Will always pass.',
            'test' => 'return true;',
          ),
          'not_empty' => array(
            'message' => 'Can NOT be empty.',
            'test' => 'return $value != "";',
          ),
          'min_length' => array(
          	  'message' => 'You must have at least 4 tokens',
              'test' => function($result){ return strlen($value) < 3 ; }
          ),
          'email_address' => array(
          	  'message' => 'Must be an email adress.', 
          	  'test' => function($value) { return preg_match('/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i', $value) === 1; } ),
        );
        //return strlen($value) !> 3 ;'
        $pass = true;
        $messages = array();
        $value = $this['value'];
        foreach($rules as $key => $val) {
          $rule = is_numeric($key) ? $val : $key;
          
        /*  if(!isset($tests[$rule])) throw new Exception("Validation of form element failed, no such validation rule exists. $rule");
          if(eval($tests[$rule]['test']) === false) {
            $messages[] = $tests[$rule]['message']; // meddelande som kommer under fromelementet
            $pass = false;
          }
        }*/
        // from CForm
       if(!isset($tests[$rule])) throw new Exception("Validation of form element failed, no such validation rule exists: $rule");
      $arg = is_numeric($key) ? null : $val;

      $test = ($rule == 'custom_test') ? $arg : $tests[$rule];
      $status = null;
      if(is_callable($test['test'])) {
        $status = $test['test']($value);
      } else {
        $status = eval($test['test']);
      }

      if($status === false) {
      	  $messages[] = $test['message']. " :". $value . " tecken: " . strlen($value);
        $pass = false;
      }
    }
        
        
        if(!empty($messages)) 
        	$this['validation_messages'] = $messages;
        return $pass;
      }
//----------------------------------------------------------------------------------      

/**
       * Get HTML code for a element.
       *
       * @returns HTML code for the element.
       */
      public function GetHTML() {
        $id = isset($this['id']) ? $this['id'] : 'form-element-' . $this['name'];
        $class = isset($this['class']) ? " class='{$this['class']}'" : null;
        //new validating
        $validates = (isset($this['validation-pass']) && $this['validation-pass'] === false) ? ' validation-failed' : null;
        $class = (isset($class) || isset($validates)) ? " class='{$class}{$validates}'" : null;
        
        $name = " name='{$this['name']}'";
        $label = isset($this['label']) ? ($this['label'] . (isset($this['required']) && $this['required'] ? "<span class='form-element-required'>*</span>" : null)) : null;
        $autofocus = isset($this['autofocus']) && $this['autofocus'] ? " autofocus='autofocus'" : null;   
        $readonly = isset($this['readonly']) && $this['readonly'] ? " readonly='readonly'" : null;   
        $type    = isset($this['type']) ? " type='{$this['type']}'" : null;
        $value    = isset($this['value']) ? " value='{$this['value']}'" : null;
        //new validation
         $messages = null;
         if(isset($this['validation_messages'])) {
         	 
         	 $message = null;
         	 foreach($this['validation_messages'] as $val) {
         	 	 $message .= "<li>{$val}</li>\n";
         	 	 //echo $message; //Test: funkar att skriva ut $val och $message här
         	 }
         	 $messages = "<ul class='validation-message'>\n{$message}</ul>\n";
         	 // echo $messages;//Test: funkar med styling
         	 //echo "Type: ". $type . " this->Type: " .$this['type'];//Test ok
         }

        if($type && $this['type'] == 'submit') {
        	return "<p><input id='$id'{$type}{$class}{$name}{$value}{$autofocus}{$readonly} />{$messages}</p>\n";   
        } else {
          return "<p><label for='$id'>$label</label><br><input id='$id'{$type}{$class}{$name}{$value}{$autofocus}{$readonly} />{$messages}</p>\n";          
        }
      }
//-------------------------------------------------------------------------------

      /**
       * Use the element name as label if label is not set.
       */
      public function UseNameAsDefaultLabel() {
        if(!isset($this['label'])) {
          $this['label'] = ucfirst(strtolower(str_replace(array('-','_'), ' ', $this['name']))).':';
        }
      }

//-------------------------------------------------------------------------------
      /**
       * Use the element name as value if value is not set.
       */
      public function UseNameAsDefaultValue() {
        if(!isset($this['value'])) {
          $this['value'] = ucfirst(strtolower(str_replace(array('-','_'), ' ', $this['name'])));
        }
      }
    }

//==============================================================================
    class CFormElementText extends CFormElement {
      /**
       * Constructor
       *
       * @param string name of the element.
       * @param array attributes to set to the element. Default is an empty array.
       */
      public function __construct($name, $attributes=array()) {
        parent::__construct($name, $attributes);
        $this['type'] = 'text';
        $this->UseNameAsDefaultLabel();
      }
    }

//==============================================================================
    class CFormElementPassword extends CFormElement {
      /**
       * Constructor
       *
       * @param string name of the element.
       * @param array attributes to set to the element. Default is an empty array.
       */
      public function __construct($name, $attributes=array()) {
        parent::__construct($name, $attributes);
        $this['type'] = 'password';
        $this->UseNameAsDefaultLabel();
      }
    }

//==============================================================================
    class CFormElementSubmit extends CFormElement {
      /**
       * Constructor
       *
       * @param string name of the element.
       * @param array attributes to set to the element. Default is an empty array.
       */
      public function __construct($name, $attributes=array()) {
        parent::__construct($name, $attributes);
        $this['type'] = 'submit';
        $this->UseNameAsDefaultValue();
      }
    }

//==============================================================================
    class CForm implements ArrayAccess {

      /**
       * Properties
       */
      public $form;     // array with settings for the form
      public $elements; // array with all form elements
     

      /**
       * Constructor
       */
      public function __construct($form=array(), $elements=array()) {
        $this->form = $form;
        $this->elements = $elements;
      }

//-------------------------------------------------------------------------------
      /**
       * Implementing ArrayAccess for this->elements
       */
      public function offsetSet($offset, $value) { if (is_null($offset)) { $this->elements[] = $value; } else { $this->elements[$offset] = $value; }}
      public function offsetExists($offset) { return isset($this->elements[$offset]); }
      public function offsetUnset($offset) { unset($this->elements[$offset]); }
      public function offsetGet($offset) { return isset($this->elements[$offset]) ? $this->elements[$offset] : null; }

//-------------------------------------------------------------------------------
      /**
       * Add a form element
       */
      public function AddElement($element) {
        $this[$element['name']] = $element;
        return $this;
      }
//-------------------------------------------------------------------------------     
      /**
      * Set validation to a form element
      *
      * @param $element string the name of the formelement to add validation rules to.
      * @param $rules array of validation rules.
      * @returns $this CForm
      */
      public function SetValidation($element, $rules) {
      	  $this[$element]['validation'] = $rules;
      	  return $this;
      }   
//-------------------------------------------------------------------------------
      /**
       * Return HTML for the form
       * @param $type string what part of the form to return.
       * @returns string with HTML for the form.
       */
      public function GetHTML($type=null) {
        $id      = isset($this->form['id'])      ? " id='{$this->form['id']}'" : null;
        $class    = isset($this->form['class'])   ? " class='{$this->form['class']}'" : null;
        $name    = isset($this->form['name'])    ? " name='{$this->form['name']}'" : null;
        $action = isset($this->form['action'])  ? " action='{$this->form['action']}'" : null;
        $method = " method='post'";
        // new part to have a more dynamic form that can handle form element and not just the total form.
        // method can also take an argument know
         if($type == 'form') {
         	 return "<form{$id}{$class}{$name}{$action}{$method}>";
         }
               
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

//-------------------------------------------------------------------------------
      /**
       * Return HTML for the elements
       */
      public function GetHTMLForElements() {
        $html = null;
        foreach($this->elements as $element) {
          $html .= $element->GetHTML();
        }
        return $html;
      }
     
//-------------------------------------------------------------------------------
      /**
       * Check if a form was submitted and perform call callbacks
       */
    public function CheckIfSubmitted() {
    $submitted = false;
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
      $submitted = true;
      foreach($this->elements as $element) {
        if(isset($_POST[$element['name']])) {
          $element['value'] = $_POST[$element['name']];
          if(isset($element['callback'])) {
            call_user_func($element['callback'], $this);
          }
        }
      }
    }
    return $submitted;
  }

  
//-----------------------------------------------------------------------------  
/**
* Check if a form was submitted and perform validation and call callbacks.
*
* The form is stored in the session if validation fails. The page should then be redirected
* to the original form page, the form will populate from the session and should then be
* rendered again.
*
* @returns boolean true if validates, false if not validate, null if not submitted.
*/

public function Check() {
    $validates = null;//new
    $values = array();//new
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
      unset($_SESSION['form-validation-failed']);//new
      $validates = true;//new
      foreach($this->elements as $element) {
        if(isset($_POST[$element['name']])) {
          $values[$element['name']]['value'] = $element['value'] = $_POST[$element['name']];//new HUR?
          if(isset($element['validation'])) {//new
            $element['validation-pass'] = $element->Validate($element['validation']);//new
            if($element['validation-pass'] === false) {
              $values[$element['name']] = array('value'=>$element['value'], 'validation_messages'=>$element['validation_messages']);
              $validates = false;
            }
          }
          if(isset($element['callback']) && $validates) {
             call_user_func($element['callback'], $this);
          }
        }
      }
    } else if(isset($_SESSION['form-validation-failed'])) {
      foreach($_SESSION['form-validation-failed'] as $key => $val) {
        $this[$key]['value'] = $val['value'];
        if(isset($val['validation_messages'])) {
          $this[$key]['validation_messages'] = $val['validation_messages'];
          $this[$key]['validation-pass'] = false;
        }
      }
      unset($_SESSION['form-validation-failed']);
    }
    // if anything not validdated put values array in  $_SESSION['form-validation-failed']
    if($validates === false) {
      $_SESSION['form-validation-failed'] = $values;
    }
    return $validates;
  }                                       
}

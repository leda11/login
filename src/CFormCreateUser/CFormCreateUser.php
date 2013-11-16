<?php
/**
    * A form for editing the user profile.
    *
    * @package HandyCore
    */
class CFormCreateUser extends CForm {

      /**
       * Constructor - added validation
       */
      public function __construct($object) {
    parent::__construct();
    $this->AddElement(new CFormElementText('acronym', array('required'=>true)))
         ->AddElement(new CFormElementPassword('password', array('required'=>true)))
         ->AddElement(new CFormElementPassword('password1', array('required'=>true, 'label'=>'Password again:')))
         ->AddElement(new CFormElementText('name', array('required'=>true)))
         ->AddElement(new CFormElementText('email', array('required'=>true)))
         ->AddElement(new CFormElementSubmit('create', array('callback'=>array($object, 'DoCreate'))));
         
    $this->SetValidation('acronym', array('not_empty'))
         ->SetValidation('password', array('min_length'))
         ->SetValidation('password1', array('not_empty'))
         ->SetValidation('name', array('not_empty'))
         ->SetValidation('email', array('email_address'));
  }
}
// funkar bara med en regel  kunde inte ha både mot_empty och min_length
//Skriv inte kokmmentarer i ocan this sats
//length minimum 4 letters - password
// FUNKAR INTE SÄGER OVERLOAD i CForm:: lin 118 -> SetValidation() -passworl
// check if email ok - email

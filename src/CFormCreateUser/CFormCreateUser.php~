<?php
/**
    * A form for editing the user profile.
    *
    * @package HandyCore
    */
class CFormUserLogin extends CForm {

      /**
       * Constructor - added validation
       */
      public function __construct($object) {
        parent::__construct();
        $this->AddElement(new CFormElementText('acronym'))
             ->AddElement(new CFormElementPassword('password'))
             ->AddElement(new CFormElementSubmit('login', array('callback'=>array($object, 'DoLogin'))));

        $this->SetValidation('acronym', array('not_empty'))
             ->SetValidation('password', array('not_empty'));
      }  
}

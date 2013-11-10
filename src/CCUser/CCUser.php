    <?php
    /**
    * A user controller  to manage login and view edit the user profile.
    *
    * @package HandyCore
    */
    class CCUser extends CObject implements IController  {

      
      /**
       * Constructor
       */
      public function __construct() {
        parent::__construct();
       // $this->userModel = new CMUser();// bortkommenterar i mos mom04 del3
      }

//-----------------------------------------------------------------------------
      /**
       * Show profile information of the user.
       */
       public function Index() {
        $this->views->SetTitle('User Controller');
        $this->views->AddInclude(__DIR__ . '/index.tpl.php', array(
          'is_authenticated'=>$this->user->IsAuthenticated(), 
          'user'=>$this->user->GetProfile(),
        ));
      }


//-----------------------------------------------------------------------------     
/**
* View and edit user profile. MOs mom4 del 3. direct to a profile site
*/
  public function Profile() {
    $this->views->SetTitle('User Profile');
    $this->views->AddInclude(__DIR__ . '/profile.tpl.php', array(
      'is_authenticated'=>$this->user->IsAuthenticated(),
      'user'=>$this->user->GetProfile(),
    ));
  }

//-----------------------------------------------------------------------------
 /**
* Authenticate and login a user. 
* Add login template
*/
      public function Login() {
        $form = new CForm();
        // AddElement($key, $element)
        $form->AddElement('acronym', array('label'=>'Acronym or email:', 'type'=>'text'));
        $form->AddElement('password', array('label'=>'Password:', 'type'=>'password'));
        $form->AddElement('doLogin', array('value'=>'Login', 'type'=>'submit', 'callback'=>array($this, 'DoLogin')));        
        $form->CheckIfSubmitted();      // MOS har inte med denna i userlogin		
        
        $this->views->SetTitle('Login');
        $this->views->AddInclude(__DIR__ . '/login.tpl.php', array('login_form'=>$form->GetHTML()));     

        }
//-----------------------------------------------------------------------------
      /**
       * Perform a login of the user as callback on a submitted form.
       * GetValue() can be foound in CForm
       */
      public function DoLogin($form) {
        if($this->user->Login($form->GetValue('acronym'), $form->GetValue('password'))) {
          $this->RedirectToController('profile');
        } else {
          $this->RedirectToController('login');     
        }
      }
      
 //-----------------------------------------------------------------------------
     
      /**
       * Logout a user.
       */
      public function Logout() {
        $this->user->Logout();
        $this->RedirectToController();
      }
     
//-----------------------------------------------------------------------------
      /**
       * Init the user database.
       */
      public function Init() {
        $this->user->Init();
        $this->RedirectToController();
      }
     

    } 

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
       // justerad
       public function Index() {
        $this->views->SetTitle('User Controller')
        			->AddInclude(__DIR__ . '/index.tpl.php', array(
        				'is_authenticated'=>$this->user['isAuthenticated'], 
        				'user'=>$this->user,//GetUser anropas inte mer.
        				));
      }


//-----------------------------------------------------------------------------     
/**
* View and edit user profile. MOs mom4 del 3. direct to a profile site
*/
/*  public function Profile() {
    $this->views->SetTitle('User Profile');
    $this->views->AddInclude(__DIR__ . '/profile.tpl.php', array(
      'is_authenticated'=>$this->user->IsAuthenticated(),
      'user'=>$this->user->GetProfile(),
    ));
  }*/
      /**
       * View and edit user profile. 
       */
       //uppdaterad 11/11 
      public function Profile() {   
        $form = new CFormUserProfile($this, $this->user);
        $form->CheckIfSubmitted();

        $this->views->SetTitle('User Profile')
                    ->AddInclude(__DIR__ . '/profile.tpl.php', array(
                      'is_authenticated'=>$this->user['isAuthenticated'],
                      'user'=>$this->user,
                      'profile_form'=>$form->GetHTML(),
                    ));
      }
//-----------------------------------------------------------------------------
 /**
* Authenticate and login a user. 
* Add login template
*/
	// Delar flyttade till CFormUserLogin klassen
     /* public function Login() {
        $form = new CForm();
        // AddElement($key, $element)
        $form->AddElement('acronym', array('label'=>'Acronym or email:', 'type'=>'text'));
        $form->AddElement('password', array('label'=>'Password:', 'type'=>'password'));
        $form->AddElement('doLogin', array('value'=>'Login', 'type'=>'submit', 'callback'=>array($this, 'DoLogin')));        
        $form->CheckIfSubmitted();      // MOS har inte med denna i userlogin		
        
        $this->views->SetTitle('Login');
        $this->views->AddInclude(__DIR__ . '/login.tpl.php', array('login_form'=>$form->GetHTML()));     
       */
       /**
       * Authenticate and login a user.
       */
       // new 11/11 create new class CUserLoogin
       public function Login() {
       	   $form = new CFormUserLogin($this);
       	   $form->CheckIfSubmitted();
       	   $this->views->SetTitle('Login')
                ->AddInclude(__DIR__ . '/login.tpl.php', array('login_form'=>$form->GetHTML()));
       }
        
//-----------------------------------------------------------------------------
      /**
       * Perform a login of the user as callback on a submitted form.
       * GetValue() can be foound in CForm
       */
   /*   public function DoLogin($form) {
        if($this->user->Login($form->GetValue('acronym'), $form->GetValue('password'))) {
          $this->RedirectToController('profile');
        } else {
          $this->RedirectToController('login');     
        }
      }*/
      /**
      * Perform a login of the user as callback on a submitted form.
      */
      //uppdaterad 12/11
  public function DoLogin($form) {
    if($this->user->Login($form['acronym']['value'], $form['password']['value'])) {
      $this->AddMessage('success', "Welcome {$this->user['name']}.");
      $this->RedirectToController('profile');
    } else {
      $this->AddMessage('notice', "Failed to login, user does not exist or password does not match.");
      $this->RedirectToController('login');
    }
  }
 //-----------------------------------------------------------------------------
       /**
       * Change the password.
       */
       //new 11/11
      public function DoChangePassword($form) {
        if($form['password']['value'] != $form['password1']['value'] || empty($form['password']['value']) || empty($form['password1']['value'])) {
          $this->AddMessage('error', 'Password does not match or is empty.');
        } else {
          $ret = $this->user->ChangePassword($form['password']['value']);
          $this->AddMessage($ret, 'Saved new password.', 'Failed updating password.');
        }
        $this->RedirectToController('profile');
      }
//-----------------------------------------------------------------------------      
       /**
       * Save updates to profile information.
       */
       //new 11/11
      public function DoProfileSave($form) {
        $this->user['name'] = $form['name']['value'];
        $this->user['email'] = $form['email']['value'];
        $ret = $this->user->Save();
        $this->AddMessage($ret, 'Saved profile.', 'Failed saving profile.');
        $this->RedirectToController('profile');
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

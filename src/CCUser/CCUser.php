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
      }

//-----------------------------------------------------------------------------
      /**
       * Show profile information of the user.
       */
       public function Index() {
        $this->views->SetTitle('User Controller')
        			->AddInclude(__DIR__ . '/index.tpl.php', array(
        				'is_authenticated'=>$this->user['isAuthenticated'], 
        				'user'=>$this->user,
        				));
      }
//-----------------------------------------------------------------------------     

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
       * Authenticate and login a user. using Check insted of CHeckIfSubmitted
       */
      public function Login() {
        $form = new CFormUserLogin($this);
        if($form->Check() === false) {//new
          $this->AddMessage('notice', 'Some fields did not validate and the form could not be processed.');
          $this->RedirectToController('login');
        }
        $this->views->SetTitle('Login')
                    ->AddInclude(__DIR__ . '/login.tpl.php', array('login_form'=>$form->GetHTML(),
                    						'allow_create_user' => CHandy::Instance()->config['create_new_users'],
                    						));
      }
      
      // MOS variant när länkt på loginform sidan att skapa ny användare är i formuläret och inte utanför som jag läst det.
       /**
       * Authenticate and login a user.
       */
 /*     public function Login() {
        $form = new CFormUserLogin($this);
        if($form->Check() === false) {
          $this->AddMessage('notice', 'You must fill in acronym and password.');
          $this->RedirectToController('login');
        }
        $this->views->SetTitle('Login')
                    ->AddInclude(__DIR__ . '/login.tpl.php', array(
                      'login_form' => $form,
                      'allow_create_user' => CHandy::Instance()->config['create_new_users'],
                      'create_user_url' => $this->CreateUrl(null, 'create'),
                    ));
      }
      */
//-----------------------------------------------------------------------------
 
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
       * View Create user form and create a new user account .
       */
     public function Create() {
        $form = new CFormCreateuser($this);
        if($form->Check() === false) {//new
        	$this->AddMessage('notice', 'Some fields did not validate and the form could not be processed. -in CCUser::Create()');
          $this->RedirectToController('create');
        }
        $this->views->SetTitle('Create new user')// visas i fönsterlist
                    ->AddInclude(__DIR__ . '/create.tpl.php', array('create_form'=>$form->GetHTML()));     
      }
      
      
      //mos metod
/*       public function Create() {
    $form = new CFormUserCreate($this);
    if($form->Check() === false) {
      $this->AddMessage('notice', 'You must fill in all values.');
      $this->RedirectToController('Create');
    }
    $this->views->SetTitle('Create user')
                ->AddInclude(__DIR__ . '/create.tpl.php', array('form' => $form->GetHTML()));     
  }
  */
//-----------------------------------------------------------------------------
 //new 14/11 
        /**
       * Perform a creation of a user as callback on a submitted form.
       *
       * @param $form CForm the form that was submitted
       */
  public function DoCreate($form) {
  	// 1.kolla att de 2 passwordfälten stämmer överens. eller om någon av dem är tom
  	if($form['password']['value'] != $form['password1']['value'] || empty($form['password']['value']) || empty($form['password1']['value'])) {
  		
  		$this->AddMessage('error', "Failed to create, password:( {$form['password']['value']} ) passwordl: ( {$form['password1']['value']}) does not match or  is empty. - in CCUser::DoCreater()");
  		$this->RedirectToController('create');
  	// 2. check i the rests have values
    } else if($this->user->Create( $form['acronym']['value'],
                               $form['password']['value'],                             
                               $form['name']['value'],
                               $form['email']['value']
                               )) {
    $this->AddMessage('success', "Created a user account for: {$form['name']['value']} - in CCuser::DoCreate.");// $this->user fungerar inte här. Ändra $this->user till $form OK
    	 //logga in den nya användaren
    	 $this->user->Login($form['acronym']['value'], $form['password']['value']  );
    	 $this->RedirectToController('profile');
    }
    else{
    	$this->AddMessage('notice', "Failed to create an account. Information missing. -in CUser::DoCreater()");
         $this->RedirectToController('create');	
  }
}
 
  //mos
/*      public function DoCreate($form) {   
        if($form['password']['value'] != $form['password1']['value'] || empty($form['password']['value']) || empty($form['password1']['value'])) {
          $this->AddMessage('error', 'Password does not match or is empty.');
          $this->RedirectToController('create');
        } else if($this->user->Create($form['acronym']['value'],
                               $form['password']['value'],
                               $form['name']['value'],
                               $form['email']['value']
                               )) {
          $this->AddMessage('success', "Welcome {$this->user['name']}. Your have successfully created a new account.");
          $this->user->Login($form['acronym']['value'], $form['password']['value']);
          $this->RedirectToController('profile');
        } else {
          $this->AddMessage('notice', "Failed to create an account.");
          $this->RedirectToController('create');
        }
      }
      */
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
//-----------------------------------------------------------------------------
   

    } 

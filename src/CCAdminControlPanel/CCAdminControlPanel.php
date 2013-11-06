   <?php
    /**
    * A user controller  to manage login and view edit the user profile.
    *
    * @package HandyCore
    */
    class CCAdminControlPanel extends CObject implements IController  {

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
        $this->views->SetTitle('Admin Control Pannel');
        $this->views->AddInclude(__DIR__ . '/index.tpl.php', array(
         // 'is_authenticated'=>$this->user->IsAuthenticated(), 
          //'user'=>$this->user->GetUserProfile(),
        ));
      }
 } 

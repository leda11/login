<?php 
/**
 * A model class to handle the guestbook
 * 
 * @package HandyCore
 * inherit CObject to be able to use $this
 */
 
 class CMUser extends CObject implements IHasSQL, ArrayAccess{ 
 	
 	 /**
 	 * Properties to handle arrays
 	 */
 	 public $profile = array();
   
 	 /**
   * Constructor
   */
  public function __construct($ha=null) {
    parent::__construct($ha);//uppdaHandyinstancen.-> CObject
    $profile = $this->session->GetAuthenticatedUser();
    $this->profile = is_null($profile) ? array() : $profile;
    $this['isAuthenticated'] = is_null($profile) ? false : true;
  }
// ----------------------------------------------------------------------------------  

  /**
* Implementing ArrayAccess for $this->profile
*/
  public function offsetSet($offset, $value) { 
  	if (is_null($offset)) { $this->profile[] = $value; 
  	} else { 
  		$this->profile[$offset] = $value; 
  	}
  }
  public function offsetExists($offset) { 
  	return isset($this->profile[$offset]); 
  }
  public function offsetUnset($offset) { 
  	unset($this->profile[$offset]); 
  }
  public function offsetGet($offset) { 
  	return isset($this->profile[$offset]) ? $this->profile[$offset] : null; 
  }
// ----------------------------------------------------------------------------------  
      /**
       * Implementing interface IHasSQL. Encapsulate all SQL used by this class.
       * added grouptables and questions
       * @param string $key the string that is the key of the wanted SQL-entry in the array.
       */
      public static function SQL($key=null) {
        $queries = array(
          'drop table user'         => "DROP TABLE IF EXISTS User;",
          'drop table group'        => "DROP TABLE IF EXISTS Groups;",
          'drop table user2group'   => "DROP TABLE IF EXISTS User2Groups;",
          'create table user' 		=> "CREATE TABLE IF NOT EXISTS User (id INTEGER PRIMARY KEY, acronym TEXT KEY, name TEXT, email TEXT, algorithm TEXT, salt TEXT, password TEXT, created DATETIME default (datetime('now')), updated DATETIME default NULL);",
          'create table group' 		=> "CREATE TABLE IF NOT EXISTS Groups (id INTEGER PRIMARY KEY, acronym TEXT KEY, name TEXT, created DATETIME default (datetime('now')), updated DATETIME default NULL);",
          'create table user2group' => "CREATE TABLE IF NOT EXISTS User2Groups (idUser INTEGER, idGroups INTEGER, created DATETIME default (datetime('now')), PRIMARY KEY(idUser, idGroups));",
          'insert into user'        => 'INSERT INTO User (acronym, name, email, algorithm, salt, password) VALUES (?,?,?,?,?,?);',
          'insert into group'       => 'INSERT INTO Groups (acronym,name) VALUES (?,?);',
          'insert into user2group'  => 'INSERT INTO User2Groups (idUser,idGroups) VALUES (?,?);',
          'check user password'     => 'SELECT * FROM User WHERE (acronym=? OR email=?);', // control of password is removed from this question  - password=? AND 
          'get group memberships'   => 'SELECT * FROM Groups AS g INNER JOIN User2Groups AS ug ON g.id=ug.idGroups WHERE ug.idUser=?;',
          'update profile' 			=> "UPDATE User SET name=?, email=?, updated=datetime('now') WHERE id=?;",                                             
          'update password'		 	=> "UPDATE User SET algorithm=?, salt=?, password=?, updated=datetime('now') WHERE id=?;",
         );
        if(!isset($queries[$key])) {
          throw new Exception("No such SQL query, key '$key' was not found.");
        }                                                                                                       
        return $queries[$key];
      }
// ----------------------------------------------------------------------------------

      /**
       * Init the database and create appropriate tables.
       * Added 2 users admin and doe, belonging to the group user. (shall really be put in the init of the framework.)
       */
      public function Init() {
        try {
          $this->db->ExecuteQuery(self::SQL('drop table user2group'));
          $this->db->ExecuteQuery(self::SQL('drop table group'));
          $this->db->ExecuteQuery(self::SQL('drop table user'));
          $this->db->ExecuteQuery(self::SQL('create table user'));
          $this->db->ExecuteQuery(self::SQL('create table group'));
          $this->db->ExecuteQuery(self::SQL('create table user2group'));
          // create pw with hash algoritm
          $password = $this->CreatePassword('root');
          $this->db->ExecuteQuery(self::SQL('insert into user'), array('root', 'The Administrator', 'root@dbwebb.se', $password['algorithm'], $password['salt'], $password['password']));
          $idRootUser = $this->db->LastInsertId();
          // new. create PW with hash and salt
          $password = $this->CreatePassword('doe');
          $this->db->ExecuteQuery(self::SQL('insert into user'), array('doe', 'John/Jane Doe', 'doe@dbwebb.se', $password['algorithm'], $password['salt'], $password['password']));
          $idDoeUser = $this->db->LastInsertId();
          $this->db->ExecuteQuery(self::SQL('insert into group'), array('admin', 'The Administrator Group'));
          $idAdminGroup = $this->db->LastInsertId();
          $this->db->ExecuteQuery(self::SQL('insert into group'), array('user', 'The User Group'));
          $idUserGroup = $this->db->LastInsertId();
          $this->db->ExecuteQuery(self::SQL('insert into user2group'), array($idRootUser, $idAdminGroup));
          $this->db->ExecuteQuery(self::SQL('insert into user2group'), array($idRootUser, $idUserGroup));
          $this->db->ExecuteQuery(self::SQL('insert into user2group'), array($idDoeUser, $idUserGroup));
          $this->session->AddMessage('notice', 'Successfully created the database tables and created a default admin user as root:root and an ordinary user as doe:doe.<br/>');//Algoritm: $password['algorithm']';<br/>Salt: '. $password['salt']. '<br/>Password:'. $password['password'] );
        } catch(Exception$e) {
          die("$e<br/>Failed to open database: " . $this->config['database'][0]['dsn']);
        }
      }
      
// ----------------------------------------------------------------------------------

      /**
       * Login by autenticate the user and password. Store user information in session if success.
       * added query about group belongin fo authenticated user.
       * new in loin_menu - 
       * @param string $akronymOrEmail the emailadress or user akronym.
       * @param string $password the password that should match the akronym or emailadress.
       * @returns booelan true if match else false.
       */
      public function Login($akronymOrEmail, $password) {
        $user = $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('check user password'), array($akronymOrEmail, $akronymOrEmail));
        $user = (isset($user[0])) ? $user[0] : null;
        // check if NOT  a user exists
        if(!$user){
        	return false;
        	//  check the password with salt from DB NOT exits
        } else if(!$this->CheckPassword($password, $user['algorithm'],  $user['salt'], $user['password'])){
        	return false;
      	}
        //continue login procedure - 
        unset($user['algorithm']);
        unset($user['salt']);
        unset($user['password']);
       
        if($user) {
        	$user['isAuthenticated'] = true;
        	$user['groups'] = $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('get group memberships'), array($user['id']));
          	//ny part login-menu -kolla om admin eller vanlig user
          foreach($user['groups'] as $val) {
            if($val['id'] == 1) {
              $user['hasRoleAdmin'] = true;
            }
            if($val['id'] == 2) {
              $user['hasRoleUser'] = true;
            }
          }
          // add the user to the profile variable
          $this->profile = $user;
          $this->session->SetAuthenticatedUser($this->profile);
        
        }
        return ($user != null);
      }

//----------------------------------------------------------------------------------	
 /**
   * Does the session contain an authenticated user?
   *
   * @returns boolen true or false.
   */
  //borttagen del 6 userprofileform
	public function IsAuthenticated() {
    return ($this->session->GetAuthenticatedUser() != false);
  }

//----------------------------------------------------------------------------------	
  /**
   * Logout.
   */
  public function Logout() {
    $this->session->UnsetAuthenticatedUser();
    $this->Sprofile = array();// nollsätt profilens innehåll
    $this->session->AddMessage('success', "You have logged out.");
  }
//----------------------------------------------------------------------------------  
  /**
* Create a user profile to database .14/11
*
* @param $acronym string the acronym.
* @param $password string the password plain text to use as base.
* @param $name string the user full name.
* @param $email string the user email.
* @returns boolean true if user was created or else false and sets failure message in session.
* @returns boolean true if success else false. 
*/
  public function Create($acronym, $password, $name, $email) {
  	$fixedPW= $this->CreatePassword($password);  
    $this->db->ExecuteQuery(self::SQL('insert into user' ), array($acronym, $name, $email, $fixedPW['algorithm'], $fixedPW['salt'], $fixedPW['password']));    
    //$this->db->ExecuteQuery(self::SQL('insert into user'), array($acronym, $name, $email, $pwd['algorithm'], $pwd['salt'], $pwd['password']));
   
    // check if no ok - leave error messaage
    if($this->db->RowCount() == 0){
    	$this->session->AddMessage('error', "New user could not be created");
    }// test: extra message if ok
     if($this->db->RowCount() == 1){
     	 $this->session->AddMessage('succsess', "New user {$name} have been created. Akronym is: {$acronym}</br> password saved: {$fixedPW['password']}");
    }
    //$this->session->SetAuthenticatedUser($this->profile);          
    return true;
  }

 // ----------------------------------------------------------------------------------
 /**
* Save user profile to database and update user profile in session.
*
* @returns boolean true if success else false. NY för userprofileForm
*/
  public function Save() {
    $this->db->ExecuteQuery(self::SQL('update profile'), array($this['name'], $this['email'], $this['id']));
    $this->session->SetAuthenticatedUser($this->profile);
    return $this->db->RowCount() === 1;
  }
 //---------------------------------------------------------------------------- 
  
  /**
* Change user password. NY för userprofileForm
*
* @param $password string the new password
* @returns boolean true if success else false.
*/
  public function ChangePassword($password) {
  	 $newPW = $this->CreatePassword($password);
    $this->db->ExecuteQuery(self::SQL('update password'), array($newPW['algorithm'],$newPW['salt'],$newPW['password'], $this['id']));
    return $this->db->RowCount() === 1;
  }
  //----------------------------------------------------------------------------
        /**
       * Get the user acronym. ny för login meny momentet i MOM04
       *
       * @returns string with user acronym or null
       */
       // är inte med i del 6 ta bort sen testa
      public function GetAcronym() {
        $profile = $this->GetProfile();
        return isset($profile['acronym']) ? $profile['acronym'] : null;
      }	
 // ----------------------------------------------------------------------------------

      /**
       * Does the user have the admin role? ny för login meny momentet i MOM04
       *
       * @returns boolen true or false.
       */
              // är inte med i del 6 ta bort sen testa

      public function IsAdministrator() {
        $profile = $this->GetProfile();
        return isset($profile['hasRoleAdmin']) ? $profile['hasRoleAdmin'] : null;
      }
   // ----------------------------------------------------------------------------------
   	/**
  	* Get profile information on user.
  	*
  	* @returns array with user profile or null if anonymous user.
  	*/
  	 // är inte med i del 6 ta bort sen testa

      function GetProfile(){
      	  return $this->session->GetAuthenticatedUser();      	  
      }
   // ----------------------------------------------------------------------------------            
       /**
       * Check if password matches.
       *
       * @param $plain string the password plain text to use as base.
       * @param $algorithm string the algorithm mused to hash the user salt/password.
       * @param $salt string the user salted string to use to hash the password.
       * @param $password string the hashed user password that should match.
       * @returns boolean true if match, else false.
       */
      public function CheckPassword($plain, $algorithm, $salt, $password) {
        switch($algorithm) {
          case 'sha1salt': return $password === sha1($salt.$plain); break;
          case 'md5salt': return $password === md5($salt.$plain); break;
          case 'sha1': return $password === sha1($plain); break;
          case 'md5': return $password === md5($plain); break;
          case 'plain': return $password === $plain; break;
          default: throw new Exception('Unknown hashing algorithm');
        }
      } 
 
 //----------------------------------------------------------------------------
       /**
       * Create password.
       *
       * @param $plain string the password plain text to use as base.
       * @param $algorithm string stating what algorithm to use, plain, md5, md5salt, sha1, sha1salt.
       * defaults to the settings of site/config.php.
       * @returns array with 'salt' and 'password'.
       */
      public function CreatePassword($plain, $algorithm=null) {
      	  
        $password = array(
          'algorithm'=>($algorithm ? $algorithm : CHandy::Instance()->config['hashing_algorithm']),
          'salt'=>null
        );
        switch($password['algorithm']) {
          case 'sha1salt': $password['salt'] = sha1(microtime()); $password['password'] = sha1($password['salt'].$plain); break;
          case 'md5salt': $password['salt'] = md5(microtime()); $password['password'] = md5($password['salt'].$plain); break;
          case 'sha1': $password['password'] = sha1($plain); break;
          case 'md5': $password['password'] = md5($plain); break;
          case 'plain': $password['password'] = $plain; break;
          default: throw new Exception('Unknown hashing algorithm');
        }
        echo $password;
        return $password;
      }

  }

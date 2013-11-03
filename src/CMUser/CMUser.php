<?php 
/**
 * A model class to handle the guestbook
 * 
 * @package HandyCore
 * inherit CObject to be able to use $this
 */
 
 class CMUser extends CObject implements IHasSQL{ 
 	
 	 /**
   * Constructor
   */
  public function __construct() {
    parent::__construct();
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
          'create table user'  => "CREATE TABLE IF NOT EXISTS User (id INTEGER PRIMARY KEY, acronym TEXT KEY, name TEXT, email TEXT, password TEXT, created DATETIME default (datetime('now')));",
          'create table group'      => "CREATE TABLE IF NOT EXISTS Groups (id INTEGER PRIMARY KEY, acronym TEXT KEY, name TEXT, created DATETIME default (datetime('now')));",
          'create table user2group' => "CREATE TABLE IF NOT EXISTS User2Groups (idUser INTEGER, idGroups INTEGER, created DATETIME default (datetime('now')), PRIMARY KEY(idUser, idGroups));",
          'insert into user'        => 'INSERT INTO User (acronym,name,email,password) VALUES (?,?,?,?);',
          'insert into group'       => 'INSERT INTO Groups (acronym,name) VALUES (?,?);',
          'insert into user2group'  => 'INSERT INTO User2Groups (idUser,idGroups) VALUES (?,?);',
          'check user password'     => 'SELECT * FROM User WHERE password=? AND (acronym=? OR email=?);',
          'get group memberships'   => 'SELECT * FROM Groups AS g INNER JOIN User2Groups AS ug ON g.id=ug.idGroups WHERE ug.idUser=?;',
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
          $this->db->ExecuteQuery(self::SQL('insert into user'), array('root', 'The Administrator', 'root@dbwebb.se', 'root'));
          $idRootUser = $this->db->LastInsertId();
          $this->db->ExecuteQuery(self::SQL('insert into user'), array('doe', 'John/Jane Doe', 'doe@dbwebb.se', 'doe'));
          $idDoeUser = $this->db->LastInsertId();
          $this->db->ExecuteQuery(self::SQL('insert into group'), array('admin', 'The Administrator Group'));
          $idAdminGroup = $this->db->LastInsertId();
          $this->db->ExecuteQuery(self::SQL('insert into group'), array('user', 'The User Group'));
          $idUserGroup = $this->db->LastInsertId();
          $this->db->ExecuteQuery(self::SQL('insert into user2group'), array($idRootUser, $idAdminGroup));
          $this->db->ExecuteQuery(self::SQL('insert into user2group'), array($idRootUser, $idUserGroup));
          $this->db->ExecuteQuery(self::SQL('insert into user2group'), array($idDoeUser, $idUserGroup));
          $this->session->AddMessage('notice', 'Successfully created the database tables and created a default admin user as root:root and an ordinary user as doe:doe.');
        } catch(Exception$e) {
          die("$e<br/>Failed to open database: " . $this->config['database'][0]['dsn']);
        }
      }
      
// ----------------------------------------------------------------------------------

      /**
       * Login by autenticate the user and password. Store user information in session if success.
       * added query about group belongin fo authenticated user.
       * @param string $akronymOrEmail the emailadress or user akronym.
       * @param string $password the password that should match the akronym or emailadress.
       * @returns booelan true if match else false.
       */
      public function Login($akronymOrEmail, $password) {
        $user = $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('check user password'), array($password, $akronymOrEmail, $akronymOrEmail));
        $user = (isset($user[0])) ? $user[0] : null;
        unset($user['password']);
        if($user) {
          $user['groups'] = $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('get group memberships'), array($user['id']));
          $this->session->SetAuthenticatedUser($user);
          $this->session->AddMessage('success', "Welcome '{$user['name']}'.");
        } else {
          $this->session->AddMessage('notice', "Could not login, user does not exists or password did not match.");
        }
        return ($user != null);
      }
//----------------------------------------------------------------------------------	
 /**
   * Does the session contain an authenticated user?
   *
   * @returns boolen true or false.
   */
  public function IsAuthenticated() {
    return ($this->session->GetAuthenticatedUser() != false);
  }


//----------------------------------------------------------------------------------	
/**
   * Get profile information on user.(saved in the session)
   *
   * @returns array with user profile or null if anonymous user.
   */
  public function GetUserProfile() {
    return $this->session->GetAuthenticatedUser();
  }
//----------------------------------------------------------------------------------	
  /**
   * Logout.
   */
  public function Logout() {
    $this->session->UnsetAuthenticatedUser();
    $this->session->AddMessage('success', "You have logged out.");
  }
//----------------------------------------------------------------------------------	

  /**
   * Add a new entry to the guestbook and save to database.
   */
  public function Add($entry) {
  	  $this->db->ExecuteQuery(self::SQL('insert into guestbook'), array($entry));
      	  $this->session->AddMessage('success', 'The message is now added to the database.');
        if($this->db->rowCount() != 1) {
          echo 'Failed to insert new guestbook item into database.';
        }
      }
 // ----------------------------------------------------------------------------------
     
  /**
   * Delete all entries from the guestbook and database.
   */
  public function DeleteAll() {
         $this->db->ExecuteQuery(self::SQL('delete from guestbook'));
         $this->session->AddMessage('info', 'The database is now emptied from messages. ');
      }
 // ----------------------------------------------------------------------------------

  /**
   * Read all entries from the guestbook & database.
   */
  public function ReadAll() {
	try {
          $this->db->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);          
          return $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('select * from guestbook'));
        } catch(Exception $e) {
          return array();   
        }
      }  	  
  	  
  	  
  }

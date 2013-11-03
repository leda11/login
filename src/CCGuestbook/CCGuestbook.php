    <?php
    /**
    * A guestbook controller as an example to show off some basic controller and model-stuff.
    * METHODS
    * Index()
    * Handler()
    * SQL($key=null)
    * CreateTableInDatabase()
    * SaveNewToDatabase($entry)
    * DeleteAllFromDatabase()
    * ReadAllFromDatabase()
    *
    * @package HandyCore
    */
    class CCGuestbook extends CObject implements IController{

      private $pageTitle = 'My Guestbook -in Handy MVC';// skall denna flyttas del 9 mom03
      private $guestbookModel;
     
//------------------------------------------------------------------------------
      /**
       * Constructor
       */
      public function __construct() {
        parent::__construct();
        $this->guestbookModel = new CMGuestbook();
      }
     
//------------------------------------------------------------------------------
      /**
       * Implementing interface IController. All controllers must have an index action.
       */
      public function Index() {
        $this->views->SetTitle($this->pageTitle);
        $this->views->AddInclude(__DIR__ . '/index.tpl.php', array(
          'entries'=>$this->guestbookModel->ReadAll(),
          'formAction'=>$this->request->CreateUrl('','handler')// ändrat från 'guestbook/handler'
        ));
      }
 
//------------------------------------------------------------------------------
      /**
       * Handle posts from the form and take appropriate action.
       */
      public function Handler() {
        if(isset($_POST['doAdd'])) {
          $this->guestbookModel->Add(strip_tags($_POST['newEntry']));//calls ExecuteQuery
        }
        elseif(isset($_POST['doClear'])) { // calls ExecuteQuery
          $this->guestbookModel->DeleteAll();
        }        
        elseif(isset($_POST['doCreate'])) {
          $this->guestbookModel->Init();
        }           
        //header('Location: ' . $this->request->CreateUrl('guestbook')); borttaget 
        //metod iCObject
        $this->RedirectTo($this->request->CreateUrl($this->request->controller));
      }

//------------------------------------------------------------------------------
       /** moved to CMGuestbook.php
        * Implementing interface IHasSQL. Encapsulate all SQL used by this class.
        *
        * @param string $key the string that is the key of the wanted SQL-entry in the array.
        */ 
/*        public static function SQL($key=null) {
         $queries = array(
            'create table guestbook'  => "CREATE TABLE IF NOT EXISTS Guestbook (id INTEGER PRIMARY KEY, entry TEXT, created DATETIME default (datetime('now')));",
            'insert into guestbook'   => 'INSERT INTO Guestbook (entry) VALUES (?);',
            'select * from guestbook' => 'SELECT * FROM Guestbook ORDER BY id DESC;',
            'delete from guestbook'   => 'DELETE FROM Guestbook;',
         );
         if(!isset($queries[$key])) {
            throw new Exception("No such SQL query, key '$key' was not found.");
          }
          return $queries[$key];
       }
       */
//------------------------------------------------------------------------------
      /**  moved to CMGuestbook.php
       * Save a new entry to database.
       */
/*      private function CreateTableInDatabase() {
        try {
        	$this->db->ExecuteQuery(self::SQL('create table guestbook'));
        	$this->session->AddMessage('notice', 'The table is now created of it not existed before.');
        } catch(Exception$e) {
          die("$e<br/>Failed to open database: " . $this->config['database'][0]['dsn']);
        }
      }
*/

//------------------------------------------------------------------------------
      /**  moved to CMGuestbook.php
       * Save a new entry to database.'INSERT INTO Guestbook (entry) VALUES (?);'
       */
/*      private function SaveNewToDatabase($entry) {
      	  $this->db->ExecuteQuery(self::SQL('insert into guestbook'), array($entry));
      	  $this->session->AddMessage('success', 'The message is now added to the database.');
        if($this->db->rowCount() != 1) {
          echo 'Failed to insert new guestbook item into database.';
        }
      }
*/      
 
//------------------------------------------------------------------------------                                     
      /**
       * Delete all entries from the database.
       */
/*      private function DeleteAllFromDatabase() {
        //$this->db->ExecuteQuery('DELETE FROM Guestbook;');
         $this->db->ExecuteQuery(self::SQL('delete from guestbook'));// removed return
         $this->session->AddMessage('info', 'The database is now emptied from messages. ');
      }
*/ 
 //------------------------------------------------------------------------------
     /**  moved to CMGuestbook.php
       * Read all entries from the database.
       */
 /*     private function ReadAllFromDatabase() {
        try {
          $this->db->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);          
          return $this->db->ExecuteSelectQueryAndFetchAll(self::SQL('select * from guestbook'));
        } catch(Exception $e) {
          return array();   
        }
      }
*/    
    } 
    


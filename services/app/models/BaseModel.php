<?php
namespace Models;

use DateTime;
use DateTimeZone;
use ReflectionObject;
use ReflectionProperty;
use Phalcon\Exception;
use Phalcon\Db\Column;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\Behavior\SoftDelete;
use Phalcon\Mvc\Model\Behavior\Timestampable;
use Phalcon\Mvc\Model\Message;
use Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;
use Phalcon\Annotations\Adapter\Memory as MemoryAdapter;
use Phalcon\Translate\Adapter\NativeArray;

class BaseModel extends Model
{  
    /**
     *
     * @var object
     */
    public static $_s_t        = null;
    
    /**
     * Current Exception
     *
     * @Column(skip="1")
     * @var Phalcon\Exception
     */
    protected $_curExc         = null;
    
    /**
     * Current Static Exception
     *
     * @Column(skip="1")
     * @var Phalcon\Exception
     */
    protected static $_curSExc = null;
    
    /**
     * Database
     *
     * @Column(skip="1")
     * @var object
     */
    protected $_db             = null;
    
    /**
     * Location Country
     *
     * @Column(skip="1")
     * @var string
     */
    protected static $_cid     = 'tr';
    
    /**
     * Debug
     *
     * @Column(skip="1")
     * @var boolean
     */
    protected $_debug          = false;
    
    /**
     * Method to initialize the model
     *
     * @return 	void
     * @since 	1.0
     */    
    public function initialize()
    {
        $this->addBehavior(
            new Timestampable(
                [
                    /**
                    'beforeCreate' => [
                        'field'  => 'created',
                        'format' => 'Y-m-d H:i:s'
                    ],
                    'beforeUpdate' => [
                        'field'  => 'updated',
                        'format' => 'Y-m-d H:i:s'
                    ],
                    */
                    'beforeDelete' => [
                        'field'  => 'deleted',
                        'format' => 'Y-m-d H:i:s'
                    ]
                ]
            )
        );
        
        $this->addBehavior(
            new SoftDelete(
                [
                    "field" => "status",
                    "value" => 'D'
                ]
            )
        );
    }  
    
    /**
     * Method to bind the data to the model (e.g. for storing into the database)
     *
     * @param 	array       $array        The array to bind to the object
     * @return 	boolean                   True on success, false on failure
     * @since 	2.0
     */
    public function bind($array = array())
    {
        $reflection = new ReflectionObject($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        
        foreach ($properties as $property) {
            $name = $property->getName();
            
            if ( isset($array[$name]) && substr($name, 0, 1) != '_' ) {
                $this->$name = $array[$name];
            }            
        }
        
        return true;
    }   
    
    /**
     * Method to check the whole model properties (e.g. before storage into the database)
     *
     * @return 	boolean          True on success, false on failure
     * @since 	2.0
     */
    public function check()
    {
        return true;
    }
    
    /**
     * Method to save the model data
     *
     * @param  array             The data
     * @param  array             The whiteList
     * @return boolean           True on success, false on failure
     * @since  2.0
     */
    public function save($data = NULL, $whiteList = NULL)
    {
        $this->_curExc = null;
        
        try {
            if ( !parent::save($data, $whiteList) ) {
                $messages = $this->getMessages();
                $excMsgs  = '';
                
                foreach ($messages as $message) {
                    $excMsgs .= $message.' - ';
                }
                
                throw new Exception($excMsgs, 234);
            }
            
        } catch (Exception $e) {
            $this->raiseException($e, __CLASS__.'::'.__METHOD__, 'Error');
            return false;
        }
        
        return true;
    }
    
    /**
     * Method to delete the current model data from its database table
     *
     * @return boolean           True on success, false on failure
     * @since  2.0
     */
    public function delete()
    {
        try {
            $this->_curExc    = null;
            $this->cas_status = 0;            
            
            if ( !parent::delete() ) {
                $messages = $this->getMessages();
                $excMsgs  = '';
                
                foreach ($messages as $message) {
                    $excMsgs .= $message.' - ';
                }
                
                throw new Exception($excMsgs, 234);
            }
            
        } catch (Exception $e) {
            $this->raiseException($e, __CLASS__.'::'.__METHOD__, 'Error');
            return false;
        }
        
        return true;
    }
    
    /**
     * Method to send a request
     *
     * @param  string       $url              The request url
     * @param  array        $data             The request data
     * @param  string       $type             The request type
     * @param  array        $headers          The request headers
     * @param  boolean      $json             The request format is JSON?
     * @param  string       $username         The request username
     * @param  string       $password         The request password
     * @return object                         The result in object format
     * @since  2.0
     */
    public static function sendRequest($url = null, $data = [], $type = null, $headers = [], $json = false, $username = null, $password = null)
    {
        try {
            $type    = strtoupper(trim($type));
            $curl    = curl_init();
            $headers = (array) $headers;
            
            if ($json) {
                $headers[] = 'Content-Type: application/json';
                
                if ( count($data) ) {
                    $data_string = json_encode($data);
                    $headers[]   = 'Content-Length: ' . strlen($data_string);
                }
            }
            
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_USERAGENT,      "Mozilla/4.0 (compatible;)");
            curl_setopt($curl, CURLOPT_HEADER,         0);
            curl_setopt($curl, CURLOPT_HTTPHEADER,     $headers);
            curl_setopt($curl, CURLOPT_VERBOSE,        0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            
            if ( !empty($username) ) {
                curl_setopt($curl, CURLOPT_USERPWD, $username.':'.$password);
            }
            
            if ( $type == 'POST' || $type == 'PUT' ) {
                if ( $type == 'POST' ) {
                    curl_setopt($curl, CURLOPT_POST, true);
                    
                } else {
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                }
                
                curl_setopt($curl, CURLOPT_URL,  $url);
                
                if ($json) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                    
                } else {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                }
                
            } else {
                curl_setopt($curl, CURLOPT_POST, false);
                curl_setopt($curl, CURLOPT_URL,  $url.'?'.http_build_query($data));
            }
            
            if ( !($response = curl_exec($curl)) && curl_error($curl) ) {
                throw new Exception('Could not receive a response from the system. - Error Code: ' . curl_errno($curl) . ' - Error Message: ' . curl_error($curl));
            }
            
            curl_close($curl);
            
            $response = trim($response, "\\xef\\xbb\\xbf");
            $result   = json_decode($response);
            
            if ( is_object($result) ) {
                return $result;
            }
            
            $result = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response));
            
            if ( is_object($result) ) {
                return $result;
            }
            
            $result            = [];
            $result['status']  = 'false';
            $result['content'] = $response;
            $result['error']   = $response;
            $result            = json_encode($result);
            
        } catch (Exception $e) {
            self::raiseSException($e, __CLASS__.'::'.__METHOD__, 'Error');
            return false;
        }
        
        return json_decode($result);
    }
    
    /**
     * Method to parse a given url
     *
     * @param 	string       $url        The given url
     * @return 	array                    The various parts of the given url
     * @since 	2.0
     */
    public static function parseURL($url = '')
    {
        $parts = parse_url($url);
        
        // We need to replace &amp; with & for parse_str to work right...
        if ( isset($parts['query']) && strpos($parts['query'], '&amp;') ) {
            $parts['query'] = str_replace('&amp;', '&', $parts['query']);
        }
        
        $parts['scheme'] = isset($parts['scheme']) ? $parts['scheme'] : 'http';
        $parts['user']   = isset($parts['user'])   ? $parts['user'] :   '';
        $parts['pass']   = isset($parts['pass'])   ? $parts['pass'] :   '';
        $parts['host']   = isset($parts['host'])   ? $parts['host'] :   '';
        $parts['port']   = isset($parts['port'])   ? $parts['port'] :   '';
        $parts['path']   = isset($parts['path'])   ? $parts['path'] :   '';
        $parts['query']  = isset($parts['query'])  ? $parts['query'] :  '';
        
        return $parts;
    }
     
    /**
     * Method to delete hard
     *
     * @return Phalcon\Exception
     * @since  2.0
     */
    public function deleteHard()
    {
        if ( isset(static::$_key) ) {
            $key = static::$_key;
            
        } else {
            $key = 'id';
        }
        
        $id     = $this->{$key};
        $schema = $this->getSource();
        
        return $this->getDi()->getShared('db')->query("delete from `".$schema."` where ".$key."=".(int) $id);
    }
    
    /**
     * Method to do after fetch
     *
     * @return Phalcon\Exception
     * @since  2.0
     */
    public function afterFetch() {
        foreach ( $this->getModelsMetaData()->getDataTypes($this) as $field => $type ) {
            if (is_null($this->$field)) {
                continue;
            }
            
            switch ($type) {
                case Column::TYPE_BOOLEAN:
                    $this->$field = ord($this->$field);
                    break;
                    
                case Column::TYPE_BIGINTEGER:
                case Column::TYPE_INTEGER:
                    $this->$field = intval($this->$field);
                    break;
                    
                case Column::TYPE_DECIMAL:
                case Column::TYPE_FLOAT:
                    $this->$field = floatval($this->$field);
                    break;
                    
                case Column::TYPE_DOUBLE:
                    $this->$field = doubleval($this->$field);
                    break;
                    
                case Column::TYPE_DATETIME:
                    $this->$field = date('Y-m-d H:i:s',strtotime($this->$field));
                    break;
                    
                case Column::TYPE_DATE:
                    $this->$field = date('Y-m-d',strtotime($this->$field));
                    break;
            }
        }
    }
    
    /**
     * Method to get the default values
     *
     * @return 	array           The default values
     * @since 	2.0
     */
    public function getDefaultValues()
    {
        try {
            $defaults = [];
            $metas    = self::getMetas(__CLASS__);
            
            if ( $metas === false ) {
                throw new Exception('Could not get the class meta information.', 235, self::getCurSExc());
            }
            
            foreach ( $metas['prop'] as $property => $annotations ) {
                foreach ( $annotations->getAnnotations() as $annotation ) {
                    if ( $annotation->getName() == 'Field' ) {
                        foreach ( $annotation->getArguments() as $colOption => $colValue ) {
                            if ( $colOption == 'default' ) {
                                $defaults[$property] = $colValue;
                                
                                if ( is_numeric($colValue) ) {
                                    $defaults[$property] = (int)$colValue;
                                }
                            }
                        }
                    }
                }
            }
            
        } catch (Exception $e) {
            $this->raiseException($e, __CLASS__.'::'.__METHOD__, 'Error');
            return false;
        }
        
        return $defaults;
    }
    
    /**
     * Method to raise a model exception
     *
     * @param Phalcon\Exception       $e        The exception object
     * @param string                  $field    The exception field
     * @param string                  $type     The exception type
     * @return void
     * @since 2.0
     */
    public function raiseException($e, $field, $type)
    {
        $this->_curExc = $e;
        $message       = new Message($e->__toString(), $field, $type);
        
        $this->appendMessage( $message );
    }
    
    /**
     * Method to get the current exception occured in model
     *
     * @return 	Phalcon\Exception
     * @since 2.0
     */
    public function getCurExc()
    {
        return $this->_curExc;
    }
    
    /**
     * Method to raise a model exception
     *
     * @param Phalcon\Exception       $e        The exception object
     * @param string                  $field    The exception field
     * @param string                  $type     The exception type
     * @return void
     * @since 2.0
     */
    public static function raiseSException($e, $field, $type)
    {
        self::$_curSExc = $e;
    }
    
    /**
     * Method to get the current exception occured in model
     *
     * @return 	Phalcon\Exception
     * @since 2.0
     */
    public static function getCurSExc()
    {
        return static::$_curSExc;
    }    
    
    /**
     * Method to get the user id
     *
     * @return int
     * @since  2.0
     */
    public static function getUserID()
    {
        $session = \Phalcon\DI::getDefault()->get('session');
        $user    = $session->get('auth-identity');
        
        if ( isset($user['id']) ) {
            return $user['id'];
        }
        
        return 0;
    }
    
    /**
     * Method to get the user role
     *
     * @return string
     * @since  2.0
     */
    public static function getUserRole()
    {
        $session = \Phalcon\DI::getDefault()->get('session');
        $user    = $session->get('auth-identity');
        
        if ( isset($user['role']) ) {
            return $user['role'];
        }
        
        return 'head_manager';
    }
    
    /**
     * Method to get the user
     *
     * @return mixed
     * @since  2.0
     */
    public static function getUser()
    {
        $session = \Phalcon\DI::getDefault()->get('session');
        $user    = $session->get('auth-identity');
        
        if ( isset($user) ) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Method to get the client IP address
     *
     * @return string             The client IP address
     * @since  2.0
     */
    public static function getClientIP()
    {
        $ipaddress = null;
        
        if ( empty($ipaddress) && isset($_SERVER['HTTP_CLIENT_IP']) ) {
            if ( self::validateIP($_SERVER['HTTP_CLIENT_IP']) ) {
                $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
            }
        }
        
        if ( empty($ipaddress) && isset($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
            if ( self::validateIP($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
                $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
        }
        
        if ( empty($ipaddress) && isset($_SERVER['HTTP_X_FORWARDED']) ) {
            if ( self::validateIP($_SERVER['HTTP_X_FORWARDED']) ) {
                $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
            }
        }
        
        if ( empty($ipaddress) && isset($_SERVER['HTTP_FORWARDED_FOR']) ) {
            if ( self::validateIP($_SERVER['HTTP_FORWARDED_FOR']) ) {
                $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
            }
        }
        
        if ( empty($ipaddress) && isset($_SERVER['HTTP_FORWARDED']) ) {
            if ( self::validateIP($_SERVER['HTTP_FORWARDED']) ) {
                $ipaddress = $_SERVER['HTTP_FORWARDED'];
            }
        }
        
        if ( empty($ipaddress) && isset($_SERVER['REMOTE_ADDR']) ) {
            if ( self::validateIP($_SERVER['REMOTE_ADDR']) ) {
                $ipaddress = $_SERVER['REMOTE_ADDR'];
            }
        }
        
        if ( empty($ipaddress) ) {
            $ipaddress = '0.0.0.0';
        }
        
        return $ipaddress;
    }
    
    /**
     * Method to ensure an ip address is both a valid IP and does not fall within a private network range.
     *
     * @param  string       $ip             The given IP
     * @return boolean                      True if the given IP is valid
     * @since  2.0
     */
    public static function validateIP($ip)
    {
        if (strtolower($ip) === 'unknown')
            return false;
            
            // generate ipv4 network address
            $ip = ip2long($ip);
            
            // if the ip is set and not equivalent to 255.255.255.255
            if ($ip !== false && $ip !== -1) {
                // make sure to get unsigned long representation of ip
                // due to discrepancies between 32 and 64 bit OSes and
                // signed numbers (ints default to signed in PHP)
                $ip = sprintf('%u', $ip);
                
                // do private network range checking
                if ($ip >= 0 && $ip <= 50331647) return false;
                if ($ip >= 167772160 && $ip <= 184549375) return false;
                if ($ip >= 2130706432 && $ip <= 2147483647) return false;
                if ($ip >= 2851995648 && $ip <= 2852061183) return false;
                if ($ip >= 2886729728 && $ip <= 2887778303) return false;
                if ($ip >= 3221225984 && $ip <= 3221226239) return false;
                if ($ip >= 3232235520 && $ip <= 3232301055) return false;
                if ($ip >= 4294967040) return false;
            }
            
            return true;
    }
    
    /**
     * Method to translate a text
     *
     * @param  string     $text    The given text
     * @param  array      $vars    The variables
     * @return string              The translated text
     * @since  2.0
     */
    public static function __($text = '', $vars = [])
    {
        return self::$_s_t->_($text, $vars);
    }
    
    /**
     * Method to translate a text
     *
     * @param  string     $text    The given text
     * @param  array      $vars    The variables
     * @return string              The translated text
     * @since  2.0
     */
    public function _($text = '', $vars = [])
    {
        return self::$_s_t->_($text, $vars);
    }
    
    /**
     * Method to set the language of the system
     *
     * @return string             The set language
     * @since  2.0
     */
    public static function setLanguage()
    {
        // Ask browser what is the best language
        $request    = \Phalcon\DI::getDefault()->get('request');
        $session    = \Phalcon\DI::getDefault()->get('session');
        $language   = $request->getBestLanguage();
        $language   = substr($language, 0, 2);
        $s_language = $session->get('language');
        
        SWITCH ($s_language) {
            CASE 'de':
            CASE 'tr':
                $language = $s_language;
                BREAK;
                
            DEFAULT:
                BREAK;
        }
        
        if ( !in_array($language, ['tr', 'de']) ) {
            $language = 'tr';
        }
        
        $session->set('language', $language);
        
        self::getTranslation();
        
        return $language;
    }
    
    /**
     * Method to get the translation
     *
     * @return object
     * @since  2.0
     */
    public static function getTranslation()
    {
        $session         = \Phalcon\DI::getDefault()->get('session');
        $language        = is_object($session) ? $session->get('language') : 'tr';
        $language        = !empty($language)   ? $language                 : 'tr';
        $translationFile = APP_PATH.'/languages/' . $language . '.php';
        $messages        = [];
        
        // Check if we have a translation file for that lang
        if ( file_exists($translationFile) ) {
            require $translationFile;
            
        } else {
            // Fallback to some default
            require APP_PATH.'/languages/tr.php';
        }
        
        self::$_s_t = new NativeArray(
            [
                'content' => $messages,
            ]
        );
        
        // Return a translation object $messages comes from the require
        // statement above
        return self::$_s_t;
    }
    
    /**
     * Method to build the table data
     *
     * @param  string       $phpFile         The php file
     * @param  array        $params          The parameters
     * @return boolean                       True on success, false on failure
     * @since  2.0
     */
    public static function buildTableData($phpFile, $params)
    {
        $evManager = new EventsManager();
        $mySQL     = $this->getService('db');
        
        $mySQL->setEventsManager($evManager);
        
        $tableName   = self::parseTableName($phpFile);
        $phpFile     = basename($phpFile);
        $class       = str_replace('.php', '', $phpFile);
        $class       = ucfirst($class);
        $metas       = self::getMetas($class);
        $process     = false;
        $alter       = false;
        $tableExists = $mySQL->tableExists($tableName);
        
        if ( in_array('-fresh', $params) && $tableExists ) {
            echo ' dropping/creating table <'.$tableName.'>';
            
            $mySQL->dropTable($tableName);
            
            $process = true;
            $alter   = false;
            
        } else if ( in_array('-force', $params) && $tableExists ) {
            echo ' table <'.$tableName.'> exists, modifying';
            
            $process = true;
            $alter   = true;
            
        } else if ( $tableExists ) {
            echo ' table <'.$tableName.'> exists, skipping';
            $process = false;
            $alter   = false;
            
        } else {
            echo ' creating table <'.$tableName.'>';
            $process = true;
            $alter   = false;
        }
        
        $columns = [];
        $indexes = [];
        
        foreach ( $metas['prop'] as $property => $annotations ) {
            $columns[$property] = [
                'type' => dbColumn::TYPE_INTEGER,
            ];
            
            foreach ( $annotations->getAnnotations() as $annotation ) {
                $option = trim($annotation->getName());
                
                switch (strtolower($option)){
                    case 'primary':
                        $columns[$property]['primary'] = true;
                        break;
                        
                    case 'autoincrement':
                        $columns[$property]['autoIncrement'] = true;
                        break;
                        
                    case 'notnull':
                        $columns[$property]['notNull'] = true;
                        break;
                        
                    case 'unsigned':
                        $columns[$property]['unsigned'] = true;
                        break;
                        
                    case 'index':
                        $indexes['Ind_'.$property][] = $property;
                        break;
                        
                    case 'field':
                        foreach ( $annotation->getArguments() as $colOption => $colValue ) {
                            switch ( strtolower($colOption) ) {
                                case 'type':
                                    $columns[$property]['type'] = trim($colValue);
                                    break;
                                    
                                case 'size':
                                    $columns[$property]['size'] = (int)trim($colValue);
                                    break;
                                    
                                case 'scale':
                                    $columns[$property]['scale'] = (int)trim($colValue);
                                    break;
                                    
                                case 'default':
                                    $columns[$property]['default'] = trim($colValue);
                                    break;
                                    
                                case 'after':
                                    $columns[$property]['after'] = trim($colValue);
                                    break;
                            }
                        }
                        
                        break;
                }
            }
        }
        
        if ( !$process && !$alter ) {
            return;
        }
        
        if ( $process && !$alter ) {
            // just create table with columns & indexes
            $columnMap = [];
            
            foreach($columns as $column=>$options){
                $columnMap[] = SchemaFactory::createColumn($column, $options);
            }
            
            $result = SchemaFactory::createTable($tableName, $columnMap, $mySQL);
            
            if ( $result ) {
                if ( !empty($indexes) ) {
                    foreach ( $indexes as $indName=>$indColumn ) {
                        $mySQL->addIndex($tableName, null, new \Phalcon\Db\Index($indName,$indColumn));
                    }
                }
                
                echo ' OK';
                
            } else {
                echo ' FAILED: '.$result;
            }
            
            return;
        }
        
        if ( $process && $alter ) {
            // remove non present, add new columns & remove-add indexes
            $columnMap['add']    = [];
            $columnMap['modify'] = [];
            $columnMap['drop']   = [];
            $dbFields            = $mySQL->describeColumns($tableName);
            
            foreach ( $dbFields as $dbField ) {
                foreach ( $columns as $colName => $colOptions ) {
                    if ( $dbField->getName() == $colName) {
                        $columnMap['modify'][$colName] = $colOptions;
                    }
                }
            }
            
            $checkAddCols = $columns;
            
            foreach ( $columns as $colName=>$colOptions ) {
                foreach ( $dbFields as $dbField ) {
                    if ( $dbField->getName() == $colName ) {
                        unset($checkAddCols[$colName]);
                    }
                }
            }
            
            $columnMap['add'] = $checkAddCols;
            $checkDropCols    = [];
            
            foreach($dbFields as $dbField){
                $checkDropCols[$dbField->getName()] = '';
            }
            
            foreach( $dbFields as $dbField ) {
                foreach ( $columns as $colName => $colOptions ) {
                    if ( $dbField->getName() == $colName ) {
                        unset($checkDropCols[$colName]);
                    }
                }
            }
            
            $columnMap['drop'] = $checkDropCols;
            echo PHP_EOL . ' - Dropping: ';
            
            foreach ( $columnMap['drop'] as $drop ) {
                echo $drop . ' ';
                $mySQL->dropColumn($tableName, null, $drop);
            }
            
            echo PHP_EOL . ' - Modifying: ';
            
            foreach ( $columnMap['modify'] as $modName => $modOpt ) {
                echo $modName . ' ';
                $mySQL->modifyColumn($tableName, null, new dbColumn($modName, $modOpt));
            }
            
            echo PHP_EOL . ' - Adding: ';
            foreach ( $columnMap['add'] as $addName => $addOpt ) {
                echo $addName . ' ';
                $mySQL->addColumn($tableName, null, new dbColumn($addName, $addOpt));
            }
            
            //indexes
            $dbIndexes = $mySQL->describeIndexes($tableName);
            
            echo PHP_EOL.' - Dropping Indexes, skipping primary... ';
            
            foreach ( $dbIndexes as $dbIndName => $dbIndOpts ) {
                if ( strpos($dbIndName,'Ind') !== FALSE ) {
                    $mySQL->dropIndex($tableName, null, $dbIndName);
                    echo 'dropped ('.$dbIndName.') ';
                }
            }
            
            echo PHP_EOL.' - Creating Indexes... ';
            
            foreach( $indexes as $indName => $indColumn ) {
                $mySQL->addIndex($tableName, null, new \Phalcon\Db\Index($indName, $indColumn));
                echo 'created ('.$indName.') ';
            }
            
            return;
        }
    }
    
    /**
     * Method to get the table properties of the model
     *
     * @param 	string       $class        The given class name
     * @return 	array                      The table properties of the model
     * @since 	2.0
     */
    public static function getTableProps($class = null)
    {
        $t_properties = [];
        $class        = trim($class);
        
        if ( empty($class) ) {
            return $t_properties;
        }
        
        $obj          = new $class;
        $reflection   = new ReflectionObject($obj);
        $properties   = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        
        
        foreach ($properties as $property) {
            $name = $property->getName();
            
            if ( substr($name, 0, 1) != '_' ) {
                $t_properties[] = $name;
            }
        }
        
        return $t_properties;
    }
    
    /**
     * Method to parse a full name
     *
     * @param 	string       $full_name        The full name
     * @return 	array                          The name parts of the given full name
     * @since 	2.0
     */
    public static function parseFullName($full_name = '')
    {
        $first_name = '';
        $last_name  = '';
        $full_name  = trim($full_name);
        $name_parts = explode(' ', $full_name);
        
        if ( count($name_parts) > 1 ) {
            $f_set = false;
            
            foreach ($name_parts as $name) {
                $name = trim($name);
                
                if ( !empty($name) ) {
                    if ( !$f_set ) {
                        $first_name = $name;
                        $last_name  = '';
                        $f_set      = true;
                        
                    } else {
                        $last_name .= $name.' ';
                    }
                }
            }
            
            $last_name = trim($last_name);
        }
        
        return ['first_name' => $first_name, 'last_name' => $last_name];
    }
    
    /**
     * Method to get the Query Builder
     *
     * @return Phalcon\Mvc\Model\Query\Builder
     * @since  2.0
     */
    public static function getQueryBuilder()
    {
        return \Phalcon\Di::getDefault()->getModelsManager()->createBuilder();
    }
    
    /**
     * Method to get the Elastic Database
     *
     * @return Elasticsearch\Client
     * @since  2.0
     */
    public static function getElastic()
    {
        return \Phalcon\Di::getDefault()->get('dbElastic');
    }
    
    /**
     * Method to get the app configuration
     *
     * @return object
     * @since  2.0
     */
    public static function getConfig()
    {
        return \Phalcon\Di::getDefault()->getShared('config');
    }
    
    /**
     * Method to get the metas
     *
     * @param  string       $class           The class name
     * @return mixed                         The parsed value
     * @since  2.0
     */
    public static function getMetas($class)
    {
        $reader    = new MemoryAdapter();
        $namespace = '\\Models\\'.$class;
        
        try {
            $reflector = $reader->get(new $namespace());
            
        } catch (Exception $e) {
            static::raiseSException($e, __CLASS__.'::'.__METHOD__, 'Error');
            return false;
        }
        
        return [
            'class'  => $reflector->getClassAnnotations(),      // class headers
            'prop'   => $reflector->getPropertiesAnnotations(),  // class properties
            'method' => $reflector->getMethodsAnnotations(),   // class methods
            'all'    => $reflector->getReflectionData()            // full meta
        ];
    }
    
    /**
     * Method to detect and convert the string arrays to normal arrays in the given array
     *
     * @param  array       $array                The given array
     * @param  mixed       $number_filter        False, or the number filter type
     * @return array                             The converted array
     * @since  2.0
     */
    public static function convertStringArrays($array = [], $number_filter = false)
    {
        if ( is_array($array) && count($array) ) {
            foreach ( $array as $k => $v ) {
                if ( is_array($v) ) {
                    $array[$k] = self::convertStringArrays($v);
                    
                } else if ( is_string($v) ) {
                    $v = trim($v);
                    
                    if ( substr($v, 0, 1) == '[' && substr($v, (strlen($v) - 1), 1) == ']' ) {
                        $new_v = [];
                        $v     = trim($v, '[]');
                        $v     = explode(',', $v);
                        
                        foreach ( $v as $w ) {
                            if ( is_numeric($w) ) {
                                if ( $number_filter ) {
                                    if ( $number_filter == 'int' ) {
                                        $w = (int) $w;
                                        
                                    } else if ( $number_filter == 'float' ) {
                                        $w = (float) $w;
                                    }
                                }
                            }
                            
                            $new_v[] = $w;
                        }
                        
                        $array[$k] = $new_v;
                        
                    } else if ( is_numeric($v) ) {
                        if ( $number_filter ) {
                            if ( $number_filter == 'int' ) {
                                $array[$k] = (int) $v;
                                
                            } else if ( $number_filter == 'float' ) {
                                $array[$k] = (float) $v;
                            }
                        }
                    }                    
                } 
            }
        }
        
        return $array;
    }
    
    /**
     * Method to get the phone line
     *
     * @param  string       $phone         The phone
     * @return string                      The phone line
     * @since  2.0
     */
    public static function getPhoneLine($phone)
    {
        return substr(static::cleanPhone($phone), 3);
    }
    
    /**
     * Method to get the phone area
     *
     * @param  string       $phone         The phone
     * @return string                      The phone area
     * @since  2.0
     */
    public static function getPhoneArea($phone)
    {
        return substr(static::cleanPhone($phone), 0, 3);
    }
    
    /**
     * Method to clean the phone
     *
     * @param  string       $phone         The phone
     * @return string                      The cleaned phone
     * @since  2.0
     */
    public static function cleanPhone($phone)
    {
        return str_replace([' ','(',')','_',], ['','','',''], $phone);
    }   
    
    /**
     * Method to calculate date
     *
     * @param  mixed       $modify         The modify array
     * @return string                      The date
     * @since  2.0
     */
    public static function calculateDate($modify = false)
    {
        $date = new \DateTime("now");
        
        if ( $modify !== false ) {
            if ( is_array($modify) ) {
                foreach ( $modify as $m ) {
                    $date->modify($m);
                }
                
            } else {
                $date->modify($modify);
            }
        }
        
        return $date->format('Y-m-d');
    }
    
    /**
     * Method to convert a unix time to date
     *
     * @param  string       $time        The given unix time
     * @param  string       $format      The given format
     * @return string                    The e readable date
     * @since  2.0
     */
    public static function unixTimeToDate($time = null, $format = 'Y-m-d H:i:s')
    {
        $date = new \DateTime();
        
        $date->setTimestamp($time);
        
        return $date->format($format);
    }
    
    /**
     * Method to convert to readable money
     *
     * @param  string       $money        The given money
     * @return string                     The readable money
     * @since  2.0
     */
    public static function toReadableMoney($money = null)
    {
        return number_format($money, 2, ',', '.').' <i class="fa fa-try" aria-hidden="true"></i>';
    }
    
    /**
     * Method to convert a date to readable date
     *
     * @param  string       $date        The given date
     * @return string                    The readable date
     * @since  2.0
     */
    public static function dateToReadable($date = false)
    {
        if ( empty($date) || $date==null ) {
            return '-';
        }
        
        if ( static::validateDate($date, 'Y-m-d H:i:s') ) {
            return date('d/m/Y H:i', strtotime($date));
        }
        
        return '-';
    }
    
    /**
     * Method to convert a date to e readable date
     *
     * @param  string       $date        The given date
     * @return string                    The e readable date
     * @since  2.0
     */
    public static function dateEReadable($date = false)
    {
        if ( empty($date) || $date==null ) {
            return '-';
        }
        
        if ( static::validateDate($date, 'Y-m-d\TH:i:s.000\Z') ) {
            return date('d/m/Y H:i', strtotime($date));
        }
        
        return '-';
    }
    
    /**
     * Method to convert a date to e readable short date
     *
     * @param  string       $date        The given date
     * @return string                    The e readable short date
     * @since  2.0
     */
    public static function dateEReadableShort($date = false)
    {
        if ( empty($date) || $date==null ) {
            return '-';
        }
        
        if ( static::validateDate($date, 'Y-m-d\TH:i:s.000\Z') ) {
            return date('d/m/Y', strtotime($date));
        }
        
        return '-';
    }
    
    /**
     * Method to convert a date to readable short date
     *
     * @param  string       $date        The given date
     * @return string                    The readable short date
     * @since  2.0
     */
    public static function dateToReadableShort($date = false)
    {
        if ( empty($date) || $date == null ) {
            return '-';
        }
        
        if ( static::validateDate($date) ) {
            return date('d/m/Y', strtotime($date));
        }
        
        return '-';
    }
    
    /**
     * Method to validate a given email
     *
     * @param  string       $email       The given email
     * @return boolean                   True on valid, false on invalid
     * @since  2.0
     */
    public static function validateEmail($email = null)
    {
        if ( !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Method to validate a given date
     *
     * @param  string       $date       The given date
     * @param  string       $format     The given format
     * @return boolean                  True on valid, false on invalid
     * @since  2.0
     */
    public static function validateDate($date = null, $format = 'Y-m-d H:i:s')
    {
        $d = \DateTime::createFromFormat($format, $date);
        
        return ($d !== false);
    }
    
    /**
     * Method to prepare the values
     *
     * @param  string       $type            The value type
     * @param  array        $values          The given values
     * @return mixed                         The parsed value
     * @since  2.0
     */
    public static function preparedVals($type = null, $values = array())
    {
        try {
            $metas = self::getMetas($type);
            
            if ( $metas === false ) {
                throw new Exception('Could not get the class meta information.', 235, self::getCurSExc());
            }
            
            if ( count($values) ) {
                foreach ( $values as $key => $val ) {
                    if ( isset($metas['prop'][$key]) ) {
                        foreach ( $metas['prop'][$key] as $annotation ) {
                            if ( $annotation->getName() == 'Column' ) {
                                $args = $annotation->getArguments();
                                
                                if ( isset($args['type']) && !isset($args['skip']) && !empty($val) ) {
                                    $val          = preg_replace('/\s+/', ' ', trim($val));
                                    $values[$key] = self::parseValue($args['type'], $val);                                    
                                }
                            }
                        }
                    }                    
                    
                    if ( $key == 'status' || $key == 'cas_status' ) {
                        unset($values[$key]);
                    }
                    
                    if ( !is_numeric($val) && $val == null ) {
                        unset($values[$key]);
                    }
                }
            } 
            
        } catch (Exception $e) {
            static::raiseSException($e, __CLASS__.'::'.__METHOD__, 'Error');
            return false;
        }
        
        return $values;
    }
    
    /**
     * Method to parse a value
     *
     * @param  string       $type            The value type
     * @param  mixed        $data            The data
     * @return mixed                         The parsed value
     * @since  2.0
     */
    public static function parseValue($type, $data)
    {
        if ( $type == 'integer' ) {
            return (int) $data;
        }
        
        if ( $type == 'date' ) {
            $gmtTimezone = new \DateTimeZone('UTC');
            $date        = new \DateTime($data, $gmtTimezone);
            
            return $date->format('Y-m-d H:i:s');
        }
        
        if ( $type == 'datetime' ) {
            $gmtTimezone = new \DateTimeZone('UTC');
            $date        = new \DateTime($data, $gmtTimezone);
            
            return $date->format('Y-m-d H:i:s');
        }
        
        if ( $type == 'list' ) {
            return json_decode($data);
        }
        
        if ( $type == 'json' ) {
            return $data;
        }
        
        if ( $type == 'json-kv-1' ) {
            $fData = [];
            $data  = json_decode($data);
            
            if ( !empty($data) ) {
                $data = (array) $data;
                
                foreach ( $data as $dK => $dV ) {
                    $fData[] = [
                        'key'   => $dK,
                        'value' => $dV
                    ];
                }
                
                return $fData;
            }
        }
        
        if ( $type == 'json-kv-2' ) {
            $fData = [];
            $data  = json_decode($data);
            
            if ( !empty($data) ) {
                $data = (array) $data;
                
                foreach ( $data as $dK => $dV ) {
                    $nKV = [];
                    
                    if ( !empty($dV) ) {
                        foreach( $dV as $d2K => $d2V ) {
                            $nKV[] = [
                                'key'   => $d2K,
                                'value' => $d2V
                            ];
                        }
                    }
                    
                    $fData[] = [
                        'key'   => $dK,
                        'value' => $nKV
                    ];
                }
                
                return $fData;
            }
        }
        
        return $data;
    }
    
    /**
     * Method to get the type constant
     *
     * @param  string       $type            The given type
     * @return mixed                         The type constant, or false on invalid types
     * @since  2.0
     */
    public static function getTypeConstant($type)
    {
        $type = strtoupper($type);
        switch($type){
            case 'INTEGER':
            case 'DATE':
            case 'VARCHAR':
            case 'DECIMAL':
            case 'DATETIME':
            case 'CHAR':
            case 'TEXT':
            case 'FLOAT':
            case 'BOOLEAN':
            case 'DOUBLE':
            case 'TINYBLOB':
            case 'BLOB':
            case 'MEDIUMBLOB':
            case 'LONGBLOB':
            case 'BIGINTEGER':
            case 'JSON':
            case 'JSONB':
            case 'TIMESTAMP':
                return constant(dbColumn::class.'::TYPE_'.$type);
                break;
            default:
                return false;
                break;
        }
    }
    
    /**
     * Method to do date validation
     *
     * @return Phalcon\Exception
     * @since  2.0
     */
    public static function dateValidation($date = null)
    {
        if ( empty($date) ) {
            return true;
        }
        
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        
        if ( !empty($d::getLastErrors()['warnings']) || !empty($d::getLastErrors()['errors']) ) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Method to do date time validation
     *
     * @param  string           $date        The date
     * @return boolean                       True on success, false on failure
     * @since  2.0
     */
    public static function dateTimeValidation($date = null)
    {
        $d = \DateTime::createFromFormat('Y-m-d H:i:s', $date);
        
        if ( !empty($d::getLastErrors()['warnings']) || !empty($d::getLastErrors()['errors']) ) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Method to multi array 
     *
     * @param  string       $doc             The document
     * @param  string       $path            The path
     * @return array                         The document
     * @since  2.0
     */
    public static function toMultiArray($doc, $path)
    {
        if ( !Arrays::has($doc, $path.'.0') ) {
            $doc = Arrays::set($doc, $path, [0 => Arrays::get($doc, $path)]);
        }
        
        return $doc;
    }
    
    /**
     * Method to Check the given file for a valid class
     *
     * @param  string       $file            The file name
     * @return boolean                       The class name, or false on failure
     * @since  2.0
     */
    public static function checkFileClass($file)
    {
        $php_code = file_get_contents(realpath($this->baseDir . $file));
        $classes  = array();
        $tokens   = token_get_all($php_code);
        $count    = count($tokens);
        
        for ($i = 2; $i < $count; $i++) {
            if ( $tokens[$i - 2][0] == T_CLASS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING ) {
                $classes[] = $tokens[$i][1];
            }
        }
        
        if ( !empty($classes[0]) ) {
            return $classes[0];
            
        } else {
            return false;
        }
    }
    
    /**
     * Method to create directory maps for all php files
     * Mapping like /folder1/folder2/file.php
     *
     * @param  string       $entDir            The given directory
     * @return array                           The directory tree
     * @since  2.0
     */
    public static function createDirMap($entDir)
    {
        $dirTree = [];
        $di      = new RecursiveDirectoryIterator($entDir, RecursiveDirectoryIterator::SKIP_DOTS);
        
        foreach ( new RecursiveIteratorIterator($di) as $filename ) {
            $dir       = str_replace($entDir, '', dirname($filename));
            $dir       = str_replace('\\', '/', $dir);
            $dirTree[] = trim($dir).'/'.basename($filename);
        }
        
        return $dirTree;
    }
    
    /**
     * Method to mem Cnv
     *
     * @param  string       $size      The size
     * @return void
     * @since 2.0
     */
    public static function memCnv($size = null)
    {
        $unit = array('b','kb','mb','gb','tb','pb');
        
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }
    
    /**
     * Method to lower the string
     *
     * @param 	string       $string        The given string
     * @return 	string                      The lowered string
     * @since 	2.0
     */
    public static function lowerString($string)
    {
        return str_replace(
            ['I','Ä°','Ã‡','Äž','Ã–','Åž','Ãœ'],
            ['Ä±','i','Ã§','ÄŸ','Ã¶','ÅŸ','Ã¼'],
            strtolower($string));
    }
    
    /**
     * Method to fix all keys
     *
     * @param 	array       $array        The given array
     * @return 	array                     The fixed array
     * @since 	2.0
     */
    public static function fixAllKeys($array)
    {
        foreach ($array as $k => $val) {
            if ( is_array($val) ) $array[$k] = $this->fixAllKeys($val); //recurse
        }
        
        return array_values($array);
    }
    
    /**
     * Method to convert the table name to class name
     *
     * @param  string       $tableName       The table type
     * @return string                        The class name
     * @since  2.0
     */
    public static function tableNameToClass($tableName)
    {
        $newTableName = [];
        
        foreach(explode('_', $tableName) as $part){
            $newTableName[] = ucfirst($part);
        }
        
        return implode('', $newTableName);
    }
    
    /**
     * Method to parse the php file name into the table name
     *
     * @param  string       $phpFile            The php file name
     * @return string                           The table name
     * @since  2.0
     */
    public static function parseTableName($phpFile)
    {
        $tableName    = substr(str_replace('/', '', $phpFile), 0, -4);
        $newTableName = [];
        
        foreach ( str_split($tableName) as $key => $char ) {
            if ( $key == 0 ) {
                $newTableName[] = strtolower($char);
                
            } else if ( strcspn($char, 'ABCDEFGHJIJKLMNOPQRSTUVWXYZ') == 0 ) {
                $newTableName[] = '_'.strtolower($char);
                
            } else {
                $newTableName[] = strtolower($char);
            }
        }
        
        return implode('',$newTableName);
    }
    
    /**
     * Method to convert the string to a table name
     *
     * @param  string       $tableName       The table name
     * @return string                        The new table name
     * @since  2.0
     */
    public static function stringToTableName($tableName)
    {
        $newTableName = [];
        
        foreach ( str_split($tableName) as $key => $char ) {
            if ( $key == 0 ) {
                $newTableName[] = strtolower($char);
                
            } else if ( strcspn($char, 'ABCDEFGHJIJKLMNOPQRSTUVWXYZ') == 0 ) {
                $newTableName[] = '_'.strtolower($char);
                
            } else {
                $newTableName[] = strtolower($char);
            }
        }
        
        return implode('', $newTableName);
    }
    
    /**
     * Method to get the method of a field
     *
     * @param  string       $field        The field
     * @param  object       $method       The method
     * @return string                     The method
     * @since  2.0
     */
    public static function fieldToMethod($field, $method)
    {
        $newTableName = [];
        
        foreach (explode('_', $field) as $part) {
            $newTableName[] = ucfirst($part);
        }
        
        return $method.implode('', $newTableName);
    }
    
    /**
     * Method to get the type of a class
     *
     * @param  string       $tableName        The table name
     * @return string                         The type
     * @since  2.0
     */
    public static function classToType($tableName)
    {
        $newTableName = [];
        
        foreach ( str_split($tableName) as $key => $char ) {
            if ( $key == 0 ) {
                $newTableName[] = strtolower($char);
                
            } else if ( strcspn($char, 'ABCDEFGHJIJKLMNOPQRSTUVWXYZ') == 0 ) {
                $newTableName[] = '_'.strtolower($char);
                
            } else {
                $newTableName[] = strtolower($char);
            }
        }
        
        return implode('', $newTableName);
    }
    
    /**
     * Method to get the class of a type
     *
     * @param  string       $type        The type
     * @return string                    The class
     * @since  2.0
     */
    public static function typeToClass($type)
    {
        $newTableName = [];
        
        foreach ( explode('_', $type) as $part ) {
            $newTableName[] = ucfirst($part);
        }
        
        return implode('', $newTableName);
    }
    
    /**
     * Method to convert the given string to a utf8 string
     *
     * @param  string       $string        The string
     * @return string                      The UTF8 string
     * @since  2.0
     */
    public static function toUTF8($string = '')
    {
        if ( !preg_match('!!u', $string) ) {
            $string = utf8_encode($string);
            
        } else if ( mb_detect_encoding($string, 'UTF-8', true) != 'UTF-8' || mb_detect_encoding($string, 'UTF-8', true) === false ) {
            $string = utf8_encode($string);
        }
        
        return $string;
    }
    
    /**
     * Method to get a service
     *
     * @param  string       $service           The service name
     * @return object                          The service
     * @since  2.0
     */
    public static function getSService($service)
    {
        return \Phalcon\DI::getDefault()->get($service);
    }
    
    /**
     * Method to get a service
     *
     * @param  string       $service           The service name
     * @return object                          The service
     * @since  2.0
     */
    public function getService($service)
    {
        return \Phalcon\DI::getDefault()->get($service);
    }    
}

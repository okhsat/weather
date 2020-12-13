<?php
namespace Models;

use Phalcon\Exception;
use Phalcon\Mvc\Model\Relation;
use Phalcon\Mvc\Model\Resultset;
use Models\BaseModel;

class User extends BaseModel
{
    /**
     * @Primary
     * @AutoIncrement
     * @NotNull
     * @Unsigned
     * @Field(type="integer")
     * @Column(type="integer")
     */
    public $id;
    
    /**
     * @NotNull
     * @Field(type="varchar", size=50)
     * @Column(type="string", analyzed="turkish")
     */
    public $name;
    
    /**
     * @Index
     * @NotNull
     * @Field(type="varchar", size=100)
     * @Column(type="string")
     */
    public $email;
    
    /**
     * @Index
     * @NotNull
     * @Field(type="varchar", size=30)
     * @Column(type="string")
     */
    public $username;

    /**
     * @NotNull
     * @Field(type="varchar", size=60)
     * @Column(type="string")
     */
    public $password;
    
    /**
     * @Field(type="varchar", size=6)
     * @Column(type="string")
     */
    public $gender;
    
    /**
     * @Index
     * @Field(type="varchar", size=20)
     * @Column(type="string")
     */
    public $phone;
    
    /**
     * @Field(type="varchar", size=50)
     * @Column(type="string")
     */
    public $city;
    
    /**
     * @Index
     * @NotNull
     * @Field(type="varchar", size=30)
     * @Column(type="string")
     */
    public $role;
    
    /**
     * @Field(type="varchar", size=50)
     * @Column(type="string")
     */
    public $image;
    
    /**
     * @Index
     * @Field(type="varchar", size=511)
     * @Column(type="string")
     */
    public $city_ids;
    
    /**
     * @Index
     * @Unsigned
     * @Field(type="integer")
     * @Column(type="integer")
     */
    public $coupon_id;
    
    /**
     * @Field(type="datetime")
     * @Column(type="datetime")
     */
    public $coupon_added;
    
    /**
     * @NotNull
     * @Field(type="varchar", size=3, default="+03:00")
     * @Column(type="string")
     */
    public $timezone;
    
    /**
     * @NotNull
     * @Field(type="varchar", size=3, default="tr")
     * @Column(type="string")
     */
    public $cid;
    
    /**
     * @Field(type="varchar", size=20)
     * @Column(type="string")
     */
    public $os;
    
    /**
     * @Index
     * @Field(type="varchar", size=1, default='A')
     * @Column(skip="1")
     */
    public $status;
    
    /**
     * @Field(type="datetime")
     * @Column(type="datetime")
     */
    public $created;

    /**
     * @Field(type="datetime")
     * @Column(type="datetime")
     */
    public $updated;

    /**
     * @Field(type="datetime")
     * @Column(type="datetime")
     */
    public $deleted;
        
    /**
     * Method to bind the data to the model (e.g. for storing into the database)
     *
     * @param 	array       $array        The array to bind to the object
     * @return 	boolean                   True on success, false on failure
     * @since 	2.0
     */
    public function bind($array = array())
    {
        $this->_curExc = null;
        $new           = !$this->id;
        $no_coupon     = !$this->coupon_id;
        $status        = trim($this->status);
        
        unset($array['id']);
        unset($array['created']);
        unset($array['updated']);
        unset($array['deleted']);
        
        if ( isset($array['city_ids']) && is_array($array['city_ids']) ) {
            $array['city_ids'] = implode(',', $array['city_ids']);
        }
        
        if ( isset($array['password']) && !empty(trim($array['password'])) ) {
            $this->password = password_hash(trim($array['password']), PASSWORD_DEFAULT);
        }
        
        unset($array['password']);
        
        if ( !parent::bind($array) ) {
            return false;
        }
        
        $this->role     = trim($this->role);
        $this->username = trim($this->username);
        $this->status   = trim($this->status);
        $this->cid      = trim($this->cid);
        
        if ( empty($this->role) ) {
            $this->role = 'general_user';
        }
        
        if ( empty($this->username) ) {
            $this->username = trim($this->email);
        }
        
        if ( !in_array( strtoupper($this->status), ['A', 'D'] ) ) {
            $this->status = 'A';
        }
        
        if ( $this->status == 'A' ) {
            $this->cas_status = 1;
            
        } else {
            $this->cas_status = 0;
        }
        
        if ( empty($this->cid) ) {
            $this->cid = 'tr';
        }        
        
        $date = date('Y-m-d H:i:s', time()); // Greenwich Mean Date and Time To MySQL
        
        if ( $no_coupon && $this->coupon_id > 0 ) {
            $this->coupon_added = $date;
        }
        
        if ($new) {
            $this->created = $date;
            
        } else {
            $this->updated = $date;
        }
        
        if ( $status != $this->status ) {
            if ( $this->status == 'D' ) {
                $this->deleted = $date;
            }
        }
        
        return true;
    }
    
    /**
     * Method to check the whole model properties (e.g. before storage into the database)
     *
     * @return 	boolean    True on success, false on failure
     * @since 	2.0
     */
    public function check()
    {
        $this->_curExc = null;
        
        if ( !parent::check() ) {
            return false;
        }
        
        $this->name     = trim($this->name);
        $this->username = trim($this->username);
        $this->password = trim($this->password);
        $this->email    = trim($this->email);
        $this->phone    = trim($this->phone);
        $this->role     = trim($this->role);
        $this->status   = trim($this->status);
        $this->cid      = trim($this->cid);        
        
        try {
            if ( empty($this->name) ) {
                throw new Exception('The user\'s full name can not be empty.', 234);
            }
            
            if ( empty($this->username) ) {
                throw new Exception('The username can not be empty.', 234);
            }                           
            
            if ( empty($this->email) ) {
                throw new Exception('The user\'s email can not be empty.', 234);
            }
            
            if ( empty($this->role) ) {
                throw new Exception('The user role not set', 234);
            }
            
            if ( !in_array( strtoupper($this->status), ['A', 'D'] ) ) {
                throw new Exception('Invalid product status.', 234);
            }           
            
            if ( empty($this->cid) ) {
                throw new Exception('CID not set', 234);
            }           
            
        } catch (Exception $e) {
            $this->raiseException($e, __CLASS__.'::'.__METHOD__, 'Error');
            return false;
        }
        
        return true;
    }
}

<?php
namespace Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Message;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\InclusionIn;
use Models\BaseModel;

class Coupon extends BaseModel
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
     * @Index
     * @Field(type="varchar", size=255)
     * @Column(type="string")
     */
    public $code;
    
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
    public $deleted;
}

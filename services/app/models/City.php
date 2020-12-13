<?php
namespace Models;

use Phalcon\Exception;
use Phalcon\Mvc\Model\Relation;
use Phalcon\Mvc\Model\Resultset;
use Models\BaseModel;

class City extends BaseModel
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
     * @Field(type="integer")
     * @Column(type="integer")
     */
    public $temprature;
    
    /**
     * @Index
     * @NotNull
     * @Field(type="varchar", size=100)
     * @Column(type="string")
     */
    public $weather;
    
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
}

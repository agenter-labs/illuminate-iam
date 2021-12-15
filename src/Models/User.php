<?php

namespace AgenterLab\IAM\Models;
use AgenterLab\Database\SoftDeletes;
use AgenterLab\Uid\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use AgenterLab\IAM\Traits\IamUserTrait;
use AgenterLab\IAM\Contracts\IamUserInterface;

class User extends Model implements 
    AuthenticatableContract, 
    AuthorizableContract, 
    IamUserInterface
{
    use SoftDeletes, ModelTrait, Authenticatable, Authorizable, IamUserTrait;
    
    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'U';
    
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id', 'name','email', 'country', 'mobile', 'locale', 'enabled'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'email' => '',
        'country' => '',
        'mobile' => '',
    ];

    /**
     * Creates a new instance of the model.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->table = Config::get('iam.tables.user');
    }
    
}
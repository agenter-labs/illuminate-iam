<?php

namespace AgenterLab\IAM\Models;
use AgenterLab\Database\SoftDeletes;
use AgenterLab\Uid\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use AgenterLab\Database\AuthUser;
use AgenterLab\IAM\Traits\IamRoleTrait;
use AgenterLab\IAM\Contracts\IamRoleInterface;

class Role extends Model implements IamRoleInterface
{
    use AuthUser, SoftDeletes, ModelTrait, IamRoleTrait;

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
        'company_id', 'is_default','is_system', 'title', 'description'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'description' => '',
        'created_by' => 0,
        'updated_by' => 0,
    ];

    /**
     * Creates a new instance of the model.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->table = Config::get('iam.tables.role');
    }
    
}
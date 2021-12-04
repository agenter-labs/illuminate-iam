<?php

namespace AgenterLab\IAM\Models;
use AgenterLab\Uid\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use AgenterLab\IAM\Traits\IamPermissionTrait;
use AgenterLab\IAM\Contracts\IamPermissionInterface;

class Permission extends Model implements IamPermissionInterface
{
    use ModelTrait, IamPermissionTrait;
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

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
        'name','title', 'description'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'description' => ''
    ];

    /**
     * Creates a new instance of the model.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->table = Config::get('iam.tables.permission');
    }
    
}
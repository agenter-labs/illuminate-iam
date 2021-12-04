<?php

namespace AgenterLab\IAM\Models;
use AgenterLab\Database\AuthUser;
use AgenterLab\Database\SoftDeletes;
use AgenterLab\Uid\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Company extends Model
{
    use AuthUser, SoftDeletes, ModelTrait;

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
        'name','enabled','domain'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'domain' => ''
    ];

    /**
     * Creates a new instance of the model.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->table = Config::get('iam.tables.company');
    }
    
}
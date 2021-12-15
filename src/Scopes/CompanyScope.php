<?php

namespace AgenterLab\IAM\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use AgenterLab\IAM\Contracts\TenantInterface;
use Illuminate\Support\Facades\Config;

class CompanyScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $table = $model->getTable();

        if (in_array($table, Config::get('iam.scope.skip_tables', []))) {
            return;
        }

        $column = Config::get('iam.scope.column', 'company_id');

        // Skip if already exists
        if ($this->exists($builder, $column)) {
            return;
        }

        if (! $model instanceof TenantInterface) {
            return;
        }

        if ($model->defaultTenantable()) {
            // Apply company scope
            $builder->where(function ($query) use($table, $column) {
                $query->where($table . '.is_system', '=', 1)
                    ->orWhere($table . '.' . $column, '=', session($column));
            });
        } else if ($model->tenantable()) {
            // Apply company scope
            $builder->where($table . '.' . $column, '=', session($column));
        }
    }

    /**
     * Check if scope exists.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  $column
     * @return boolean
     */
    protected function exists($builder, $column)
    {
        $query = $builder->getQuery();

        foreach ((array) $query->wheres as $key => $where) {
            if (empty($where) || empty($where['column'])) {
                continue;
            }

            if (strstr($where['column'], '.')) {
                $whr = explode('.', $where['column']);

                $where['column'] = $whr[1];
            }

            if ($where['column'] != $column) {
                continue;
            }

            return true;
        }

        return false;
    }
}

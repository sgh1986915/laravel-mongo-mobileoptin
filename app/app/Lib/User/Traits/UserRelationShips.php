<?php namespace MobileOptin\Lib\User\Traits;

trait UserRelationShips {

    /**
     * role() one-to-one relationship method
     *
     * @return QueryBuilder
     */
    public function role()
    {
        return $this->belongsTo('MobileOptin\Models\Role');
    }

}
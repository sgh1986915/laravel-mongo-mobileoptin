<?php namespace MobileOptin\Lib\User\Traits;

use PhpSpec\Exception\Exception;

trait UserACL
{

    /**
     * can Checks a Permission
     *
     * @param  String $perm Name of a permission
     * @return Boolean true if has permission, otherwise false
     */
    public function can( $perm = null )
    {
        if ( $perm ) {

            return $this->checkPermission( $this->getArray( $perm ) );
        }

        return false;
    }

    /**
     * Make string to array if already not
     *
     * @param  Mixed $perm String/Array
     * @return Array
     */
    protected function getArray( $perm )
    {
        return is_array( $perm ) ? $perm : explode( '|', $perm );
    }

    /**
     * Check if the permission matches with any permission user has
     *
     * @param  Array $perm Name of a permission (one or more separated with |)
     * @return Boolean true if permission exists, otherwise false
     */
    protected function checkPermission( Array $permArray = [ ] )
    {
        $perms = $this->role->permissions->fetch( 'permission_slug' );

        $perms = array_map( 'strtolower', $perms->toArray() );

        return count( array_intersect( $perms, $permArray ) );
    }


    function getProfileOption($option = '') {
        if ($option) {
            try {
                if ($this->hasPackageActive()) {
                    if (isset($this->profile->package->$option)) {
                        $perms = $this->profile->package->$option;
                        return $perms;
                    }
                } else {
                    if (isset($this->profile->$option)) {
                        $perms = $this->profile->$option;
                        return $perms;
                    }
                }
            } catch (Exception $e) {
                
            }
        }
        return false;
    }

    function campaignLimit()
    {
        try {
            if($this->hasPackageActive()){
               $max_nu_campaign =  $this->profile->package->max_campaigns;
            }else{
               $max_nu_campaign = $this->profile->max_campaigns;
            }
            $cur_nu_campaign = $this->campaigns->count();

            if ( $cur_nu_campaign < $max_nu_campaign ) {
                return true;
            }
        } catch ( Exception $e ) {

        }
        return false;

    }
    
    function hasPackageActive(){
        if($this->profile && $this->profile->package_id != 0){
            return $this->profile->package_id;
        }else
            return false;
    }

    function getOwner()
    {
        try {
            $ownerdata = $this->owner->first();
            if ( $ownerdata ) {


                $id = $ownerdata->id;

                return $id;
            }
        } catch ( Exception $e ) {

        }
        return false;

    }

    function getOwnerObj()
    {
        try {
            $ownerdata = $this->owner->first();

            return $ownerdata;

        } catch ( Exception $e ) {

        }
        return false;

    }

    /**
     * hasPermission Checks if has a Permission (Same as 'can')
     *
     * @param  String $perm [Name of a permission
     * @return Boolean true if has permission, otherwise false
     */
    public function hasPermission( $perm = null )
    {
        return $this->can( $perm );
    }

    /**
     * Checks if has a role
     *
     * @param  String $perm [Name of a permission
     * @return Boolean true if has permission, otherwise false
     */
    public function hasRole( $role = null )
    {
        if ( is_null( $role ) ) return false;

        return strtolower( $this->role->role_slug ) == strtolower( $role );
    }

    /**
     * Check if user has given role
     *
     * @param  String $role role_slug
     * @return Boolean TRUE or FALSE
     */
    public function is( $role )
    {
        return $this->role->role_slug == $role;
    }

    /**
     * Check if user has permission to a route
     *
     * @param  String $routeName
     * @return Boolean true/false
     */
    public function hasRoute( $routeName )
    {
        $route = app( 'router' )->getRoutes()->getByName( $routeName );

        if ( $route ) {

            $action = $route->getAction();

            if ( isset( $action[ 'permission' ] ) ) {

                $array = explode( '|', $action[ 'permission' ] );

                return $this->checkPermission( $array );
            }
        }

        return false;
    }

    /**
     * Check if a top level menu is visible to user
     *
     * @param  String $perm
     * @return Boolean true/false
     */
    public function canSeeMenuItem( $perm )
    {
        return $this->can( $perm ) || $this->hasAnylike( $perm );
    }

    /**
     * Checks if user has any permission in this group
     *
     * @param  String $perm Required Permission
     * @param  Array $perms User's Permissions
     * @return Boolean true/false
     */
    protected function hasAnylike( $perm )
    {
        $parts = explode( '_', $perm );

        $requiredPerm = array_pop( $parts );

        $perms = $this->role->permissions->fetch( 'permission_slug' );

        foreach ( $perms as $perm ) {
            if ( ends_with( $perm, $requiredPerm ) ) return true;
        }

        return false;
    }
}
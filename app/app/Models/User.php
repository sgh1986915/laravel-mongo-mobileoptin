<?php

namespace MobileOptin\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use MobileOptin\Http\Requests\Auth\RegisterRequest;
use MobileOptin\Lib\User\Traits\UserACL;
use MobileOptin\Lib\User\Traits\UserAccessors;
use MobileOptin\Lib\User\Traits\UserQueryScopes;
use MobileOptin\Lib\User\Traits\UserRelationShips;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

    use Authenticatable,
        CanResetPassword;

/**
     * 19
     * Application's Traits (Separation of various types of methods)
     * 20
     */
    use UserACL,
        UserRelationShips;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'name', 'email', 'password', 'role_id', 'popup_message'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [ 'password', 'remember_token'];

    public function profile() {
        return $this->hasOne('MobileOptin\Models\UserProfile');
    }

    public function campaigns() {
        return $this->hasMany('MobileOptin\Models\Campaigns');
    }

    public function allowed_campaigns() {
        return $this->belongsToMany('MobileOptin\Models\Campaigns', 'user_allowed_campaigns', 'user_id', 'campaign_id');
    }

    /**
     * in case that the user is advertiser (account owner) he can have multiple users wich he can manage
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users() {
        return $this->belongsToMany('MobileOptin\Models\User', 'user_owner', 'owner_id', 'user_id');
    }

    /**informazioni
     * in case that the user is advertiser (account owner) he can have multiple users wich he can manage
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function owner() {
        return $this->belongsToMany('MobileOptin\Models\User', 'user_owner', 'user_id', 'owner_id');
    }

    public function allowed_groups() {
        if($pack_id = $this->hasPackageActive()){
            $package = Package::where('id',$pack_id)->first();
            return $package->hasMany('MobileOptin\Models\PackageToTemplatesGroup', 'package_id', 'id');
        }
        return $this->hasMany('MobileOptin\Models\UsersToTemplatesGroup', 'user_id', 'id');
    }

    public function hasModule($module_name) {
        if (!$this->hasRole('admin')) {
            $module = \MobileOptin\Models\Modules::where('name', 'LIKE', $module_name)->where('status', 1)->first();
            if ($module) {
                $check = \MobileOptin\Models\ModuleUser::where('user_id', $this->id)->where('module_id', $module->id)->where('status', 1)->get();
                if (count($check) == 0) {
                    return false;
                } else {
                    return true;
                }
            } else {
                return false;
            }
        } else {
            $module = \MobileOptin\Models\Modules::where('name', 'LIKE', $module_name)->where('status', 0)->first();
            if ($module) {
                return false;
            }else{
            return true;}
        }
    }
    
    public function foo(){
      return 'foo!';
    }

}

<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Topic;
use Auth;
// 引入权限角色trait
use Spatie\Permission\Traits\HasRoles;

// JWT 验证
use Tymon\JWTAuth\Contracts\JWTSubject;

// oauth2 passport 验证
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements JWTSubject
{
    // 用户最后活跃时间
    use Traits\LastActivedAtHelper;

    use Notifiable{
        notify as protected laravelNotify;
    }

    use Traits\ActiveUserHelper;

    // 使用trait
    use HasRoles;
    
    use HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'introduction', 'avatar', 'phone','weixin_openid', 'weixin_unionid', 'registration_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    //判断是否登录本人编辑或删除贴子
    public function isAuthorOf($model)
    {
        return $this->id == $model->user_id;
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function notify($instance)
    {
        // 如果要通知的人是当前用户，就不必通知了！
        if ($this->id == Auth::id()) {
            return;
        }
        $this->increment('notification_count');
        $this->laravelNotify($instance);
    } 

    public function markAsRead()
    {
        $this->notification_count = 0;
        $this->save();
        // $this->unreadNotifications->markAsRead()  unreadNotifications markAsRead() 是消息通已经定义实体和方法
        $this->unreadNotifications->markAsRead(); 
    }

    public function setPasswordAttribute($value)
    {
        // 如果值的长度等于 60，即认为是已经做过加密的情况
        if (strlen($value) != 60) {

            // 不等于 60，做密码加密处理
            $value = bcrypt($value);
        }

        $this->attributes['password'] = $value;
    }          

    public function setAvatarAttribute($path)
    {
        // 如果不是 `http` 子串开头，那就是从后台上传的，需要补全 URL
        if ( ! starts_with($path, 'http')) {

            // 拼接完整的 URL
            $path = config('app.url') . "/uploads/images/avatars/$path";
        }

        $this->attributes['avatar'] = $path;
    }


    // Rest omitted for brevity 以下两个方法是实现 JWTSubject 接口两个方法 getJWTIdentifier，getJWTCustomClaims

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function findForPassport($username)
    {
        filter_var($username, FILTER_VALIDATE_EMAIL) ?
          $credentials['email'] = $username :
          $credentials['phone'] = $username;

        return self::where($credentials)->first();
    }       
}

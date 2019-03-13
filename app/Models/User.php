<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Topic;
use Auth;
// 引入权限角色trait
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable{
        notify as protected laravelNotify;
    }

    // 使用trait
    use HasRoles;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','introduction','avatar'
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

}

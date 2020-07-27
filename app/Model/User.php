<?php

declare (strict_types=1);

namespace App\Model;

use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Model\Model;
use Hyperf\ModelCache\Cacheable;

/**
 * @property int $id
 * @property string $username
 * @property string $avatar_url
 * @property string $gender
 * @property string $province
 * @property string $city
 * @property string $country
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $openid
 * @property string $unionid
 * @property string $access_token
 * @property Builder $userCollect
 * @property Builder $userAte
 */
class User extends Model
{
    use Cacheable;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
//    protected $fillable = [];
    
    /**
     * @var array 除了数组里的字段都能被赋值
     */
    protected $guarded = [];
    
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
    
    /**
     * 用户收藏的食物
     * @return \Hyperf\Database\Model\Relations\HasMany
     */
    public function userCollect()
    {
        return $this->hasMany(UserCollect::class, 'user_id', 'id');
    }
    
    /**
     * 用户吃过的食物
     * @return \Hyperf\Database\Model\Relations\HasMany
     */
    public function userAte()
    {
        return $this->hasMany(UserAte::class, 'user_id', 'id');
    }
    
    /**
     * 是否已登录
     * @return bool
     */
    public function hasInfo()
    {
        return !empty($this->username);
    }
    
    /**
     * 是否是管理人员的账号 (已登录且被设置为管理员)
     * @return bool
     */
    public function isAdmin()
    {
        return $this->hasInfo() && Admin::query()->where('user_id', '=', $this->id)->exists();
    }
}
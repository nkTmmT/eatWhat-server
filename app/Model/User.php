<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Model\Model;
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
 * @property Builder $userCollect
 * @property Builder $userAte
 */
class User extends Model
{
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
}
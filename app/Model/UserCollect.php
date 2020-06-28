<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Model\Model;
use Hyperf\ModelCache\Cacheable;

/**
 * @property int $id 
 * @property int $food_id 
 * @property int $user_id 
 * @property Builder $user
 * @property Builder $foodInfo
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class UserCollect extends Model
{
    use Cacheable;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_collect';
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
    protected $casts = ['id' => 'integer', 'food_id' => 'integer', 'user_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
    
    /**
     * 收藏的食物
     * @return \Hyperf\Database\Model\Relations\HasOne
     */
    public function foodInfo()
    {
        return $this->hasOne(FoodInfo::class, 'id', 'food_id');
    }
    
    /**
     * 所属的用户
     * @return \Hyperf\Database\Model\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
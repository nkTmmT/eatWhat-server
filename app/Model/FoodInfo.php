<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\DbConnection\Model\Model;
/**
 * @property int $id 
 * @property string $name 
 * @property string $reason 
 * @property string $image 
 * @property int $reference_id 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class FoodInfo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'food_info';
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
    protected $casts = ['id' => 'integer', 'reference_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
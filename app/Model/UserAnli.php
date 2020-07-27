<?php

declare (strict_types=1);

namespace App\Model;

use Hyperf\Contract\ConfigInterface;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;

/**
 * @property int $id
 * @property string $name
 * @property string $reason
 * @property string $image
 * @property int $status
 * @property int $reference_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class UserAnli extends Model
{
    const TABLE = 'user_anli';
    const STATUS_NORMAL = 1;
    const STATUS_SUCCESS = 2;
    const STATUS_FAIL = 3;
    /**
     * @Inject()
     * @var RequestInterface
     */
    private $request;
    
    /**
     * @Inject()
     * @var ConfigInterface
     */
    private $config;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_anli';
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
    protected $casts = ['id' => 'integer', 'reference_id' => 'integer', 'status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
    
    /**
     * 所属的用户
     * @return \Hyperf\Database\Model\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'reference_id');
    }
    
    /**
     *  获取用户的姓名.
     * @param  string $value
     * @return string
     */
    public function getImageAttribute($value)
    {
        return 'http://' . $this->request->header('host', $this->config->get('host')) . $value;
    }
}
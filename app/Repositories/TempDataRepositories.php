<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class TempDataRepositories
{
    const PLAY_VIDEO_TIMES = 'play_video_times';
    const TOTAL_VIEWED_TIMES = 'total_viewed_times';
    const VIDEO_RECOMMEND_MAX_ID = 'view_recommend_max_id';
    const PLAY_VIDEO_SECOND = 'play_video_second';
    const TOTAL_VIDEO_TIMES = 10;
    protected $table_name = 'temp_data';

    public function GetValue($user_id, $key)
    {
        return DB::selectOne("select * from temp_data where user_id = ? AND temp_key = ?", [$user_id, $key]);
    }

    public function GetValueByKey($key)
    {
        return DB::selectOne("select * from temp_data where  temp_key = ?", [$key]);
    }

    public function ClearValue($user_id, $key)
    {
        return DB::update('update temp_data set temp_value = ?  where user_id=? AND temp_key = ? ', [0, $user_id, $key]);
    }

    /**
     * 更新临时数据
     * @param $user_id
     * @param $key
     * @param $value
     * @return int
     */
    public function UpdateTempValue($user_id, $key, $value)
    {
        $data['user_id'] = $user_id;
        $data['temp_key'] = $key;
        return DB::table($this->table_name)->where($data)->update($value);
    }

    /**
     * 更新或者插入
     * @param $attributes
     * @param $values
     * @return bool
     */
    public function UpateOrInsertTempData($attributes, $values)
    {
       return  DB::table($this->table_name)->updateOrInsert($attributes, $values);
    }

}
<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class TempDataRepositories
{
    public function GetValue($user_id, $key)
    {
        return DB::selectOne("select * from temp_data where user_id = ? AND temp_key = ?", [$user_id, $key]);
    }

    public function ClearValue($user_id, $key)
    {
        return DB::update('update temp_data set temp_value = ?  where user_id=? AND temp_key = ? ', [0, $user_id, $key]);
    }

    /**
     * 更新自己看的数值
     * @param $user_id
     * @param $key
     */
    public function UpdateValue($user_id, $key, $temp_value = 1)
    {
        $value = $this->GetValue($user_id, $key);

        if(empty($value)){
            DB::table('temp_data')->insertGetId(
                [
                    'user_id' =>$user_id,
                    'temp_key'=>$key,
                    'temp_value'=>1,
                    'add_time'=>date('Y-m-d H:i:s'),
                ]
            );

        }else{
            DB::update('update temp_data set temp_value = temp_value + ?  where user_id=? AND temp_key = ? AND temp_value = ?', [$temp_value, $user_id, $key,$value->temp_value]);
        }

        return true;
    }
}
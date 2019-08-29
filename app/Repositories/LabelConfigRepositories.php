<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class LabelConfigRepositories
{
    const TODAY_HOT_TYPE = 1;
    const POPULAR_HOT_TYPE = 2;
    const RANK_HOT_TYPE = 3;
    const LABEL_HOT_TYPE = 4;
    public static $label_type = [1=>'today_hot', 2=>'popular_hot', 3=>'rank_hot', 4=>'label_hot',];
    protected $table_name = 'label_config';

    /**
     * @param $data
     * @return int
     */
    public function InsertLabelConfig($data)
    {
        return DB::table($this->table_name)->insertGetId($data);
    }

    /**
     * @param $data
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function GetLabelConfigByName($data)
    {
        return DB::table($this->table_name)->where($data)->first();
    }

    /**
     * 获取所有的配置
     * @return array
     */
    public function GetAllLabelConfig()
    {
        $data = DB::table($this->table_name)->get()->toarray();

        if(empty($data)){
            return [];
        }
        $result = [];
        foreach($data as $key=>$value){
            $result[$value->type][] = $value;
        }

        return $result;
    }




    public function GetLabelConfigByType($type)
    {
        return DB::table($this->table_name)->where('type', '=', $type)->orderBy('sort', 'desc')->get();
    }


}
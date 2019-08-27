<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class LabelConfigRepositories
{

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




}
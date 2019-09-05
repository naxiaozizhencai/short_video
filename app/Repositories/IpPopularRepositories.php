<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class IpPopularRepositories
{

    protected $table_name = 'ip_popular';


    public function GetIpPopularData($data)
    {
        return DB::table($this->table_name)->where($data)->first();
    }

    /**
     * 更新信息
     * @param $uid
     * @param $data
     * @return mixed
     */
    public function UpdateIpPopularData($condition, $data)
    {
        return DB::table($this->table_name)->where($condition)->update($data);
    }



}
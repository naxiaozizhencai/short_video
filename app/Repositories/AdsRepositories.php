<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class AdsRepositories
{

    protected $table_name = 'ads';

    /**
     * 获取广告
     * @param $data
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function GetAdsData($data)
    {
        return DB::table($this->table_name)->where($data)->orderBy('sort', 'desc')->get();
    }



}
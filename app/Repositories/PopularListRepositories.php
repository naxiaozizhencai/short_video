<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class PopularListRepositories
{
    protected $table_name = 'popular_list';

    /**
     *
     * @param $data
     * @return int
     */
    public function InsertPopularData($data)
    {
        return DB::table($this->table_name)->insertGetId($data);
    }

    /**
     * 获取数据
     * @param $data
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function GetUserPopularData($data)
    {
        return DB::table($this->table_name)->where($data)->first();
    }
}
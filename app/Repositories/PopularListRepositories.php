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

    /**
     * 获取邀请列表
     * @param $condition
     * @return array
     */
    public function GetUserPopolarList($condition)
    {
        return DB::table($this->table_name)->leftJoin('users', 'popular_list.user_id', '=', 'users.id')->
        leftJoin('users_detail', 'users_detail.user_id', '=', 'users.id')->
        where($condition)->paginate(20, ['users.is_register', 'users.phone', 'users_detail.popular_num'])->toarray();

    }
}
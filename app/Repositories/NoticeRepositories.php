<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class NoticeRepositories
{

    protected $table_name = 'notice';

    /**
     * 获取公告
     * @param $data
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function GetNoticeData($data)
    {
        return DB::table($this->table_name)->where($data)->first();
    }



}
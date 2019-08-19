<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class DiscussReportRepositories
{

    protected $table_name = 'discuss_report';

    /**
     * 添加评论
     * @param $data
     */
    public function InsertDiscussReport($data)
    {
        return DB::table($this->table_name)->insertGetId($data);
    }


}
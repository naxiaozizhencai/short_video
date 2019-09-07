<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class DomainRepositories
{

    protected $table_name = 'domain';

    /**
     * 获取广告
     * @param $data
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function GetDomainData($data)
    {
        return DB::table($this->table_name)->where($data)->orderBy('id', 'desc')->get();
    }

    
}
<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class IpPopularRepositories
{

    protected $table_name = 'ip_popular';


    public function GetIpPopularData($data)
    {
        return DB::table($this->table_name)->where($data)->get();
    }



}
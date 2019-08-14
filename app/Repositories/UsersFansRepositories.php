<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class UsersFansRepositories
{

    protected $table_name = 'users_fans';

    /**
     * @param $data
     * @return int
     */
    public function InsertFans($data)
    {
        return DB::table($this->table_name)->insertGetId($data);
    }


    public function DeleteFans()
    {

    }


    public function GetMyFans()
    {

    }
}
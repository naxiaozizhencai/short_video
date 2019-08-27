<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class VideoLabelRepositories
{

    protected $table_name = 'video_label_list';

    /**
     * @param $data
     * @return int
     */
    public function InsertVideoLabel($data)
    {
        return DB::table($this->table_name)->insertGetId($data);
    }


}
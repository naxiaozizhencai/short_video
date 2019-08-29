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

    /**
     * 获取带标签的数据
     * @param $search_arr
     * @return mixed
     */
    public function GetVideoLabelData($search_arr)
    {
        $query = DB::table($this->table_name);
        $query->where(function ($query) use ($search_arr){
            foreach($search_arr as $key=>$search){
                switch ($key){
                    case 'user_id':
                        $query->where('video_list.user_id', '=', $search);
                        break;

                        case 'label_name':
                        $query->where('video_label_list.label_name', '=', $search);
                        break;
                }
            }
        })->where('is_check', '=', 1);

        $query->leftjoin('video_list', 'video_label_list.video_id', '=', 'video_list.id')->leftjoin('users', 'video_list.user_id', '=', 'users.id')->leftjoin('users_detail', 'users.id', '=', 'users_detail.user_id');

        return $query->paginate(6, ['video_label_list.*', 'video_list.id as video_id', 'video_list.*', 'users.id as user_id','users.*', 'users_detail.*'])->toarray();
    }

}
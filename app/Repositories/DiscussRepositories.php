<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class DiscussRepositories
{

    protected $table_name = 'video_discuss';

    /**
     * 添加评论
     * @param $data
     */
    public function InsertDiscuss($data)
    {
        return DB::table($this->table_name)->insertGetId($data);
    }


    public function getDiscussById($id)
    {
        return DB::table($this->table_name)->find($id);
    }

    /**
     * 增加喜欢
     */
    public function IncrDiscussfavorById($id)
    {
        return DB::table($this->table_name)->where('id', '=', $id)->increment('favorite_number');
    }

    /**
     * 增加喜欢
     */
    public function DecrDiscussfavorById($id)
    {
        return DB::table($this->table_name)->where('id', '=', $id)->decrement('favorite_number');
    }

    /**
     * 获取评论列表
     * @param $video_id
     * @return array
     */
    public function getDiscussList($video_id)
    {

        return DB::table($this->table_name)->
        where([['video_id' ,'=',$video_id], ['parent_id' ,'=',0]])->orderBy('discuss_time', 'desc')->paginate(5)->toarray();

    }

    /**
     * 获取子评论
     * @param $video_id
     * @param int $pid
     * @return array
     */
    public function getSubList($video_id, $pid=0){

        $sub_list = DB::select("select * from video_discuss WHERE video_id=$video_id AND parent_id=$pid");
        if(empty($sub_list)){
            return [];
        }

        foreach ($sub_list as $k=>&$v){
            $v->sub = $this->getSubList($video_id, $v->id);
        }

        return $sub_list;
    }
}
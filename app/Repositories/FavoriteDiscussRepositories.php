<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class FavoriteDiscussRepositories
{
    protected static $table_name = "video_discuss_favorite_list";

    public function __construct()
    {
    }

    /**
     *查看是有有喜欢的讨论数据
     * @param $user_id
     * @param $video_id
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function FindFavoriteDiscussData($user_id, $discuss_id)
    {
        $result = DB::table(self::$table_name)->where([['discuss_id', '=', $discuss_id],['user_id', '=', $user_id]])->first();
        return $result;
    }

    /**
     * 删除喜欢视频评论
     * @param $user_id
     * @param $video_id
     * @return int
     */
    public function DeleteFavoriteVideoDiscuss($user_id, $discuss_id)
    {
        return DB::table(self::$table_name)->where([['discuss_id', '=', $discuss_id],['user_id', '=', $user_id]])->delete();
    }

    /**
     * @param $data
     * @return bool
     */
    public function InsertVideoDiscussFavorite($data)
    {
        return DB::table(self::$table_name)->updateOrInsert($data);
    }


    /**
     * @param $user_id
     * @param $video_id
     * @return \Illuminate\Support\Collection
     */
    public function GetUsersFavoriteDiscussData($user_id, $video_id)
    {
        return DB::table(self::$table_name)->where(['user_id'=>$user_id, 'video_id'=>$video_id])->pluck('user_id', 'discuss_id');
    }


}
<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class FavoriteRepositories
{
    protected static $table_name = "video_favorite_list";
    public function __construct()
    {
    }

    public function FindFavoriteRow($user_id, $video_id)
    {
        $result = DB::table(self::$table_name)->where([['video_id', '=', $video_id],['user_id', '=', $user_id]])->first();
        return $result;
    }

    public function DeleteFavoriteVideo($user_id, $video_id)
    {
        return DB::table(self::$table_name)->where([['video_id', '=', $video_id],['user_id', '=', $user_id]])->delete();
    }

/*    public function GetFavoriteVideoList($user_id)
    {
        return DB::table($this->table_name)->leftjoin('video_list', 'video_favorite_list.video_id', '=', 'video_list.id')->
        where('video_favorite_list.user_id', '=', $user_id)->orderby('video_favorite_list.add_time', 'desc')->paginate(6)->toarray();
    }*/
    public function UpdateFavoriteVideo($data, $update_data)
    {


    }
}
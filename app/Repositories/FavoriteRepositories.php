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

    public function UpdateFavoriteVideo($data, $update_data)
    {
        return DB::table(self::$table_name)->updateOrInsert($data, $update_data);

    }
}
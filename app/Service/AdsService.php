<?php
namespace App\Service;
use App\Repositories\AdsRepositories;

class AdsService
{
    protected $adsRepositories;
    public function __construct(AdsRepositories $adsRepositories)
    {
        $this->adsRepositories = $adsRepositories;
    }

    /**
     * 获取广告数据
     * @param $request
     * @return array
     */
    public function GetAdsList($request)
    {
        $condition = [];
        $time = date('Y-m-d');
        $condition[] = ['start_date', '<', $time];
        $condition[] = ['end_date', '>', $time];
        $condition[] = ['status', '=', 1];

        $ads_data = $this->adsRepositories->GetAdsData($condition);

        if(empty($ads_data)){
            return ['code'=>200, 'data'=>[]];
        }

        $data = ['code'=>200];

        foreach($ads_data as $key=>$value){

            $temp_data['id']  = $value->id;
            $temp_data['title']  = $value->title;
            $temp_data['link_url']  = $value->link_url;
            $temp_data['images']  = $value->images;
//            $temp_data['images']  ='http://39.100.242.186/1.png';
            $temp_data['ads_play_second']  = $value->time_out;
            $data['ads_data'][] = $temp_data;
        }

        return $data;
    }
}
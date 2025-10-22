<?php

namespace App\Models;

use App\Services\ApiService;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $table = 'devices';

    protected $fillable = [
        'device_id',
        'name'
    ];

    public static function getDevices()
    {
        $client = new Client();
        $sessionId = ApiService::apiLogin();

        if (!$sessionId) {
            return null;
        }

        $url = 'https://hst-api.wialon.com/wialon/ajax.html?svc=core/search_items&params={"spec":{"itemsType":"avl_unit","propName":"sys_name","propValueMask":"*","sortType":"rel_creation_time"},"force":1,"flags":4611686018427387903,"from":0,"to":0}&sid=' . $sessionId;
        $data = $client->post($url);
        $rows = json_decode($data->getBody()->getContents());

        return isset($rows->items) ? $rows->items : [];
    }
}

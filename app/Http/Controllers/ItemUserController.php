<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Item;

class ItemUserController extends Controller
{
    public function want()
    {
        $itemCode = request()->itemCode;

        // itemCode から商品を検索
        $client = new \RakutenRws_Client();
        $client->setApplicationId(env('RAKUTEN_APPLICATION_ID'));
        $rws_response = $client->execute('IchibaItemSearch', [
            'itemCode' => $itemCode,
        ]);
        $rws_item = $rws_response->getData()['Items'][0]['Item'];

        // Item 保存 or 検索（見つかると作成せずにそのインスタンスを取得する）
        $item = Item::firstOrCreate([
            'code' => $rws_item['itemCode'],
            'name' => $rws_item['itemName'],
            'url' => $rws_item['itemUrl'],
            // 画像の URL の最後に ?_ex=128x128 とついてサイズが決められてしまうので取り除く
            'image_url' => str_replace('?_ex=128x128', '', $rws_item['mediumImageUrls'][0]['imageUrl']),
        ]);

        \Auth::user()->want($item->id);

        return redirect()->back();
    }

    public function dont_want()
    {
        $itemCode = request()->itemCode;

        if (\Auth::user()->is_wanting($itemCode)) {
            $itemId = Item::where('code', $itemCode)->first()->id;
            \Auth::user()->dont_want($itemId);
        }
        return redirect()->back();
    }
    
    public function have()
    {
        $itemCode = request()->itemCode;

        // itemCode から商品を検索
        $client = new \RakutenRws_Client();
        $client->setApplicationId(env('RAKUTEN_APPLICATION_ID'));
        $rws_response = $client->execute('IchibaItemSearch', [
            'itemCode' => $itemCode,
        ]);
        $rws_item = $rws_response->getData()['Items'][0]['Item'];

        // Item 保存 or 検索（見つかると作成せずにそのインスタンスを取得する）
        $item = Item::firstOrCreate([
            'code' => $rws_item['itemCode'],
            'name' => $rws_item['itemName'],
            'url' => $rws_item['itemUrl'],
            // 画像の URL の最後に ?_ex=128x128 とついてサイズが決められてしまうので取り除く
            'image_url' => str_replace('?_ex=128x128', '', $rws_item['mediumImageUrls'][0]['imageUrl']),
        ]);

        \Auth::user()->have($item->id);

        return redirect()->back();
    }

    public function dont_have()
    {
        $itemCode = request()->itemCode;

        if (\Auth::user()->is_having($itemCode)) {
            $itemId = Item::where('code', $itemCode)->first()->id;
            \Auth::user()->dont_have($itemId);
        }
        return redirect()->back();
    }
}

<?php

namespace App;

use App\User;
use App\Notifications\WeGotOne;
use Illuminate\Database\Eloquent\Model;

class StoreRecord extends Model
{
    protected $fillable = [
    	'name',
    	'sale',
    	'available'
    ];

    public static function record($name, $sale, $available)
    {
    	$latest = static::whereName($name)->latest()->first();

        if ($latest && $latest->sale != (int) $sale && $latest->available != (int) $available){

            if ($sale > 0 | $available > 0){
                $user = (new User(['slack_webhook_url' => 'https://hooks.slack.com/services/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']));
                $user->notify(new WeGotOne($name, $sale, $available));
            }

            return static::create(compact('name', 'sale', 'available'));

        }
        
        return static::create(compact('name', 'sale', 'available'));
    }
}

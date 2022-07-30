<?php

namespace App\Providers;

use App\Models\CardNumber;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('phone_number', function($attribute, $value, $parameters)
        {
            $pattern ="/^(9|09)(12|19|35|36|37|38|39|32|21)\d{7}$/";
           return preg_match($pattern,$value);
        });
        Validator::extend('fromCard', function($attribute, $value, $parameters)
        {
            if (!CardNumber::where(['cart_number' => $value])->first()){
                return false;
            }
            return true;
        });
        Validator::extend('toCard', function($attribute, $value, $parameters)
        {
            $card = (string) preg_replace('/\D/','',$value);
            $strlen = strlen($card);
            if($strlen!=16)
                return false;
            if(($strlen<13 or $strlen>19))
                return false;
            if(!in_array($card[0],[2,4,5,6,9]))
                return false;

            for($i=0; $i<$strlen; $i++)
            {
                $res[$i] = $card[$i];
                if(($strlen%2)==($i%2))
                {
                    $res[$i] *= 2;
                    if($res[$i]>9)
                        $res[$i] -= 9;
                }
            }
            return array_sum($res)%10==0?true:false;
        });
    }
}

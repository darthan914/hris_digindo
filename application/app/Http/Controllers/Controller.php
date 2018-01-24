<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\User;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function filter($request, $default = null)
    {
        $return = $default;

        if($request != '')
        {
            if($request == 'all')
            {
                $return = '';
            }
            else
            {
                $return = $request;
            }
        }

        return $return;
    }

    public function strtocolor(string $text, int $lightness = 50)
    {
        $hex = bin2hex(substr($text, 0, 5));
        $hex = substr($hex, 0, 3);

        $hue = hexdec($hex) % 360;

        $lightness = max(0, $lightness);

        $color = 'hsl('.$hue.', 100%, '.$lightness.'%)';

        return $color;
    }

    public function usergrant($id_user, string $permission = NULL)
    {
        $collect = User::where('leader', Auth::id())->get();

        foreach ($collect as $list) {
            $gatherId[] = $list->id;
        }

        $staff = $gatherId ?? [];

        if( $id_user == Auth::id() || in_array($id_user, $staff) || ( $permission != NULL && Auth::user()->can($permission) ) )
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}

<?php

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
});


////{{--ZO--}}
//@if(Entrust::hasRole('administrator') || Entrust::hasRole('owner'))
//                <a href="#data" class="list-group-item" data-toggle="collapse" data-parent="#MainMenu">
//                    <i class="fa fa-upload sidebar-icon"></i>
//                    <span id="menu-txt">{{ __('Data') }}</span>
//                    <i class="icon ion-md-arrow-dropup arrow-side sidebar-arrow"></i>
//                </a>
//                <div class="collapse" id="data">
//                    <a href="{{ route('csv.view')}}"
//                       class="list-group-item childlist">
//                        <i class="bullet-point"><span></span></i> {{ __('Import Data') }}
//                    </a>
//                </div>
//@endif
//<a href="{{route('reset')}}" class="list-group-item" data-parent="#MainMenu"><i
//            class="fa fa-refresh sidebar-icon"></i><span id="menu-txt">{{ __('Reset') }} </span></a>
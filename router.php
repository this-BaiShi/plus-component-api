<?php

use function baishi\Component\ZhiyiPlus\PlusComponentAPI\base_path as component_base_path;

Route::middleware('web')
    ->namespace('baishi\\Component\\ZhiyiPlus\\PlusComponentAPI\\Controllers')
    ->group(component_base_path('/routes/web.php'));

Route::prefix('api/v1.5')
    ->middleware('api')
    ->namespace('baishi\\Component\\ZhiyiPlus\\PlusComponentAPI\\Controllers')
    ->group(component_base_path('/routes/api.php'));

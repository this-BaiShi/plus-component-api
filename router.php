<?php

use function Medz\Component\ZhiyiPlus\PlusComponentExample\base_path as component_base_path;

Route::middleware('web')
    ->namespace('Medz\\Component\\ZhiyiPlus\\PlusComponentExample\\Controllers')
    ->group(component_base_path('/routes/web.php'));

Route::prefix('api/v1')
    ->middleware('api')
    ->namespace('Medz\\Component\\ZhiyiPlus\\PlusComponentExample\\Controllers')
    ->group(component_base_path('/routes/api.php'));

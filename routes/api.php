<?php

Route::any('/component-example', 'ExampleApiController@example');

Route::get('/test',function (){
    return 1;
} );

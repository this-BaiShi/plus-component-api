<?php

namespace this-baishi\Component\ZhiyiPlus\PlusComponentAPI\Controllers;

use Zhiyi\Plus\Http\Controllers\Controller;
use function this-baishi\Component\ZhiyiPlus\PlusComponentAPI\view;

class ExampleWebController extends Controller
{
    public function example()
    {
        return view('example');
    }

    public function admin()
    {
        return view('example');
    }
}

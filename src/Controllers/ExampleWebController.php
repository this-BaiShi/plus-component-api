<?php

namespace baishi\Component\ZhiyiPlus\PlusComponentAPI\Controllers;

use Zhiyi\Plus\Http\Controllers\Controller;
use function baishi\Component\ZhiyiPlus\PlusComponentAPI\view;

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

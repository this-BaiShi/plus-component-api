<?php

namespace Medz\Component\ZhiyiPlus\PlusComponentExample\Controllers;

use Zhiyi\Plus\Http\Controllers\Controller;
use function Medz\Component\ZhiyiPlus\PlusComponentExample\view;

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

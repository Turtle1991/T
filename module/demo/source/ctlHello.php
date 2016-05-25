<?php
/**
 * Created by PhpStorm.
 * User: Turtle
 * Date: 2016/5/25
 * Time: 10:20
 */
namespace Demo;

class ctlHello extends ctlBase
{
    public function funcSay()
    {
        echo "hello " . $_GET['name'];
    }
}
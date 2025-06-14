<?php
namespace app\shop\controller;

use think\Controller;

class StaticResource extends Controller
{
    public function index()
    {
        // 直接返回静态HTML文件
        return $this->fetch('/shop/shop/index2');
    }
} 
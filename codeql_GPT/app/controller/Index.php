<?php

namespace app\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\View;

class Index extends BaseController
{
    public function index()
    {
        $list = Db::table('project')->paginate([
            'list_rows' => 15,
            'var_page' => 'page',
        ]);
        $page = $list->render();
        $data = [
            'list' => $list,
            'page' => $page,
        ];
        return View::fetch('index', $data);
    }
}

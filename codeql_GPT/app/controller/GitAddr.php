<?php

namespace app\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\View;
use think\Request;

class GitAddr extends BaseController
{
    public function index()
    {
        $list = Db::table('git_addr')->paginate([
            'list_rows' => 15,
            'var_page' => 'page',
        ]);
        $page = $list->render();
        $data = ['list' => $list, 'page' => $page];
        return View::fetch('index',$data);
    }

    public function _updateLanguage(Request $request)
    {
        $id="{$request->param('id')}";
        $language="{$request->param('language')}";
        Db::table('git_addr')->where('id',$id)->update(['language'=>$language]);
        return redirect('index');
    }

    public function scan(Request $request){
        $id = $request->param('id');
        $output = runThinkScan($id);
        return $output;
    }

    public function bailian(Request $request){
        $id = $request->param('id');
        $output = runThinkBailian($id);
        return $output;
    }

}
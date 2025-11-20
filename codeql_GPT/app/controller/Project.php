<?php

namespace app\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\View;
use think\Request;

class Project extends BaseController
{
    public function index()
    {
        $list = Db::table('project')->paginate([
            'list_rows' => 15,
            'var_page' => 'page',
        ]);
        $page = $list->render();
        $data = ['list' => $list, 'page' => $page];
        return View::fetch('index',$data);
    }

    public function _add(Request $request)
    {
        $name = $request->param('name');
        $gitAddrs = $request->param('git_addrs');
        $projectId = Db::table('project')->insertGetId([
            'name' => $name,
        ]);
        $gitAddrArr = explode("\n", $gitAddrs);
        $gitIdAddr = "";
        foreach ($gitAddrArr as $gitAddr) {
            $gitAddr = trim($gitAddr);
            Db::table('git_addr')->insert([
                'project_id' => $projectId,
                'addr' => $gitAddr,
            ]);
            $gitAddrId = Db::table('git_addr')->where('addr', $gitAddr)->value('id');
            $gitIdAddr .= "$gitAddrId "."-"." $gitAddr" . PHP_EOL;
        }
        Db::table('project')->where('id', $projectId)->update(['git_id_addr' => $gitIdAddr]);
        return redirect('index');
    }

    public function _del(Request $request)
    {
        $id="{$request->param('id')}";

        $git_addr_list = Db::table('git_addr')->where('project_id',$id)->select()->toArray();
        foreach ($git_addr_list as $gitAddr) {
            $git_addr_id = $gitAddr['id'];
            $codePath = $gitAddr['code_path'];
            $extendPath = root_path() . 'extend' . DIRECTORY_SEPARATOR . 'databases' . DIRECTORY_SEPARATOR . $git_addr_id;
            $resultFile = root_path() . 'extend' . DIRECTORY_SEPARATOR . 'results' . DIRECTORY_SEPARATOR . $git_addr_id . '.json';
            if ($codePath) {
                system("rm -rf " . escapeshellarg($codePath));
            }
            if($extendPath){
                system("rm -rf " . escapeshellarg($extendPath));
            }
            if($resultFile){
                system("rm -rf " . escapeshellarg($resultFile));
            }
        }


        Db::table('project')->where('id',$id)->delete();
        Db::table('git_addr')->where('project_id',$id)->delete();
        Db::table('codeql')->where('project_id',$id)->delete();

        return redirect('index');
    }
}

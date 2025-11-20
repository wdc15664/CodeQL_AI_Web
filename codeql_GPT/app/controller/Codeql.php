<?php

namespace app\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\View;
use think\Request;

class Codeql extends BaseController
{
    public function index()
    {
        // 获取筛选类型（默认全部）
        $filterType = input('get.filter_type', 'all');
        $projectId  = input('get.project_id', '');
        $repoId     = input('get.git_addr_id', '');

        // 基础查询
        $query = Db::table('codeql');

        // 根据过滤类型处理筛选逻辑
        if ($filterType === 'project' && $projectId !== '') {
            $query->where('project_id', $projectId);
        }

        if ($filterType === 'repo' && $repoId !== '') {
            $query->where('git_addr_id', $repoId);
        }

        // 数据分页（保留当前查询条件）
        $list = $query->paginate([
            'list_rows' => 15,
            'var_page'  => 'page',
            'query'     => request()->param(), // 保留 GET 参数用于下次显示
        ]);

        // 分页 HTML
        $page = $list->render();

        // 下拉框：去重的项目ID与仓库ID
        $projectIds = Db::table('codeql')->distinct(true)->column('project_id');
        $repoIds    = Db::table('codeql')->distinct(true)->column('git_addr_id');

        return View::fetch('index', [
            'list'       => $list,
            'page'       => $page,
            'projectIds' => $projectIds,
            'repoIds'    => $repoIds,

            // 在前端显示时需要用到
            'filterType' => $filterType,
            'projectId'  => $projectId,
            'repoId'     => $repoId,
        ]);
    }

    public function detail(Request $request)
    {
        $id = $request->param('id');
        $info = Db::table('codeql')->where('id',$id)->find();
        $info['locations'] = json_decode($info['locations'],true);
        $info['codeFlows'] = json_decode($info['codeFlows'],true);
        $data = ['info' => $info];
        return View::fetch('detail',$data);
    }

    public function loadFileContent(Request $request)
    {
        $aid = $request->param('aid');
        $gitInfo = Db::table('git_addr')->where('id',$aid)->find();
        $fileName = $request->param('fileName');
        $filePath = $gitInfo['code_path'].'/'.$fileName;

        $fileContent = file_get_contents($filePath);
        return $fileContent;
    }

    public function _del(Request $request)
    {
        $id="{$request->param('id')}";
        Db::table('codeql')->where('id',$id)->delete();
        return redirect('index');
    }
}

<?php
declare (strict_types=1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\Db;

class scan extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('app\command\scan')
            ->setDescription('the app\command\scan command')
            ->addArgument('id', Argument::OPTIONAL, '仓库ID');
    }

    protected function execute(Input $input, Output $output)
    {
        $id = $input->getArgument('id');
        $msg = "仓库{$id}扫描任务开始";

        // 1. 从git_addr表里读取数据，使用git命令下载代码到 /data/code 中
        $git = Db::table('git_addr')->where('id', $id)->find();
        $baseDir = '/data/code';
        // 判断目录是否存在，不存在则创建
        if (!is_dir($baseDir)) {
            mkdir($baseDir, 0755, true);
        }
        if ($git['language'] == '') {
            $msg .= "\n 未选择执行语言";
            $output->writeln($msg);
            die();
        } else {
            $addr = trim($git['addr']);
            $prName = md5($addr);
            $projectPath = "{$baseDir}/{$prName}";
            // 如果项目目录已存在则跳过，防止重复clone
            if (!is_dir($projectPath)) {
                // 自动识别地址协议
                if (preg_match('/^(git@|ssh:\/\/)/i', $addr)) {
                    // SSH 协议
                    $cmd = "git clone {$addr} {$projectPath}";
                } elseif (preg_match('/^(git:\/\/)/i', $addr)) {
                    // Git 原生协议
                    $cmd = "git clone {$addr} {$projectPath}";
                } elseif (preg_match('/^(file:\/\/)/i', $addr) || is_dir($addr)) {
                    // 本地仓库或 file 协议
                    $cmd = "git clone {$addr} {$projectPath}";
                } elseif (preg_match('/^rsync:\/\//i', $addr)) {
                    // rsync 镜像仓库
                    $cmd = "rsync -av {$addr} {$projectPath}";
                } else {
                    // 默认按 http/https 处理（保持兼容）
                    $cmd = "git clone {$addr} {$projectPath}";
                }
                system("cd {$baseDir} && {$cmd}");
            }
            // 更新数据库中的 code_path 字段
            Db::table('git_addr')->where('id', $git['id'])->update(['code_path' => $projectPath]);
            $msg .= "\n code_path：{$projectPath}";
        }


        // 2. 从git_addr读取代码仓库列表，然后使用codeql扫描
        $updatedGit = Db::table('git_addr')->where('id', $id)->find();
        // 如果不存在数据库，则创建数据库
        if (!file_exists('extend/databases/' . $updatedGit['id'])) {
            //创建数据库
            $cmd = "codeql database create extend/databases/{$updatedGit['id']} --language={$updatedGit['language']} --source-root {$updatedGit['code_path']}";
            $msg .= "\n 创建数据库：extend/databases/{$updatedGit['id']}";
        } else {
            //选择数据库(默认不需要)
            $cmd = "codeql database finalize extend/databases/{$updatedGit['id']}";
            $msg .= "\n 选择数据库：extend/databases/{$updatedGit['id']}";
        }
        system($cmd);
        // 分析代码，如果已经分析过了则跳过
        if (!file_exists('extend/results/' . $updatedGit['id'] . '.json')) {

            $cmd = "codeql database analyze extend/databases/{$updatedGit['id']} extend/rules/{$updatedGit['language']}/ql/src/Security/ --format=sarifv2.1.0 --output=extend/results/{$updatedGit['id']}.json";
            system($cmd);
            $msg .= "\n 分析代码：extend/databases/{$updatedGit['id']}";
        } else {
            $msg .= "\n 已经分析过了，跳过分析";
        }

        // 3. 读取扫描结果存储至codeql表中
        $result = file_get_contents('extend/results/' . $updatedGit['id'] . '.json');
        $msg .= "\n 扫描结果：extend/results/{$updatedGit['id']}";

        $result = json_decode($result, true);
        if ($result === null) {
            $msg .= "\n JSON解析失败!!!!!!!";
        }
        if (isset($result['runs'][0]['results'])) {
            $result = $result['runs'][0]['results'];
            foreach ($result as $codeqlItem) {
                // 使用foreach遍历$codeqlItem，判断value是否为字符串，如果不是字符串需要转成JSON字符串
                $codeqlItem['rule'] = json_encode($codeqlItem['rule']);
                $codeqlItem['message'] = json_encode($codeqlItem['message']);
                $codeqlItem['locations'] = json_encode($codeqlItem['locations']);
                $codeqlItem['partialFingerprints'] = json_encode($codeqlItem['partialFingerprints']);
                $codeqlItem['codeFlows'] = json_encode($codeqlItem['codeFlows'] ?? []);
                $codeqlItem['relatedLocations'] = json_encode($codeqlItem['relatedLocations'] ?? []);
                $codeqlItem['project_id'] = $updatedGit['project_id'];
                $codeqlItem['git_addr_id'] = $updatedGit['id'];
                // 如果数据库中已经有此条数据则跳过
                $isHas = Db::table('codeql')
                    ->where('rule', $codeqlItem['rule'])
                    ->where('project_id', $updatedGit['project_id'])
                    ->where('git_addr_id', $updatedGit['id'])
                    ->count();
                if (!$isHas) {
                    Db::table('codeql')->strict(false)->insert($codeqlItem);
                }
            }
        }
        $msg .= "\n 扫描任务完成！";
        $output->writeln($msg);

//        {
//            $id = $input->getArgument('id');
////         1. 从git_addr表里读取数据，使用git命令下载代码到 /data/code 中(all -> list bianli)
//        $gitList = Db::table('git_addr') -> whereNull('code_path') -> select() -> toArray();
//            $baseDir = '/data/code';
//            // 判断目录是否存在，不存在则创建
//            if (!is_dir($baseDir)) {
//                mkdir($baseDir, 0755, true);
//            }
//            foreach ($gitList as $item) {
//                if ($item['language'] == '') {
//                    continue;
//                }else{
//                    $prName = md5($item['addr']);
//                    $projectPath = "{$baseDir}/{$prName}";
//                    // 如果项目目录已存在则跳过，防止重复clone
//                    if (!is_dir($projectPath)) {
//                        $cmd = "git clone {$item['addr']} {$projectPath}";
//                        system("cd {$baseDir} && {$cmd}");
//                    }
//                    // 更新数据库中的 code_path 字段
//                    Db::table('git_addr') -> where('id', $item['id']) -> update(['code_path' => $projectPath]);
//                }
//            }
//
//            // 2. 从git_addr读取代码仓库列表，然后使用codeql扫描
//            $codeList = Db::table('git_addr') -> whereNotNull('code_path') -> select() -> toArray();
//            foreach ($codeList as $item) {
//                // 如果不存在数据库，则创建数据库
//                if(!file_exists('extend/databases/'.$item['id'])){
//                    //创建数据库
//                    $cmd = "codeql database create extend/databases/{$item['id']} --language={$item['language']} --source-root {$item['code_path']}";
//                }else{
//                    //选择数据库(默认不需要)
//                    $cmd = "codeql database finalize extend/databases/{$item['id']}";
//                }
//                system($cmd);
//                // 分析代码，如果已经分析过了则跳过
//                if(!file_exists('extend/results/'.$item['id'].'.json')){
//                    $cmd = "codeql database analyze extend/databases/{$item['id']} extend/rules/{$item['language']}/ql/src/Security/ --format=sarifv2.1.0 --output=extend/results/{$item['id']}.json";
//                    system($cmd);
//                }else{
//                    echo "{$item['addr']} 已经分析过了，跳过分析\n";
//                }
//                // 3. 读取扫描结果存储至codeql表中
//                $result = file_get_contents('extend/results/'.$item['id'].'.json');
//                $result = json_decode($result,true);
//                if(isset($result['runs'][0]['results'])){
//                    $result = $result['runs'][0]['results'];
//                    foreach ($result as $codeqlItem){
//                        // 使用foreach遍历$codeqlItem，判断value是否为字符串，如果不是字符串需要转成JSON字符串
//                        $codeqlItem['rule'] = json_encode($codeqlItem['rule']);
//                        $codeqlItem['message'] = json_encode($codeqlItem['message']);
//                        $codeqlItem['locations'] = json_encode($codeqlItem['locations']);
//                        $codeqlItem['partialFingerprints'] = json_encode($codeqlItem['partialFingerprints']);
//                        $codeqlItem['codeFlows'] = json_encode($codeqlItem['codeFlows'] ?? []);
//                        $codeqlItem['relatedLocations'] = json_encode($codeqlItem['relatedLocations'] ?? []);
//                        $codeqlItem['project_id'] = $item['project_id'];
//                        $codeqlItem['git_addr_id'] = $item['id'];
//
//                        // 如果数据库中已经有此条数据则跳过
//                        $isHas = Db::table('codeql')
//                            -> where('rule', $codeqlItem['rule'])
//                            -> where('project_id', $item['project_id'])
//                            -> where('git_addr_id', $item['id'])
//                            -> count();
//                        if(!$isHas){
//                            Db::table('codeql') -> strict(false) -> insert($codeqlItem);
//                        }
//                    }
//                }
//            }
//        }


    }
}

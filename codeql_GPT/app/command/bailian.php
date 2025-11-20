<?php
declare (strict_types=1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\Db;

class bailian extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('app\command\bailian')
            ->setDescription('the app\command\bailian command')
            ->addArgument('id', Argument::OPTIONAL, '仓库ID');
    }

    protected function execute(Input $input, Output $output)
    {
//        $codeqlList = Db::table('codeql')->whereNull('ai_result')->limit(30)->select()->toArray();
        $git_addr_id = $input->getArgument('id');
        $codeqlList = Db::table('codeql')->whereNull('ai_result')->where('git_addr_id',$git_addr_id)->select()->toArray();
        $msg = "仓库{$git_addr_id}AI任务开始\n";

        foreach ($codeqlList as $item) {
            $prompt = $this->createPrompt($item);
            $result = callQwen($prompt);

            // 关键修复：移除 4 字节 UTF-8 字符（Emoji）
            // AI回复的响应中包含Emoji，而数据库中的字符集不支持，导致在写入数据库update会产生报错
            $result = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $result);

            $data = ['prompt' => $prompt, 'ai_result' => $result];
            Db::table('codeql')->where('id', $item['id'])->update($data);
        }
        $msg .= "仓库{$git_addr_id}AI任务结束";
        $output->writeln($msg);
    }

    private function getFileContent($filepath, $lineNumber)
    {
        $fileContent = file_get_contents($filepath);
        $lines = explode("\n", $fileContent);
        return $lines[$lineNumber - 1];
    }

    private function createPrompt($info)
    {
        $gitInfo = Db::table('git_addr')->where('id', $info['git_addr_id'])->find();
        // 将其转为数据可供使用
        $info['locations'] = json_decode($info['locations'], true);
        $info['codeFlows'] = json_decode($info['codeFlows'], true);
        $info['message'] = json_decode($info['message'], true);

        $prompt = "帮我分析一下该代码的漏洞" . PHP_EOL;
        $prompt .= "规则ID：" . $info['ruleId'] . PHP_EOL;
        $prompt .= "Codeql描述：" . $info['message']['text'] . PHP_EOL;
        $prompt .= "locations描述：\n 文件名" . $info['locations'][0]['physicalLocation']['artifactLocation']['uri'];
        $prompt .= " 行号：" . $info['locations'][0]['physicalLocation']['region']['startLine'] . PHP_EOL;
        $filePath = $gitInfo['code_path'] . '/' . $info['locations'][0]['physicalLocation']['artifactLocation']['uri'];
        $prompt .= " 代码内容：" .
            $this->getFileContent($filePath, $info['locations'][0]['physicalLocation']['region']['startLine'])
            . PHP_EOL;

        foreach ($info['codeFlows'] as $key => $item) {
            $locations = $item['threadFlows'][0]['locations'];
            $prompt .= "第{$key}条数据流：" . PHP_EOL;
            foreach ($locations as $k => $val) {
                $prompt .= " 第{$k}次传播：\n  文件名" . $val['location']['physicalLocation']['artifactLocation']['uri'];
                $prompt .= "  行号" . $val['location']['physicalLocation']['region']['startLine'] . PHP_EOL;
                $filePath = $gitInfo['code_path'] . '/' . $val['location']['physicalLocation']['artifactLocation']['uri'];
                $prompt .= "  代码内容：" .
                    $this->getFileContent($filePath, $val['location']['physicalLocation']['region']['startLine'])
                    . PHP_EOL;
            }
        }
        return $prompt;
    }
}

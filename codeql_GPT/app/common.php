<?php
function callQwen($prompt, $model = "qwen-plus") {
    // 确保输入是字符串
    $prompt = (string)$prompt;

    // 从环境变量获取API密钥
    $apiKey = env('BAILIAN_SK');

    if (empty($apiKey)) {
        return "错误：DASHSCOPE_API_KEY 环境变量未设置";
    }

    $url = "https://dashscope.aliyuncs.com/compatible-mode/v1/chat/completions";

    $headers = [
        "Authorization: Bearer " . $apiKey,
        "Content-Type: application/json"
    ];

    $messages = [
        [
            "role" => "system",
            "content" => "你是一个安全漏洞分析专家，请用中文详细分析代码漏洞。"
        ],
        [
            "role" => "user",
            "content" => $prompt
        ]
    ];

    $data = [
        "model" => $model,
        "messages" => $messages
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data, JSON_UNESCAPED_UNICODE),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 45,
        CURLOPT_SSL_VERIFYPEER => true
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    // 处理网络错误
    if ($curlError) {
        return "网络错误 [{$httpCode}]: {$curlError}";
    }

    // 解析响应
    $apiResponse = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return "API响应解析失败: " . json_last_error_msg();
    }

    // 处理API错误
    if ($httpCode >= 400) {
        $errorMsg = $apiResponse['error']['message'] ?? 'API请求失败';
        return "API错误 [{$httpCode}]: {$errorMsg}";
    }

    // 直接返回字符串内容
    return $apiResponse['choices'][0]['message']['content'] ?? "未获取到有效回复";
}

function runThinkScan($id){
    $command = 'php think scan ';
    $command .= $id;
    $output = shell_exec($command);
    return $output;
}

function runThinkBailian($id){
    $command = 'php think bailian ';
    $command .= $id;
    $output = shell_exec($command);
    return $output;
}

?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>漏洞详情</title>
    <link href="/static/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">

    <!-- highlight.js 语法高亮 -->
    <link rel="stylesheet" href="/static/css/github-dark.min.css">
    <script src="/static/js/highlight.min.js"></script>

    <!-- 动态加载语言包 -->
    <script src="/static/js/php.min.js"></script>
    <script src="/static/js/python.min.js"></script>
    <script src="/static/js/java.min.js"></script>
    <script src="/static/js/javascript.min.js"></script>
    <script src="/static/js/cpp.min.js"></script>
    <script src="/static/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <style>
        #contentContainer {
            display: flex;
            flex-direction: column;
            height: 50vh;
            overflow-y: auto;
            background-color: #1e1e1e;
            color: #dcdcdc;
            font-family: Consolas, Monaco, 'Courier New', monospace;
            font-size: 13px;
            border-radius: 6px;
            padding: 10px;
            box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.4);
            position: relative;
        }

        .code-line {
            display: flex;
            align-items: flex-start;
            white-space: pre;
            border-left: 4px solid transparent;
            transition: background 0.2s ease, border-color 0.2s ease;
            position: relative;
            z-index: 1;
        }

        .line-number {
            width: 45px;
            text-align: right;
            padding-right: 10px;
            color: #666;
            user-select: none;
            flex-shrink: 0;
        }

        .line-content {
            flex: 1;
            white-space: pre-wrap;
            word-break: break-word;
            position: relative;
            z-index: 1;
        }

        /* 柔和高亮始终在最上层 */
        .highlightedRow {
            background: rgba(56, 139, 253, 0.25);
            border-left-color: #388bfd;
            box-shadow: 0 0 6px rgba(56, 139, 253, 0.5);
            position: relative;
            z-index: 99;
        }

        .fileList ul li.highlight {
            background-color: #0d6efd !important;
            color: white !important;
        }

        /* 默认收起右侧 */
        #vulnDetail {
            display: none;
        }

        .hljs {
            background: transparent !important;
        }

        .error-fragment {
            background: rgba(255, 77, 77, 0.35);
            border-bottom: 2px solid #ff4d4d;
            border-radius: 2px;
            padding: 1px 0;
        }


        /* 提示信息 */
        #vulnPlaceholder {
            height: 50vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #666;
            font-style: italic;
            font-size: 14px;
            background-color: #f9f9f9;
            border-radius: 6px;
            border: 1px dashed #ccc;
        }

        /* 展开动画 */
        #vulnDetail.show {
            display: block;
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .analysis-section .card {
            transition: all 0.2s ease;
        }

        .analysis-section .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .analysis-section textarea {
            font-size: 13px;
            font-family: Consolas, Monaco, 'Courier New', monospace;
            color: #333;
        }

        /* GitHub风格的Markdown渲染（搭配 highlight.js 非常协调） */
        .markdown-body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
        }

        .markdown-body h3 {
            border-bottom: 1px solid #ddd;
            padding-bottom: 4px;
            margin-top: 16px;
            font-size: 16px;
        }

        .markdown-body pre {
            background: #f6f8fa;
            border-radius: 6px;
            padding: 8px 12px;
            overflow-x: auto;
        }

        .markdown-body code {
            font-family: Consolas, Monaco, monospace;
            background: rgba(27, 31, 35, 0.05);
            padding: 2px 4px;
            border-radius: 4px;
        }

        .markdown-body table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }

        .markdown-body table,
        .markdown-body th,
        .markdown-body td {
            border: 1px solid #ddd;
        }

        .markdown-body th {
            background-color: #f2f2f2;
            text-align: left;
            padding: 6px;
        }

        .markdown-body td {
            padding: 6px;
        }

        .markdown-body blockquote {
            border-left: 4px solid #ccc;
            padding-left: 10px;
            color: #555;
            margin: 8px 0;
        }

    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-1">
            {include file="common/leftNav" /}
        </div>

        <div class="col-11">
            <div class="row">
                <div class="col-4">
                    <div class="fileList">
                        <p><strong>漏洞位置</strong></p>
                        <?php foreach ($info['locations'] as $item) { ?>
                            <ul class="list-group" style="font-size: 12px">
                                <li class="list-group-item"
                                    title="<?php echo $item['physicalLocation']['artifactLocation']['uri'] ?>"
                                    data-file="<?php echo $item['physicalLocation']['artifactLocation']['uri'] ?>"
                                    data-line="<?php echo $item['physicalLocation']['region']['startLine'] ?? 0 ?>"
                                    data-start="<?php echo $item['physicalLocation']['region']['startColumn'] ?? 0 ?>"
                                    data-end="<?php echo $item['physicalLocation']['region']['endColumn'] ?? 0 ?>"
                                    data-aid="<?php echo $info['git_addr_id'] ?>">

                                    <?php echo basename($item['physicalLocation']['artifactLocation']['uri']); ?>
                                    <?php echo $item['physicalLocation']['region']['startLine']; ?>行
                                    <?php
                                    $startCol = $item['physicalLocation']['region']['startColumn'] ?? null;
                                    $endCol = $item['physicalLocation']['region']['endColumn'] ?? null;
                                    if ($startCol && $endCol) {
                                        echo $startCol . "-" . $endCol . "字符";
                                    }
                                    ?>
                                </li>
                            </ul>
                        <?php } ?>
                    </div>

                    <div class="fileList">
                        <p><strong>数据流</strong></p>
                        <div class="accordion" id="accordionExample">
                            <?php foreach ($info['codeFlows'] as $key => $item) { ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseOne<?php echo $key; ?>" aria-expanded="true"
                                                aria-controls="collapseOne<?php echo $key; ?>">
                                            <?php echo "第" . ($key + 1) . "条数据流"; ?>
                                        </button>
                                    </h2>
                                    <div id="collapseOne<?php echo $key; ?>" class="accordion-collapse collapse show"
                                         data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <ul class="list-group" style="font-size: 12px">
                                                <?php foreach ($item['threadFlows'][0]['locations'] as $itemLocation) { ?>
                                                    <li class="list-group-item"
                                                        title="<?php echo $itemLocation['location']['physicalLocation']['artifactLocation']['uri'] ?>"
                                                        data-file="<?php echo $itemLocation['location']['physicalLocation']['artifactLocation']['uri'] ?>"
                                                        data-line="<?php echo $itemLocation['location']['physicalLocation']['region']['startLine'] ?? 0 ?>"
                                                        data-start="<?php echo $itemLocation['location']['physicalLocation']['region']['startColumn'] ?? 0 ?>"
                                                        data-end="<?php echo $itemLocation['location']['physicalLocation']['region']['endColumn'] ?? 0 ?>"
                                                        data-aid="<?php echo $info['git_addr_id'] ?>">
                                                        <?php echo basename($itemLocation['location']['physicalLocation']['artifactLocation']['uri']); ?>
                                                        <?php echo $itemLocation['location']['physicalLocation']['region']['startLine']; ?>
                                                        行
                                                        <?php
                                                        $startColumn = $itemLocation['location']['physicalLocation']['region']['startColumn'] ?? null;
                                                        $endColumn = $itemLocation['location']['physicalLocation']['region']['endColumn'] ?? null;
                                                        if ($startColumn && $endColumn) {
                                                            echo $startColumn . '-' . $endColumn . '字符';
                                                        }
                                                        ?>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="col-8">
                    <div id="vulnPlaceholder">
                        左侧点击漏洞位置/数据流展开具体报错代码
                    </div>

                    <div id="vulnDetail">
                        <p><strong>漏洞详情</strong></p>
                        <div style="background-color: #efefef">
                            <div id="contentContainer"></div>
                        </div>

                        <div class="analysis-section mt-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="card shadow-sm border-0 h-100">
                                        <div class="card-header bg-primary text-white py-2">
                                            <strong>审计关键词 Prompt</strong>
                                        </div>
                                        <div class="card-body p-0">
                    <textarea class="form-control border-0 rounded-0"
                              style="height:200px; resize:none; background-color:#fafafa;"
                              disabled>{$info['prompt']}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card shadow-sm border-0 h-100">
                                        <div class="card-header bg-success text-white py-2">
                                            <strong>AI 分析结果</strong>
                                        </div>
                                        <div class="card-body p-3">
                                            <div id="aiResult" class="markdown-body"
                                                 style="max-height: 300px; overflow-y: auto;">
                                                {$info['ai_result']}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<script src="/static/js/jquery.min.js"></script>
<script src="/static/js/markdown-it.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/markdown-it/dist/markdown-it.min.js"></script>
<script>
    $(document).ready(function () {
        const promptEl = $('textarea[disabled]');
        let text = promptEl.val() || '';
        let lines = text.split('\n');
        lines.shift();  // 删除第一行
        promptEl.val(lines.join('\n'));
        // ==============================
        // 一、左侧点击文件/数据流 -> 显示代码
        // ==============================
        $(document).on('click', '.fileList ul li', function () {
            const file = $(this).data('file');
            const aid = $(this).data('aid');
            const line = parseInt($(this).data('line'), 10);
            const startCol = parseInt($(this).data('start'), 10);
            const endCol = parseInt($(this).data('end'), 10);

            $('.fileList ul li').removeClass('highlight');
            $(this).addClass('highlight');

            // 显示详情区
            $('#vulnPlaceholder').hide();
            $('#vulnDetail').addClass('show');

            $.ajax({
                url: 'loadFileContent',
                type: 'GET',
                data: {fileName: file, aid: aid},
                success: function (data) {
                    const ext = getFileExtension(file);
                    showFileContent(data, line, ext, startCol, endCol);
                    scrollToLine(line);
                },
                error: function () {
                    alert('Error loading file content.');
                }
            });
        });

        // ==============================
        // 二、显示代码 + 高亮错误段
        // ==============================
        function showFileContent(content, highlightedLine, ext, startCol, endCol) {
            content = content.replace(/^\uFEFF/, ''); // 去除BOM
            const lines = content.split('\n');

            // 如果首行是空白则移除
            if (lines[0].trim() === '') {
                lines.shift();
            }

            let contentHtml = '';
            lines.forEach((line, index) => {
                const lineNumber = index + 1;
                const isHighlighted = (lineNumber === highlightedLine);
                contentHtml += `
                    <div class="code-line ${isHighlighted ? 'highlightedRow' : ''}" data-line="${lineNumber}">
                        <span class="line-number">${lineNumber}</span>
                        <span class="line-content"><code class="hljs language-${ext}">${escapeHtml(line)}</code></span>
                    </div>`;
            });

            $('#contentContainer').html(contentHtml);

            // highlight.js 渲染
            $('#contentContainer code').each(function (i, block) {
                hljs.highlightElement(block);
            });

            // 高亮错误片段
            if (highlightedLine && startCol && endCol && endCol > startCol) {
                const $targetLine = $(`#contentContainer .code-line[data-line='${highlightedLine}'] code`);
                let html = $targetLine.html();
                let plain = $('<div>').html(html).text();
                const before = plain.slice(0, startCol - 1);
                const highlightPart = plain.slice(startCol - 1, endCol - 1);
                const after = plain.slice(endCol - 1);

                const rebuilt = escapeHtml(before)
                    + `<span class="error-fragment">${escapeHtml(highlightPart)}</span>`
                    + escapeHtml(after);
                $targetLine.html(rebuilt);
            }
        }

        // ==============================
        // 三、辅助函数
        // ==============================
        function scrollToLine(lineNumber) {
            const $container = $('#contentContainer');
            const $target = $container.find(`.code-line[data-line='${lineNumber}']`);
            if ($target.length) {
                const targetOffset = $target.position().top;
                const scrollTo = targetOffset - ($container.height() / 2);
                $container.animate({scrollTop: scrollTo}, 500);
            }
        }

        function getFileExtension(fileName) {
            const match = fileName.split('.').pop().toLowerCase();
            const map = {
                'php': 'php',
                'py': 'python',
                'java': 'java',
                'js': 'javascript',
                'html': 'html',
                'cpp': 'cpp',
                'c': 'cpp'
            };
            return map[match] || 'plaintext';
        }

        function escapeHtml(text) {
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;");
        }

        // ==============================
        // 四、Markdown 渲染 (AI 分析结果) - 更健壮，带预处理
        // ==============================
        const md = window.markdownit({
            html: true,
            linkify: true,
            typographer: true,
            breaks: true
        });

        // 获取原始文本（含 Markdown）
        let raw = $('#aiResult').text() || '';

        // 规范化换行（把 \r\n 或 \r 统一为 \n）
        raw = raw.replace(/\r\n?/g, '\n');

        // 1) 如果某行末尾紧跟 '---'（没有换行），将其放到单独一行并保证前后空行，防止解析失败
        //    比如 "具体分析如下： ---"  -> "具体分析如下：\n\n---\n"
        raw = raw.replace(/([^\n])\s+---(\s*[\n]|$)/g, function (m, p1) {
            return p1 + '\n\n---\n';
        });

        // 2) 另外也保证单独一行的 --- 前后至少有一个空行（更健壮）
        raw = raw.replace(/\n{0,1}---\n{0,1}/g, '\n\n---\n\n');

        // 3) 去掉连续超过3个空行，避免渲染异常
        raw = raw.replace(/\n{3,}/g, '\n\n');

        // 修剪首尾空白
        raw = raw.trim();

        // 最终渲染（如果为空则不渲染）
        if (raw !== '') {
            $('#aiResult').html(md.render(raw));
        }

    });
</script>

</body>
</html>

<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>仓库管理</title>
    <link href="/static/css/bootstrap.min.css" rel="stylesheet">
    <script src="/static/js/bootstrap.bundle.min.js"></script>
    <script src="/static/js/jquery-3.6.0.min.js"></script>

    <style>
        body {
            background-color: #f4f6f9;
            font-family: "Segoe UI", "PingFang SC", "Microsoft YaHei", sans-serif;
        }

        .sidebar {
            background: #ffffff;
            border-right: 1px solid #e5e5e5;
            min-height: 100vh;
            padding: 20px 10px;
            position: sticky;
            top: 0;
        }

        .content-wrapper {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            padding: 25px;
            margin-top: 20px;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .content-header h4 {
            font-weight: 600;
            color: #333;
        }

        .table {
            border-radius: 8px;
            overflow: visible;
        }

        .table thead {
            background-color: #f8f9fa;
        }

        .table tbody tr:hover {
            background-color: #f1f3f5;
        }

        .table td, .table th {
            vertical-align: middle !important;
        }

        .pagination {
            justify-content: center;
        }

        .dropdown-toggle {
            min-width: 130px;
        }

        .btn-outline-primary {
            transition: all 0.2s ease-in-out;
        }

        .btn-outline-primary:hover {
            background-color: #0d6efd;
            color: #fff;
        }

        .btn-outline-warning:hover {
            background-color: #ffc107;
            color: #fff;
        }

        .btn-outline-info:hover {
            background-color: #0dcaf0;
            color: #fff;
        }

        .dropdown-menu {
            z-index: 4000 !important;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- 左侧导航 -->
        <div class="col-2 sidebar shadow-sm">
            <h5 class="text-center fw-bold mb-4 text-primary">导航菜单</h5>
            {include file="common/leftNav" /}
        </div>

        <!-- 主体内容 -->
        <div class="col-10">
            <div class="content-wrapper">
                <div class="content-header">
                    <h4>仓库列表</h4>
                </div>

                <table class="table table-hover align-middle">
                    <thead class="table-light">
                    <tr>
                        <th>仓库ID</th>
                        <th>项目ID</th>
                        <th>项目地址</th>
                        <th>创建时间</th>
                        <th>语言类型</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($list as $item) { ?>
                        <tr>
                            <td>{$item['id']}</td>
                            <td>{$item['project_id']}</td>
                            <td class="text-break" style="max-width: 250px;">{$item['addr']}</td>
                            <td>{$item['create_time']}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-outline-primary dropdown-toggle btn-sm" type="button"
                                            id="dropdownMenuButton{$item['id']}"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                        {$item.language ?: '选择语言'}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{$item['id']}">
                                        <li><a class="dropdown-item" href="{:URL('_updateLanguage',['id'=>$item['id'],'language'=>'python'])}">python</a></li>
                                        <li><a class="dropdown-item" href="{:URL('_updateLanguage',['id'=>$item['id'],'language'=>'java'])}">java</a></li>
                                        <li><a class="dropdown-item" href="{:URL('_updateLanguage',['id'=>$item['id'],'language'=>'cpp'])}">cpp</a></li>
                                        <li><a class="dropdown-item" href="{:URL('_updateLanguage',['id'=>$item['id'],'language'=>'javascript'])}">javascript</a></li>
                                        <li><a class="dropdown-item" href="{:URL('_updateLanguage',['id'=>$item['id'],'language'=>'go'])}">go</a></li>
                                        <li><a class="dropdown-item" href="{:URL('_updateLanguage',['id'=>$item['id'],'language'=>'ruby'])}">ruby</a></li>
                                        <li><a class="dropdown-item" href="{:URL('_updateLanguage',['id'=>$item['id'],'language'=>'rust'])}">rust</a></li>
                                        <li><a class="dropdown-item" href="{:URL('_updateLanguage',['id'=>$item['id'],'language'=>'misc'])}">misc</a></li>
                                    </ul>
                                </div>
                            </td>
                            <td>
                                <!-- 每行独立 Scan 按钮 -->
                                <a href="{:URL('scan',['id'=>$item['id']])}"
                                   class="btn btn-sm btn-outline-info scan-btn"
                                   data-id="{$item['id']}">
                                    Scan
                                </a>
                                <a href="{:URL('bailian',['id'=>$item['id']])}"
                                   class="btn btn-sm btn-outline-danger bailian-btn"
                                   data-id="{$item['id']}">
                                    AI
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>

                <div class="mt-3">
                    <?php echo $page ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 模态框 -->
<div class="modal fade" id="bailianModal" tabindex="-1" aria-labelledby="bailianModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg rounded-4">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-info" id="bailianModalLabel">BAILIAN 扫描结果</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="关闭"></button>
            </div>
            <div class="modal-body" id="bailianResult" style="font-family: Consolas, monospace; white-space: pre-wrap;">
                正在请求中...
            </div>
        </div>
    </div>
</div>
<!-- 模态框 -->
<div class="modal fade" id="scanModal" tabindex="-1" aria-labelledby="scanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg rounded-4">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-info" id="scanModalLabel">scan 扫描结果</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="关闭"></button>
            </div>
            <div class="modal-body" id="scanResult" style="font-family: Consolas, monospace; white-space: pre-wrap;">
                正在请求中...
            </div>
        </div>
    </div>
</div>
<!-- ---------- JS逻辑 ---------- -->
<script>
    $(function () {
        /* --------------------------- 通用 AJAX 模态框函数 --------------------------- */
        function ajaxToModal(btnSelector, modalId, resultId) {
            $('body').on('click', btnSelector, function (e) {
                e.preventDefault();

                const url = $(this).attr('href');
                $(resultId).text('正在扫描，请稍候...');

                const modal = new bootstrap.Modal(document.getElementById(modalId));
                modal.show();

                $.get(url)
                    .done(function (data) {
                        // 清理 BOM、空行
                        data = data.replace(/^[\s\uFEFF\xA0]+/, '');

                        try {
                            const parsed = JSON.parse(data);
                            $(resultId).html('<pre>' + JSON.stringify(parsed, null, 2) + '</pre>');
                        } catch (e) {
                            $(resultId).text(data);
                        }
                    })
                    .fail(function (xhr) {
                        $(resultId).text('请求失败: ' + xhr.status + ' ' + xhr.statusText);
                    });
            });
        }

        // 绑定 Scan / Bailian 按钮（事件委托，不会重复绑定）
        ajaxToModal('.scan-btn', 'scanModal', '#scanResult');
        ajaxToModal('.bailian-btn', 'bailianModal', '#bailianResult');

    });
</script>

<!-- Dropdown 只初始化一次，不重复 dispose -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.dropdown-toggle').forEach(function (btn) {
            new bootstrap.Dropdown(btn, {
                popperConfig: function (defaultBsPopper) {
                    return Object.assign({}, defaultBsPopper, {
                        strategy: 'fixed',
                        modifiers: [
                            ...(defaultBsPopper?.modifiers || []),
                            { name: 'computeStyles', options: { adaptive: false } },
                            { name: 'preventOverflow', options: { boundary: document.body } },
                            { name: 'offset', options: { offset: [0, 6] } }
                        ]
                    });
                }
            });
        });
    });
</script>

</body>
</html>

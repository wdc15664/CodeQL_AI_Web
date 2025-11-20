<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>漏洞管理</title>
    <link href="/static/css/bootstrap.min.css" rel="stylesheet">
    <script src="/static/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            background-color: #f4f6f9;
            font-family: "Segoe UI", "PingFang SC", "Microsoft YaHei", sans-serif;
        }

        /* 左侧导航栏 */
        .sidebar {
            background: #ffffff;
            border-right: 1px solid #e5e5e5;
            min-height: 100vh;
            padding: 20px 10px;
            position: sticky;
            top: 0;
        }

        /* 内容区域外框 */
        .content-wrapper {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
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
            overflow: hidden;
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

        /* 按钮样式一致 */
        .btn-outline-primary:hover {
            background-color: #0d6efd;
            color: #fff;
        }

        .btn-outline-info:hover {
            background-color: #0dcaf0;
            color: #fff;
        }

        .btn-outline-warning:hover {
            background-color: #ffc107;
            color: #fff;
        }

        .modal-content {
            border-radius: 10px;
        }

        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e5e5e5;
        }

        /* 滑动选择框容器 */
        .filter-segment {
            background: #f8fafc;
            padding: 6px;
            border-radius: 14px;
            justify-content: space-between;
            border: 1px solid #e2e8f0;
        }

        /* 每个按钮样式 */
        .filter-btn {
            flex: 1;
            text-align: center;
            padding: 10px 0;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 500;
            color: #4a5568;
            transition: all .25s ease;
            border: 1px solid transparent;
        }

        /* 选中高亮 */
        .btn-check:checked + .filter-btn {
            background: #0d6efd;
            color: #fff;
            box-shadow: 0 2px 6px rgba(13,110,253,0.25);
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
                <div class="content-wrapper">

                    <!-- 滑动选择栏 -->
                    <div class="d-flex gap-3 mb-3">

                        <!-- 全部 -->
                        <div class="form-check form-check-inline">
                            <input class="btn-check" type="radio" name="filter_type" id="filter_all" value="all"
                                    <?php echo $filterType=='all' ? 'checked' : '';?>>
                            <label class="btn btn-outline-primary" for="filter_all">全部</label>
                        </div>

                        <!-- 项目ID选择 -->
                        <div class="form-check form-check-inline">
                            <input class="btn-check" type="radio" name="filter_type" id="filter_project" value="project"
                                    <?php echo $filterType=='project' ? 'checked' : '';?>>
                            <label class="btn btn-outline-primary" for="filter_project">项目 ID 选择</label>
                        </div>

                        <!-- 仓库ID选择 -->
                        <div class="form-check form-check-inline">
                            <input class="btn-check" type="radio" name="filter_type" id="filter_repo" value="repo"
                                    <?php echo $filterType=='repo' ? 'checked' : '';?>>
                            <label class="btn btn-outline-primary" for="filter_repo">仓库 ID 选择</label>
                        </div>

                    </div>


                    <!-- 下拉框容器（根据选项自动切换显示） -->
                    <form method="get" id="filterForm">

                        <!-- 项目ID下拉 -->
                        <div id="projectSelect" class="mb-3" style="<?php echo $filterType=='project'?'':'display:none'; ?>">
                            <select class="form-select" name="project_id" onchange="document.getElementById('filterForm').submit()">
                                <option value="">项目ID选择</option>
                                <?php foreach ($projectIds as $pid) { ?>
                                    <option value="<?php echo $pid; ?>"
                                            <?php echo $projectId==$pid ? 'selected':''; ?>>
                                        项目ID: <?php echo $pid; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- 仓库ID下拉 -->
                        <div id="repoSelect" class="mb-3" style="<?php echo $filterType=='repo'?'':'display:none'; ?>">
                            <select class="form-select" name="git_addr_id" onchange="document.getElementById('filterForm').submit()">
                                <option value="">仓库ID选择</option>
                                <?php foreach ($repoIds as $rid) { ?>
                                    <option value="<?php echo $rid; ?>"
                                            <?php echo $repoId==$rid ? 'selected':''; ?>>
                                        仓库ID: <?php echo $rid; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                    </form>





                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                        <tr>
                            <th>仓库ID</th>
                            <th>项目ID</th>
                            <th>漏洞编号</th>
                            <th>规则ID</th>
                            <th>AI</th>
                            <th>创建时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $item) { ?>
                            <tr>
                                <td>{$item['git_addr_id']}</td>
                                <td>{$item['project_id']}</td>
                                <td>{$item['id']}</td>
                                <td>{$item['ruleId']}</td>
                                <td><?php echo !empty($item['ai_result']) ? 'Yes' : 'No'; ?></td>
                                <td>{$item['create_time']}</td>
                                <td>
                                    <a href="{:URL('detail',['id'=>$item['id']])}" class="btn btn-sm btn-outline-info">详情</a>
                                    <a href="{:URL('_del',['id'=>$item['id']])}"
                                       class="btn btn-sm btn-outline-warning">删除</a>
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
</body>

<script>
    const form = document.getElementById("filterForm");
    const projectSelect = document.getElementById("projectSelect");
    const repoSelect = document.getElementById("repoSelect");

    // 简单方法：确保 form 中只有 1 个 filter_type
    function setFilterType(val) {
        let hidden = form.querySelector("input[name='filter_type']");
        if (!hidden) {
            hidden = document.createElement("input");
            hidden.type = "hidden";
            hidden.name = "filter_type";
            form.appendChild(hidden);
        }
        hidden.value = val;
    }

    // 顶部三个按钮点击事件
    document.querySelectorAll("input[name='filter_type']").forEach(radio => {
        radio.addEventListener("change", function () {

            // 控制显示
            if (this.value === "project") {
                projectSelect.style.display = "block";
                repoSelect.style.display = "none";
            } else if (this.value === "repo") {
                repoSelect.style.display = "block";
                projectSelect.style.display = "none";
            } else {
                projectSelect.style.display = "none";
                repoSelect.style.display = "none";
            }

            // 强制更新隐藏 filter_type，避免冲突
            setFilterType(this.value);

            // 自动提交
            form.submit();
        });
    });

    // 页面加载时初始化 hidden 值
    window.addEventListener("DOMContentLoaded", () => {
        let selected = document.querySelector("input[name='filter_type']:checked");
        if (selected) {
            setFilterType(selected.value);
        }
    });
</script>








</html>

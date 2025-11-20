<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>项目管理面板</title>
    <link href="/static/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f5f6fa;
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

        .content-area {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            padding: 20px;
            margin-top: 20px;
        }

        .table thead {
            background-color: #f8f9fa;
        }

        .table th, .table td {
            vertical-align: middle !important;
        }

        .table-hover tbody tr:hover {
            background-color: #f1f3f5;
        }
        .content-header h4 {
            font-weight: 600;
            color: #333;
        }
        .pagination {
            justify-content: center;
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

        <!-- 右侧内容区 -->
        <div class="col-md-10 col-lg-10 col-xl-10 py-3">
            <div class="content-area">
                <div class="content-header">
                    <h4>项目概要</h4>
                </div>

                <table class="table table-hover align-middle">
                    <thead class="table-light">
                    <tr>
                        <th>项目ID</th>
                        <th>项目名称</th>
                        <th>仓库编号 - 仓库地址</th>
                        <th>创建时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($list as $item) { ?>
                        <tr>
                            <td>{$item['id']}</td>
                            <td>{$item['name']}</td>
                            <td>{:nl2br($item['git_id_addr'] ?? '')}</td>
                            <td>{$item['create_time']}</td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>

                <!-- 分页 -->
                <div class="mt-3">
                    <?php echo $page ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/static/js/bootstrap.bundle.min.js"></script>
</body>
</html>

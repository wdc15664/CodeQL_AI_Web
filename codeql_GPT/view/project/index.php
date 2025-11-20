<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>项目管理</title>
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

        /* 内容卡片 */
        .content-wrapper {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            padding: 25px;
            margin-top: 20px;
        }

        /* 标题栏 */
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

        /* 表格样式 */
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

        /* 弹窗优化 */
        .modal-content {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .modal-header {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .btn-outline-primary {
            transition: all 0.2s ease-in-out;
        }

        .btn-outline-primary:hover {
            background-color: #0d6efd;
            color: #fff;
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
                    <h4>项目列表</h4>
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        + 添加项目
                    </button>
                </div>

                <table class="table table-hover align-middle">
                    <thead class="table-light">
                    <tr>
                        <th>项目ID</th>
                        <th>项目名称</th>
                        <th>创建时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($list as $item) { ?>
                        <tr>
                            <td>{$item['id']}</td>
                            <td>{$item['name']}</td>
                            <td>{$item['create_time']}</td>
                            <td>
                                <a href="{:URL('_del',['id'=>$item['id']])}" class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('确定要删除此项目吗？')">删除</a>
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

<!-- 添加项目弹窗 -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">添加项目</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="_add">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">项目名称</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Git 仓库地址</label>
                        <textarea class="form-control" name="git_addrs" rows="3" placeholder="支持多行地址输入"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">提交</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>

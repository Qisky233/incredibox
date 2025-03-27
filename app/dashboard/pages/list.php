<?php
// 数据库连接
function getDb() {
    $db = new SQLite3(__DIR__ . '/../../database/db.sqlite'); // 确保路径正确
    return $db;
}

// 获取页面内容
function getPages() {
    $db = getDb();
    $result = $db->query('SELECT * FROM list');
    $pages = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $pages[] = $row;
    }
    $db->close();
    return $pages;
}

// 更新页面内容
function updatePageContent($id, $iframe) {
    $db = getDb();
    $stmt = $db->prepare('UPDATE list SET iframe = :iframe WHERE id = :id');
    $stmt->bindValue(':iframe', $iframe, SQLITE3_TEXT);
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $db->close();
    return $result;
}

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['iframe'])) {
    $id = $_POST['id'];
    $iframe = $_POST['iframe'];
    if (updatePageContent($id, $iframe)) {
        echo "<script>alert('更新成功！'); window.location.href = window.location.href;</script>";
    } else {
        echo "<script>alert('更新失败！');</script>";
    }
}

$pages = getPages();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>游戏管理</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
</head>
<body>
<section class="page-content">
    <h1>游戏管理</h1>
    <div class="container mt-5">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>标题</th>
                    <th>iframe</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pages as $page): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($page['title']); ?></td>
                        <td><?php echo htmlspecialchars($page['iframe']); ?></td>
                        <td><?php echo htmlspecialchars($page['status']); ?></td>
                        <td>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $page['id']; ?>">
                                编辑
                            </button>
                        </td>
                    </tr>
                    <!-- 编辑模态框 -->
                    <div class="modal fade" id="editModal<?php echo $page['id']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">编辑 <?php echo htmlspecialchars($page['title']); ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="">
                                        <input type="hidden" name="id" value="<?php echo $page['id']; ?>">
                                        <!-- <textarea id="editor<?php echo $page['id']; ?>" name="iframe"><?php echo htmlspecialchars($page['iframe']); ?></textarea>
                                        <script>
                                            // 初始化 CKEditor
                                            document.addEventListener('DOMContentLoaded', function() {
                                                ClassicEditor
                                                    .create(document.querySelector('#editor<?php echo $page['id']; ?>'))
                                                    .then(editor => {
                                                        console.log(editor);
                                                    })
                                                    .catch(error => {
                                                        console.error(error);
                                                    });
                                            });
                                        </script> -->
                                        <input type="text" id="iframeInput<?php echo $page['id']; ?>" name="iframe" value="<?php echo htmlspecialchars($page['iframe']); ?>" class="form-control">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                                    <button type="submit" class="btn btn-primary">保存</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
</body>
</html>
<?php
// if (extension_loaded('sqlite3')) {
//     echo "SQLite3 扩展已启用。\n";
//     echo "SQLite3 版本: " . sqlite3_lib_version() . "\n";
// } else {
//     echo "SQLite3 扩展未启用。\n";
// }
?>

<?php
error_reporting(E_ALL); // 开启所有错误报告
ini_set('display_errors', 1); // 显示错误信息

// 数据库连接
function getDb() {
    $db = new SQLite3(__DIR__ . '/database/db.sqlite'); // 确保路径正确
    return $db;
}

// 查询所有数据
$db = getDb();
$stmt = $db->prepare('SELECT * FROM list');
$result = $stmt->execute();
$data = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $data[] = $row;
}

// 打印调试信息
echo '<pre>';
// print_r($data); // 打印查询结果
echo '</pre>';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Incredibox</title>
    <link rel="icon" href="favicon.ico">
</head>
<body>
    <h1>Incredibox Data</h1>
    <table border="1">
        <thead>
            <tr>
                <th>id</th>
                <th>title</th>
                <th>desc</th>
                <th>info</th>
                <th>iframe</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['desc']); ?></td>
                    <td><?php echo htmlspecialchars($row['info']); ?></td>
                    <td><?php echo htmlspecialchars($row['iframe']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
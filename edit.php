<?php
require_once 'db.php';

// 1. Получаем ID товара из URL
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: admin.php");
    exit;
}

// 2. Загружаем текущие данные товара
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    die("Товар не найден!");
}

// 3. Обработка сохранения изменений
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $cat = $_POST['category'];
    $price = $_POST['price'];
    $desc = $_POST['description'];
    $image_url = $_POST['image_url'];

    $sql = "UPDATE products SET name=?, category=?, price=?, description=?, image_url=? WHERE id=?";
    $pdo->prepare($sql)->execute([$name, $cat, $price, $desc, $image_url, $id]);
    
    // После сохранения возвращаемся в админку
    header("Location: admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование: <?= htmlspecialchars($product['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin.php">Админка</a></li>
                    <li class="breadcrumb-item active">Редактирование</li>
                </ol>
            </nav>

            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white p-3">
                    <h4 class="mb-0">Редактировать товар #<?= $id ?></h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Название товара</label>
                            <input type="text" name="name" class="form-control" 
                                   value="<?= htmlspecialchars($product['name']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Категория</label>
                            <input type="text" name="category" class="form-control" 
                                   value="<?= htmlspecialchars($product['category']) ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Цена (₽)</label>
                                <input type="number" name="price" class="form-control" 
                                       value="<?= $product['price'] ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Превью</label>
                                <div class="mt-1">
                                    <img src="<?= $product['image_url'] ?>" id="preview" 
                                         class="rounded border" width="60" height="60" style="object-fit: cover;">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Ссылка на изображение</label>
                            <input type="url" name="image_url" id="img_input" class="form-control" 
                                   value="<?= htmlspecialchars($product['image_url']) ?>">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Описание</label>
                            <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">Сохранить изменения</button>
                            <a href="admin.php" class="btn btn-outline-secondary">Отмена</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Живое обновление превью при вставке ссылки
    document.getElementById('img_input').addEventListener('input', function() {
        document.getElementById('preview').src = this.value;
    });
</script>

</body>
</html>
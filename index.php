<?php 
require_once 'db.php'; 

// Логика фильтрации
$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';

$sql = "SELECT * FROM products WHERE 1=1";
$params = [];

if ($category) { $sql .= " AND category = ?"; $params[] = $category; }
if ($search) { $sql .= " AND name LIKE ?"; $params[] = "%$search%"; }

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query("SELECT DISTINCT category FROM products")->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Мебельный Салон</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">🛋️ MODERN FURNITURE</a>
        <a href="admin.php" class="btn btn-outline-light btn-sm">Панель управления</a>
    </div>
</nav>

<header class="bg-dark text-white py-5 mb-4 shadow-sm" style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://images.unsplash.com/photo-1524758631624-e2822e304c36?auto=format&fit=crop&w=1350&q=80') center/cover;">
    <div class="container text-center py-4">
        <h1 class="display-3 fw-bold">Новая коллекция</h1>
        <p class="lead">Качество и комфорт в каждой детали</p>
    </div>
</header>

<div class="container">
    <!-- Поиск и фильтр -->
    <form class="row g-3 mb-5 p-4 bg-white rounded shadow-sm">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control" placeholder="Поиск по названию..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-4">
            <select name="category" class="form-select">
                <option value="">Все категории</option>
                <?php foreach($categories as $cat): ?>
                    <option value="<?= $cat ?>" <?= $category == $cat ? 'selected' : '' ?>><?= $cat ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">Найти</button>
        </div>
    </form>

    <!-- Сетка товаров -->
    <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
        <?php foreach ($products as $p): ?>
        <div class="col">
            <div class="card h-100 border-0 shadow-sm product-card">
                <!-- Прямой вывод ссылки из БД -->
                <?php $img = !empty($p['image_url']) ? $p['image_url'] : 'https://via.placeholder.com/400x300?text=Нет+фото'; ?>
                <img src="<?= $img ?>" class="card-img-top" alt="<?= htmlspecialchars($p['name']) ?>" style="height: 250px; object-fit: cover;">
                
                <div class="card-body">
                    <span class="badge bg-light text-dark mb-2 border"><?= htmlspecialchars($p['category']) ?></span>
                    <h5 class="card-title fw-bold"><?= htmlspecialchars($p['name']) ?></h5>
                    <p class="card-text text-muted small"><?= mb_strimwidth(htmlspecialchars($p['description']), 0, 100, "...") ?></p>
                </div>
                <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center pb-3">
                    <span class="h5 mb-0 text-primary fw-bold"><?= number_format($p['price'], 0, '.', ' ') ?> ₽</span>
                    <button class="btn btn-dark btn-sm">В корзину</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<footer class="bg-dark text-white py-4 mt-5">
    <div class="container text-center">
        <p>© 2023 Мебельный Салон. Все права защищены.</p>
    </div>
</footer>

</body>
</html>
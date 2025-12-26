<?php 
require_once 'db.php'; 

// Логика фильтрации и сортировки
$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'newest'; // Получаем тип сортировки

// Базовый запрос
$sql = "SELECT * FROM products WHERE 1=1";
$params = [];

// Применяем фильтры
if ($category) { 
    $sql .= " AND category = ?"; 
    $params[] = $category; 
}
if ($search) { 
    $sql .= " AND name LIKE ?"; 
    $params[] = "%$search%"; 
}

// Применяем сортировку (Безопасная подстановка через белый список)
switch ($sort) {
    case 'price_asc':
        $sql .= " ORDER BY price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY price DESC";
        break;
    case 'newest':
    default:
        $sql .= " ORDER BY id DESC";
        break;
}

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
<body class="bg-light">

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
    <form class="row g-3 mb-5 p-4 bg-white rounded shadow-sm" method="GET">
        <div class="col-md-4">
            <label class="form-label small text-muted">Поиск</label>
            <input type="text" name="search" class="form-control" placeholder="Название..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted">Категория</label>
            <select name="category" class="form-select">
                <option value="">Все категории</option>
                <?php foreach($categories as $cat): ?>
                    <option value="<?= $cat ?>" <?= $category == $cat ? 'selected' : '' ?>><?= $cat ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted">Сортировать</label>
            <select name="sort" class="form-select">
                <option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>Сначала новые</option>
                <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Дешевле</option>
                <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Дороже</option>
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Применить</button>
        </div>
    </form>

    <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
        <?php if (empty($products)): ?>
            <div class="col-12 text-center py-5">
                <h3 class="text-muted">Товары не найдены</h3>
                <a href="index.php" class="btn btn-link">Сбросить все фильтры</a>
            </div>
        <?php else: ?>
            <?php foreach ($products as $p): ?>
            <div class="col">
                <div class="card h-100 border-0 shadow-sm product-card">
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
        <?php endif; ?>
    </div>
</div>

<footer class="bg-dark text-white py-4 mt-5">
    <div class="container text-center">
        <p>© 2025 Мебельный Салон. Все права защищены.</p>
    </div>
</footer>

</body>
</html>
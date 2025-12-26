<?php
require_once 'db.php';

// --- ЛОГИКА: УДАЛЕНИЕ ТОВАРА ---
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin.php?msg=deleted");
    exit;
}

// --- ЛОГИКА: ДОБАВЛЕНИЕ ТОВАРА ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $cat = $_POST['category'];
    $price = $_POST['price'];
    $desc = $_POST['description'];
    $image_url = $_POST['image_url'];

    if (!empty($name) && !empty($price)) {
        $sql = "INSERT INTO products (name, category, price, description, image_url) VALUES (?, ?, ?, ?, ?)";
        $pdo->prepare($sql)->execute([$name, $cat, $price, $desc, $image_url]);
        header("Location: admin.php?msg=added");
        exit;
    }
}

// --- ПОЛУЧЕНИЕ СПИСКА ТОВАРОВ ---
$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель | Мебельный салон</title>
    <!-- Подключаем Bootstrap 5 для дизайна -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; }
        .table img { object-fit: cover; border-radius: 5px; shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .card { border: none; border-radius: 12px; }
        .sticky-form { position: sticky; top: 20px; }
    </style>
</head>
<body>

<!-- Навигация -->
<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">🛋️ Панель управления салоном</a>
        <a href="index.php" class="btn btn-outline-light btn-sm">Вернуться на сайт</a>
    </div>
</nav>

<div class="container">
    
    <!-- Уведомления об операциях -->
    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
                if($_GET['msg'] == 'added') echo "Товар успешно добавлен!";
                if($_GET['msg'] == 'deleted') echo "Товар удален из базы.";
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- ЛЕВАЯ КОЛОНКА: ФОРМА ДОБАВЛЕНИЯ -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm sticky-form">
                <div class="card-body">
                    <h5 class="card-title mb-4">Добавить мебель</h5>
                    <form method="POST">
                        <input type="hidden" name="add_product" value="1">
                        
                        <div class="mb-3">
                            <label class="form-label">Название товара</label>
                            <input type="text" name="name" class="form-control" placeholder="Напр: Диван 'Престиж'" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Категория</label>
                            <select name="category" class="form-select">
                                <option value="Гостиная">Гостиная</option>
                                <option value="Спальня">Спальня</option>
                                <option value="Кухня">Кухня</option>
                                <option value="Офис">Офис</option>
                                <option value="Прихожая">Прихожая</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Цена (руб.)</label>
                            <input type="number" name="price" class="form-control" placeholder="50000" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ссылка на фото (URL)</label>
                            <input type="url" name="image_url" class="form-control" placeholder="https://example.com/image.jpg">
                            <div class="form-text small text-muted">Скопируйте адрес изображения из интернета.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Описание</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Краткое описание характеристик..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold">Добавить в каталог</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- ПРАВАЯ КОЛОНКА: ТАБЛИЦА ТОВАРОВ -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">Список товаров (<?= count($products) ?>)</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Превью</th>
                                    <th>Товар</th>
                                    <th>Цена</th>
                                    <th class="text-end">Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($products)): ?>
                                    <tr><td colspan="4" class="text-center py-4 text-muted">В базе пока нет товаров.</td></tr>
                                <?php endif; ?>

                                <?php foreach($products as $p): ?>
                                <tr>
                                    <td>
                                        <img src="<?= !empty($p['image_url']) ? htmlspecialchars($p['image_url']) : 'https://via.placeholder.com/80?text=Нет+фото' ?>" 
                                             alt="img" width="60" height="60">
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($p['name']) ?></div>
                                        <span class="badge bg-secondary opacity-75" style="font-size: 0.7rem;"><?= htmlspecialchars($p['category']) ?></span>
                                    </td>
                                    <td class="text-nowrap fw-bold text-primary">
                                        <?= number_format($p['price'], 0, '.', ' ') ?> ₽
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-outline-primary btn-sm" title="Редактировать">
                                                ✏️
                                            </a>
                                            <a href="?delete=<?= $p['id'] ?>" 
                                               class="btn btn-outline-danger btn-sm" 
                                               onclick="return confirm('Вы действительно хотите удалить этот товар?')" 
                                               title="Удалить">
                                                🗑️
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div> <!-- /table-responsive -->
                </div>
            </div>
        </div>
    </div> <!-- /row -->
</div>

<!-- Скрипты Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
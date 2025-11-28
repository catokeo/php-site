<?php
$sections = [
    [
        'title' => 'Программирование',
        'description' => 'Короткие тесты на знание Python, списков, словарей и алгоритмов с мгновенной проверкой.',
        'link' => 'proga.php',
        'cta' => 'Перейти к тестам'
    ],
    [
        'title' => 'Робототехника',
        'description' => 'Сенсоры, приводы, навигация и безопасность — пять модулей для быстрого повторения.',
        'link' => 'robot.php',
        'cta' => 'Открыть раздел'
    ],
    [
        'title' => 'Компьютерные технологии',
        'description' => 'Изучение современных компьютерных технологий, архитектуры систем и сетевых решений.',
        'link' => 'computers.php',
        'cta' => 'Открыть раздел'
    ],
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Учебная платформа</title>
    <style>
        :root {
            --bg: #f4f6fb;
            --card: #ffffff;
            --accent: #2563eb;
            --muted: #6b7280;
            --text: #111827;
        }
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: var(--bg);
            color: var(--text);
        }
        header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
        }
        .hero {
            max-width: 960px;
            margin: 0 auto;
            padding: 48px 20px;
            text-align: center;
        }
        .hero h1 {
            margin: 0 0 12px;
            font-size: 36px;
        }
        .hero p {
            margin: 0 auto;
            max-width: 680px;
            color: var(--muted);
            line-height: 1.6;
        }
        .hero-actions {
            margin-top: 24px;
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .btn {
            padding: 10px 22px;
            border-radius: 999px;
            border: 1px solid transparent;
            text-decoration: none;
            font-weight: 600;
            color: white;
            background: var(--accent);
        }
        .btn.secondary {
            background: transparent;
            color: var(--accent);
            border-color: var(--accent);
        }
        main {
            max-width: 1100px;
            margin: 30px auto 60px;
            padding: 0 20px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
        }
        .card {
            background: var(--card);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .card h2 {
            margin: 0;
            font-size: 20px;
        }
        .card p {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
            flex: 1;
        }
        .card a {
            align-self: flex-start;
            padding: 8px 16px;
            border-radius: 999px;
            background: var(--accent);
            color: white;
            font-weight: 600;
            text-decoration: none;
        }
        footer {
            text-align: center;
            padding: 30px 20px 60px;
            color: var(--muted);
            font-size: 14px;
        }
        .admin-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #6b7280;
            color: white;
            padding: 10px 16px;
            border-radius: 999px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: background 0.2s ease;
            z-index: 1000;
        }
        .admin-btn:hover {
            background: #4b5563;
        }
    </style>
</head>
<body>
    <header>
        <div class="hero">
            <h1>Онлайн-тесты для подготовки</h1>
            <p>Выберите направление и тренируйтесь: программирование, робототехника или оставьте заявку на новые темы. 
               Все результаты сохраняются на соответствующих страницах.</p>
            <div class="hero-actions">
                <a class="btn" href="proga.php">К программированию</a>
                <a class="btn secondary" href="robot.php">К робототехнике</a>
                <a class="btn secondary" href="computers.php">Компьютерные технологии</a>
            </div>
        </div>
    </header>
    <main>
        <div class="grid">
            <?php foreach ($sections as $section): ?>
                <article class="card">
                    <h2><?= htmlspecialchars($section['title'], ENT_QUOTES) ?></h2>
                    <p><?= htmlspecialchars($section['description'], ENT_QUOTES) ?></p>
                    <a href="<?= htmlspecialchars($section['link'], ENT_QUOTES) ?>">
                        <?= htmlspecialchars($section['cta'], ENT_QUOTES) ?>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </main>
    <footer>
        © <?= date('Y') ?> Учебная платформа. Все направления в одном месте.
    </footer>
    <a href="admin.php" class="admin-btn">⚙️ Админ</a>
</body>
</html>
<?php
session_start();

$tests = require __DIR__ . '/tests_data.php';
$programmingTests = array_filter(
    $tests,
    static fn ($test) => ($test['topic'] ?? '') === 'Программирование'
);
$latestResult = $_SESSION['test_result'] ?? null;
$history = $_SESSION['test_history'] ?? [];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Программирование</title>
    <style>
        :root {
            --bg: #f4f6fb;
            --card: #ffffff;
            --accent: #2563eb;
            --text: #1f2937;
            --muted: #6b7280;
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
            padding: 40px 20px;
        }
        .hero h1 {
            margin: 0 0 12px;
            font-size: 32px;
        }
        .hero p {
            margin: 0;
            color: var(--muted);
        }
        .hero-actions {
            margin-top: 24px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 999px;
            border: 1px solid transparent;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
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
            margin: 30px auto;
            padding: 0 20px 60px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }
        section {
            background: var(--card);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
        }
        section h2 {
            margin-top: 0;
            margin-bottom: 12px;
            font-size: 20px;
        }
        section p {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
        }
        .result-box {
            border: 1px solid #e0e7ff;
            border-radius: 14px;
            padding: 18px;
            background: #eff4ff;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .result-score {
            font-size: 30px;
            color: var(--accent);
        }
        .test-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
            margin-top: 12px;
        }
        .test-card {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .test-card h3 {
            margin: 0;
            font-size: 18px;
        }
        .test-card p {
            margin: 0;
            color: var(--muted);
            flex: 1;
        }
        .history-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
            gap: 8px;
            flex-wrap: wrap;
        }
        .history-item:last-child {
            border-bottom: none;
        }
        .history-item span {
            color: var(--muted);
        }
        footer {
            text-align: center;
            padding: 30px 20px 60px;
            color: var(--muted);
            font-size: 14px;
        }
        footer a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <header>
        <div class="hero">
            <h1>Тесты по программированию на Python</h1>
            <p>Пять модулей для проверки знаний Python: основы, списки, функции, строки и ООП.</p>
            <div class="hero-actions">
                <a class="btn" href="#tests">Перейти к тестам</a>
                <a class="btn secondary" href="#results">История</a>
                <a class="btn secondary" href="index.php">На главную</a>
            </div>
        </div>
    </header>

    <main>
        <section id="highlight">
            <h2>Последняя попытка</h2>
            <?php if ($latestResult && ($tests[$latestResult['test_id']] ?? null) && ($tests[$latestResult['test_id']]['topic'] ?? '') === 'Программирование'): ?>
                <div class="result-box">
                    <strong><?= htmlspecialchars($latestResult['test_title'], ENT_QUOTES) ?></strong>
                    <span class="result-score"><?= htmlspecialchars($latestResult['percentage'], ENT_QUOTES) ?>%</span>
                    <span><?= htmlspecialchars($latestResult['score'], ENT_QUOTES) ?>/<?= htmlspecialchars($latestResult['total'], ENT_QUOTES) ?> баллов</span>
                    <a class="btn" href="test_page.php?test_id=<?= urlencode($latestResult['test_id']) ?>&from=proga.php">Пройти снова</a>
                </div>
            <?php else: ?>
                <p>Пока ещё нет результатов. Выберите модуль ниже и начните.</p>
            <?php endif; ?>
        </section>

        <section id="tests">
            <h2>Доступные модули</h2>
            <?php if (!empty($programmingTests)): ?>
                <div class="test-grid">
                    <?php foreach ($programmingTests as $testId => $test): ?>
                        <div class="test-card">
                            <h3><?= htmlspecialchars($test['title'], ENT_QUOTES) ?></h3>
                            <span><?= count($test['questions']) ?> вопроса • <?= htmlspecialchars($test['topic'], ENT_QUOTES) ?></span>
                            <p><?= htmlspecialchars($test['description'], ENT_QUOTES) ?></p>
                            <a class="btn" href="test_page.php?test_id=<?= urlencode($testId) ?>&from=proga.php">Начать</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>Тесты для программирования пока не добавлены.</p>
            <?php endif; ?>
        </section>

        <section id="results">
            <h2>История</h2>
            <?php
            $programmingHistory = array_filter(
                array_reverse($history),
                static function ($entry) use ($tests) {
                    $testId = $entry['test_id'] ?? '';
                    return isset($tests[$testId]) && ($tests[$testId]['topic'] ?? '') === 'Программирование';
                }
            );
            ?>
            <?php if (!empty($programmingHistory)): ?>
                <?php foreach ($programmingHistory as $item): ?>
                    <div class="history-item">
                        <strong><?= htmlspecialchars($item['test_title'], ENT_QUOTES) ?></strong>
                        <span><?= htmlspecialchars($item['score'], ENT_QUOTES) ?>/<?= htmlspecialchars($item['total'], ENT_QUOTES) ?> • <?= htmlspecialchars($item['percentage'], ENT_QUOTES) ?>%</span>
                        <span><?= htmlspecialchars($item['completed_at'], ENT_QUOTES) ?></span>
                        <a class="btn secondary" href="test_page.php?test_id=<?= urlencode($item['test_id']) ?>&from=proga.php">Повторить</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>История появится после первого прохождения теста.</p>
            <?php endif; ?>
        </section>
    </main>

</body>
</html>

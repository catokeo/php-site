<?php
session_start();

// Простая авторизация (в продакшене использовать более безопасный метод)
$admin_password = 'admin123'; // Пароль по умолчанию
$is_authenticated = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    if ($_POST['password'] === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        $is_authenticated = true;
    } else {
        $error = 'Неверный пароль';
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

if (!$is_authenticated) {
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Вход в админ-панель</title>
        <style>
            body {
                margin: 0;
                font-family: 'Inter', sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .login-box {
                background: white;
                padding: 40px;
                border-radius: 16px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                max-width: 400px;
                width: 100%;
            }
            .login-box h1 {
                margin: 0 0 24px;
                color: #1f2937;
            }
            .login-box input {
                width: 100%;
                padding: 12px;
                margin: 12px 0;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                font-size: 16px;
            }
            .login-box button {
                width: 100%;
                padding: 12px;
                background: #2563eb;
                color: white;
                border: none;
                border-radius: 8px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
            }
            .error {
                color: #dc2626;
                margin-bottom: 12px;
            }
        </style>
    </head>
    <body>
        <div class="login-box">
            <h1>Вход в админ-панель</h1>
            <?php if (isset($error)): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="POST">
                <input type="password" name="password" placeholder="Пароль" required>
                <button type="submit" name="login">Войти</button>
            </form>
            <p style="margin-top: 20px; color: #6b7280; font-size: 14px;">
                Пароль по умолчанию: <code>admin123</code>
            </p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Загрузка тестов
$tests_file = __DIR__ . '/tests_data.php';
$tests = file_exists($tests_file) ? require $tests_file : [];
$availableTopics = [
    'Программирование',
    'Робототехника',
    'Компьютерные технологии',
    'Алгоритмы',
    'Математические основы',
    'Другое'
];
$selectedTopic = $_GET['filter'] ?? 'all';

// Обработка сохранения теста
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_test'])) {
    $testId = $_POST['test_id'] ?? 'new_test_' . time();
    $testData = [
        'title' => $_POST['title'] ?? '',
        'topic' => $_POST['topic'] ?? 'Программирование',
        'description' => $_POST['description'] ?? '',
        'questions' => []
    ];
    
    // Обработка вопросов
    if (isset($_POST['questions']) && is_array($_POST['questions'])) {
        foreach ($_POST['questions'] as $q) {
            if (!empty($q['text'])) {
                $testData['questions'][] = [
                    'text' => $q['text'],
                    'options' => array_filter($q['options'] ?? []),
                    'correct' => (int)($q['correct'] ?? 0),
                    'explanation' => $q['explanation'] ?? ''
                ];
            }
        }
    }
    
    $tests[$testId] = $testData;
    
    // Сохранение в файл
    $content = "<?php\n\nreturn [\n";
    foreach ($tests as $id => $test) {
        $content .= "    '" . addslashes($id) . "' => [\n";
        $content .= "        'title' => " . var_export($test['title'], true) . ",\n";
        $content .= "        'topic' => " . var_export($test['topic'], true) . ",\n";
        $content .= "        'description' => " . var_export($test['description'], true) . ",\n";
        $content .= "        'questions' => [\n";
        foreach ($test['questions'] as $q) {
            $content .= "            [\n";
            $content .= "                'text' => " . var_export($q['text'], true) . ",\n";
            $content .= "                'options' => " . var_export($q['options'], true) . ",\n";
            $content .= "                'correct' => " . $q['correct'] . ",\n";
            $content .= "                'explanation' => " . var_export($q['explanation'], true) . ",\n";
            $content .= "            ],\n";
        }
        $content .= "        ],\n";
        $content .= "    ],\n";
    }
    $content .= "];\n";
    
    file_put_contents($tests_file, $content);
    $success = 'Тест успешно сохранён!';
}

// Удаление теста
if (isset($_GET['delete']) && isset($tests[$_GET['delete']])) {
    unset($tests[$_GET['delete']]);
    // Пересохранить файл
    $content = "<?php\n\nreturn [\n";
    foreach ($tests as $id => $test) {
        $content .= "    '" . addslashes($id) . "' => [\n";
        $content .= "        'title' => " . var_export($test['title'], true) . ",\n";
        $content .= "        'topic' => " . var_export($test['topic'], true) . ",\n";
        $content .= "        'description' => " . var_export($test['description'], true) . ",\n";
        $content .= "        'questions' => [\n";
        foreach ($test['questions'] as $q) {
            $content .= "            [\n";
            $content .= "                'text' => " . var_export($q['text'], true) . ",\n";
            $content .= "                'options' => " . var_export($q['options'], true) . ",\n";
            $content .= "                'correct' => " . $q['correct'] . ",\n";
            $content .= "                'explanation' => " . var_export($q['explanation'], true) . ",\n";
            $content .= "            ],\n";
        }
        $content .= "        ],\n";
        $content .= "    ],\n";
    }
    $content .= "];\n";
    file_put_contents($tests_file, $content);
    header('Location: admin.php');
    exit;
}

$editing_test = isset($_GET['edit']) ? $tests[$_GET['edit']] ?? null : null;
$editing_id = $_GET['edit'] ?? null;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: #f4f6fb;
            color: #1f2937;
        }
        .header {
            background: #2563eb;
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
        }
        .header a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 6px;
        }
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .tabs {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }
        .tab {
            padding: 12px 24px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            color: #1f2937;
        }
        .tab.active {
            background: #2563eb;
            color: white;
            border-color: #2563eb;
        }
        .card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 24px;
        }
        .card h2 {
            margin-top: 0;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            font-size: 14px;
        }
        .form-group textarea {
            min-height: 100px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: #2563eb;
            color: white;
        }
        .btn-danger {
            background: #dc2626;
            color: white;
        }
        .btn-success {
            background: #10b981;
            color: white;
        }
        .tests-list {
            display: grid;
            gap: 16px;
        }
        .test-item {
            padding: 16px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .question-item {
            background: #f8fafc;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 16px;
        }
        .success {
            background: #d1fae5;
            color: #065f46;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Админ-панель</h1>
        <div>
            <a href="main.php">На главную</a>
            <a href="?logout=1" style="margin-left: 12px;">Выйти</a>
        </div>
    </div>
    
    <div class="container">
        <?php if (isset($success)): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <div class="tabs">
            <a href="?tab=tests" class="tab <?= (!isset($_GET['tab']) || $_GET['tab'] === 'tests') ? 'active' : '' ?>">Тесты</a>
            <a href="?tab=theory" class="tab <?= (isset($_GET['tab']) && $_GET['tab'] === 'theory') ? 'active' : '' ?>">Теория</a>
        </div>
        
        <?php if (!isset($_GET['tab']) || $_GET['tab'] === 'tests'): ?>
            <?php if ($editing_test): ?>
                <div class="card">
                    <h2>Редактирование теста</h2>
                    <form method="POST">
                        <input type="hidden" name="test_id" value="<?= htmlspecialchars($editing_id) ?>">
                        <div class="form-group">
                            <label>Название теста</label>
                            <input type="text" name="title" value="<?= htmlspecialchars($editing_test['title']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Тема</label>
                            <select name="topic" required>
                                <?php foreach ($availableTopics as $topic): ?>
                                    <option value="<?= htmlspecialchars($topic) ?>" <?= $editing_test['topic'] === $topic ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($topic) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Описание</label>
                            <textarea name="description" required><?= htmlspecialchars($editing_test['description']) ?></textarea>
                        </div>
                        <div id="questions-container">
                            <?php foreach ($editing_test['questions'] as $idx => $q): ?>
                                <div class="question-item">
                                    <h3>Вопрос <?= $idx + 1 ?></h3>
                                    <div class="form-group">
                                        <label>Текст вопроса</label>
                                        <textarea name="questions[<?= $idx ?>][text]" required><?= htmlspecialchars($q['text']) ?></textarea>
                                    </div>
                                    <?php for ($i = 0; $i < 4; $i++): ?>
                                        <div class="form-group">
                                            <label>
                                                <input type="radio" name="questions[<?= $idx ?>][correct]" value="<?= $i ?>" <?= $q['correct'] == $i ? 'checked' : '' ?> required>
                                                Вариант <?= $i + 1 ?>
                                            </label>
                                            <input type="text" name="questions[<?= $idx ?>][options][<?= $i ?>]" value="<?= htmlspecialchars($q['options'][$i] ?? '') ?>" required>
                                        </div>
                                    <?php endfor; ?>
                                    <div class="form-group">
                                        <label>Объяснение</label>
                                        <textarea name="questions[<?= $idx ?>][explanation]"><?= htmlspecialchars($q['explanation']) ?></textarea>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="addQuestion()">Добавить вопрос</button>
                        <button type="submit" name="save_test" class="btn btn-success">Сохранить тест</button>
                        <a href="admin.php" class="btn">Отмена</a>
                    </form>
                </div>
            <?php else: ?>
                <div class="card">
                    <h2>Список тестов</h2>
                    <a href="?new=1" class="btn btn-primary">Создать новый тест</a>
                    <?php
                        $testsForListing = $tests;
                        if ($selectedTopic !== 'all') {
                            $testsForListing = array_filter(
                                $tests,
                                static fn ($test) => ($test['topic'] ?? '') === $selectedTopic
                            );
                        }
                        $groupedTests = [];
                        foreach ($testsForListing as $id => $test) {
                            $topic = $test['topic'] ?? 'Без темы';
                            $groupedTests[$topic][] = ['id' => $id, 'data' => $test];
                        }
                    ?>
                    <div style="margin:16px 0; display:flex; flex-wrap:wrap; gap:8px;">
                        <a class="tab <?= $selectedTopic === 'all' ? 'active' : '' ?>" href="?tab=tests&filter=all">Все темы</a>
                        <?php foreach ($availableTopics as $topic): ?>
                            <a class="tab <?= $selectedTopic === $topic ? 'active' : '' ?>" href="?tab=tests&filter=<?= urlencode($topic) ?>">
                                <?= htmlspecialchars($topic) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <div class="tests-list" style="margin-top: 20px;">
                        <?php if (empty($groupedTests)): ?>
                            <p>Тестов для выбранной темы пока нет.</p>
                        <?php endif; ?>
                        <?php foreach ($groupedTests as $topic => $items): ?>
                            <h3 style="margin-bottom: 12px;"><?= htmlspecialchars($topic) ?></h3>
                            <?php foreach ($items as $item): ?>
                                <div class="test-item">
                                    <div>
                                        <strong><?= htmlspecialchars($item['data']['title']) ?></strong>
                                        <div style="color: #6b7280; font-size: 14px; margin-top: 4px;">
                                            <?= count($item['data']['questions']) ?> вопросов
                                        </div>
                                    </div>
                                    <div>
                                        <a href="?edit=<?= urlencode($item['id']) ?>" class="btn btn-primary">Редактировать</a>
                                        <a href="?delete=<?= urlencode($item['id']) ?>" class="btn btn-danger" onclick="return confirm('Удалить тест?')">Удалить</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <?php if (isset($_GET['new'])): ?>
                    <div class="card">
                        <h2>Создание нового теста</h2>
                        <form method="POST">
                            <input type="hidden" name="test_id" value="new_test_<?= time() ?>">
                            <div class="form-group">
                                <label>Название теста</label>
                                <input type="text" name="title" required>
                            </div>
                            <div class="form-group">
                                <label>Тема</label>
                                <select name="topic" required>
                                    <?php foreach ($availableTopics as $topic): ?>
                                        <option value="<?= htmlspecialchars($topic) ?>" <?= $topic === 'Программирование' ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($topic) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Описание</label>
                                <textarea name="description" required></textarea>
                            </div>
                            <div id="questions-container"></div>
                            <button type="button" class="btn btn-primary" onclick="addQuestion()">Добавить вопрос</button>
                            <button type="submit" name="save_test" class="btn btn-success">Сохранить тест</button>
                            <a href="admin.php" class="btn">Отмена</a>
                        </form>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        <?php else: ?>
            <div class="card">
                <h2>Управление теоретическим материалом</h2>
                <p>Теоретический материал редактируется в файле <code>test_page.php</code> в массиве <code>$topicInfo</code>.</p>
                <p>Для каждого теста можно добавить раздел <code>'theory'</code> с HTML-разметкой теоретического материала.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        let questionCount = <?= isset($editing_test) ? count($editing_test['questions']) : 0 ?>;
        
        function addQuestion() {
            const container = document.getElementById('questions-container');
            const questionDiv = document.createElement('div');
            questionDiv.className = 'question-item';
            questionDiv.innerHTML = `
                <h3>Вопрос ${questionCount + 1}</h3>
                <div class="form-group">
                    <label>Текст вопроса</label>
                    <textarea name="questions[${questionCount}][text]" required></textarea>
                </div>
                ${[0,1,2,3].map(i => `
                    <div class="form-group">
                        <label>
                            <input type="radio" name="questions[${questionCount}][correct]" value="${i}" required>
                            Вариант ${i + 1}
                        </label>
                        <input type="text" name="questions[${questionCount}][options][${i}]" required>
                    </div>
                `).join('')}
                <div class="form-group">
                    <label>Объяснение</label>
                    <textarea name="questions[${questionCount}][explanation]"></textarea>
                </div>
            `;
            container.appendChild(questionDiv);
            questionCount++;
        }
    </script>
</body>
</html>


<?php
session_start();

$tests = require __DIR__ . '/tests_data.php';

$testId = $_GET['test_id'] ?? $_POST['test_id'] ?? null;
$returnPage = $_GET['from'] ?? $_POST['return_to'] ?? 'proga.php';
$allowedPages = ['proga.php', 'robot.php', 'computers.php', 'index.php'];
if (!in_array($returnPage, $allowedPages, true)) {
    $returnPage = 'proga.php';
}

if (!$testId || !isset($tests[$testId])) {
    header('Location: proga.php');
    exit;
}

$test = $tests[$testId];
$questions = $test['questions'];
$startTest = isset($_GET['start']) && $_GET['start'] === '1';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_test'])) {
    $answers = $_POST['answers'] ?? [];
    $score = 0;
    $details = [];

    foreach ($questions as $index => $question) {
        $userAnswer = isset($answers[$index]) ? (int) $answers[$index] : null;
        $isCorrect = $userAnswer === $question['correct'];
        if ($isCorrect) {
            $score++;
        }

        $details[] = [
            'question' => $question['text'],
            'selected' => $userAnswer,
            'correct' => $question['correct'],
            'options' => $question['options'],
            'explanation' => $question['explanation'],
            'is_correct' => $isCorrect,
        ];
    }

    $total = count($questions);
    $percentage = $total > 0 ? round(($score / $total) * 100) : 0;

    $resultPayload = [
        'test_id' => $testId,
        'test_title' => $test['title'],
        'score' => $score,
        'total' => $total,
        'percentage' => $percentage,
        'details' => $details,
    ];

    $_SESSION['test_result'] = $resultPayload;

    if (!isset($_SESSION['test_history']) || !is_array($_SESSION['test_history'])) {
        $_SESSION['test_history'] = [];
    }

    $_SESSION['test_history'][] = array_merge($resultPayload, [
        'completed_at' => date('d.m.Y H:i'),
    ]);

    header('Location: ' . $returnPage . '?result=1');
    exit;
}

// Информация о темах для вводной страницы
$topicInfo = [
    'python_basics' => [
        'intro' => 'Этот тест проверяет ваши базовые знания Python. Вы изучите основные операторы, типы данных, работу с переменными и простейшие операции. Тест поможет закрепить фундаментальные понятия языка Python.',
        'topics' => ['Синтаксис Python', 'Типы данных', 'Операторы вывода', 'Списки и словари', 'Функции'],
        'theory' => '
            <h3>Основы Python</h3>
            <p><strong>Вывод данных:</strong> Для вывода текста в консоль используется функция <code>print()</code>:</p>
            <pre>print("Hello, World!")</pre>
            
            <p><strong>Списки:</strong> Список создаётся с помощью квадратных скобок:</p>
            <pre>my_list = [1, 2, 3, "hello"]
empty_list = []</pre>
            
            <p><strong>Длина:</strong> Функция <code>len()</code> возвращает количество элементов:</p>
            <pre>length = len([1, 2, 3])  # вернёт 3</pre>
            
            <p><strong>Словари:</strong> Словарь хранит пары ключ-значение:</p>
            <pre>my_dict = {"name": "Python", "version": 3.9}
value = my_dict["name"]  # "Python"</pre>
            
            <p><strong>Функции:</strong> Функции объявляются с помощью <code>def</code>:</p>
            <pre>def greet(name):
    return f"Hello, {name}!"</pre>
        '
    ],
    'python_lists' => [
        'intro' => 'Тест посвящён работе со списками и циклами в Python. Вы проверите знания методов списков, срезов, циклов for и while, а также list comprehensions. Эти навыки необходимы для эффективной работы с данными.',
        'topics' => ['Методы списков', 'Срезы (slicing)', 'Циклы for и while', 'List comprehensions', 'Обработка списков'],
        'theory' => '
            <h3>Работа со списками</h3>
            <p><strong>Добавление элементов:</strong> Метод <code>append()</code> добавляет элемент в конец:</p>
            <pre>my_list = [1, 2, 3]
my_list.append(4)  # [1, 2, 3, 4]</pre>
            
            <p><strong>Срезы:</strong> Синтаксис <code>list[start:end]</code> создаёт срез (end не включается):</p>
            <pre>numbers = [0, 1, 2, 3, 4, 5]
slice = numbers[2:5]  # [2, 3, 4]</pre>
            
            <p><strong>Циклы:</strong> Цикл <code>for</code> перебирает элементы:</p>
            <pre>for item in [1, 2, 3]:
    print(item)</pre>
            
            <p><strong>Удаление:</strong> Метод <code>remove()</code> удаляет первое вхождение значения:</p>
            <pre>my_list = [1, 2, 3, 2]
my_list.remove(2)  # [1, 3, 2]</pre>
            
            <p><strong>List comprehensions:</strong> Создание списков в одну строку:</p>
            <pre>squares = [x**2 for x in range(5)]  # [0, 1, 4, 9, 16]</pre>
        '
    ],
    'python_functions' => [
        'intro' => 'Этот модуль проверяет понимание функций в Python. Вы изучите создание функций, работу с параметрами, возврат значений, lambda-функции и работу с модулями. Функции — основа структурированного программирования.',
        'topics' => ['Определение функций', 'Параметры и аргументы', 'Return и возврат значений', 'Lambda-функции', 'Импорт модулей'],
        'theory' => '
            <h3>Функции в Python</h3>
            <p><strong>Определение функции:</strong> Функции создаются с помощью <code>def</code>:</p>
            <pre>def add(a, b):
    return a + b</pre>
            
            <p><strong>Возврат значений:</strong> Ключевое слово <code>return</code> возвращает результат:</p>
            <pre>def multiply(x, y):
    return x * y</pre>
            
            <p><strong>*args:</strong> Позволяет передать переменное количество аргументов:</p>
            <pre>def sum_all(*args):
    return sum(args)

sum_all(1, 2, 3, 4)  # 10</pre>
            
            <p><strong>Импорт модулей:</strong> Модули импортируются с помощью <code>import</code>:</p>
            <pre>import math
result = math.sqrt(16)  # 4.0</pre>
            
            <p><strong>Lambda-функции:</strong> Анонимные функции в одну строку:</p>
            <pre>square = lambda x: x**2
square(5)  # 25</pre>
            
            <p><strong>Именованные аргументы:</strong> Можно передавать параметры по имени:</p>
            <pre>def greet(name, age):
    return f"{name} is {age} years old"

greet(age=25, name="Alice")</pre>
        '
    ],
    'python_strings' => [
        'intro' => 'Тест по работе со строками в Python. Вы проверите знания методов строк, форматирования, f-строк и операций со строками. Строки — один из самых используемых типов данных в Python.',
        'topics' => ['Методы строк', 'Форматирование строк', 'F-строки (f-strings)', 'Операции со строками', 'Обработка текста'],
        'theory' => '
            <h3>Работа со строками</h3>
            <p><strong>Преобразование регистра:</strong> Метод <code>upper()</code> преобразует в верхний регистр:</p>
            <pre>text = "hello"
upper_text = text.upper()  # "HELLO"</pre>
            
            <p><strong>Разделение строк:</strong> Метод <code>split()</code> разделяет строку по разделителю:</p>
            <pre>text = "apple,banana,orange"
fruits = text.split(",")  # ["apple", "banana", "orange"]</pre>
            
            <p><strong>F-строки:</strong> Позволяют встраивать выражения в строки:</p>
            <pre>name = "Python"
version = 3.9
message = f"{name} version {version}"  # "Python version 3.9"</pre>
            
            <p><strong>Проверка начала:</strong> Метод <code>startswith()</code> проверяет префикс:</p>
            <pre>text = "Hello World"
text.startswith("Hello")  # True</pre>
            
            <p><strong>Объединение:</strong> Метод <code>join()</code> объединяет список строк:</p>
            <pre>words = ["Hello", "World"]
sentence = " ".join(words)  # "Hello World"</pre>
        '
    ],
    'python_oop' => [
        'intro' => 'Модуль по объектно-ориентированному программированию в Python. Вы проверите понимание классов, объектов, конструкторов, наследования и специальных методов. ООП позволяет создавать сложные и масштабируемые программы.',
        'topics' => ['Классы и объекты', 'Конструктор __init__', 'Атрибуты и методы', 'Наследование', 'Специальные методы'],
        'theory' => '
            <h3>Объектно-ориентированное программирование</h3>
            <p><strong>Объявление класса:</strong> Класс создаётся с помощью <code>class</code>:</p>
            <pre>class MyClass:
    pass</pre>
            
            <p><strong>Конструктор:</strong> Метод <code>__init__</code> вызывается при создании объекта:</p>
            <pre>class Person:
    def __init__(self, name, age):
        self.name = name
        self.age = age</pre>
            
            <p><strong>Доступ к атрибутам:</strong> Атрибуты доступны через точку:</p>
            <pre>person = Person("Alice", 25)
print(person.name)  # "Alice"</pre>
            
            <p><strong>self:</strong> Ссылка на текущий экземпляр класса:</p>
            <pre>class Dog:
    def __init__(self, name):
        self.name = name
    
    def bark(self):
        return f"{self.name} says woof!"</pre>
            
            <p><strong>Наследование:</strong> Родительский класс указывается в скобках:</p>
            <pre>class Animal:
    def speak(self):
        return "Some sound"

class Dog(Animal):
    def speak(self):
        return "Woof!"</pre>
        '
    ],
    'robot_intro' => [
        'intro' => 'Основы робототехники: сенсоры, приводы, контроллеры и источники питания.',
        'topics' => ['Сенсоры расстояния', 'Контроллеры', 'Сервоприводы', 'Питание', 'PID-регуляторы'],
        'theory' => '
            <h3>Сенсоры и приводы</h3>
            <p><strong>Ультразвуковые датчики</strong> измеряют расстояние до препятствий.</p>
            <p><strong>Контроллеры</strong> (Arduino, STM32) управляют периферией и выполняют прошивку.</p>
            <p><strong>Сервоприводы</strong> обеспечивают точное позиционирование узлов.</p>
            <p><strong>PID-регулятор</strong> стабилизирует движение и удерживает параметры системы.</p>
        '
    ],
    'robot_navigation' => [
        'intro' => 'Навигация роботов: SLAM, лидары, IMU и алгоритмы планирования пути.',
        'topics' => ['SLAM', 'IMU', 'Лидары', 'Линейные датчики', 'Алгоритмы A*'],
        'theory' => '
            <h3>Навигация</h3>
            <p><strong>SLAM</strong> — одновременная локализация и построение карты.</p>
            <p><strong>IMU</strong> сочетает акселерометры и гироскопы для определения ориентации.</p>
            <p><strong>A*</strong> ищет кратчайший путь в графе состояний.</p>
            <p><strong>Лидар</strong> создаёт облако точек и помогает избегать препятствий.</p>
        '
    ],
    'comp_networks' => [
        'intro' => 'Компьютерные сети: модель OSI, протоколы TCP/IP и сетевое оборудование.',
        'topics' => ['Модель OSI', 'TCP и UDP', 'IP-адресация', 'Коммутаторы и маршрутизаторы', 'Широковещание'],
        'theory' => '
            <h3>Основы сетей</h3>
            <p><strong>OSI</strong> состоит из 7 уровней — от физического до прикладного.</p>
            <p><strong>TCP</strong> обеспечивает надёжность доставки, <strong>UDP</strong> — минимальную задержку.</p>
            <p><strong>Коммутаторы</strong> работают на канальном уровне, <strong>маршрутизаторы</strong> соединяют сети на сетевом уровне.</p>
        '
    ],
    'comp_hardware' => [
        'intro' => 'Аппаратное обеспечение ПК: процессор, память, накопители и блок питания.',
        'topics' => ['CPU', 'RAM', 'SSD/HDD', 'Блок питания', 'Интерфейсы PCIe/SATA'],
        'theory' => '
            <h3>Компоненты ПК</h3>
            <p><strong>CPU</strong> выполняет инструкции и измеряется в герцах.</p>
            <p><strong>Оперативная память</strong> хранит данные для быстрого доступа.</p>
            <p><strong>SSD NVMe</strong> используют PCIe и обеспечивают высокую скорость чтения/записи.</p>
            <p><strong>Блок питания</strong> преобразует напряжение сети в уровни для компонентов.</p>
        '
    ],
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($test['title'], ENT_QUOTES) ?></title>
    <style>
        body {
            margin: 0;
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: #f5f7fb;
            color: #2c3e50;
            min-height: 100vh;
        }
        header {
            background: #2563eb;
            color: #fff;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        header h1 {
            margin: 0;
            font-size: 24px;
        }
        header a {
            color: #fff;
            text-decoration: none;
            border: 1px solid rgba(255,255,255,0.6);
            border-radius: 6px;
            padding: 8px 16px;
        }
        .wrapper {
            max-width: 900px;
            margin: 30px auto 60px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 15px 45px rgba(30, 60, 114, 0.12);
            padding: 40px;
        }
        .intro-section {
            text-align: center;
        }
        .intro-section h2 {
            color: #2563eb;
            margin-top: 0;
            font-size: 28px;
        }
        .intro-section .description {
            color: #6b7280;
            line-height: 1.8;
            margin: 20px 0;
            font-size: 16px;
        }
        .info-box {
            background: #eff4ff;
            border-left: 4px solid #2563eb;
            padding: 20px;
            border-radius: 8px;
            margin: 24px 0;
            text-align: left;
        }
        .info-box h3 {
            margin: 0 0 12px;
            color: #1e40af;
            font-size: 18px;
        }
        .info-box ul {
            margin: 0;
            padding-left: 20px;
            color: #4b5563;
        }
        .info-box li {
            margin: 8px 0;
            line-height: 1.6;
        }
        .test-stats {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 30px 0;
            flex-wrap: wrap;
        }
        .stat-item {
            text-align: center;
        }
        .stat-item .number {
            font-size: 32px;
            font-weight: bold;
            color: #2563eb;
        }
        .stat-item .label {
            color: #6b7280;
            font-size: 14px;
            margin-top: 4px;
        }
        .start-btn {
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 16px 40px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        .start-btn:hover {
            background: #1d4ed8;
        }
        .theory-section {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 30px;
            margin: 30px 0;
            text-align: left;
        }
        .theory-section h3 {
            color: #1e40af;
            margin-top: 0;
            font-size: 22px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
        }
        .theory-section p {
            color: #4b5563;
            line-height: 1.8;
            margin: 16px 0;
        }
        .theory-section code {
            background: #1e293b;
            color: #e2e8f0;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: "Courier New", monospace;
            font-size: 14px;
        }
        .theory-section pre {
            background: #1e293b;
            color: #e2e8f0;
            padding: 16px;
            border-radius: 8px;
            overflow-x: auto;
            margin: 16px 0;
            font-family: "Courier New", monospace;
            font-size: 14px;
            line-height: 1.6;
        }
        .theory-section strong {
            color: #1e293b;
        }
        .question {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ecf0f1;
        }
        .question h2 {
            margin: 0 0 15px;
        }
        .options label {
            display: block;
            padding: 12px;
            border: 2px solid #dfe6e9;
            border-radius: 6px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: border 0.2s ease, background 0.2s ease;
        }
        .options input {
            margin-right: 10px;
        }
        .options label:hover {
            border-color: #2563eb;
            background: #f0f7ff;
        }
        .submit-btn {
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 15px 35px;
            font-size: 18px;
            cursor: pointer;
            transition: background 0.2s ease;
        }
        .submit-btn:hover {
            background: #1d4ed8;
        }
        @media (max-width: 600px) {
            header, .wrapper {
                padding: 20px;
            }
            .test-stats {
                gap: 20px;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1><?= htmlspecialchars($test['title'], ENT_QUOTES) ?></h1>
        <a href="<?= htmlspecialchars($returnPage, ENT_QUOTES) ?>">← Вернуться к списку</a>
    </header>
    <div class="wrapper">
        <?php if (!$startTest): ?>
            <!-- Вводная страница -->
            <div class="intro-section">
                <h2>Добро пожаловать на тест!</h2>
                <p class="description">
                    <?= htmlspecialchars($test['description'], ENT_QUOTES) ?>
                </p>
                
                <?php if (isset($topicInfo[$testId])): ?>
                    <div class="info-box">
                        <h3>О чём этот тест?</h3>
                        <p style="margin: 0 0 12px; color: #4b5563;">
                            <?= htmlspecialchars($topicInfo[$testId]['intro'], ENT_QUOTES) ?>
                        </p>
                        <h3 style="margin-top: 16px; margin-bottom: 12px;">Темы, которые будут проверены:</h3>
                        <ul>
                            <?php foreach ($topicInfo[$testId]['topics'] as $topic): ?>
                                <li><?= htmlspecialchars($topic, ENT_QUOTES) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="test-stats">
                    <div class="stat-item">
                        <div class="number"><?= count($questions) ?></div>
                        <div class="label">вопросов</div>
                    </div>
                    <div class="stat-item">
                        <div class="number">~10</div>
                        <div class="label">минут</div>
                    </div>
                    <div class="stat-item">
                        <div class="number">4</div>
                        <div class="label">варианта ответа</div>
                    </div>
                </div>

                <?php if (isset($topicInfo[$testId]['theory'])): ?>
                    <div class="theory-section">
                        <?= $topicInfo[$testId]['theory'] ?>
                    </div>
                <?php endif; ?>

                <a href="?test_id=<?= urlencode($testId) ?>&from=<?= urlencode($returnPage) ?>&start=1" class="start-btn">Начать тест</a>
            </div>
        <?php else: ?>
            <!-- Страница с вопросами -->
            <form method="POST">
                <input type="hidden" name="test_id" value="<?= htmlspecialchars($testId, ENT_QUOTES) ?>">
                <input type="hidden" name="return_to" value="<?= htmlspecialchars($returnPage, ENT_QUOTES) ?>">
                <?php foreach ($questions as $index => $question): ?>
                    <div class="question">
                        <h2><?= ($index + 1) ?>. <?= htmlspecialchars($question['text'], ENT_QUOTES) ?></h2>
                        <div class="options">
                            <?php foreach ($question['options'] as $optionIndex => $option): ?>
                                <label>
                                    <input type="radio" name="answers[<?= $index ?>]" value="<?= $optionIndex ?>" required>
                                    <?= htmlspecialchars($option, ENT_QUOTES) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <button class="submit-btn" type="submit" name="submit_test">Отправить ответы</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>

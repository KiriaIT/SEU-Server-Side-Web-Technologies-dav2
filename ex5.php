<?php
// სავარჯიშო 5: POST-ით მთავარი საქაღალდისა და ქვესაქაღალდის შექმნა, GET-ით მთავარის შიგთავსის ჩვენება

$message = '';
$result = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $main = trim($_POST['main'] ?? '');
    $sub = trim($_POST['sub'] ?? '');

    if (!preg_match('/^[\w-]+$/', $main) || !preg_match('/^[\w-]+$/', $sub)) {
        $message = 'არასწორი სახელი';
    } elseif (is_dir("$main/$sub")) {
        $message = "'$main/$sub' უკვე არსებობს";
    } elseif (mkdir("$main/$sub", 0777, true)) { // true = რეკურსიულად: თუ მთავარი არ არსებობს, მასაც შექმნის
        $message = "შეიქმნა სტრუქტურა: $main/$sub";
    } else {
        $message = 'შექმნა ვერ მოხერხდა';
    }
}

if (isset($_GET['folder'])) {
    $folder = trim($_GET['folder']);

    if (!preg_match('/^[\w-]+$/', $folder)) {
        $result = 'არასწორი სახელი';
    } elseif (!is_dir($folder)) {
        $result = "საქაღალდე '$folder' არ არსებობს";
    } else {
        $items = array_diff(scandir($folder), ['.', '..']);

        if (count($items) === 0) {
            $result = "საქაღალდე '$folder' ცარიელია";
        } else {
            $result = "<b>$folder</b> საქაღალდის შიგთავსი:<ul>";
            foreach ($items as $item) {
                $icon = is_dir("$folder/$item") ? '📁' : '📄';
                $result .= "<li>$icon " . htmlspecialchars($item) . '</li>';
            }
            $result .= '</ul>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>სავარჯიშო 5</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>მთავარი და ქვესაქაღალდის შექმნა (POST)</h2>
    <form method="post">
        <input type="text" name="main" placeholder="მთავარი საქაღალდე" required>
        <input type="text" name="sub" placeholder="ქვესაქაღალდე" required>
        <button type="submit">შექმნა</button>
    </form>
    <p><?php echo $message; ?></p>

    <h2>მთავარი საქაღალდის შიგთავსი (GET)</h2>
    <form method="get">
        <input type="text" name="folder" placeholder="მთავარი საქაღალდე" required>
        <button type="submit">ნახვა</button>
    </form>
    <div class="result"><?php echo $result; ?></div>
</body>
</html>

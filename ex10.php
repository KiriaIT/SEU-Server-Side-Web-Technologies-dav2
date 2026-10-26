<?php
// სავარჯიშო 10: POST-ით წაშლა მხოლოდ ცარიელობის შემთხვევაში, GET-ით შიგთავსის წინასწარ ნახვა

$message = '';
$result = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['folder'] ?? '');

    if (!preg_match('/^[\w-]+$/', $name)) {
        $message = 'არასწორი სახელი';
    } elseif (!is_dir($name)) {
        $message = "საქაღალდე '$name' არ არსებობს";
    } else {
        $items = array_diff(scandir($name), ['.', '..']);

        // ჯერ ვამოწმებთ ცარიელობას, მხოლოდ შემდეგ ვშლით
        if (count($items) > 0) {
            $message = "საქაღალდე '$name' ცარიელი არ არის (" . count($items) . " ელემენტი), არ წაიშალა";
        } elseif (rmdir($name)) {
            $message = "საქაღალდე '$name' ცარიელი იყო და წაიშალა";
        } else {
            $message = 'წაშლა ვერ მოხერხდა';
        }
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
            $result = "საქაღალდე '$folder' ცარიელია, მისი წაშლა შესაძლებელია";
        } else {
            $result = "<b>$folder</b> ცარიელი არ არის, შიგთავსი:<ul>";
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
    <title>სავარჯიშო 10</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>შიგთავსის წინასწარ ნახვა (GET)</h2>
    <form method="get">
        <input type="text" name="folder" placeholder="საქაღალდის სახელი" required>
        <button type="submit">ნახვა</button>
    </form>
    <div class="result"><?php echo $result; ?></div>

    <h2>საქაღალდის წაშლა (POST)</h2>
    <form method="post">
        <input type="text" name="folder" placeholder="საქაღალდის სახელი" required>
        <button type="submit">წაშლა</button>
    </form>
    <p><?php echo $message; ?></p>
</body>
</html>

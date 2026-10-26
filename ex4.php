<?php
// სავარჯიშო 4: POST-ით მთავარი საქაღალდის შექმნა, GET-ით მისი შიგთავსის (ფაილები, ქვესაქაღალდეები) ჩვენება

$message = '';
$result = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['folder'] ?? '');

    if (!preg_match('/^[\w-]+$/', $name)) {
        $message = 'არასწორი სახელი';
    } elseif (is_dir($name)) {
        $message = "საქაღალდე '$name' უკვე არსებობს";
    } elseif (mkdir($name)) {
        $message = "საქაღალდე '$name' შეიქმნა";
    } else {
        $message = 'საქაღალდის შექმნა ვერ მოხერხდა';
    }
}

if (isset($_GET['folder'])) {
    $folder = trim($_GET['folder']);

    if (!preg_match('/^[\w-]+$/', $folder)) {
        $result = 'არასწორი სახელი';
    } elseif (!is_dir($folder)) {
        $result = "საქაღალდე '$folder' არ არსებობს";
    } else {
        // "." (მიმდინარე საქაღალდე) და ".." (მშობელი) რეალური ელემენტები არ არის, ამიტომ ვაშორებთ
        $items = array_diff(scandir($folder), ['.', '..']);

        if (count($items) === 0) {
            $result = "საქაღალდე '$folder' ცარიელია";
        } else {
            $result = "<b>$folder</b> საქაღალდის შიგთავსი:<ul>";
            foreach ($items as $item) {
                // is_dir-ს სრული გზა სჭირდება ("folder/item"), მარტო სახელი არ კმარა
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
    <title>სავარჯიშო 4</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>მთავარი საქაღალდის შექმნა (POST)</h2>
    <form method="post">
        <input type="text" name="folder" placeholder="საქაღალდის სახელი" required>
        <button type="submit">შექმნა</button>
    </form>
    <p><?php echo $message; ?></p>

    <h2>შიგთავსის ნახვა (GET)</h2>
    <form method="get">
        <input type="text" name="folder" placeholder="საქაღალდის სახელი" required>
        <button type="submit">ნახვა</button>
    </form>
    <div class="result"><?php echo $result; ?></div>
</body>
</html>

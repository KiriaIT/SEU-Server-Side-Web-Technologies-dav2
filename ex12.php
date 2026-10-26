<?php
// სავარჯიშო 12: სრული პროგრამა — სტრუქტურის შექმნა (POST), ნახვა (GET) და წაშლა (ცალკე POST)

// შიგთავსს აბრუნებს ჩადგმული სიის სახით; ქვესაქაღალდისთვის ფუნქცია თავის თავს იძახებს (რეკურსია)
function listFolder($path)
{
    $html = '<ul>';
    foreach (array_diff(scandir($path), ['.', '..']) as $item) {
        $full = "$path/$item";
        if (is_dir($full)) {
            $html .= '<li>📁 ' . htmlspecialchars($item) . listFolder($full) . '</li>';
        } else {
            $html .= '<li>📄 ' . htmlspecialchars($item) . '</li>';
        }
    }
    return $html . '</ul>';
}

// rmdir მხოლოდ ცარიელ საქაღალდეს შლის, ამიტომ ჯერ შიგთავსს ვასუფთავებთ:
// ფაილებს unlink-ით, ქვესაქაღალდეებს ამავე ფუნქციით, ბოლოს კი თავად საქაღალდეს
function deleteFolder($path)
{
    foreach (array_diff(scandir($path), ['.', '..']) as $item) {
        $full = "$path/$item";
        if (is_dir($full)) {
            deleteFolder($full);
        } else {
            unlink($full);
        }
    }
    return rmdir($path);
}

$message = '';
$result = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ორივე ფორმა ამავე ფაილს აგზავნის, ამიტომ დამალული ველით (action) ვარჩევთ, რომელი ფორმა გაიგზავნა
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $main = trim($_POST['main'] ?? '');
        $sub = trim($_POST['sub'] ?? '');
        $file = trim($_POST['file'] ?? '');

        if (!preg_match('/^[\w-]+$/', $main) || !preg_match('/^[\w-]+$/', $sub) || !preg_match('/^[\w-]+(\.\w+)?$/', $file)) {
            $message = 'არასწორი სახელი';
        } else {
            if (!is_dir("$main/$sub")) {
                mkdir("$main/$sub", 0777, true);
            }

            $path = "$main/$sub/$file";
            if (file_exists($path)) {
                $message = "'$path' უკვე არსებობს";
            } elseif (touch($path)) {
                $message = "შეიქმნა სტრუქტურა: $path";
            } else {
                $message = 'შექმნა ვერ მოხერხდა';
            }
        }
    } elseif ($action === 'delete') {
        $main = trim($_POST['main'] ?? '');

        if (!preg_match('/^[\w-]+$/', $main)) {
            $message = 'არასწორი სახელი';
        } elseif (!is_dir($main)) {
            $message = "საქაღალდე '$main' არ არსებობს";
        } elseif (deleteFolder($main)) {
            $message = "საქაღალდე '$main' მთელი შიგთავსით წაიშალა";
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
        $result = "<b>$folder</b> საქაღალდის სტრუქტურა:" . listFolder($folder);
    }
}
?>
<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>სავარჯიშო 12</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>სტრუქტურის შექმნა (POST)</h2>
    <form method="post">
        <input type="hidden" name="action" value="create">
        <input type="text" name="main" placeholder="მთავარი საქაღალდე" required>
        <input type="text" name="sub" placeholder="ქვესაქაღალდე" required>
        <input type="text" name="file" placeholder="ფაილი (მაგ. notes.txt)" required>
        <button type="submit">შექმნა</button>
    </form>

    <h2>მთავარი საქაღალდის წაშლა (POST)</h2>
    <form method="post">
        <input type="hidden" name="action" value="delete">
        <input type="text" name="main" placeholder="მთავარი საქაღალდე" required>
        <button type="submit">წაშლა</button>
    </form>
    <p><?php echo $message; ?></p>

    <h2>შიგთავსის ნახვა (GET)</h2>
    <form method="get">
        <input type="text" name="folder" placeholder="მთავარი საქაღალდე" required>
        <button type="submit">ნახვა</button>
    </form>
    <div class="result"><?php echo $result; ?></div>
</body>
</html>

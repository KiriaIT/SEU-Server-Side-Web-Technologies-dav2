<?php
// სავარჯიშო 8: POST-ით ორი საქაღალდის შექმნა, GET-ით ერთ-ერთის შიგთავსი (პირობითი ოპერატორები + ციკლი)

$message = '';
$result = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $names = [trim($_POST['folder1'] ?? ''), trim($_POST['folder2'] ?? '')];

    // ორივე სახელს ერთი ციკლით ვამუშავებთ; .= შედეგს ემატება და არ გადაიწერება
    foreach ($names as $name) {
        if (!preg_match('/^[\w-]+$/', $name)) {
            $message .= 'არასწორი სახელი<br>';
        } elseif (is_dir($name)) {
            $message .= "'$name' უკვე არსებობს<br>";
        } elseif (mkdir($name)) {
            $message .= "'$name' შეიქმნა<br>";
        } else {
            $message .= "'$name'-ის შექმნა ვერ მოხერხდა<br>";
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
            $result = "საქაღალდე '$folder' ცარიელია";
        } else {
            $result = "<b>$folder</b> საქაღალდეში მოთავსებულია:<ul>";
            foreach ($items as $item) {
                // თითოეულ ელემენტზე ვამოწმებთ, საქაღალდეა თუ ფაილი
                if (is_dir("$folder/$item")) {
                    $result .= '<li>ქვესაქაღალდე: ' . htmlspecialchars($item) . '</li>';
                } else {
                    $result .= '<li>ფაილი: ' . htmlspecialchars($item) . '</li>';
                }
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
    <title>სავარჯიშო 8</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>ორი საქაღალდის შექმნა (POST)</h2>
    <form method="post">
        <input type="text" name="folder1" placeholder="პირველი საქაღალდე" required>
        <input type="text" name="folder2" placeholder="მეორე საქაღალდე" required>
        <button type="submit">შექმნა</button>
    </form>
    <p><?php echo $message; ?></p>

    <h2>ერთ-ერთის შიგთავსი (GET)</h2>
    <form method="get">
        <input type="text" name="folder" placeholder="საქაღალდის სახელი" required>
        <button type="submit">ნახვა</button>
    </form>
    <div class="result"><?php echo $result; ?></div>
</body>
</html>

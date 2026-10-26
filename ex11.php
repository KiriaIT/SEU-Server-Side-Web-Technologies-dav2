<?php
// სავარჯიშო 11: POST-ით საქაღალდე + N ცალი ტექსტური ფაილი, GET-ით შიგთავსის დანომრილი სია

$message = '';
$result = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['folder'] ?? '');
    $count = (int)($_POST['count'] ?? 0); // (int) ტექსტს რიცხვად აქცევს

    if (!preg_match('/^[\w-]+$/', $name)) {
        $message = 'არასწორი სახელი';
    } elseif ($count < 1 || $count > 50) {
        $message = 'ფაილების რაოდენობა უნდა იყოს 1-დან 50-მდე';
    } else {
        if (!is_dir($name)) {
            mkdir($name);
        }

        for ($i = 1; $i <= $count; $i++) {
            file_put_contents("$name/file$i.txt", "ეს არის ფაილი ნომერი $i");
        }
        $message = "საქაღალდე '$name'-ში შეიქმნა $count ტექსტური ფაილი";
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
        // ბუნებრივი დალაგება: file2 მოვა file10-მდე (ჩვეულებრივი დალაგებით file10 იქნებოდა წინ)
        natsort($items);

        if (count($items) === 0) {
            $result = "საქაღალდე '$folder' ცარიელია";
        } else {
            // <ol> ბრაუზერში დანომრილ სიას ქმნის
            $result = "<b>$folder</b> საქაღალდის ელემენტები:<ol>";
            foreach ($items as $item) {
                $result .= '<li>' . htmlspecialchars($item) . '</li>';
            }
            $result .= '</ol>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>სავარჯიშო 11</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>საქაღალდე + ფაილები (POST)</h2>
    <form method="post">
        <input type="text" name="folder" placeholder="საქაღალდის სახელი" required>
        <input type="number" name="count" placeholder="ფაილების რაოდენობა" min="1" max="50" required>
        <button type="submit">შექმნა</button>
    </form>
    <p><?php echo $message; ?></p>

    <h2>ელემენტების სია (GET)</h2>
    <form method="get">
        <input type="text" name="folder" placeholder="საქაღალდის სახელი" required>
        <button type="submit">ნახვა</button>
    </form>
    <div class="result"><?php echo $result; ?></div>
</body>
</html>

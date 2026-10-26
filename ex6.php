<?php
// სავარჯიშო 6: POST-ით საქაღალდისა და მასში ცარიელი ფაილის შექმნა, GET-ით შიგთავსის ჩვენება

$message = '';
$result = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $folder = trim($_POST['folder'] ?? '');
    $file = trim($_POST['file'] ?? '');

    // ფაილის სახელში ერთი წერტილიც დასაშვებია გაფართოებისთვის (მაგ. notes.txt)
    if (!preg_match('/^[\w-]+$/', $folder) || !preg_match('/^[\w-]+(\.\w+)?$/', $file)) {
        $message = 'არასწორი სახელი';
    } else {
        // საქაღალდეს მხოლოდ მაშინ ვქმნით, თუ ჯერ არ არსებობს
        if (!is_dir($folder)) {
            mkdir($folder);
        }

        $path = "$folder/$file";
        if (file_exists($path)) {
            $message = "ფაილი '$path' უკვე არსებობს";
        } elseif (touch($path)) { // touch ქმნის ცარიელ ფაილს
            $message = "შეიქმნა: $path";
        } else {
            $message = 'ფაილის შექმნა ვერ მოხერხდა';
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
            $result = "<b>$folder</b> საქაღალდეში არსებული ფაილები და საქაღალდეები:<ul>";
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
    <title>სავარჯიშო 6</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>საქაღალდე + ცარიელი ფაილი (POST)</h2>
    <form method="post">
        <input type="text" name="folder" placeholder="საქაღალდის სახელი" required>
        <input type="text" name="file" placeholder="ფაილის სახელი (მაგ. notes.txt)" required>
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

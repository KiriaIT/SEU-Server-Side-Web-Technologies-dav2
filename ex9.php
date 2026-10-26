<?php
// სავარჯიშო 9: POST-ით საქაღალდე + მასში info.txt, GET-ით ფაილებისა და საქაღალდეების ცალ-ცალკე ჩვენება

$message = '';
$result = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['folder'] ?? '');

    if (!preg_match('/^[\w-]+$/', $name)) {
        $message = 'არასწორი სახელი';
    } elseif (is_dir($name)) {
        $message = "საქაღალდე '$name' უკვე არსებობს";
    } elseif (mkdir($name)) {
        // file_put_contents ერთი ბრძანებით ქმნის ფაილს და წერს მასში ტექსტს
        file_put_contents("$name/info.txt", "საქაღალდე: $name\nშექმნის დრო: " . date('Y-m-d H:i:s'));
        $message = "შეიქმნა საქაღალდე '$name' და მასში info.txt";
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
        // ელემენტებს ორ ცალკე მასივში ვანაწილებთ
        $dirs = [];
        $files = [];
        foreach (array_diff(scandir($folder), ['.', '..']) as $item) {
            if (is_dir("$folder/$item")) {
                $dirs[] = $item;
            } else {
                $files[] = $item;
            }
        }

        $result = '<h3>საქაღალდეები (' . count($dirs) . ')</h3><ul>';
        foreach ($dirs as $dir) {
            $result .= '<li>' . htmlspecialchars($dir) . '</li>';
        }
        $result .= '</ul><h3>ფაილები (' . count($files) . ')</h3><ul>';
        foreach ($files as $file) {
            $result .= '<li>' . htmlspecialchars($file) . '</li>';
        }
        $result .= '</ul>';
    }
}
?>
<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>სავარჯიშო 9</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>საქაღალდე + info.txt (POST)</h2>
    <form method="post">
        <input type="text" name="folder" placeholder="საქაღალდის სახელი" required>
        <button type="submit">შექმნა</button>
    </form>
    <p><?php echo $message; ?></p>

    <h2>შიგთავსის წაკითხვა (GET)</h2>
    <form method="get">
        <input type="text" name="folder" placeholder="საქაღალდის სახელი" required>
        <button type="submit">წაკითხვა</button>
    </form>
    <div class="result"><?php echo $result; ?></div>
</body>
</html>

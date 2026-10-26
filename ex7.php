<?php
// სავარჯიშო 7: POST-ით შექმნა (ან შეტყობინება, რომ უკვე არსებობს), GET-ით ელემენტების რაოდენობა

$message = '';
$result = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['folder'] ?? '');

    if (!preg_match('/^[\w-]+$/', $name)) {
        $message = 'არასწორი სახელი';
    } elseif (is_dir($name)) {
        $message = "საქაღალდე '$name' უკვე არსებობს, ხელახლა არ შეიქმნა";
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
        // ვითვლით "." და ".."-ის გარეშე, ანუ მხოლოდ რეალურ ფაილებსა და საქაღალდეებს
        $count = count(array_diff(scandir($folder), ['.', '..']));
        $result = "საქაღალდე '$folder' შეიცავს $count ელემენტს";
    }
}
?>
<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>სავარჯიშო 7</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>საქაღალდის შექმნა (POST)</h2>
    <form method="post">
        <input type="text" name="folder" placeholder="საქაღალდის სახელი" required>
        <button type="submit">შექმნა</button>
    </form>
    <p><?php echo $message; ?></p>

    <h2>ელემენტების რაოდენობა (GET)</h2>
    <form method="get">
        <input type="text" name="folder" placeholder="საქაღალდის სახელი" required>
        <button type="submit">დათვლა</button>
    </form>
    <p><?php echo $result; ?></p>
</body>
</html>

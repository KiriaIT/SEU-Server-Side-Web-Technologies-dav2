<?php
// სავარჯიშო 2: POST-ით საქაღალდის შექმნა, GET-ით შემოწმება, არსებობს თუ არა

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

// GET-ის სახელი URL-იდან მოდის, მაგ: ex2.php?folder=test
if (isset($_GET['folder'])) {
    $folder = trim($_GET['folder']);

    if (!preg_match('/^[\w-]+$/', $folder)) {
        $result = 'არასწორი სახელი';
    } elseif (is_dir($folder)) {
        $result = "საქაღალდე '$folder' არსებობს სერვერზე";
    } else {
        $result = "საქაღალდე '$folder' არ არსებობს";
    }
}
?>
<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>სავარჯიშო 2</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>საქაღალდის შექმნა (POST)</h2>
    <form method="post">
        <input type="text" name="folder" placeholder="საქაღალდის სახელი" required>
        <button type="submit">შექმნა</button>
    </form>
    <p><?php echo $message; ?></p>

    <h2>არსებობის შემოწმება (GET)</h2>
    <form method="get">
        <input type="text" name="folder" placeholder="საქაღალდის სახელი" required>
        <button type="submit">შემოწმება</button>
    </form>
    <p><?php echo $result; ?></p>
</body>
</html>

<?php
// სავარჯიშო 3: POST-ით საქაღალდის წაშლა (თუ ცარიელია), GET-ით არსებობის შემოწმება

$message = '';
$result = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['folder'] ?? '');

    if (!preg_match('/^[\w-]+$/', $name)) {
        $message = 'არასწორი სახელი';
    } elseif (!is_dir($name)) {
        $message = "საქაღალდე '$name' არ არსებობს";
    } elseif (count(array_diff(scandir($name), ['.', '..'])) > 0) {
        // scandir-ს "." და ".." ყოველთვის მოაქვს, ამიტომ მათ მოშორების შემდეგ ვითვლით
        $message = "საქაღალდე '$name' ცარიელი არ არის, ამიტომ არ წაიშალა";
    } elseif (rmdir($name)) {
        $message = "საქაღალდე '$name' წაიშალა";
    } else {
        $message = 'წაშლა ვერ მოხერხდა';
    }
}

if (isset($_GET['folder'])) {
    $folder = trim($_GET['folder']);

    if (!preg_match('/^[\w-]+$/', $folder)) {
        $result = 'არასწორი სახელი';
    } elseif (is_dir($folder)) {
        $result = "საქაღალდე '$folder' არსებობს";
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
    <title>სავარჯიშო 3</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>საქაღალდის წაშლა (POST)</h2>
    <form method="post">
        <input type="text" name="folder" placeholder="საქაღალდის სახელი" required>
        <button type="submit">წაშლა</button>
    </form>
    <p><?php echo $message; ?></p>

    <h2>არსებობის შემოწმება წაშლამდე ან წაშლის შემდეგ (GET)</h2>
    <form method="get">
        <input type="text" name="folder" placeholder="საქაღალდის სახელი" required>
        <button type="submit">შემოწმება</button>
    </form>
    <p><?php echo $result; ?></p>
</body>
</html>

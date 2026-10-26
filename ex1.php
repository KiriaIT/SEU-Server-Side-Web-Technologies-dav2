<?php
// სავარჯიშო 1: POST-ით საქაღალდის შექმნა, GET პარამეტრით show=1 შედეგის ჩვენება

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['folder'] ?? '');

    // სახელში მხოლოდ ლათინური ასოები, ციფრები, _ და - (ასე "../"-ით სხვა ადგილას ვერაფერს შექმნის)
    if (!preg_match('/^[\w-]+$/', $name)) {
        $message = 'არასწორი სახელი';
    } elseif (is_dir($name)) {
        $message = "საქაღალდე '$name' უკვე არსებობს";
    } elseif (mkdir($name)) {
        $message = "საქაღალდე '$name' წარმატებით შეიქმნა";
    } else {
        $message = 'საქაღალდის შექმნა ვერ მოხერხდა';
    }
}
?>
<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>სავარჯიშო 1</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>საქაღალდის შექმნა</h2>

    <!-- ფორმა POST-ით იგზავნება, მაგრამ action-ში ?show=1 წერია, ამიტომ GET პარამეტრიც მიყვება -->
    <form method="post" action="ex1.php?show=1">
        <input type="text" name="folder" placeholder="საქაღალდის სახელი" required>
        <button type="submit">შექმნა</button>
    </form>

    <?php
    // შეტყობინება მხოლოდ მაშინ ჩანს, როცა URL-ში show=1 წერია
    if (isset($_GET['show']) && $_GET['show'] == 1 && $message !== '') {
        echo "<p>$message</p>";
    }
    ?>
</body>
</html>

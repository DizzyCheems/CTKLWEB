<?php include 'config.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Download PDFs</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Available Downloads</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="admin.php">Admin</a>
        </nav>
    </header>
    
    <main>
        <h2>PDF Documents</h2>
        <ul>
        <?php
        $stmt = $pdo->query("SELECT * FROM pdf_files ORDER BY upload_date DESC");
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<li>";
            echo "<a href='uploads/{$row['filename']}' download>{$row['filename']}</a>";
            echo " (Uploaded: {$row['upload_date']})";
            echo "</li>";
        }
        ?>
        </ul>
    </main>
</body>
</html>
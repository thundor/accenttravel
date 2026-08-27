<?php

declare(strict_types=1);
// echo password_hash('Modificari2026!~', PASSWORD_BCRYPT); die;

session_start();

/*
|--------------------------------------------------------------------------
| CONFIG
|--------------------------------------------------------------------------
*/
$maxFileSize = 1024 * 1024; // 1 MB
$uploadDir   = __DIR__ . '/';
$targetFile  = $uploadDir . '/index.html';

// MIME-uri acceptate pentru HTML
$allowedMimeTypes = [
    'text/html',
    'application/xhtml+xml',
];

// Creeaza token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';
$isSuccess = false;

/*
|--------------------------------------------------------------------------
| HANDLE UPLOAD
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Verificare CSRF
        if (
            !isset($_POST['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], (string)$_POST['csrf_token'])
        ) {
            throw new RuntimeException('Cerere invalida.');
        }

        if (!isset($_FILES['html_file'])) {
            throw new RuntimeException('Nu a fost trimis niciun fisier.');
        }

        $file = $_FILES['html_file'];

        if (!is_array($file) || !isset($file['error'], $file['tmp_name'], $file['name'], $file['size'])) {
            throw new RuntimeException('Date fisier invalide.');
        }

        // Verificare erori standard PHP upload
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('Nu a fost selectat niciun fisier.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('Fisierul depaseste dimensiunea maxima permisa.');
            default:
                throw new RuntimeException('Eroare la upload.');
        }

        // Verificare dimensiune
        if ($file['size'] <= 0 || $file['size'] > $maxFileSize) {
            throw new RuntimeException('Fisierul trebuie sa aiba intre 1 byte si 1 MB.');
        }

        // Verificare ca fisierul a fost incarcat prin HTTP POST
        if (!is_uploaded_file($file['tmp_name'])) {
            throw new RuntimeException('Upload invalid.');
        }

        // Curatare nume fisier
        $originalName = basename((string)$file['name']);

        // Accepta doar fisier numit exact index.html
        if ($originalName !== 'index.html') {
            throw new RuntimeException('Este permis doar fisierul numit exact index.html.');
        }

        // Verificare extensie
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if ($extension !== 'html') {
            throw new RuntimeException('Doar fisierele .html sunt permise.');
        }

        // Verificare MIME real
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if ($mimeType === false || !in_array($mimeType, $allowedMimeTypes, true)) {
            throw new RuntimeException('Tipul fisierului nu este permis.');
        }

        // Creeaza directorul daca nu exista
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
                throw new RuntimeException('Nu s-a putut crea directorul de upload.');
            }
        }
        if(is_file($targetFile)){
            copy($targetFile, __DIR__ . '/archive/' . date('Y-m-d-h-i-s') . '-' . basename($targetFile));
        }

        // Permisiuni suplimentare: scrie intr-un fisier fix
        if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
            throw new RuntimeException('Fisierul nu a putut fi salvat.');
        }

        // Permisiuni restrictive pe fisier
        @chmod($targetFile, 0644);

        $message = 'Fisierul index.html a fost incarcat cu succes.';
        $isSuccess = true;

        // Regenereaza tokenul dupa upload reusit
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    } catch (Throwable $e) {
        $message = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Upload index.html</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 700px;
            margin: 40px auto;
            padding: 0 20px;
            line-height: 1.5;
        }
        .box {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
        }
        .msg {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 16px;
        }
        .success {
            background: #eafaf1;
            color: #1e6b3a;
            border: 1px solid #b7e4c7;
        }
        .error {
            background: #fff1f0;
            color: #a61b1b;
            border: 1px solid #ffccc7;
        }
        input[type="file"] {
            display: block;
            margin-bottom: 16px;
        }
        button {
            padding: 10px 16px;
            cursor: pointer;
        }
        .note {
            font-size: 14px;
            color: #555;
            margin-top: 16px;
        }
        code {
            background: #f3f3f3;
            padding: 2px 5px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>Upload fisier index.html</h1>

        <?php if ($message !== ''): ?>
            <div class="msg <?php echo $isSuccess ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <form action="" method="post" enctype="multipart/form-data" novalidate>
            <input
                type="hidden"
                name="csrf_token"
                value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>"
            >

            <label for="html_file">Selecteaza doar fisierul <code>index.html</code>:</label><br><br>
            <input
                type="file"
                name="html_file"
                id="html_file"
                accept=".html,text/html"
                required
            >

            <button type="submit">Incarca fisierul</button>
        </form>

        <p class="note">
            Fisierul acceptat trebuie sa fie numit exact <code>index.html</code> si sa aiba maximum 1 MB.
        </p>
    </div>
</body>
</html>
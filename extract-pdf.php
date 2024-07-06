<?php

use Smalot\PdfParser\Parser;

require 'vendor/autoload.php';

use App\ChatGPT;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$prompt = '';
$pdfContent = '';
$error = '';
$reply = '';

$promptTemplate = "Given the following extracted text from pdf file within the delimiter ```:
```
{pdfContent}
```

Give the answer in the following JSON format:
{
    message: topic about the question,
    status: ( found/not-found ),
    result: answer
}

Set message key as keyword what the content is looking for,
If you can't find the answer, set status: 'not-found' and result with `null` value,
If you can find the answer, set status: 'found' and result with exact value (no more description).

Please find the content about:
{prompt}
";

if (($_SERVER['REQUEST_METHOD'] === 'POST') && (isset($_POST['prompt']))) {
    $prompt = $_POST['prompt'];
    $targetFile = 'uploads/' . basename($_FILES["file"]["name"]);
    $ext = strtolower(pathinfo($targetFile,PATHINFO_EXTENSION));
    if ($ext != 'pdf') {
        $error = 'Only PDF files are allowed';
    }
    if ($_FILES["file"]["size"] > 500000) {
        $error = 'Sorry, your file is too large.';
    }

    if (empty($error)) {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
            $parser = new Parser();
            $pdf = $parser->parseFile($targetFile);
            $pdfContent = $pdf->getText();

            $client = new ChatGPT();
            $templatePrompt = str_replace("{pdfContent}", $pdfContent, $promptTemplate);
            $templatePrompt = str_replace("{prompt}", $prompt, $templatePrompt);
            $reply = $client->ask($templatePrompt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Ask to PDF</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>
<body>

<div class="container my-3">
    <div class="row">
        <div class="col-lg-6">
            <h4 class="mb-3">Extract PDF</h4>
            <?php if (!empty($error)): ?>
                <div class="alert alert-warning"><?= $error ?></div>
            <?php endif; ?>
            <form action="" method="POST" class="mb-4" enctype="multipart/form-data">
                <div class="mb-2">
                    <label for="file" class="form-label">File</label>
                    <input type="file" name="file" id="file" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="prompt" class="form-label">Prompt</label>
                    <textarea class="form-control" placeholder="Ask anything..."
                              id="prompt" name="prompt" rows="3" required><?= $prompt; ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary px-3">Upload & Parse</button>
            </form>

            <p class="fw-bold">Answer:</p>
            <?php if ($reply): ?>
                <pre><?= $reply; ?></pre>
            <?php else: ?>
                <p class="text-muted">No answer available</p>
            <?php endif; ?>
        </div>
        <div class="col-lg-6">
            <p class="fw-bold">PDF Content:</p>
            <?php if ($pdfContent): ?>
                <div>
                    <p><?= $pdfContent; ?></p>
                </div>
            <?php else: ?>
                <p class="text-muted">No pdf content available</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>
</html>
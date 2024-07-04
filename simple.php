<?php

require 'vendor/autoload.php';

use App\ChatGPT;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$prompt = '';
$reply = '';

if (($_SERVER['REQUEST_METHOD'] === 'POST') && (isset($_POST['prompt']))) {
    $prompt = $_POST['prompt'];

    $client = new ChatGPT();
    $reply = $client->ask($prompt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Hello ChatGPT</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>
<body>

<div class="container my-3">
    <div class="row">
        <div class="col-lg-6 mx-auto">
            <h4>Simple Prompt To ChatGPT</h4>
            <form action="" method="POST" class="mb-4">
                <label for="prompt" class="form-label">Prompt</label>
                <textarea class="form-control" placeholder="Ask anything..."
                          id="prompt" name="prompt" rows="5"><?= $prompt; ?></textarea>
                <br/>
                <button type="submit" class="btn btn-primary px-3">Ask ChatGPT</button>
            </form>

            <?php if ($reply): ?>
                <p class="fw-bold">Answer:</p>
                <div>
                    <p><?= $reply; ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
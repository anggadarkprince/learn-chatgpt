<?php

require 'vendor/autoload.php';

use App\ChatGPT;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$promptTemplate = "Given the following input:
{prompt}

If the input topic is not based on tourism or vacation, then answer only 'I dont know'

Give the answer in the following JSON format:
{
    output_type: ( text/list ) ,
    output: answer
}

If output_type is a list, the answer should be formatted like this JSON array:
['Museum','Beach']

";

function generatePrompt($prompt, $promptTemplate)
{
    return str_replace("{prompt}", $prompt, $promptTemplate);
}

$prompt = '';
$modifiedPrompt = '';
$output = '';
$outputType = '';

if (($_SERVER['REQUEST_METHOD'] === 'POST') && (isset($_POST['prompt']))) {
    $prompt = $_POST['prompt'];
    $modifiedPrompt = generatePrompt($prompt, $promptTemplate);

    $client = new ChatGPT();
    $reply = $client->ask($modifiedPrompt);

    $responseJson = json_decode($reply);

    $outputType = $responseJson->output_type;
    $output = $responseJson->output;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Prompt Template ChatGPT</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>
<body>

<div class="container my-3">
    <div class="row">
        <div class="col-lg-6 mx-auto">
            <h4>Prompt Template To ChatGPT About: Tourism</h4>
            <form action="" method="POST" class="mb-4">
                <label for="prompt" class="form-label">Prompt</label>
                <textarea class="form-control" placeholder="Ask anything about tourism..."
                          id="prompt" name="prompt" rows="5"><?= $prompt; ?></textarea>
                <br/>
                <button type="submit" class="btn btn-primary px-3">Ask ChatGPT</button>
            </form>

            <?php if ($modifiedPrompt): ?>
                <p class="fw-bold">Modified Prompt:</p>
                <p><?= nl2br($modifiedPrompt) ?></p>
            <?php endif; ?>

            <?php if ($output): ?>
                <p class="fw-bold">Answer:</p>
                <div>
                    <?php if ($outputType == 'text'): ?>
                        <p><?= $output ?></p>
                    <?php endif; ?>


                    <?php if ($outputType == 'list'): ?>
                        <ul>
                            <?php foreach ($output as $li): ?>
                                <li><?= $li ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
<?php

require 'vendor/autoload.php';

use App\ChatGPT;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$promptTemplateHeadline = "
Generate a headline for this article: 
{prompt}
";
$promptTemplateKeywords = '
Generate relevant SEO keywords for this article: 
{prompt}

Output should be as a JSON array like:
["keyword1", "keyword2"]
';
$promptTemplateSummary = '
Summarize this article within 100 words: 
{prompt}
';

function generatePrompt($prompt, $promptTemplate)
{
    return str_replace("{prompt}", $prompt, $promptTemplate);
}

$prompt = '';
$prompts = [];
$modifiedPrompt = '';
$output = '';
$outputType = '';

if (($_SERVER['REQUEST_METHOD'] === 'POST') && (isset($_POST['prompt']))) {
    $prompt = $_POST['prompt'];

    $prompts = [
        'headline' => generatePrompt($prompt, $promptTemplateHeadline),
        'keyword' => generatePrompt($prompt, $promptTemplateKeywords),
        'summary' => generatePrompt($prompt, $promptTemplateSummary),
    ];

    $client = new ChatGPT();
    foreach ($prompts as $key => $promptTemplate) {
        $reply = $client->ask($promptTemplate);
        $prompts[$key] = $reply;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Article Headline</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>
<body>

<div class="container my-3">
    <div class="row">
        <div class="col-lg-6 mx-auto">
            <h4>Write an Article</h4>
            <form action="" method="POST" class="mb-4">
                <label for="prompt" class="form-label">Article</label>
                <textarea class="form-control" placeholder="Write content of article"
                          id="prompt" name="prompt" rows="5"><?= $prompt; ?></textarea>
                <br/>
                <button type="submit" class="btn btn-success px-3">Generate Title Summary</button>
            </form>

            <?php if ($prompts): ?>
                <?php foreach ($prompts as $key => $result): ?>
                    <p class="fw-bold mb-2"><?= str_replace('_', ' ', ucwords($key)) ?></p>
                    <div>
                        <p><?= $result ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
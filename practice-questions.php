<?php

require 'vendor/autoload.php';

use App\ChatGPT;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$promptTemplate = 'Need to generate practice exam questions. Provide questions, 3 choices with 1 correct answer. And a hint.
Generate 2 questions based on the input topic within the delimiter ```:
``` {prompt} ```

Output format should follow a valid JSON array format like:
    [{
        "question" : "What is X?",
        "choices" : ["A", "B", "C"],
        "hint" : "X maybe A",
        "answer": "A"
    },
    {
        "question" : "What is X?",
        "choices" : ["A", "B", "C"],
        "hint" : "X maybe A",
        "answer": "A"
    }]

';

function generatePrompt($prompt, $promptTemplate)
{
    return str_replace("{prompt}", $prompt, $promptTemplate);
}

$questions = [];
$modifiedPrompt = '';

if (($_SERVER['REQUEST_METHOD'] === 'POST') && (isset($_POST['topic']))) {
    $prompt = $_POST['topic'];
    $modifiedPrompt = generatePrompt($prompt, $promptTemplate);

    $client = new ChatGPT();
    $reply = $client->ask($modifiedPrompt, ['model' => 'gpt-4', 'max_tokens' => 5000]);
    $questions = json_decode($reply, true);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <title>Practice</title>
</head>
<body>

<div class="container my-3">
    <div class="row">
        <div class="col-lg-6 mx-auto">

            <?php if ($modifiedPrompt): ?>
                <p class="fw-bold">Modified Prompt:</p>
                <p><?= nl2br($modifiedPrompt) ?></p>
            <?php endif; ?>

            <h4>Quiz Questions</h4>
            <div>
                <?php foreach ($questions as $index => $question): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <p class="mb-2">Question <?= $index + 1 ?>. <?= $question['question'] ?></p>
                            <div class="mb-3">
                                <?php foreach ($question['choices'] as $innerIndex => $choice): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="answers[<?= $index ?>]" value="<?= $choice ?>" id="choice_<?= $index ?>_<?= $innerIndex ?>">
                                        <label class="form-check-label" for="choice_<?= $index ?>_<?= $innerIndex ?>">
                                            <?= htmlentities($choice) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="mb-3">
                                <button type="button" class="btn btn-info btn-hint">Hint</button>
                                <button type="button" class="btn btn-warning btn-answer">Show Answer</button>
                            </div>
                            <p class="hint d-none mb-1">Hint: <?= $question['hint'] ?></p>
                            <p class="answer d-none mb-1">Answer: <?= $question['answer'] ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const hintButtons = document.querySelectorAll(".btn-hint");
        const answerButtons = document.querySelectorAll(".btn-answer");

        hintButtons.forEach(button => {
            button.addEventListener("click", function () {
                const hint = button.parentElement.nextElementSibling;
                hint.classList.toggle("d-none");
            });
        });

        answerButtons.forEach(button => {
            button.addEventListener("click", function () {
                const answer = button.parentElement.nextElementSibling.nextElementSibling;
                answer.classList.toggle("d-none");
            });
        });
    });
</script>

</body>
</html>
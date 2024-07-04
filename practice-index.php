<!DOCTYPE html>
<html lang="en">
<head>
    <title>Practice Exam</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>
<body>

<div class="container my-3">
    <div class="row">
        <div class="col-lg-6 mx-auto">
            <h4>Practice Exam Generator</h4>
            <form action="practice-questions.php" method="POST" class="mb-4" id="form-topic">
                <label for="topic" class="form-label">Enter the topic you want to practice on:</label>
                <input class="form-control" placeholder="Type a topic" id="topic" name="topic">
                <br/>
                <button id="btn-submit" type="submit" class="btn btn-primary px-3">
                    Generate
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const btnGenerate = document.getElementById("btn-submit");
        const topicInput = document.getElementById("topic");
        const buttonText = document.getElementById("button-text");
        const form = document.getElementById("form-topic");

        btnGenerate.addEventListener("click", function () {
            const selectedTopic = topicInput.value.trim();

            if (selectedTopic !== "") {
                btnGenerate.disabled = true;
                btnGenerate.textContent = "Generating...";
                form.submit();
            }
        });
    });
</script>

</body>
</html>
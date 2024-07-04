<!DOCTYPE html>
<html lang="en">
<head>
    <title>SQL Command Generator</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>
<body>

<div class="container my-3">
    <div class="row">
        <div class="col-lg-6 mx-auto">
            <h4>SQL Command Generator</h4>
            <form action="" method="POST" class="mb-4">
                <label for="prompt" class="form-label">Need Data</label>
                <textarea class="form-control" placeholder="What data do you want? ..."
                          id="prompt" name="prompt" rows="5"></textarea>
                <br/>
                <button type="button" class="btn btn-primary px-3" id="btn-generate">Generate SQL</button>
            </form>

            <p class="fw-bold">Generated SQL Command:</p>
            <div id="code-block">
                <pre></pre>
            </div>
        </div>
    </div>
</div>

<script>
    const generateButton = document.getElementById("btn-generate");
    const textInput = document.getElementById("prompt");
    const codeBlock = document.getElementById("code-block");
    const sqlCode = codeBlock.querySelector("pre");

    generateButton.addEventListener("click", async function () {
        generateButton.disabled = true;

        const query = textInput.value;

        try {
            const formData = new FormData();
            formData.append("query", query);

            const response = await fetch("/sql-prompt-api.php", {
                method: "POST",
                body: formData
            });

            if (response.ok) {
                sqlCode.textContent = await response.text();
                codeBlock.classList.remove("hidden");
            } else {
                console.error("Failed to fetch SQL command");
            }
        } catch (error) {
            console.error("An error occurred:", error);
        } finally {
            generateButton.disabled = false;
        }
    });
</script>

</body>
</html>

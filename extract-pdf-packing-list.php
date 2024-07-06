<?php

use Smalot\PdfParser\Parser;

require 'vendor/autoload.php';

use App\ChatGPT;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$pdfContent = '';
$error = '';
$reply = '';

$promptTemplate = 'Given the following extracted text from pdf file within the delimiter ```:
```
{pdfContent}
```

Extract packages and item list from this packing list pdf file with this rule:
1. Identify Packages and Items: Look for sections that denote package details and item details within each package.
2. Extract Package Information: For each package, extract the package number (which will be the same as the package name) and the unit name.
3. Extract Item Information: For each item within a package, extract the item number, item name, quantity, and unit name.
4. Build JSON Structure: Construct the JSON structure with the specified format, ensuring that items within packages are nested appropriately.
5. Handle Unpackaged Items: If there are items not associated with any package, they will be included in the top-level "items" array.
6. Return Empty Arrays if No Data Found: Ensure that if no packages or items are found, the arrays are returned empty.
return output in json format with sample structure as follow:
{
  "packages": [
	"package_number": "value",
	"package_name": "value",
	"unit_name": "value",
	"items": [
		{
			"item_number": "value",
			"item_name": "value",
			"quantity": "value",
			"unit_name": "value"
		}
	]
   ],
   "items": [
	{
		"item_number": "value",
		"item_name": "value",
		"quantity": "value",
		"unit_name": "value"
	}
   ]
}
';

if (($_SERVER['REQUEST_METHOD'] === 'POST')) {
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
            $pdf = $parser->parseFile($targetFile); // $parser->parseContent(file_get_contents('document.pdf'));
            $pdfContent = $pdf->getText();

            $client = new ChatGPT();
            $templatePrompt = str_replace("{pdfContent}", $pdfContent, $promptTemplate);
            $reply = $client->ask($templatePrompt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Extract Packing List from PDF</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>
<body>

<div class="container my-3">
    <div class="row">
        <div class="col-lg-6">
            <h4 class="mb-3">Extract Packing List From PDF</h4>
            <?php if (!empty($error)): ?>
                <div class="alert alert-warning"><?= $error ?></div>
            <?php endif; ?>
            <form action="" method="POST" class="mb-4" enctype="multipart/form-data">
                <div class="mb-2">
                    <label for="file" class="form-label">Packing List File</label>
                    <input type="file" name="file" id="file" class="form-control" required>
                    <p class="form-text">Example of packing list file <a href="statics/packing-list.pdf">Download</a></p>
                </div>
                <div class="mb-3">
                    <label for="prompt" class="form-label">Task</label>
                    <input class="form-control-plaintext" value="Extract packaging and item" id="prompt"/>
                </div>
                <button type="submit" class="btn btn-primary px-3">Extract</button>
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
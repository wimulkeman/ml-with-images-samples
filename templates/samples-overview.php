<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ML Afbeelding voorbeelden</title>

    <style>
        .grid {
            width: 100%;
        }
        .article {
            width: 30%;
            float: left;
            border: darkblue 2px solid;
            margin: 10px;
            padding: 10px;
        }
        .article:hover {
            background-color: aliceblue;
        }
    </style>
</head>
<body>
    <h1>Machine Learning - afbeeldingen</h1>
    <div class="grid">
        <div class="article">
            <h2>Sleutelwoorden achterhalen uit afbeeldingen</h2>
            <?php
                foreach (scandir(dirname(__DIR__) . '/web/assets/img/analysis') as $image) {
                    if (strpos($image, '.') === 0) {
                        continue;
                    }

                    $imageSrc = "assets/img/analysis/{$image}";
                    $imageSrcEncoded = base64_encode($imageSrc);

                    echo "<a href='?service=analyse&action=keywords&image_src=$imageSrcEncoded'><img src='{$imageSrc}' width='20%'></a>";
                }
            ?>
        </div>
        <div class="article">
            <h2>Alt tekst generatie voor afbeeldingen</h2>
            <?php
            foreach (scandir(dirname(__DIR__) . '/web/assets/img/analysis') as $image) {
                if (strpos($image, '.') === 0) {
                    continue;
                }

                $imageSrc = "assets/img/analysis/{$image}";
                $imageSrcEncoded = base64_encode($imageSrc);

                echo "<a href='?service=analyse&action=alt_text&image_src=$imageSrcEncoded'><img src='{$imageSrc}' width='20%'></a>";
            }
            ?>
        </div>
        <!--<div class="article">
            <h2>Sleutelwoorden voor productpagina's</h2>
        </div>-->
        <div class="article">
            <h2>Genereren van thumbnails</h2>
            <?php
            foreach (scandir(dirname(__DIR__) . '/web/assets/img/generate') as $image) {
                if (strpos($image, '.') === 0) {
                    continue;
                }

                if (!is_file(dirname(__DIR__) . '/web/assets/img/generate/' . $image)) {
                    continue;
                }

                $imageSrc = "assets/img/generate/{$image}";
                $imageSrcEncoded = base64_encode($imageSrc);

                echo "<a href='?service=generate&action=gerenate_thumbmail&image_src=$imageSrcEncoded'><img src='{$imageSrc}' width='20%'></a>";
            }
            ?>
        </div>
    </div>
</body>
</html>
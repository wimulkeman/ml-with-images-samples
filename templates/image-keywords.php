<?php
/**
 * Created by IntelliJ IDEA.
 * User: wimulkeman
 * Date: 20-05-17
 * Time: 17:00
 */
$keywords = '';
foreach ($analysisResponse['tags'] as $tag) {
    $keywords .= "<li>{$tag['name']} - {$tag['confidence']}</li>";
}

echo <<<html
<h1>Sleutelwoorden afbeelding</h1>
<p>
    De volgende sleutelwoorden zijn er gevonden voor deze afbeelding zijn:<br>
    <img src="{$decodedImageSrc}" style="max-width: 30%; max-height: 25%">
    <ul>
        {$keywords}
    </ul>
</p>
<p>
    <a href="./">Probeer opnieuw</a>
</p>
html;

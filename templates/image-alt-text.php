<?php
/**
 * Created by IntelliJ IDEA.
 * User: wimulkeman
 * Date: 20-05-17
 * Time: 17:00
 */
echo <<<html
<h1>Alt tekst afbeelding</h1>
<p>
    De volgende is met een zekerheid van {$analysisResponse['description']['confidence']} gegenereerd voor deze afbeelding:<br>
    <img src="{$decodedImageSrc}" style="max-width: 30%; max-height: 25%">
</p>
<p>
    {$analysisResponse['description']['text']}
</p>
<p>
    <a href="./">Probeer opnieuw</a>
</p>
html;

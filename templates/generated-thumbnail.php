<?php
/**
 * Created by IntelliJ IDEA.
 * User: wimulkeman
 * Date: 20-05-17
 * Time: 18:01
 */
echo <<<html
<h1>Gegenereerde thumbnail</h1>
<p>
    De gegenereerde thumbnail van deze afbeelding
</p>
<p>
    <img srcset="{$decodedImageSrc}" style="max-height: 25%; max-width: 25%">
</p>
<p>
    is geworden:
</p>
<p>
    <img src="{$assetThumbnailLocation}" style="width: 100px; height: 100px;">
</p>
<p>
    <a href="./">Probeer opnieuw</a>
</p>
html;

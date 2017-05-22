<?php
$templatesDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR;
$resourcesIncludeDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR;
$srcDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
$vendorDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR;
$webDir = __DIR__ . DIRECTORY_SEPARATOR;

if (empty($_GET['service'])) {
    include $templatesDir . 'samples-overview.php';
    return;
}

include $vendorDir . 'autoload.php';
include $resourcesIncludeDir . 'parameters.php';
include $srcDir . 'AzureConnection.php';

$azureConnection = new AzureConnection();
$azureConnection->setApiKey(AZURE_VISION_API_KEY_EU);

if ($_GET['service'] === 'analyse' && $_GET['action'] === 'keywords') {
    $decodedImageSrc = base64_decode($_GET['image_src']);
    $analysisResponse = $azureConnection->analyseImage($webDir . $decodedImageSrc);

    include $templatesDir . 'image-keywords.php';
    return;
}

if ($_GET['service'] === 'analyse' && $_GET['action'] === 'alt_text') {
    $decodedImageSrc = base64_decode($_GET['image_src']);
    $analysisResponse = $azureConnection->analyseImage($webDir . $decodedImageSrc);

    include $templatesDir . 'image-alt-text.php';
    return;
}

if ($_GET['service'] === 'generate' && $_GET['action'] === 'gerenate_thumbmail') {
    $decodedImageSrc = base64_decode($_GET['image_src']);
    $currentImage = $webDir . $decodedImageSrc;

    $assetThumbnailLocation = dirname($decodedImageSrc) . '/thumbnails/' . pathinfo($decodedImageSrc)['basename'];
    $fullThumbnailLocation = $webDir . $assetThumbnailLocation;

    $analysisResponse = $azureConnection->generateImageThumbnail($currentImage, $fullThumbnailLocation);
    if (!$analysisResponse) {
        throw new \RuntimeException('No thumbnail image vould be generated');
    }

    include $templatesDir . 'generated-thumbnail.php';
    return;
}

if ($_GET['service'] === 'analyse' && $_GET['action'] === 'page_analysis') {
    return;
}

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

function getApiResponseFilename() {
    global $webDir;

    $decodedImageSrc = base64_decode($_GET['image_src']);
    $service = $_GET['service'];
    $action = $_GET['action'];

    $resourceFile = $webDir . 'api_response' . DIRECTORY_SEPARATOR . "{$service}_{$action}" . DIRECTORY_SEPARATOR . md5($decodedImageSrc) . '.json';
    $resourceDir = dirname($resourceFile);

    if (!is_dir($resourceDir)) {
        mkdir($resourceDir, 0777, true);
    }

    return $resourceFile;
}

function saveApiResponse($apiResponse) {
    $resourceFile = getApiResponseFilename();

    file_put_contents($resourceFile, json_encode($apiResponse));
}

function getApiResponse() {
    $resourceFile = getApiResponseFilename();

    if (!is_file($resourceFile)) {
        return '';
    }

    $fileContent = file_get_contents($resourceFile);

    return $fileContent ? json_decode($fileContent, true) : '' ;
}

$azureConnection = new AzureConnection();
$azureConnection->setApiKey(AZURE_VISION_API_KEY_EU);

if ($_GET['service'] === 'analyse' && $_GET['action'] === 'keywords') {
    $decodedImageSrc = base64_decode($_GET['image_src']);

    $analysisResponse = getApiResponse();
    if (!$analysisResponse) {
        $analysisResponse = $azureConnection->analyseImage($webDir . $decodedImageSrc);

        saveApiResponse($analysisResponse);
    }

    include $templatesDir . 'image-keywords.php';
    return;
}

if ($_GET['service'] === 'analyse' && $_GET['action'] === 'alt_text') {
    $decodedImageSrc = base64_decode($_GET['image_src']);

    $analysisResponse = getApiResponse();
    if (!$analysisResponse) {
        $analysisResponse = $azureConnection->analyseImage($webDir . $decodedImageSrc);

        saveApiResponse($analysisResponse);
    }

    include $templatesDir . 'image-alt-text.php';
    return;
}

if ($_GET['service'] === 'generate' && $_GET['action'] === 'generate_thumbnail') {
    $decodedImageSrc = base64_decode($_GET['image_src']);
    $currentImage = $webDir . $decodedImageSrc;

    $assetThumbnailLocation = dirname($decodedImageSrc) . '/thumbnails/' . pathinfo($decodedImageSrc)['basename'];
    $fullThumbnailLocation = $webDir . $assetThumbnailLocation;

    if (!is_file($fullThumbnailLocation)) {
        $analysisResponse = $azureConnection->generateImageThumbnail($currentImage, $fullThumbnailLocation);
        if (! $analysisResponse) {
            throw new \RuntimeException('No thumbnail image could be generated');
        }
    }

    include $templatesDir . 'generated-thumbnail.php';
    return;
}

if ($_GET['service'] === 'analyse' && $_GET['action'] === 'page_analysis') {
    return;
}

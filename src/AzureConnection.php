<?php
use GuzzleHttp\Client;

/**
 * Created by IntelliJ IDEA.
 * User: wimulkeman
 * Date: 14-05-17
 * Time: 18:25
 */
class AzureConnection
{
    /**
     * The urls for the available APIs.
     */
    const TextAnalyseAPI = 'https://westus.api.cognitive.microsoft.com/text/analytics/v2.0/sentiment';
    const VisionAnalysisAPI = 'https://westeurope.api.cognitive.microsoft.com/vision/v1.0/analyze';
//    const VisionAnalysisAPI = 'https://westus.api.cognitive.microsoft.com/vision/v1.0/analyze';
    const ImageThumbnailAPI = 'https://westeurope.api.cognitive.microsoft.com/vision/v1.0/generateThumbnail';
//    const ImageThumbnailAPI = 'https://westus.api.cognitive.microsoft.com/vision/v1.0/generateThumbnail';

    /**
     * @var string
     */
    private $apiKey = '';

    /**
     * Assign a API key to use for the connection.
     *
     * @param string $apiKey
     *
     * @return AzureConnection
     */
    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Make a request for a text sentiment analyse.
     *
     * @param string $textToAnalyse
     *
     * @return float
     */
    public function getTextSentimentAnalysis(string $textToAnalyse): float
    {
        $apiResponse = $this->requestApi(
            self::TextAnalyseAPI,
            ['json' => [
                'documents' => [['id' => 1, 'language' => 'nl', 'text' => $textToAnalyse]]
            ]]
        );

        if (!$apiResponse) {
            throw new \RuntimeException('No response was recieved from the API call');
        }

        if (empty($apiResponse['documents'])) {
            throw new \RuntimeException('The API call returned a invalid data structure.');
        }

        return (float) $apiResponse['documents'][0]['score'];
    }

    /**
     * Analyseer een afbeelding en haal informatie op over de inhoud ervan.
     *
     * @param string $imageLocation
     *
     * @return array
     */
    public function analyseImage(string $imageLocation): array
    {
        if (!is_file($imageLocation)) {
            throw new \RuntimeException('Op de opgegeven locatie kan geen afbeelding worden gevonden.');
        }

        $imageContent = file_get_contents($imageLocation);

        $apiResponse = $this->requestApi(
            self::VisionAnalysisAPI . '?visualFeatures=Tags,Description',
            ['body' => $imageContent, 'headers' => ['Content-Type' => 'application/octet-stream']]
        );
//        $apiResponse = $this->fakeRequest();

        if (!$apiResponse) {
            throw new \RuntimeException('No response was recieved from the API call');
        }

        if (empty($apiResponse['tags']) || empty($apiResponse['description'])) {
            throw new \RuntimeException('The API call returned a invalid data structure.');
        }

        $response = [
            'tags' => $apiResponse['tags'],
            'description' => [
                'text' => $apiResponse['description']['captions'][0]['text'],
                'confidence' => $apiResponse['description']['captions'][0]['confidence'],
                'tags' => $apiResponse['description']['tags']
            ]
        ];

        return $response;
    }

    /**
     * This function generates a thumbnail of a image.
     *
     * It returns a boolean to indicate if the image was created and saved to the disk
     * successfully.
     *
     * @param string $imageLocation
     * @param string $saveLocation
     *
     * @return bool
     * @internal param string $imageContent
     */
    public function generateImageThumbnail(string $imageLocation, string $saveLocation): bool
    {
        if (!is_file($imageLocation)) {
            throw new \RuntimeException('Op de opgegeven locatie kan geen afbeelding worden gevonden.');
        }

        $imageContent = file_get_contents($imageLocation);

        $apiResponse = $this->requestApi(
            self::ImageThumbnailAPI . '?width=100&height=100&smartCrop=true',
            [
                'body' => $imageContent,
                'headers' => ['Content-Type' => 'application/octet-stream'],
                'returnBody' => true,
                'sink' => $saveLocation
            ]
        );
//        $apiResponse = $this->fakeRequest();

        if (!$apiResponse) {
            throw new \RuntimeException('No response was recieved from the API call');
        }

        return is_file($saveLocation);
    }

    /**
     * Request a API action.
     *
     * @param string $serviceUrl
     * @param array  $options
     *
     * @return mixed
     */
    private function requestApi(string $serviceUrl, array $options)
    {
        $headers = [
            'Ocp-Apim-Subscription-Key' => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];
        if (!empty($options['headers'])) {
            $headers = array_merge($headers, $options['headers']);
        }

        $requestClient = new Client([
            'headers' => $headers
        ]);

        $bodyParams = ['http_errors' => false];
        if (!empty($options['json'])) {
            $bodyParams['json'] = $options['json'];
        }
        if (!empty($options['body'])) {
            $bodyParams['body'] = $options['body'];
        }
        if (!empty($options['sink'])) {
            $bodyParams['sink'] = $options['sink'];
        }

        $response = $requestClient->request('POST', $serviceUrl, $bodyParams);

        if (empty($response)) {
            throw new \RuntimeException('The Azure API connection returned a blank response');
        }

        if (!empty($options['returnBody'])) {
            return $response->getBody();
        }

        $json = json_decode($response->getBody(), true);
        if ($json === null) {
            throw new \RuntimeException('The Azure API connection returned a non-json response');
        }

        return $json;
    }

    private function fakeRequest(): array
    {
        return [
            'tags' => [
                ['name' => 'ground', 'confidence' => 0.98717784881592],
                ['name' => 'outdoor', 'confidence' => 0.90505832433701],
                ['name' => 'person', 'confidence' => 0.87824529409409],
                ['name' => 'animal', 'confidence' => 0.84344041347504],
                ['name' => 'mammal', 'confidence' => 0.69184619188309, 'hint' => 'animal']
            ],
            'description' => [
                'tags' => [
                    'building', 'outdoor', 'person', 'animal', 'dog', 'man', 'small', 'sitting',
                    'front', 'leash', 'woman', 'standing', 'holding', 'little', 'brown', 'white',
                    'riding', 'horse', 'large', 'young'
                ],
                'captions' => [
                    ['text' => 'a man riding a horse in front of a building', 'confidence' => 0.93307182794592]
                ]
            ]
        ];
    }
}
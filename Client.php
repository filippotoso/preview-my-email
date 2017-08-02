<?php

namespace FilippoToso\PreviewMyEmail;

use GuzzleHttp\Client as HTTPClient;
use GuzzleHttp\Exception\BadResponseException;

class Client
{

    protected $apikey = null;

    const API_URL = 'https://previewmyemail.com/api/';

    /**
     * Create an instance of the client.
     * @param String ;     PreviewMyEmail api key
     */
    public function __construct($apikey) {
        $this->$apikey = $apikey;
    }

    /**
     * Build a full API url
     * @param  String $path The path of the api
     * @param  Array $params The array of params
     * @return String  The complete url
     */
    protected function buildUrl($path, $params = []) {

        $params['apikey'] = $this->apikey;

        $url = self::API_URL . $path . '?' . http_build_query($params);

        return $url;

    }


    /**
     * Execute an HTTP GET request to BrowserStack API
     * @param  String $url The url of the API endpoint
     * @return Array|FALSE  The result of the request
     */
    protected function get($url) {

        $client = new HTTPClient();

        try {
            $res = $client->request('GET', $url);
        }
        catch (BadResponseException $e) {
            return FALSE;
        }

        $data = json_decode($res->getBody(), TRUE);

        return $data;

    }

    /**
     * Execute an HTTP POST request to BrowserStack API
     * @param  String $url The url of the API endpoint
     * @param  Array $data The parameters of the request
     * @return Array|FALSE  The result of the request
     */
    protected function post($url, $data) {

        $client = new HTTPClient();

        try {
            $res = $client->request('POST', $url, [
                'json' => $data,
            ]);
        }
        catch (BadResponseException $e) {
            return FALSE;
        }

        $data = json_decode($res->getBody(), TRUE);

        return $data;

    }

    /**
     * Returns the list of email clients available for email design testing
     * @return Array|FALSE  The list of emails clients or FALSE in case of error
     * Source: https://previewmyemail.com/resources/articles/retrieveemailclients
     */
    public function retrieveEmailClients() {
        $url = $this->buildUrl('RetrieveEmailClients');
        return $this->get($url);
    }

    /**
     * Creates a new email design test request
     * @param  String $emailBody    Provide the HTML part of your email
     * @param  String $emailSubject Provide the subject of your email
     * @param  Array $targetEmailApps   Provide the list of email clients you wish to get screen shots from. This parameter should be passed in array format.
     * @return String|FALSE  The jobid of the preview request or FALSE in case of error
     * Source: https://previewmyemail.com/resources/articles/createpreview
     */
    public function createPreview($emailBody, $emailSubject, $targetEmailApps = []) {

        $url = $this->buildUrl('CreatePreview');

        $params = [
            'emailbody' => base64_encode($emailBody),
            'emailsubject' => $emailSubject,
            'targetemailapps' => $targetEmailApps,
        ];

        return $this->get($url);

    }

    /**
     * Creates a new email design test request
     * @param  String $jobId    The jobid returned by createPreview()
     * @return Array|FALSE  The preview data or FALSE in case of error
     * Source: https://previewmyemail.com/resources/articles/fetchpreview
     */
    public function fetchPreview($jobId) {

        $params = ['job' => $jobId];

        $url = $this->buildUrl('FetchPreview', $params);

        return $this->get($url);

    }


    /**
     * Returns the list of email design test requests you have done so far.
     * @return Array|FALSE  The list of email design test requests you have done so far.
     * Source: https://previewmyemail.com/resources/articles/getpreviewlist
     */
    public function getPreviewList() {

        $url = $this->buildUrl('GetPreviewList');

        return $this->get($url);

    }


    /**
     *Deletes the target email design test
     * @param  String $jobId    The jobid returned by createPreview()
     * @return Boolean
     * Source: https://previewmyemail.com/resources/articles/deletepreview
     */
    public function deletePreview($jobId) {

        $params = ['job' => $jobId];

        $url = $this->buildUrl('DeletePreview', $params);

        return $this->get($url);

    }

    /**
     *Deletes the target email design test
     * @param  String $jobId    The jobid returned by createPreview()
     * @param  String $clientCode   The client code returned by retrieveEmailClients()
     * @return Boolean
     * Source: https://previewmyemail.com/resources/articles/retryemailclient
     */
    public function RetryEmailClient($jobId, $clientCode) {

        $params = [
            'job' => $jobId,
            'clientcode' => $clientCode,

        ];

        $url = $this->buildUrl('RetryEmailClient', $params);
        
        return $this->get($url);

    }

}

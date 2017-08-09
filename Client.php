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
    protected function buildUrl($path, &$params = []) {

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
     * @return Array  The result of the request
     * Source: https://previewmyemail.com/resources/articles/retrieveemailclients
     */
    public function retrieveEmailClients() {

        $url = $this->buildUrl('RetrieveEmailClients', $params);

        return $this->post($url, $params);

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

        $params = [
            'emailbody' => base64_encode($emailBody),
            'emailsubject' => $emailSubject,
            'targetemailapps' => $targetEmailApps,
        ];

        $url = $this->buildUrl('CreatePreview', $params);

        return $this->post($url);

    }

    /**
     * Creates a new email design test request
     * @param  String $jobId    The jobid returned by createPreview()
     * @return Array  The result of the request
     * Source: https://previewmyemail.com/resources/articles/fetchpreview
     */
    public function fetchPreview($jobId) {

        $params = ['job' => $jobId];

        $url = $this->buildUrl('FetchPreview', $params);

        return $this->post($url, $params);

    }

    /**
     * Returns the list of email design test requests you have done so far.
     * @return Array  The result of the request
     * Source: https://previewmyemail.com/resources/articles/getpreviewlist
     */
    public function getPreviewList() {

        $url = $this->buildUrl('GetPreviewList', $params);

        return $this->post($url, $params);

    }

    /**
     * Returns the current status of email design test servers.
     * @return Array  The current status of email design test servers.
     * Source: https://previewmyemail.com/resources/articles/getpreviewlist
     */
    public function systemStatus() {

        $url = $this->buildUrl('SystemStatus', $params);

        return $this->post($url, $params);

    }

    /**
     * Deletes the target email design test
     * @param  String $jobId    The jobid returned by createPreview()
     * @return Boolean
     * Source: https://previewmyemail.com/resources/articles/deletepreview
     */
    public function deletePreview($jobId) {

        $params = ['job' => $jobId];

        $url = $this->buildUrl('DeletePreview', $params);

        return $this->post($url, $params);

    }

    /**
     * Runs the email design test again for the target email application.
     * Please note: you cannot execute this command on target mail apps, that
     * were not included when you created a design test.
     * @param  String $jobId    The jobid returned by createPreview()
     * @param  String $clientCode   The client code returned by retrieveEmailClients()
     * @return Boolean
     * Source: https://previewmyemail.com/resources/articles/retryemailclient
     */
    public function retryEmailClient($jobId, $clientCode) {

        $params = [
            'job' => $jobId,
            'clientcode' => $clientCode,
        ];

        $url = $this->buildUrl('RetryEmailClient', $params);

        return $this->post($url, $url);

    }

    /**
     * Creates a new email design test request. Please note*: creating design
     * test for all email applications on PreviewMyEmail, may delay API response to
     * 1 minute, so keep your timings right.
     * @param  String $tag    Provide the name of the tracking code tag to create the tracking code.
     * @return Array  The result of the request
     * Source: https://previewmyemail.com/resources/articles/createemailanalyticscode
     */
    public function createEmailAnalyticsCode($tag) {

        $params = ['tag' => $tag];

        $url = $this->buildUrl('CreateEmailAnalyticsCode', $params);

        return $this->post($url, $params);

    }

    /**
     * This API command returns total numbers such as opens, unique opens,
     * forwards, prints, etc.
     * @param  String $tag   Provide the name of the tracking code tag to get results for. Cannot be
     *                       empty. Please note*: stating non-existent analytics tag will return a
     *                       successful response with “0” counts.
     * @return Array  The result of the request
     * Source: https://previewmyemail.com/resources/articles/getemailanalyticscounts
     */
    public function getEmailAnalyticsCounts($tag) {

        $params = ['tag' => $tag];

        $url = $this->buildUrl('GetEmailAnalyticsCounts', $params);

        return $this->post($url, $params);

    }

    /**
     * This API command returns “most hit” email applications/clients detected
     * in your tag.
     * @param  String $tag   Provide the name of the tracking code tag to get results for. Cannot be
     *                       empty. Please note*: stating non-existent analytics tag will return a
     *                       successful response with “0” counts.
     * @return Array  The result of the request
     * Source: https://previewmyemail.com/resources/articles/getemailanalyticstopclients
     */
    public function getEmailAnalyticsTopClients($tag) {

        $params = ['tag' => $tag];

        $url = $this->buildUrl('GetEmailAnalyticsTopClients', $params);

        return $this->post($url, $params);

    }

    /**
     * This API command returns top geo-locations detected in your tag.
     * @param  String $tag   Provide the name of the tracking code tag to get results for. Cannot be
     *                       empty. Please note*: stating non-existent analytics tag will return a
     *                       successful response with “0” counts.
     * @return Array  The result of the request
     * Source: https://previewmyemail.com/resources/articles/getemailanalyticstoplocations
     */
    public function getEmailAnalyticsTopLocations($tag) {

        $params = ['tag' => $tag];

        $url = $this->buildUrl('GetEmailAnalyticsTopLocations', $params);

        return $this->post($url, $params);

    }

    /**
     * This API command will return the list of email “open”, “print” and
     * “forward” activities, that happened during some specific time. Maximum
     * amount of returned entries is 150. If you wish to retrieve all records
     * you will have to use time parser and retrieve 150 entries with every
     * parsed-time request.
     * @param  String $tag   Provide the name of the tracking code tag to get results for. Cannot be
     *                       empty. Please note*: stating non-existent analytics tag will return a
     *                       successful response with “0” counts.
     * @param Enum $get      Optional parameter. Retrieve feed data “after” or “before” the
     *                       timestamp. Please note: this parameter won’t work without
     *                       timestamp declaration.
     * @param Int $timestamp Optional parameter. Pass this info if you wish to retrieve the
     *                       feed after/before a certain date/time. It should be in unix
     *                       timestamp format. Please note***: server is running in
     *                       “Europe/Istanbul” time zone, you have to calculate UTC time
     *                       accordingly.
     * @param Int $limit     Optional parameter. If you wish to retrieve limited records below 150,
     *                       pass the number of records you wish to get back.
     * @return Array  The result of the request
     * Source: https://previewmyemail.com/resources/articles/getemailanalyticsfeed
     */
    public function getEmailAnalyticsFeed($tag, $get = null, $timestamp = null, $limit = null) {

        $params = ['tag' => $tag];

        if (!is_null($get)) {
            $params['get'] = $get;
        }

        if (!is_null($timestamp)) {
            $params['timestamp'] = $timestamp;
        }

        if (!is_null($limit)) {
            $params['limit'] = $limit;
        }

        $url = $this->buildUrl('GetEmailAnalyticsFeed', $params);

        return $this->post($url, $params);

    }

}

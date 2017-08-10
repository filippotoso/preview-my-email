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
        $this->apikey = $apikey;
    }

    /**
     * Build a full API url
     * @param  String $path The path of the api
     * @param  Array $params The array of params
     * @return String  The complete url
     */
    protected function buildUrl($path, &$params = []) {

        $params['apikey'] = $this->apikey;

        $url = self::API_URL . $path;

        return $url;

    }


    /**
     * Execute an HTTP GET request to BrowserStack API
     * @param  String $url The url of the API endpoint
     * @return Array|FALSE  The result of the request
     */
    protected function get($url, $json = TRUE) {

        $client = new HTTPClient();

        try {
            $res = $client->request('GET', $url);
        }
        catch (BadResponseException $e) {
            return FALSE;
        }

        if ($json) {
            return json_decode($res->getBody(), TRUE);
        }

        return $res->getBody(TRUE);

    }

    /**
     * Execute an HTTP POST request to BrowserStack API
     * @param  String $url The url of the API endpoint
     * @param  Array $data The parameters of the request
     * @param  Bool $json Specify if the answer is json and needs to be decoded
     * @return Array|FALSE  The result of the request
     */
    protected function post($url, $data, $json = TRUE) {

        $client = new HTTPClient();

        try {
            $res = $client->request('POST', $url, [
                'form_params' => $data,
                'read_timeout' => 90, // Check createPreview() notes
            ]);
        }
        catch (BadResponseException $e) {
            return FALSE;
        }

        if ($json) {
            return json_decode($res->getBody(), TRUE);
        }

        return (string)$res->getBody();

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
     * Creates a new email design test request. Please note: creating design
     * test for all email applications on PreviewMyEmail, may delay API response to
     * 1 minute, so keep your timings right.
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

        return $this->post($url, $params);

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
     * Please note: we are wiping design tests every six months).
     * @return Array  The result of the request
     * Source: https://previewmyemail.com/resources/articles/getpreviewlist
     */
    public function getPreviewList() {

        $url = $this->buildUrl('GetPreviewList', $params);

        $result = $this->post($url, $params);

        return is_null($result) ? [] : $result;

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

        return $this->post($url, $params);

    }

    /**
     * Creates a new email design test request. Please note: creating design
     * test for all email applications on PreviewMyEmail, may delay API response to
     * 1 minute, so keep your timings right.
     * @param  String $tag    Provide the name of the tracking code tag to create the tracking code.
     * @return Array  The result of the request
     * Source: https://previewmyemail.com/resources/articles/createemailanalyticscode
     */
    public function createEmailAnalyticsCode($tag) {

        $params = ['tag' => $tag];

        $url = $this->buildUrl('CreateEmailAnalyticsCode', $params);

        return $this->post($url, $params, FALSE);

    }

    /**
     * This API command returns total numbers such as opens, unique opens,
     * forwards, prints, etc.
     * @param  String $tag   Provide the name of the tracking code tag to get results for. Cannot be
     *                       empty. Please note: stating non-existent analytics tag will return a
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
     *                       empty. Please note: stating non-existent analytics tag will return a
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
     *                       empty. Please note: stating non-existent analytics tag will return a
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
     *                       empty. Please note: stating non-existent analytics tag will return a
     *                       successful response with “0” counts.
     * @param Enum $get      Optional parameter. Retrieve feed data “after” or “before” the
     *                       timestamp. Please note: this parameter won’t work without
     *                       timestamp declaration.
     * @param Int $timestamp Optional parameter. Pass this info if you wish to retrieve the
     *                       feed after/before a certain date/time. It should be in unix
     *                       timestamp format. Please note: server is running in
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

    /**
     * This public API command will get your email content and take a small thumbnail
     * screen shot of it. Useful for displaying a small thumbnail next your email campaigns.
     * This API command will return a request ID. You can query this request ID with
     * “GetThumbnail” API command in a few minutes to retrieve the URL of your email
     * screen shot thumbnail.

     * @param  String $contentType   Email content type. It can be ‘HTML’ or ‘Text’
     * @param  String $content   Email content
     * @return Array  The result of the request
     * Source: http://previewmyemail.com/resources/articles/newthumbnail
     */
    public function newThumbnail($contentType, $content) {

        $params = [
            'ContentType' => $contentType,
            'Content' => $content
        ];

        $url = $this->buildUrl('NewThumbnail', $params);

        return $this->post($url, $params);

    }

    /**
     * This public API command will return the URL of the generated thumbnail.
     * If it’s not ready yet, it will return an error.
     * @param  String $requestId  The request ID gathered from NewThumbnail API call
     * @return Array  The result of the request
     * Source: http://previewmyemail.com/resources/articles/getthumbnail
     */
    public function getThumbnail($requestId) {

        $params = ['RequestID' => $requestId];

        $url = $this->buildUrl('GetThumbnail', $params);

        return $this->post($url, $params);

    }

    /**
     * Returns the list of inbox monitor tests you have made so far.
     * @return Array  The result of the request
     * Source: https://previewmyemail.com/resources/articles/retrieveemailclients
     */
    public function getInboxMonitoringResults() {

        $url = $this->buildUrl('GetInboxMonitoringResults', $params);

        return $this->post($url, $params);

    }

}

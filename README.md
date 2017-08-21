# PreviewMyEmail API Client

A simple client for previewmyemail.com API.

## Requirements

- PHP 5.6+
- guzzlehttp/guzzle 6.2+

## Installing

Use Composer to install it:

```
composer require filippo-toso/composer require filippo-toso/preview-my-email
```

## Using It

Create the client
```
use FilippoToso\PreviewMyEmail\Client as PreviewMyEmail;

$client = new PreviewMyEmail('apikey');
```

Returns the list of email clients available for email design testing
```
$clients = $client->retrieveEmailClients();
```

Returns the list of email design test requests you have done so far.
Please note: we are wiping design tests every six months.
```
$previews = $client->getPreviewList();
```

Returns the current status of email design test servers.
```
$status = $client->systemStatus();
```

This public API command will get your email content and take a small thumbnail screen shot of it. Useful for displaying a small thumbnail next your email campaigns.
This API command will return a request ID. You can query this request ID with `GetThumbnail` API command in a few minutes to retrieve the URL of your email screen shot thumbnail.
```
$contentType = 'TEXT';
$content = 'Hello World!';
$request = $client->newThumbnail($contentType, $content)
```

This public API command will return the URL of the generated thumbnail.
If it’s not ready yet, it will return an error.
```
$thumbnail = $client->getThumbnail($requestId);
$requestId = '20170810092854d834ebfe9af9dcac1eb781e668f295a6';
```

Returns the list of inbox monitor tests you have made so far.
```
$results = $client->getInboxMonitoringResults();
```

Creates a new email design test request. Please note: creating design test for all email applications on PreviewMyEmail, may delay API response to 1 minute, so keep your timings right.
```
$tag = 'filippo-toso-2';
$code = $client->createEmailAnalyticsCode($tag);
```

This API command returns total numbers such as opens, unique opens, forwards, prints, etc.
```
$tag = 'filippo-toso-2';
$statistics = $client->getEmailAnalyticsCounts($tag);
```

This API command returns “most hit” email applications/clients detected in your tag.
```
$tag = 'filippo-toso-2';
$topClients = $client->getEmailAnalyticsTopClients($tag);
```

This API command returns top geo-locations detected in your tag.
```
$tag = 'filippo-toso-2';
$topLocations = $client->getEmailAnalyticsTopLocations($tag);
```

This API command will return the list of email “open”, “print” and “forward” activities, that happened during some specific time. Maximum amount of returned entries is 150. If you wish to retrieve all records you will have to use time parser and retrieve 150 entries with every parsed-time request.
```
$tag = 'filippo-toso-2';
$feed = $client->getEmailAnalyticsFeed($tag);
```

Creates a new email design test request. Please note: creating design test for all email applications on PreviewMyEmail, may delay API response to 1 minute, so keep your timings right.
```
$emailBody = '<h1>Hello World!</h1>';
$emailSubject = 'Hello World!';
$targetEmailApps = [74];
$preview = $client->createPreview($emailBody, $emailSubject, $targetEmailApps);
```

Get a preview job.
```
$jobId = 1718271789;
$job = $client->fetchPreview($jobId);
```

Runs the email design test again for the target email application.
Please note: you cannot execute this command on target mail apps, that were not included when you created a design test.
```
$jobId = 1718267428;
$clientCode = 74;
$result = $client->retryEmailClient($jobId, $clientCode);
```

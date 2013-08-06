Pingback 2 Webhook
==================

This is pingback2webhook.

This service, installable on your local server, allows you to add pingback and webmention support to any static page, and as well as collecting the data
you are able to specify a webhook(s) to which a ping is sent, allowing you to chain services together, and also to query the database.

This is very similar/inspired by Aaron Parecki's service Pingback.me (which I recommend you check out - <https://github.com/aaronpk/Pingback>), but this is 
1) written in PHP (I had reasons I didn't want to run an entire ruby stack on my server), 2) Supports a few extra things that I needed - namely webhook pings and couchdb support, 3) Needed it self hosted.

Requirements
------------

* PHP 5.3
* CouchDB

Usage
-----

* Install, then define your endpoints in the definitions directory (see corresponding readme). This will provide you with webhook and pingback endpoints at 
http://SERVER/webmention/myendpoint/ and http://SERVER/pingback/myendpoint/xmlrpc respectively.
* In your static page, expose the endpoint in the normal way, either in headers:
```php
    // Webmention
    header('Link: <http://SERVER/webmention/myendpoint/>; rel="http://webmention.org/"');
    
    // Pingback
    header('X-Pingback: http://SERVER/pingback/myendpoint/xmlrpc');
```
And/Or in the page metadata


```html
<html>
    <head>
        <link href="http://SERVER/webmention/myendpoint/" rel="http://webmention.org/" />
        <link rel="pingback" href="http://SERVER/pingback/myendpoint/xmlrpc" />

        ...

    </head>

    ...

</html>
```

Todo
----

* [ ] Kick the tires on pingback (should work, but primarily using webmention)
* [ ] Sexy #indieweb microformats parsing to pull semantic content from source pages.

See
---
* Marcus Povey <http://www.marcus-povey.co.uk>
* Webmention Spec <http://webmention.org/>
* Pingback <http://www.hixie.ch/specs/pingback/pingback>

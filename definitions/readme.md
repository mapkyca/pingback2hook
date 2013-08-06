Definitions
===========

This is a directory which defines various endpoints which accept pingbacks/webmentions.

They can essentially be thought of as "users" (eventually, they may actually be users), and 
define a receiving URL and a number of web hooks, as well as a password for querying the database.

You can define multiple entries in one ini file, or multiple entries in the same file. 

Each definition MUST have a section header as this defines you endpoint label. Post webmentions to 
http://SERVER/webmention/myendpoint/, post pingbacks to http://SERVER/pingback/myendpoint/xmlrpc, 
and make a GET request to http://SERVER/api/myendpoint/youquery.json passing secret as a header (e.g. http://SERVER/api/myendpoint/latest.json?target_url=http://mypermalink.com&limit=10.)

Example configuration:
----------------------

```
[myendpoint]
; Shared secret authorisation (yes, basic, but very simple). Pass this as a X-PINGBACK2HOOK-SECRET HTTP header. It goes without saying that this should be done over HTTPS
secret = "some random code"

; Zero or more webhook endpoints
webhooks[] = "https://alice.com/webhook"
webhooks[] = "https://bob.com/webhook"
```
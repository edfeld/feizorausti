Simple Analytics (version 1.56)
==============================
*last updated 5/15/2015 by Luke Hollenback*


Simple Analytics is intended to be a light-weight visitor tracking software
with an easy-to-use implementation. Along with the ability to track visitors
and to view data using the various interfaces that are included in the package,
Simple Analytics also has a simple JSON API to satisfy further needs.

Below you will find license information, usage instructions, as well as
documentation for the JSON API and contact details of the developer for bug
reporting.


Table of Contents
-----------------
1. License Information
2. Usage Instructions
    * Manual Installation
        1. Installing Necessary Files
        2. Importing MySQL Table
        3. Configuring Installation/Customizing API Keys
        4. Adding Tracking Code to Website
3. JSON API Documentation
    * Understanding the JSON API
    * Action 1: Retrieve List of Unique Visitors
    * Action 2: Retrieve Most Popular Location
    * Action 3: Retrieve Counts of Views and Visits
    * Action 4: Retrieve Information and Tracking Data for a Particular Visitor
    * Action 5: Output the Included Analytics Statistics Control Panel
    * Action 6: Output the Included Analytics Visitor Statistics Panel
    * Action 7: Output the tracking JavaScript to be linked to on every desired
      page
    * Action 8: Output the Print Page
    * Action null: Track a Visitor
    * Error Information
4. Contact Details


License Information
--------------------
After purchase, you may use Simple Analytics in any personal or commercial
project that you are a contributing developer for. If Simple Analytics is used
in a project developed for you, but not by you, or if you are not the party that
purchased Simple Analytics, your use of the product is restricted to the
implementation at hand (in other words, you cannot use it on any other project).

Re-sale of Simple Analytics by third parties is prohibited without the consent
of its developer.

The Google Maps Static Maps API functionality included in Simple Analytics is
subject to its own terms. Inside of Simple Analytics' configuration
(*config.php*), there is the ability to turn this functionality on or off, as
well as to provide an API key if necessary. Please review the [Google Maps
    Static Maps API](http://google.com/) license and determine how it applies
to you as a developer and to each of your projects on an individual basis.

Similarly, Simple Analytics can be configured to use a variety of different
Geo-IP services inside of its configuration. Please make sure that you review
the particular license for whichever service you opt to use and configure
Simple Analytics to use it legally and appropriately for each specific
implementation.


Usage Instructions
------------------
###Manual Installation###
These are the instructions to install Simple Analytics manually.
####1. Installing Necessary Files###
The Simple Analytics package contains the following files:
- analytics.php (the core software)
- config.php (edittable configuration file)
- datacontent.sql (importable MySQL database table for Simple Analytics)
- styles.css (primary stylesheet)
- dark.css (a basic dark theme)
- readme.md (what you are currently reading)
- demo.html (an example of how to use the software)

Put all of these files in a directory on your web server. If they are not placed
in the root directory of the website which will be using Simple Analytics. be
sure to configure the *$analyticsFolder* setting inside of *config.php*.

####2. Importing MySQL Table####
The process of importing the MySQL table for Simple Analytics will vary
depending on you're preferred method of accessing your database. In general
terms, the table that Simple Analytics uses should look like the following:

`id (int) (primary) (auto-increment) | visitId (text) | visitorId (text) |`
`ipAddress (text) | intervalCounter (int) | city (text) | region (text) |`
`country (text) |postal (text) | location (text) | userAgent (text) |`
`windowLocation (text) | specialNote (text) | dateStamp (bigint)`

If you use a program such as phpMyAdmin or Sequel Pro, you can import
*datacontent.sql* into your database in an effort to avoid manually creating
the table.

####3. Configuring Installation/Customizing API Keys####
In order for Simple Analytics to run properly for your particular situation,
`config.php` must be edited. The file is well-documented, and some of the
settings inside of it may even be configured automatically by using the
self-installing PHP script, but it is best to manually check. Most importantly,
you must edit the *DATABASE* information, the *API KEYS* information, and the *$analyticsFolder* variable under the *CLIENT TRACKING SCRIPT* information.

**NOTE:** API keys should be changed immediately upon installation. Do not keep
          the default API keys or else potentially sensitive information may
          be accessible by unwanted eyes.

####4. Adding Tracking Code to Website####
Once you have installed the backbone of Simple Analytics, you must add tracking
code to each page of your website or web app that you wish to record visits to.
In order to do this, you must include the JavaScript that is output by *action
7* of the API, as described below, into the `<head>` of the HTML file for the
page. An example of what this line looks like is as follows:

```
<script type='text/javascript' src='analytics.php/?action=7' note='this is a demo' id='analytics'></script>
```

If you will notice, there is an extra attribute to this `<script>` element
called "note". Although this attribute is completely optional, it does provide
the ability to attach notes to page visits. This may be useful, for example,
if your website was still a work in progress and you wanted to distiguish
visits to it in its unfinished state from visits to it once it is a completed
project.


JSON API Documentation
----------------------
###Understanding the JSON API###
Simply Analytics includes a JSON API that it not only uses itself, but that is
available for use outside of that explicitly implemented by the software. In
order to have Simple Analytics return JSON data, simply call `analytics.php`
with a query string containing the necessary parameters. The API functions as
described below each have different required parameters, but the two most common
are:

- action: an *integer* referring to the action number that is desired
- key: a *string* containing the API key necessary to execute and retrieve data
  of and from the desired action; API keys can be created and configured inside
  of `config.php` (and it is STRONGLY RECOMMENDED that the default ones are
  changed)

Most of the actions described below provide an example use case. Most
customization of the data returned by this JSON API actually happens in
`config.php`, so be sure to acquaint yourself with that file as well and edit it
as you see fit.


###Action 1: Retrieve List of Unique Visitors###
If an appropriate key is provided, this answers the request with a JSON object
containing and array named "visitors" which contains objects which hold data
for each of the unique visitors to the site.

Query String Parameters:
- (integer) action
- (string) key
- (integer) need [optional; default = 4] the visitors that should be returned
    - 0: today (visitors in the past 24 hours)
    - 1: this week (visitors in the last 7 days)
    - 2: this month (visitors in the last 31 days)
    - 3: this year (visitors in the last 365 days)
    - 4: total

Example:

A call to ``analytics.php?action=1&key=b0b9b8b7b6b5b4b3b2b1&need=0`` might return the following:
```
{
    "visitors":[
        {"visitorId":"19917", "date":"15 . 04 . 2015", "ipAddress":"localhost", "duration":"35", "location":"US", "city":"", "region":"", "country":"US", "isMobile":"no", "browser":"Safari", "system":"Mac OS"},
        {"visitorId":"20214", "date":"15 . 04 . 2015", "ipAddress":"localhost", "duration":"70", "location":"Seattle, Washington, US", "city":"Seattle", "region":"Washington", "country":"US", "isMobile":"no", "browser":"Safari", "system":"Mac OS"},
        {"visitorId":"19856", "date":"15 . 04 . 2015", "ipAddress":"localhost", "duration":"10", "location":"Seattle, Washington, US", "city":"Seattle", "region":"Washington", "country":"US", "isMobile":"no", "browser":"Safari", "system":"Mac OS"}
    ],
    "time":"1429155472"
}
```


###Action 2: Retrieve Most Popular Location###
If an appropriate key is provided, this answers the request with JSON data
containing the all-time most popular country, region, and city assuming that all
fields are available.

Query String Parameters:
- (integer) action
- (string) key

Example:

A call to ``analytics.php?action=2&key=b0b9b8b7b6b5b4b3b2b1`` might return the following:
```
{
    "country":"US",
    "region":"Washington",
    "city":"Seattle",
    "time":"1429166370"
}
```


###Action 3: Retrieve Counts of Views and Visits###
If an appropriate key is provided, this answers the request with JSON data
containing the count of views and visits separated by time periods and devices.

Query String Parameters:
- (integer) action
- (string) key

Example:

A call to ``analytics.php?action=3&key=b0b9b8b7b6b5b4b3b2b1`` might return the following:
```
{
    "dayVisits":"17",
    "dayViews":"53",
    "weekVisits":"19",
    "weekViews":"80",
    "monthVisits":"22",
    "monthViews":"80",
    "yearVisits":"28",
    "yearViews":"190",
    "totalVisits":"28",
    "totalViews":"190",
    "desktopVisits":"28",
    "desktopViews":"190",
    "mobileVisits":"0",
    "mobileViews":"0",
    "unknownVisits":"0",
    "unknownViews":"0",
    "time":"1429166647"
}
```


###Action 4: Retrieve Information and Tracking Data for a Particular Visitor###
If an appropriate key is provided, this answers the request with JSON data about
the particular visitor given by "need", included an array "visited" of the
various pages that the visitor visited.

Query String Parameters:
- (integer) action
- (string) key
- (integer) need the visitorId of the visitor who's details are needed

Example:

A call to ``analytics.php?action=4&key=b0b9b8b7b6b5b4b3b2b1&need=19917`` might return the following:
```
{
    "ipAddress":"localhost",
    "city":"",
    "region":"",
    "country":"US",
    "postal":"",
    "coordinates":"38,-97",
    "location":"US",
    "userAgent":"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/600.5.17 (KHTML, like Gecko) Version/8.0.5 Safari/600.5.17",
    "isMobile":"no",
    "browser":"Safari",
    "system":"Mac OS",
    "dateStamp":"1429130444219",
    "date":"15 . 04 . 2015",
    "visited":[
        {"page":"http://local.analytics.com/index.html", "dateStamp":"1429130501605", "date":"15 . 04 . 2015", "time":"20:41:41", "duration":"5", "specialNote":"this is a demo"},
        {"page":"http://local.analytics.com/index.html", "dateStamp":"1429130444219", "date":"15 . 04 . 2015", "time":"20:40:44", "duration":"30", "specialNote":"this is a demo"}
    ],
    "totalDuration":"35",
    "time":"1429167175"
}
```


###Action 5: Output the Included Analytics Statistics Control Panel###
If an appropriate key is provided, this answers the request with HTML and
JavaScript for the Simple Analytics control panel. The actual formating will
differ slightly depending on settings specified in *config.php*.

Query String Parameters:
- (integer) action
- (string) key


###Action 6: Output the Included Analytics Visitor Statistics Panel###
If an appropriate key is provided, this answers the request with HTML and
JavaScript for the Simple Analytics visitor statistics control panel. The actual
formatting will differ slightly depending on settings specified in *config.php*.

Query String Parameters:
- (integer) action
- (string) key


###Action 7: Output the tracking JavaScript to be linked to on every desired page###
This answers the request with the JavaScript that allows for a visitor to a
page to be tracked. It should be called as "text/javascript" with an HTML
`<script>` tag.

Query String Parameters:
- (integer) action

Further, any page that calls this action will also have a JSON object
names *simpleAnalytics_data* available for use throughout the rest of the page
which contains the following properties:
- (string) outputInfo
- (string) uniqueIdentifier
- (string) randomNumber
- (string) visitStamp
- (string) visitorStamp
- (string) ipAddress
- (string) intervalCounter
- (string) city
- (string) region
- (string) country
- (string) postal
- (string) location
- (string) userAgent
- (string) windowLocation
- (string) specialNote
- (string) timeStamp
- (string) debug

###Action 8: Output the Print Page###
Assuming a valid API key was provided, this outputs a simply, printable HTML
document containing a list of visitors in the time frame specified by "need".

Query String Parameters:
- (integer) action
- (string) key
- (integer) need [optional; default = 0] the visitors that should be returned
    - 0: today (visitors in the past 24 hours)
    - 1: this week (visitors in the last 7 days)
    - 2: this month (visitors in the last 31 days)
    - 3: this year (visitors in the last 365 days)
    - 4: total

###Action null: Track a Visitor###
This records tracking data for a visitor to the website into the database
assuming appropriate information has been posted with the request.

Post Parameters:
- (string) windowLocation the content of the visitor's address bar
- (integer) msTimestamp a Unix timestamp that was generated by the visitor's browser upon page-load
- (string) specialNote text specified in the custom "note" attribute of the HTML `<script>` that called the tracking JavaScript
- (integer) intervalCounter the number of seconds that the page has been loaded on the visitor's browser


###Error Information###
Simple Analytics has some light error-detection built in. If something goes
terrible wrong for some reason, this will probably not satisfy all debugging
needs. That said, if an error is detected, a JSON object will be returned
after the normal request response that looks similar to the following:
```
{
    "errors":[
        {"code":"0000", "description":"Unable to perform the requested action due to either a missing API key, a missing requested action, or missing client tracking data."}
    ]
}
```

Common Error Codes:
- 0000: Unable to perform the requested action due to either a missing API key, a missing requested action, or missing client tracking data.
- 0001: Visitor ID is missing.
- 0002: API Key is missing.
- 0003: There was an error with the SQL query.


Contact Details
---------------
Always be sure that you are using the latest version of Simple Analytics if you
experience an issue. If you find a major bug with Simple Analytics, or have a
question about how to use the software, please email me at
[luke@mynamewasluke.com](mailto:luke@mynamewasluke.com).

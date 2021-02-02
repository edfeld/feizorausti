<?php
/**
 * The configuration file for the Simple Analytics system.
 *
 * This file contains the configuration settings for the entire analytics
 * system. Only edit the values below unless you are an advanced user and are
 * willing to risk breaking things.
 *
 * @package Simple Analytics
 * @author Luke Hollenback <luke@mynamewasluke.com>
 * @version 1.56
 * 
 * History:
 * ERE20201124 - Ed Einfeld -  Add enviroment file, statsEnv.php.
 * ERE20210118 - Ed Einfeld -  Change pagination count to 25.  Add large text 
 *                             variables to this file: JSConfig, JSScripting.
 *                             Added Edge to the list of browsers.
 */


////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////// CONFIGURATION //////////////////////////////
////////////////////////////////////////////////////////////////////////////////

// CLIENT TRACKING SCRIPT
$sessionTime = 15; //time to save sessions before they expire (in seconds); also the time between checks of geoIP data
$updateInterval = 5; //the interval to update a visitor's log (in seconds)
$scriptElementId = 'analytics'; //the id attribute of the script element used to call the analytics.js file
$analyticsFolder = 'analytics/'; //path to the folder containing analytix.php (excluding the opening "/")

// CONTROL PANEL
$showErrors = false; //for debug purposes only; whether or not to output errors when they occur
$htmlMode = true; //whether the control panel should be parsed as proper HTML5 rather than parsed to simply be included
$htmlTitle = 'Analytics'; //if in HTML mode, the title of the control pannel webpage
$themeSheet = 'custom.css'; //file name and extension of an external stylesheet (blank or null if none)
$loadTime = 0; //the speed at which the control-panel counters are incremented (the smaller, the faster) (0 bypasses ticker animation completely)
$autoRefreshTime = 1000; //the speed at which data auto-refreshes (in milleseconds)
$selectedType = 0; //the selected timeframe (whatever is initialized here is the default value)
$paginationEnabled = 0; //whether or not to paginate visitors in the control panel by default (0 = no pagination; 1 = pagination)
$paginationCount = 25; //how many visitors to display per page if using pagination

// EXTERNAL API SETTINGS
$googleStaticMaps = true; //whether or not Google Static Maps should display the location of the visitor visually
$googleStaticMapsAPIkey = ''; //optional; the API key, if you need to include one, that you have obtained for the Google Static Maps API that is used by this analytics system
$ipinfoRequestString = 'http://emissiontoislam.org/GeoIPinator/?ip=[ipaddr]'; //this is the request string that is CURL'd to get the geoIP JSON data; [ipaddr] is a placeholder for the visitors IP address
$ipinfoRequestResp = array( //the JSON object names for various pieces of geoip data
    'city' => 'city',
    'region' => 'region',
    'country' => 'country',
    'postal' => 'postal',
    'location' => null, //set this to null if separate latitude and longitude objects are needed
    'latitude' => 'latitude',
    'longitude' => 'longitude'
);

// DATABASE
// Environment file - ERE20201123
require_once("../config/statsEnv.php");
// ERE20201005 - attach to GRO database
require_once("../config/connectionGROStats.php");

// $conCreative = null; // This is no longer needed ERE2020
$db_host = $hostname;
$db_user = $username;
$db_pass = $password;
$db_name = $database;
$db_tble = 'stats';

// USER DEVICE INFORMATION
// This array describes strings to look for in the visitor's user agent to
// determine what type of device they are connecting from and associates it with
// either a 'yes' if the device is mobile, or a 'no' if the device is not
// mobile.
$userDeviceIsMobileInfo = array(
    '(Macintosh)|(Windows)' => 'no',

    '(iPhone)|(iPad)|(iPod)' => 'yes',
    'Windows Phone' => 'yes',
    'Android' => 'yes',
    'Blackberry' => 'yes',
    '(SymbOS)|(SymbianOS)' => 'yes'
);

// USER BROWSER INFORMATION
// This array describes strings to look for in the visitor's user agent to
// determine what browser they are using and associates it with a string to
// represent it.
$userDeviceBrowserInfo = array(
    'MSIE' => 'Internet Explorer',
    'MSIE 6' => 'Internet Explorer 6',
    'MSIE 5' => 'Internet Explorer 5',
    'Firefox' => 'Firefox',
    'Safari' => 'Safari',
    'Chrome' => 'Chrome',
    'Edge' => 'Edg' // Adding Edge Browser - ERE20210119
);

// USER SYSTEM INFORMATION
// This array describes strings to look for in the visitor's user agent to
// determine what system they are on and associates it with a string to
// represent it.
$userDeviceSystemInfo = array(
    'Win16' => 'Windows 3.11',
    '(Windows 95)|(Win95)|(Windows_95)' => 'Windows 95',
    '(Windows 98)|(Win98)' => 'Windows 98',
    '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)' => 'Windows NT 4.0',
    '(Windows NT 5.0)|(Windows 2000)' => 'Windows 2000',
    '(Windows NT 5.1)|(Windows XP)' => 'Windows XP',
    '(Windows NT 5.2)' => 'Windows Server 2003',
    '(Windows NT 6.0)' => 'Windows Vista',
    '(Windows NT 6.1)' => 'Windows 7',
    '(Windows NT 6.2)' => 'Windows 8',
    '(Windows NT 6.3)' => 'Windows 8.1',
    '(Windows NT 10.0)' => 'Windows 10',
    'Windows ME' => 'Windows ME',
    'OpenBSD' => 'Open BSD',
    'SunOS' => 'Sun OS',
    '(Linux)|(X11)' => 'Linux',
    '(Mac_PowerPC)|(Macintosh)' => 'Mac OS',
    'QNX' => 'QNX',
    'BeOS' => 'BeOS',
    'OS\/2' => 'OS/2',
    '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp)|(MSNBot)|(Ask Jeeves\/Teoma)|(ia_archiver)' => 'Search Bot',

    '(iPhone)|(iPad)|(iPod)' => 'iOS',
    'Windows Phone' => 'Windows Phone',
    'Android' => 'Android',
    'Blackberry' => 'Blackberry OS',
    'SymbOS' => 'Symbian OS',
    'SymbianOS' => 'Symbian OS'
);

// SEARCH BOT FILTERS
// This array describes strings to look for in a user's user agent string to
// indicate that it is a searchbot and therefore should not be added as an entry
// to the database
$userAgentFilters = array(
    'nuhk',
    'Googlebot',
    'Yammybot',
    'Openbot',
    'Slurp',
    'MSNBot',
    'Ask Jeeves\/Teoma',
    'ie_archiver'
);

// API KEYS
// In order for access to the JSON API, an application must provide an API KEY
// via a POST or GET 'key' value to this script in order to prevent sensitive
// data from being seen by unwanted eyes.
//
// Public keys should be used when accessing the JSON API from plaintext that is
// sent to an unknown group of users (e.g. Javascript on a public web page,
// etc...), while private keys can be used on protected applications (e.g.
// Javascript on private a admin panel, server-side PHP, etc...).
//
// The form of a permission is as follows...
//
//      $permission['api key'] = 'permission values';
//
// ...where 'permission values' is a string containing numbers corresponding to
// the actions listed below who's access should be granted to clients using the
// associated key.
//
// NOTE: A * inside of the 'permission values' string signifies complete access.
$permission = array(
    'a1a2a3a4a5a6a7a8a9a0' => '23', //a public key giving access only to the most popular location and the counts of views and visits
    'b0b9b8b7b6b5b4b3b2b1' => '*' //a private key giving access to everything
);

// Move config text from analytics.php to here. - ERE20210118
/* The variables below are used when parsing out HTML pages, such as the
* control panel and the visitor details pages. Many values are pulled from
* the config.php file.
*/
$jsConfigData = "
  // PREPARE FOR CONFIGURATION
  var updateInterval, scriptElementId, analyticsFolder;
  var loadTime, autoRefreshTime, selectedType, paginationEnabled, paginationCount;
  var googleStaticMapsAPIkey;

  // SETTINGS FOR CLIENT'S BROWSER
  updateInterval = $updateInterval; //the interval to update a visitor's log (in seconds)
  scriptElementId = '$scriptElementId'; //the id attribute of the script element used to call the analytix.js file
  analyticsFolder = '$analyticsFolder'; //path to the folder containing analytix.php

  // SETTINGS FOR CONTROL PANEL
  loadTime = $loadTime; //the speed at which counters are incremented (the smaller, the faster) (0 bypasses ticker animation completely)
  autoRefreshTime = $autoRefreshTime; //the speed at which data auto-refreshes (in milleseconds)
  selectedType = $selectedType; //the selected timeframe (whatever is initialized here is the default value)
  paginationEnabled = $paginationEnabled; //whether or not to paginate visitors in the control panel
  paginationCount = $paginationCount; //how many visitors to display per page if using pagination

  // SETTINGS FOR EXTERNAL APIs
  var googleStaticMapsAPIkey = '$googleStaticMapsAPIkey'; //optional; the API key, if you need to include one, that you have obtained for the Google Static Maps API that is used by this analytics system

  // CREATE GOOGLE STATIC MAPS API KEY STRING
  // if an API key was provided above, create a string to append to all Google
  // Static Maps API requests
  var googleStaticMapsAPIkeyString = ''
  if (googleStaticMapsAPIkey != '' && googleStaticMapsAPIkey.length >= 0) {
    googleStaticMapsAPIkeyString = '&key=' + googleStaticMapsAPIkey;
  }

  // OPEN POPUP
  // opens a popup window (new window, not new tab) with a width of 640 and
  // a height of 480 to the specified url
  //
  // url: the location to load in the popup
  function openPopup(url) {
    var newWindow = window.open(url, '_blank', 'width=640, height=480, location=no, menubar=no, resizable=yes, scrollbars=yes, status=no, toolbar=no');

    return newWindow;
  }

  // GET QUERY
  // returns the value of the requested 'variable' from the URL's query
  // string, or returns false if the variable is not found
  function get(variable) {
    var query, vars;

    query = window.location.search.slice(1);
    vars = query.split('&');

    for (var i = 0; i < vars.length; i++) {
      var pair = vars[i].split('=');
      if (pair[0] == variable) {
        return decodeURIComponent(pair[1]);
      }
    }

    return false;
  }

  // AJAX CALL
  // asynchronously connects to a server and requests a file, then processes
  // the responses with the given function
  //
  // url: file to call
  // data: post or get data
  // method: either post or get
  // respond: function to call and pass xmlhttp.responseText as argument to
  function ajaxCall(url, data, method, respond) {
    var xmlhttp;
    data = data || '';
    method = method || 'GET';

    if (window.XMLHttpRequest) { //IE7+, Firefox, Chrome, Opera, Safari
      xmlhttp = new XMLHttpRequest();
    }
    else {//IE6, IE5
      xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
    }

    xmlhttp.onreadystatechange = function() {
      if (xmlhttp.readyState == 4 && xmlhttp.status == 200 && !(!respond)) {
        respond(xmlhttp.responseText);
      }
    }
    xmlhttp.open(method, url, true);
    xmlhttp.setRequestHeader('Content-type','application/x-www-form-urlencoded');
    xmlhttp.send(data);
  }

  // PARSE SECONDS
  // takes seconds and returns a time string in the format of HH:MM:SS
  function parseSeconds(seconds) {
    var min, sec, hr;

    min = Math.floor(seconds / 60);
    sec = (seconds - (min * 60));
    hr = Math.floor(min / 60);
    min = (min - (hr * 60));

    return padNumber(hr, 2) + ':' + padNumber(min, 2) + ':' + padNumber(sec, 2);
  }

  // PAD NUMBER
  // adds zeros in front of the given 'number' until it is of the desired
  // 'length', then returns it as a string
  function padNumber(number, length){
    var num = number + '';

    while (num.length < length) {
      num = '0' + num;
    }

    return num;
  }

  // PARSE TIME
  // parses a time given the time since the UNIX epoch in seconds
  function parseTime(seconds) {
    var sec, date, ret;
    sec = (seconds * 1000);
    date = new Date(sec);

    ret = padNumber(date.getUTCDate(), 2) + '.' + padNumber((date.getUTCMonth() + 1), 2) + '.' + padNumber(date.getUTCFullYear(), 2) + ' @ ' + padNumber(date.getUTCHours(), 2) + ':' + padNumber(date.getUTCMinutes(), 2) + ':' + padNumber(date.getUTCSeconds(), 2);

    return ret;
  }

  // API KEY
  // give the code the API key to use when making AJAX requests for
  // JSON data
  var APIkey = '$key';
";

// Moving JS scripting from analytics.php to here. - ERE20210118
// JS Scripting Text for Action 5 
$JSScripting = "
  // DECLARE VARIABLES
  // these variables hold the handles of bars and counters, as well as
  // their values
  var desktopBar, mobileBar, unknownBar;
  var desktopBarWidth, deskttopBarWidthMax, desktopBarPageViewsWidth, desktopBarPageViewsWidthMax, mobileBarWidth, mobileBarWidthMax, mobileBarPageViewsWidth, mobileBarPageViewsWidthMax, unknownBarWidth, unknownBarWidthMax, unknownBarPageViewsWidth, unknownBarPageViewsWidthMax;
  var totalVisitsEl, totalViewsEl, yearVisitsEl, yearViewsEl, monthVisitsEl, monthViewsEl, weekVisistsEl, weekViewsEl, dayVisitsEl, dayViewsEl;
  var totalVisits, totalVisitsMax, totalViews, totalViewsMax, yearVisits, yearVisitsMax, yearViews, yearViewsMax, monthVisits, monthVisitsMax, monthViews, monthViewsMax, weekVisits, weekVisitsMax, weekViews, weekViewsMax, dayVisits, dayVisitsMax, dayViews, dayViewsMax;
  var currentServerTime;
  var autoRefresh, autoRefreshTimeout;

  // REFRESH
  // updates and initializes a recount of the various bars and counters
  // on the page
  function refresh() {
    reload();

    if(autoRefresh == false) {
      desktopBarWidth = 0;
      desktopBarWidthMax = 0;
      desktopBarPageViewsWidth = 0;
      desktopBarPageViewsWidthMax = 0;
      mobileBarWidth = 0;
      mobileBarWidthMax = 0;
      mobileBarPageViewsWidth = 0;
      mobileBarPageViewsWidthMax = 0;
      unknownBarWidth = 0;
      unknownBarWidthMax = 0;
      unknownBarPageViewsWidth = 0;
      unknownBarPageViewsWidthMax = 0;

      totalVisits = 0;
      totalVisitsMax = 0;
      totalViews = 0;
      totalViewsMax = 0;
      yearVisits = 0;
      yearVisitsMax = 0;
      yearViews = 0;
      yearViewsMax = 0;
      monthVisits = 0;
      monthVisitsMax = 0;
      monthViews = 0;
      monthViewsMax = 0;
      weekVisits = 0;
      weekVisitsMax = 0;
      weekViews = 0;
      weekViewsMax = 0;
      dayVisits = 0;
      dayVisitsMax = 0;
      dayViews = 0;
      dayViewsMax = 0;
    }
    else {
      autoRefreshTimeout = setTimeout(refresh, autoRefreshTime);
    }

    // Change name from analytics.php to analytix.php - ERE20210119
    ajaxCall('analytix.php', 'key=' + APIkey + '&action=3', 'POST', function(resp) {
      var vvObj = JSON.parse(resp);

      currentServerTime = vvObj.time;

      desktopBarWidthMax = ((vvObj.desktopVisits / vvObj.totalVisits) * 100);
      desktopBarPageViewsWidthMax = ((vvObj.desktopViews / vvObj.totalViews) * 100);
      mobileBarWidthMax = ((vvObj.mobileVisits / vvObj.totalVisits) * 100);
      mobileBarPageViewsWidthMax = ((vvObj.mobileViews / vvObj.totalViews) * 100);
      unknownBarWidthMax = ((vvObj.unknownVisits / vvObj.totalVisits) * 100);
      unknownBarPageViewsWidthMax = ((vvObj.unknownViews / vvObj.totalViews) * 100);

      dayViewsMax = vvObj.dayViews;
      dayVisitsMax = vvObj.dayVisits;
      weekViewsMax = vvObj.weekViews;
      weekVisitsMax = vvObj.weekVisits;
      monthViewsMax = vvObj.monthViews;
      monthVisitsMax = vvObj.monthVisits;
      yearViewsMax = vvObj.yearViews;
      yearVisitsMax = vvObj.yearVisits;
      totalViewsMax = vvObj.totalViews;
      totalVisitsMax = vvObj.totalVisits;

      loadBars();
    });
  }

  // SERVER TIMER
  function serverTimer() {
    currentServerTime++;
    document.getElementById('serverTimeCounter').innerHTML = parseTime(currentServerTime);
  }

  // RELOAD
  // updates the visitors table on the page
  function reload() {
    // Change name from analytics.php to analytix.php - ERE20210119
    ajaxCall('analytix.php', 'key=' + APIkey + '&action=1&need=' + selectedType, 'POST', function(resp) {
      var visitorsObj = JSON.parse(resp);
      // console.log('visitorsObj: --->', visitorsObj); // ERE20201119
      var replacementHTML = \"\";
      var page = 1;

      currentServerTime = visitorsObj.time;

      if (paginationEnabled == 1) {
        // removed + 1 from pagination clause - ERE20201113
        replacementHTML += \"(Pages) \";
        let isDoNotWriteLast;
        // Test to see if the number of visitor is exactly divisible by the pagination count. 
        if ( visitorsObj.visitors.length % paginationCount === 0) {
          isDoNotWriteLast = true;
        }
        for (y = 0; y < (Math.floor(visitorsObj.visitors.length / (paginationCount)) + 1); y++) {
          // if the pagination value is a factor of the amount of rows 
          // do not print out the last page marker in this loop ('y' will be equal to the last number in the loop) 
          if ( isDoNotWriteLast && (y === Math.floor(visitorsObj.visitors.length / paginationCount))) { 
            // do nothing
          } else {
            var pageNumber = (y + 1);
            replacementHTML += \"<a href='#page\" + pageNumber + \"'>\" + pageNumber + \"</a> \";
          }
        }
      }
      replacementHTML += \"<table id='visitorTable'>\";
      replacementHTML += \"<tr><th>SiteId</th><th>Timestamp</th><th>IP Address</th><th>Is Mobile?</th><th>Browser</th><th>System</th></th><th>Duration</th><th>Location</th></tr>\"; // Added SiteId to the table header

      if (paginationEnabled == 1) { // Added column 8 for companyStatsId - ERE20201028
        replacementHTML += \"<tr class='page'><td></td><td></td><td></td><td></td><td><h3>Page \" + page + \"</h3></td><td></td><td></td><td></td></tr>\";
      }
      // Add companyStatsId to the table - ERE20201028
      for (i = 0; i < visitorsObj.visitors.length; i++) {
        // Change name from analytics.php to analytix.php - ERE20210119
        replacementHTML += \"<tr onclick=openPopup('analytix.php?action=6&key=$key&vid=\" + visitorsObj.visitors[i].visitorId + \"&siteId=\" + visitorsObj.visitors[i].companyStatsId + \"')><td>\" + visitorsObj.visitors[i].companyStatsId + \"</td><td>\" + visitorsObj.visitors[i].date + \"</td><td>\" + visitorsObj.visitors[i].ipAddress + \"</td><td>\" + visitorsObj.visitors[i].isMobile + \"</td><td>\" + visitorsObj.visitors[i].browser + \"</td><td>\" + visitorsObj.visitors[i].system + \"</td><td>\" + parseSeconds(visitorsObj.visitors[i].duration) + \"</td><td>\" + visitorsObj.visitors[i].location + \"</td></tr>\";
        if (paginationEnabled == 1 && ((i + 1) % (paginationCount)) == 0 && (i < (visitorsObj.visitors.length - 1))) {
          page++;
          replacementHTML += \"<tr class='page'><td></td><td></td><td></td><td></td><td><a name='page\" + page + \"'></a><h3>Page \" + page + \"</h3></td><td></td><td></td><td></td></tr>\"; // Added column for companyStatsId - ERE20201028 
        }
      }

      replacementHTML += \"</table>\";

      if (visitorsObj.visitors.length == 0)
        replacementHTML += \"<div class='center'><i>no visited pages found</i></div>\";

      document.getElementById('visitorBox').innerHTML = \"\";
      document.getElementById('visitorBox').innerHTML = replacementHTML;
    });

    // Change name from analytics.php to analytix.php - ERE20210119
    ajaxCall('analytix.php', 'key=' + APIkey + '&action=2', 'POST', function(resp) {
      var locObj = JSON.parse(resp);

      currentServerTime = locObj.time;

      locObj.location = '';

      if(locObj.city != '')
        locObj.location += locObj.city + ', ';
      if(locObj.region != '')
        locObj.location += locObj.region + ', ';
      if(locObj.country != '' && locObj.region != '' && locObj.city != '')
        locObj.location += '<br>' + locObj.country;
      else if(locObj.country != '')
        locObj.location += locObj.country;

      document.getElementById('geosummary').innerHTML = \"The majority of visitors to this site are from...<br><br><b>\" + locObj.location + \"</b>\";
    });
  }

  // SELECT
  // selects which timeframe is being looked at, updates the css to reflect
  // the change, and calls function to update data appropriately
  function select(type) {
    selectedType = type;

    document.getElementById('today').className = 'overviewBoxInner';
    document.getElementById('thisWeek').className = 'overviewBoxInner';
    document.getElementById('thisMonth').className = 'overviewBoxInner';
    document.getElementById('thisYear').className = 'overviewBoxInner';
    document.getElementById('total').className = 'overviewBoxInner';

    if(selectedType == 0)
      document.getElementById('today').className = 'overviewBoxInner selected';
    else if (selectedType == 1)
      document.getElementById('thisWeek').className = 'overviewBoxInner selected';
    else if (selectedType == 2)
      document.getElementById('thisMonth').className = 'overviewBoxInner selected';
    else if (selectedType == 3)
      document.getElementById('thisYear').className = 'overviewBoxInner selected';
    else if (selectedType == 4)
      document.getElementById('total').className = 'overviewBoxInner selected';

    refresh();
  }

  // ON LOAD
  // retrieve handles for all the bars and counters on the page, then call
  // function to load the data from database
  window.onload = function() {
    desktopBar = document.getElementById(\"desktopBar\");
    mobileBar = document.getElementById(\"mobileBar\");
    unknownBar = document.getElementById(\"unknownBar\");

    totalVisitsEl = document.getElementById(\"totalVisits\");
    totalViewsEl = document.getElementById(\"totalViews\");
    yearVisitsEl = document.getElementById(\"yearVisits\");
    yearViewsEl = document.getElementById(\"yearViews\");
    monthVisitsEl = document.getElementById(\"monthVisits\");
    monthViewsEl = document.getElementById(\"monthViews\");
    weekVisitsEl = document.getElementById(\"weekVisits\");
    weekViewsEl = document.getElementById(\"weekViews\");
    dayVisitsEl = document.getElementById(\"dayVisits\");
    dayViewsEl = document.getElementById(\"dayViews\");

    autoRefresh = false;

    refresh();
    setInterval(serverTimer, 1000);
  };

  // LOAD BARS
  // increase all counters and bar sizes until they are their appropriate
  // value
  function loadBars() {
    if (loadTime > 0) {
      var mustReload = false;

      if(desktopBarWidth < desktopBarWidthMax) {
        desktopBarWidth += 1;
        desktopBar.style.width = desktopBarWidth.toString() + '%';
        desktopBar.innerHTML = desktopBarWidth.toFixed(2) + '%';
        mustReload = true;
      }
      if(desktopBarPageViewsWidth < desktopBarPageViewsWidthMax) {
        desktopBarPageViewsWidth += 1;
        desktopBarPageViews.style.width = desktopBarPageViewsWidth.toString() + '%';
        desktopBarPageViews.innerHTML = desktopBarPageViewsWidth.toFixed(2) + '%';
        mustReload = true;
      }
      if(mobileBarWidth < mobileBarWidthMax) {
        mobileBarWidth += 1;
        mobileBar.style.width = mobileBarWidth.toString() + '%';
        mobileBar.innerHTML = mobileBarWidth.toFixed(2) + '%';
        mustReload = true;
      }
      if(mobileBarPageViewsWidth < mobileBarPageViewsWidthMax) {
        mobileBarPageViewsWidth += 1;
        mobileBarPageViews.style.width = mobileBarPageViewsWidth.toString() + '%';
        mobileBarPageViews.innerHTML = mobileBarPageViewsWidth.toFixed(2) + '%';
        mustReload = true;
      }
      if(unknownBarWidth < unknownBarWidthMax) {
        unknownBarWidth += 1;
        unknownBar.style.width = unknownBarWidth.toString() + '%';
        unknownBar.innerHTML = unknownBarWidth.toFixed(2) + '%';
        mustReload = true;
      }
      if(unknownBarPageViewsWidth < unknownBarPageViewsWidthMax) {
        unknownBarPageViewsWidth += 1;
        unknownBarPageViews.style.width = unknownBarPageViewsWidth.toString() + '%';
        unknownBarPageViews.innerHTML = unknownBarPageViewsWidth.toFixed(2) + '%';
        mustReload = true;
      }

      if(totalVisits < totalVisitsMax) {
        totalVisits += 1;
        totalVisitsEl.innerHTML = totalVisits.toString();
        mustReload = true;
      }
      if(totalViews < totalViewsMax) {
        totalViews += 1;
        totalViewsEl.innerHTML = totalViews.toString();
        mustReload = true;
      }
      if(yearVisits < yearVisitsMax) {
        yearVisits += 1;
        yearVisitsEl.innerHTML = yearVisits.toString();
        mustReload = true;
      }
      if(yearViews < yearViewsMax) {
        yearViews += 1;
        yearViewsEl.innerHTML = yearViews.toString();
        mustReload = true;
      }
      if(monthVisits < monthVisitsMax) {
        monthVisits += 1;
        monthVisitsEl.innerHTML = monthVisits.toString();
        mustReload = true;
      }
      if(monthViews < monthViewsMax) {
        monthViews += 1;
        monthViewsEl.innerHTML = monthViews.toString();
        mustReload = true;
      }
      if(weekVisits < weekVisitsMax) {
        weekVisits += 1;
        weekVisitsEl.innerHTML = weekVisits.toString();
        mustReload = true;
      }
      if(weekViews < weekViewsMax) {
        weekViews += 1;
        weekViewsEl.innerHTML = weekViews.toString();
        mustReload = true;
      }
      if(dayVisits < dayVisitsMax) {
        dayVisits += 1;
        dayVisitsEl.innerHTML = dayVisits.toString();
        mustReload = true;
      }
      if(dayViews < dayViewsMax) {
        dayViews += 1;
        dayViewsEl.innerHTML = dayViews.toString();
        mustReload = true;
      }

      if(mustReload) {
        setTimeout(loadBars, loadTime);
      }
    }
    else {
      desktopBarWidth = desktopBarWidthMax;
      desktopBar.style.width = desktopBarWidth.toString() + '%';
      desktopBar.innerHTML = desktopBarWidth.toFixed(2) + '%';
      desktopBarPageViewsWidth = desktopBarPageViewsWidthMax;
      desktopBarPageViews.style.width = desktopBarPageViewsWidth.toString() + '%';
      desktopBarPageViews.innerHTML = desktopBarPageViewsWidth.toFixed(2) + '%';
      mobileBarWidth = mobileBarWidthMax;
      mobileBar.style.width = mobileBarWidth.toString() + '%';
      mobileBar.innerHTML = mobileBarWidth.toFixed(2) + '%';
      mobileBarPageViewsWidth = mobileBarPageViewsWidthMax;
      mobileBarPageViews.style.width = mobileBarPageViewsWidth.toString() + '%';
      mobileBarPageViews.innerHTML = mobileBarPageViewsWidth.toFixed(2) + '%';
      unknownBarWidth = unknownBarWidthMax;
      unknownBar.style.width = unknownBarWidth.toString() + '%';
      unknownBar.innerHTML = unknownBarWidth.toFixed(2) + '%';
      unknownBarPageViewsWidth = unknownBarPageViewsWidthMax;
      unknownBarPageViews.style.width = unknownBarPageViewsWidth.toString() + '%';
      unknownBarPageViews.innerHTML = unknownBarPageViewsWidth.toFixed(2) + '%';

      totalVisits = totalVisitsMax;
      totalVisitsEl.innerHTML = totalVisits.toString();
      totalViews = totalViewsMax;
      totalViewsEl.innerHTML = totalViews.toString();
      yearVisits = yearVisitsMax;
      yearVisitsEl.innerHTML = yearVisits.toString();
      yearViews = yearViewsMax;
      yearViewsEl.innerHTML = yearViews.toString();
      monthVisits = monthVisitsMax;
      monthVisitsEl.innerHTML = monthVisits.toString();
      monthViews = monthViewsMax;
      monthViewsEl.innerHTML = monthViews.toString();
      weekVisits = weekVisitsMax;
      weekVisitsEl.innerHTML = weekVisits.toString();
      weekViews = weekViewsMax;
      weekViewsEl.innerHTML = weekViews.toString();
      dayVisits = dayVisitsMax;
      dayVisitsEl.innerHTML = dayVisits.toString();
      dayViews = dayViewsMax;
      dayViewsEl.innerHTML = dayViews.toString();
    }
  }
</script>
";
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

?>

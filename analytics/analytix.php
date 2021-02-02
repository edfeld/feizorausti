<?php
/**
 * This is the main file for the Simple Analytics system.
 *
 * This file contains the backend for the analytics system. It updates the
 * database and actually does the dirty work in saving visitors. It is called by
 * analytics.js with AJAX.
 *
 * @package Simple Analytics
 * @author Luke Hollenback <luke@mynamewasluke.com>
 * @version 1.56
 * 
 * History
 * ERE20201005 - Ed Einfeld -   Changes to use the consolidated GRO DB in Admin
 *                              Reporting on emissiontoislam.org
 *                              I created a new stats table on the GRO DB.  All stats records for
 *                              views and visits are being recorded on this consolidated table. 
 *                              Each Company/Property has a unique key called companyStatsId
 *                              This key has been added to the new stats table on the GRO Database.
 *                              All records associated with a specific company have this key.
 *                              This key must match the ID field on the GRO-Sites table for the 
 *                              specific site.
 * ERE20201120 - Ed Einfeld -   Change from Select * to individual columns in queries.
 * ERE20210112 - Ed Einfeld -   Add a server side API that returns a UTC time stamp in Milliseconds.  Code
 *                              changes were added which validate the client's timestamp. if the client's 
 *                              timestamp is within a 10 minute window of our server's time, (-5 to +5) the
 *                              client's timestamp is used, otherwise it is replaced with our server's time-
 *                              stamp. This change prevents errors in the web site analytics reporting.
 * ERE20210118 - Ed Einfeld -   I changed this file name from analytics.php to analytix.php. Move some 
 *                              Large text string variable to the config.php file.
 */


/**
 * SESSION START
 */
if (!isset($_SESSION)) {
    session_start();
}

/**
 * GET API KEY
 */
if (isset($_GET['key']))
    $key = $_GET['key'];
else if(isset($_POST['key']))
    $key = $_POST['key'];
else {
    $key = '';
    $error[] = '0002';
}


/**
 * CONFIGURATION
 */
include('config.php');

/**
 * DOCUMENT OUTPUT CONFIGURATION
 *
 * The variables below are used when parsing out HTML pages, such as the
 * control panel and the visitor details pages. Many values are pulled from
 * the config.php file.
 */

// $jsConfigData was removed from here - ERE20210118


//determine if an extra stylesheet is being used
if($themeSheet != '' && $themeSheet != null)
    $extraStyles = "<link rel='stylesheet' href='$themeSheet'>";
else
    $extraStyles = "";

//create the top, middle, and bottom portions of the HTML for the control panel
//and the visitor summary window
if ($htmlMode == true) {
    $docTop = "
        <!DOCTYPE html>
        <html>

        <head>
            <meta name='viewport' content='width=device-width, height=device-height'>
            <link rel='stylesheet' href='styles.css'>
            $extraStyles
            <link rel='stylesheet' href='dark.css' id='nightMode' disabled>
            <title>$htmlTitle</title>

            <script type='text/javascript'>
                document.getElementById(\"nightMode\").disabled = true;
            </script>
    ";
    $docMiddle = "
        </head>

        <body>
    ";
    $docBottom = "
        </body>

        </html>
    ";
}
else {
    $docTop = "
        <meta name='viewport' content='width=device-width, height=device-height'>
        <link rel='stylesheet' href='styles.css'>
        $extraStyles
        <link rel='stylesheet' href='dark.css' id='nightMode' disabled>
    ";
    $docMiddle = "";
    $docBottom = "";
}


/**
 * DATABASE INITILIZATION
 */
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}


/**
 * START ERROR HANDLING
 */
$error = array();
$errors = array(
    '0000' => 'Unable to perform the requested action due to either a missing API key, a missing requested action, or missing client tracking data.',
    '0001' => 'Visitor ID is missing.',
    '0002' => 'API Key is missing.',
    '0003' => 'There was an error with the SQL query.'
);


/**
 * TIME JSON STRING
 */
$timeJSON = '"time":"' . time() . '"';


/**
 * RETRIEVE REQUESTED ACTION
 */
if (isset($_GET['action']))
    $action = $_GET['action'];
else if (isset($_POST['action']))
    $action = $_POST['action'];
else
    $action = null;


/**
 * ACTION 1: Retrieve List of Unique Visitors
 *
 * If an appropriate key is provided, this answers the request with a JSON
 * object containing and array named "visitors" which contains objects which
 * hold data for each of the unique visitors to the site.
 */
if ($action == 1 && (isset($permission[$key]) && (strpos($permission[$key], '1') !== false || strpos($permission[$key], '*') !== false))) {
  if (isset($_GET['need']))
      $need = $_GET['need'];
  else if(isset($_POST['need']))
      $need = $_POST['need'];
  else
      $need = 4;

  // echo "allSites: $allSites"; // ERE20201119DB
  // Capture the companyStatsId from the current Database - ERE20201006
  if (!$allSites) { // if $allSites is true we are in emissiontoIslam.org. there is no need to get the companyStatsId.
    $companyStatsId = getCompanyStatsId();
  }
  $stmt = $conn->stmt_init();

  if ($need == 0) {//data requested for last day
      $timeAgo = ((time() - 86400) * 1000);
  } else if ($need == 1) {//data requested for last week
      $timeAgo = ((time() - (86400 * 7)) * 1000);
  } else if ($need == 2) {//data requested for last month
      $timeAgo = ((time() - (86400 * 31)) * 1000);
  } else if ($need == 3) {//data requested for last year
      $timeAgo = ((time() - (86400 * 365)) * 1000);
  } else if ($need == 4) {//data requested for all time
      $timeAgo = 0;
  }
  // Add companyStatsId to the query - ERE20201006
  // replace select * with all columns - ERE20201120
  if ($stmt->prepare("SELECT id, companyStatsId, visitId, visitorId, ipAddress, intervalCounter, city, region, country, postal, location, userAgent, windowLocation, specialNote, dateStamp FROM $db_tble
                      WHERE dateStamp>=?
                      AND companyStatsId=?
                      GROUP BY visitorId
                      ORDER BY id DESC")) {
      $stmt->bind_param('si', $timeAgo, $companyStatsId);      
    $stmt->execute();
    $stmt->bind_result($row['id'], $row['companyStatsId'], $row['visitId'], $row['visitorId'], $row['ipAddress'], $row['intervalCounter'], $row['city'], $row['region'], $row['country'], $row['postal'], $row['location'], $row['userAgent'], $row['windowLocation'], $row['specialNote'], $row['dateStamp']);
    $stmt->store_result();
  }
  else {
      $error[] = '0003';
  }

  //begin building JSON data
  $json = '{"visitors":[';

  //add visitors to an array in the JSON data as they are fetched from the database
  $counter = 0;
  if ($stmt->num_rows > 0) {
    // echo("number of rows: ". $stmt->num_rows);
      while($stmt->fetch()) {
          //count the total duration that visitor was visiting
          $stmtDuration = $conn->stmt_init();
          // Add companyStatsId to the query - ERE20201006
          if ($stmtDuration->prepare("SELECT intervalCounter FROM $db_tble
                                      WHERE visitorId=?
                                      AND companyStatsId=?")) {
              $stmtDuration->bind_param('si', $row['visitorId'], $row['companyStatsId']); // ERE20201027 - changed bind from $companyStatsId to $row['companyStatsId']
              $stmtDuration->execute();
              $stmtDuration->bind_result($row_duration['intervalCounter']);
              $stmtDuration->store_result();
          }
          else {
              $error[] = '0003';
          }
              
          $totalDuration = 0;
          if ($stmtDuration->num_rows > 0) {
              while($stmtDuration->fetch()) {
                  $totalDuration += $row_duration['intervalCounter'];
              }
          }
          $stmtDuration->close();

          //parse ip address of visitor
          $ipAddress = $row['ipAddress'];
          if($ipAddress == "::1" || $ipAddress == "127.0.0.1") {
              $ipAddress = "localhost";
          }

          //parse location of visitor
          $location = "";
          if($row['city'] != "" && $row['city'] != null)
              $location .= $row['city'] . ", ";
          if($row['region'] != "" && $row['region'] != null)
              $location .= $row['region'] . ", ";
          if($row['country'] != "" && $row['country'] != null)
              $location .= $row['country'];

          //parse date visit occurred
          $seconds = $row['dateStamp'] / 1000;
          $date = gmdate("d . m . Y", $seconds);

          //detect if visitor is mobile
          $userIsMobile = 'unknown';
          foreach ($userDeviceIsMobileInfo as $system => $type) {
              if (preg_match('/' . $system . '/i', $row['userAgent']))
                  $userIsMobile = $type;
          }

          //detect visitor's browser
          $userBrowser = 'unknown';
          foreach ($userDeviceBrowserInfo as $browser => $name) {
              if (preg_match('/' . $browser . '/i', $row['userAgent']))
                  $userBrowser = $name;
          }

          //detect visitor's operating system
          $userSystem = 'unknown';
          foreach ($userDeviceSystemInfo as $system => $name) {
              if (preg_match('/' . $system . '/i', $row['userAgent']))
                  $userSystem = $name;
          }

          // Add companyStatsId - ERE20201028 
          $json .= '{"companyStatsId":"' . $row['companyStatsId'] . '" , "visitorId":"' . $row['visitorId'] . '" , "date":"' . $date . '" , "ipAddress":"' . $ipAddress . '" , "duration":"' . $totalDuration .
              '" , "location":"' . $location . '" , "city":"' . $row['city'] . '" , "region":"' . $row['region'] .
              '" , "country":"' . $row['country'] . '" , "isMobile":"' . $userIsMobile . '" , "browser":"' . $userBrowser . '" , "system":"' . $userSystem . '"}';

          $counter++;

          if ($counter < $stmt->num_rows)
              $json .= ' , ';
      }
  }

  $stmt->close();

  //add server timestamp data to JSON data
  $json .= '] , ' . $timeJSON . '}';

  echo $json;
}

/**
 * ACTION 2: Retrieve Most Popular Location
 *
 * If an appropriate key is provided, this answers the request with JSON data
 * containing the most popular country, region, and city assuming that all
 * fields are available.
 */
else if ($action == 2 && (isset($permission[$key]) && (strpos($permission[$key], '2') !== false || strpos($permission[$key], '*') !== false))) {
  // Get companyStatsId - ERE20201007
  $companyStatsId = getCompanyStatsId();
  $stmt = $conn->stmt_init();

  //find the most popular country (or none if no countries have been tracked)
    // Add companyStatsId to the query - ERE20201007
    if ($stmt->prepare("SELECT country FROM $db_tble
                        WHERE companyStatsId=?
                        GROUP BY country
                        ORDER BY COUNT(DISTINCT visitorId) DESC
                        LIMIT 1;")) {
        $stmt->bind_param("i", $companyStatsId);
        $stmt->execute();
        $stmt->bind_result($row['country']);
        $stmt->store_result();

        $stmt->fetch();
        $country = $row['country'];
        
    }
    else {
      $error[] = '0003';
    }
  //find the most popular region in the most popular country (or none if no
  //regions in the country have been tracked
  // Add companyStatsId to the query - ERE20201007
    if ($stmt->prepare("SELECT region FROM $db_tble
                        WHERE country='$country'
                        AND companyStatsId=?
                        GROUP BY region
                        ORDER BY COUNT(DISTINCT visitorId) DESC
                        LIMIT 1;")) {
        $stmt->bind_param("i", $companyStatsId);
        $stmt->execute();
        $stmt->bind_result($row['region']);
        $stmt->store_result();

        $stmt->fetch();
        $region = $row['region'];
    }
    else {
      $error[] = '0003';
    }
  //find the most popular city in the most popular region (or none if no
  //cities in the region have been tracked)
  // Add companyStatsId to the query - ERE20201007
    if ($stmt->prepare("SELECT city FROM $db_tble
                        WHERE country='$country' 
                        AND region='$region' 
                        AND companystatsId=?
                        GROUP BY city
                        ORDER BY COUNT(DISTINCT visitorId) DESC
                        LIMIT 1;")) {
        $stmt->bind_param("i", $companyStatsId);
        $stmt->execute();
        $stmt->bind_result($row['city']);
        $stmt->store_result();

        $stmt->fetch();
        $city = $row['city'];
    }
    else {
        $error[] = '0003';
    }
  $stmt->close();

  //build the JSON data
  $json = '{"country":"' . $country . '" , "region":"' . $region . '", "city":"' . $city . '" , ' . $timeJSON . '}';

  echo $json;
}

/**
 * ACTION 3: Retrieve Counts of Views and Visits
 *
 * If an appropriate key is provided, this answers the request with JSON data
 * containing the count of views and visits separated by time periods and
 * devices.
 */
else if ($action == 3 && (isset($permission[$key]) && (strpos($permission[$key], '3') !== false || strpos($permission[$key], '*') !== false))) {
  // Get companyStatsId - ERE20201007
    $companyStatsId = getCompanyStatsId();    
  $stmt = $conn->stmt_init();
  //count visits and views from the past day (a.k.a. the past 24 hours)
  $dayAgo = ((time() - 86400) * 1000);
    // Add companyStatsId to the query - ERE20201007
    if ($stmt->prepare("SELECT COUNT(DISTINCT visitorId) FROM $db_tble
                      WHERE dateStamp>=?
                      AND companyStatsId=?")) {
      $stmt->bind_param('si', $dayAgo, $companyStatsId);
      $stmt->execute();
      $stmt->bind_result($dayVisits);
      $stmt->store_result();

      while ($stmt->fetch()) {
          //do nothing
      }

      if ($dayVisits == null) {
        $dayViews = 0;
      }

    } else {
        $error[] = '0003';
    }
  // count views from the past day (a.k.a 24hrs)
  // Add companyStatsId - ERE20201007
    if ($stmt->prepare("SELECT COUNT(id) FROM $db_tble
                        WHERE dateStamp>=?
                        AND companyStatsId=?")) {
      $stmt->bind_param('si', $dayAgo, $companyStatsId);
      $stmt->execute();
      $stmt->bind_result($dayViews);
      $stmt->store_result();

      while ($stmt->fetch()) {
      //do nothing
      }

      if ($dayViews == null) { 
        $dayViews = 0;
      }

    } else {
      $error[] = '0003';
    }
  //count visits and views from the past week (a.k.a. the past 7 days)
  $weekAgo = ((time() - (86400 * 7)) * 1000);
  // Add companyStatsId to the query - ERE20201007
    if ($stmt->prepare("SELECT COUNT(DISTINCT visitorId) FROM $db_tble
                        WHERE dateStamp>=?
                        AND companyStatsId=?")) {
      $stmt->bind_param('si', $weekAgo, $companyStatsId);
      $stmt->execute();
      $stmt->bind_result($weekVisits);
      $stmt->store_result();

      while ($stmt->fetch()) {
      //do nothing
      }

      if ($weekVisits == null) {
        $weekVisits = 0;
      }
    } else {
      $error[] = '0003';
    }
  

    // Added companyStatsId to the query - ERE20201007
    if ($stmt->prepare("SELECT COUNT(id) FROM $db_tble
                        WHERE dateStamp>=?
                        AND companyStatsId=?")) {
      $stmt->bind_param('si', $weekAgo, $companyStatsId);
      $stmt->execute();
      $stmt->bind_result($weekViews);
      $stmt->store_result();

      while ($stmt->fetch()) {
          //do nothing
      }

      if ($weekViews == null) {
        $weekViews = 0;
      }

    }
    else {
        $error[] = '0003';
    }
  //count visits and views from the past month (a.k.a. the past 31 days)
  // Added companyStatsId to the query - ERE20201007
  $monthAgo = ((time() - (86400 * 31)) * 1000);
  if ($stmt->prepare("SELECT COUNT(DISTINCT visitorId) FROM $db_tble
                      WHERE dateStamp>=?
                      AND companyStatsId=?")) {
    $stmt->bind_param('si', $monthAgo, $companyStatsId);
    $stmt->execute();
    $stmt->bind_result($monthVisits);
    $stmt->store_result();

    while ($stmt->fetch()) {
        //do nothing
    }

    if ($monthVisits == null) {
      $monthVisits = 0;
    }

  } else {
      $error[] = '0003';
  }


  // Count views in the last month
  // Added companyStatsId to the query - ERE20201007
  if ($stmt->prepare("SELECT COUNT(id) FROM $db_tble
                      WHERE dateStamp>=? 
                      AND companyStatsId=?")) {
    $stmt->bind_param('si', $monthAgo, $companyStatsId);
    $stmt->execute();
    $stmt->bind_result($monthViews);
    $stmt->store_result();

    while ($stmt->fetch()) {
    //do nothing
    }

    if ($monthViews == null) {
      $monthViews = 0;
    }

  } else {
    $error[] = '0003';
  }

  //count visits and views from the past year (a.k.a. the past 365 days)
  $yearAgo = ((time() - (86400 * 365)) * 1000);
  if ($stmt->prepare("SELECT COUNT(DISTINCT visitorId) FROM $db_tble
                    WHERE dateStamp>=? 
                    AND companyStatsId=?")) {
    $stmt->bind_param('si', $yearAgo, $companyStatsId);
    $stmt->execute();
    $stmt->bind_result($yearVisits);
    $stmt->store_result();

    while ($stmt->fetch()) {
        //do nothing
    }

    if ($yearVisits == null) {
      $yearVisits = 0;
    }

  } else {
      $error[] = '0003';
  }

  //count views from the past year (a.k.a. the past 365 days)
  // Added companyStatsId to the query - ERE20201007
  if ($stmt->prepare("SELECT COUNT(id) FROM $db_tble
                      WHERE dateStamp>=? 
                      AND companyStatsId=?")) {
    $stmt->bind_param('si', $yearAgo, $companyStatsId);
    $stmt->execute();
    $stmt->bind_result($yearViews);
    $stmt->store_result();

    while ($stmt->fetch()) {
    //do nothing
    }

    if ($yearViews == null) {
      $yearViews = 0;
    }

  } else {      
    $error[] = '0003';
  }
  

  //count total visits and views
  // Added companyStatsId to the query - ERE20201007
  if ($stmt->prepare("SELECT COUNT(DISTINCT visitorId) 
                    FROM $db_tble
                    WHERE companyStatsId=?")) {
    $stmt->bind_param('i', $companyStatsId);
    $stmt->execute();
    $stmt->bind_result($totalVisits);
    $stmt->store_result();

    while ($stmt->fetch()) {
        //do nothing
    }

    if ($totalVisits == null) {
      $totalVisits = 0;
    }

  }  else {
    $error[] = '0003';
  }
  

  // Count the total views
  // added companyStatsId to the query - ERE20201008
  if ($stmt->prepare("SELECT COUNT(id) FROM $db_tble
                      WHERE companyStatsId=?")) {
    $stmt->bind_param('i', $companyStatsId);
    $stmt->execute();
    $stmt->bind_result($totalViews);
    $stmt->store_result();

    while ($stmt->fetch()) {
    //do nothing
    }

    if ($totalViews == null) {
      $totalViews = 0;
    }

  } else {
    $error[] = '0003';
  }
  

  //construct regular expressions for mobile, desktop, and unknown
  $regexpDesktop = '';
  $regexpMobile = '';
  $regexpUnknown = '';
  foreach ($userDeviceIsMobileInfo as $system => $type) {
      if ($type == 'no') {
          if (strlen($regexpDesktop) > 0)
              $regexpDesktop .= '|';
          $regexpDesktop .= '(' . $system . ')';
      }
      else if ($type == 'yes') {
          if (strlen($regexpMobile) > 0)
              $regexpMobile .= '|';
          $regexpMobile .= '(' . $system . ')';
      }
  }
  $regexpUnknown = '(' . $regexpDesktop . ')|(' . $regexpMobile . ')';

  //count desktop visists and views
  // added companyStatsId to the query - ERE20201008
  if ($stmt->prepare("SELECT COUNT(DISTINCT visitorId) FROM $db_tble
                      WHERE userAgent RLIKE ?
                      AND companyStatsId=?")) {
    $stmt->bind_param('si', $regexpDesktop, $companyStatsId);
    $stmt->execute();
    $stmt->bind_result($desktopVisits);
    $stmt->store_result();

    while ($stmt->fetch()) {
    //do nothing
    }

    if ($desktopVisits == null) {
      $desktopVisits = 0;
    }
  } else {
    $error[] = '0003';
  }
  
  // count total desktop views
  // added companyStatsId to the query - ERE20201008
  if ($stmt->prepare("SELECT COUNT(id) FROM $db_tble
                      WHERE userAgent RLIKE ?
                      AND companyStatsId=?")) {
      $stmt->bind_param('si', $regexpDesktop, $companyStatsId);
      $stmt->execute();
      $stmt->bind_result($desktopViews);
      $stmt->store_result();

      while ($stmt->fetch()) {
          //do nothing
      }

      if ($desktopViews == null) {
        $desktopViews = 0;
      }

  } else {
    $error[] = '0003';
  }
  
  //count mobile visists and views
    // added companyStatsId to the query - ERE20201008
  if ($stmt->prepare("SELECT COUNT(DISTINCT visitorId) FROM $db_tble
                    WHERE userAgent RLIKE ?
                    AND companyStatsId=?")) {
    $stmt->bind_param('si', $regexpMobile, $companyStatsId);
    $stmt->execute();
    $stmt->bind_result($mobileVisits);
    $stmt->store_result();

    while ($stmt->fetch()) {
        //do nothing
    }

    if ($mobileVisits == null) {
      $mobileVisits = 0;
    }
  } else {
    $error[] = '0003';
  }
  
  //count mobile views
  // added companyStatsId to the query - ERE20201008
  if ($stmt->prepare("SELECT COUNT(id) FROM $db_tble
                      WHERE userAgent RLIKE ?
                      AND companyStatsId=?")) {
    $stmt->bind_param('si', $regexpMobile, $companyStatsId);
    $stmt->execute();
    $stmt->bind_result($mobileViews);
    $stmt->store_result();

    while ($stmt->fetch()) {
    //do nothing
    }

    if ($mobileViews == null) {
      $mobileViews = 0;
    }

  } else {
    $error[] = '0003';
  }
  
  //count unknown visits and views
  // added companyStatsId to the query - ERE20201008
  if ($stmt->prepare("SELECT COUNT(DISTINCT visitorId) FROM $db_tble
                      WHERE userAgent NOT RLIKE ?
                      AND companyStatsId=?")) {
    $stmt->bind_param('si', $regexpUnknown, $companyStatsId);
    $stmt->execute();
    $stmt->bind_result($unknownVisits);
    $stmt->store_result();

    while ($stmt->fetch()) {
    //do nothing
    }

    if ($unknownVisits == null) {
      $unknownVisits = 0;
    }
  } else {
    $error[] = '0003';
  }
  
  //count unknown views
  // added companyStatsId to the query - ERE20201008
  if ($stmt->prepare("SELECT COUNT(id) FROM $db_tble
                      WHERE userAgent NOT RLIKE ?
                      AND companyStatsId=?")) {
    $stmt->bind_param('si', $regexpUnknown, $companyStatsId);
    $stmt->execute();
    $stmt->bind_result($unknownViews);
    $stmt->store_result();

    while ($stmt->fetch()) {
        //do nothing
    }

    if ($unknownViews == null) {
      $unknownViews = 0;
    }
  } else {
    $error[] = '0003';
  }

  $stmt->close();

  //build JSON data (including the server timestamp at the end)
  // ERE202010008 - Fixed Typo on $monthViews 
  $json = '{"dayVisits":"' . $dayVisits . '" , "dayViews":"' . $dayViews .
      '" , "weekVisits":"' . $weekVisits . '" , "weekViews":"' . $weekViews .
      '" , "monthVisits":"' . $monthVisits . '" , "monthViews":"' . $monthViews . 
      '" , "yearVisits":"' . $yearVisits . '" , "yearViews":"' . $yearViews .
      '" , "totalVisits":"' . $totalVisits . '" , "totalViews":"' . $totalViews .
      '" , "desktopVisits":"' . $desktopVisits . '" , "desktopViews":"' . $desktopViews .
      '" , "mobileVisits":"' . $mobileVisits . '" , "mobileViews":"' . $mobileViews .
      '" , "unknownVisits":"' . $unknownVisits . '" , "unknownViews":"' . $unknownViews .
      '" , ' . $timeJSON . '}';

  echo $json;
}

/**
 * ACTION 4: Retrieve Information and Tracking Data for a Particular Visitor
 *
 * If an appropriate key is provided, this answers the request with JSON data
 * about the particular visitor given by "need", included an array "visited" of
 * the various pages that the visitor visited.
 */
else if ($action == 4 && (isset($permission[$key]) && (strpos($permission[$key], '4') !== false || strpos($permission[$key], '*') !== false))) {
  // Get the companyStatsId value - ERE20201007
    $companyStatsId=getCompanyStatsId();
    if (isset($_GET['need'])) {
      $need = $_GET['need'];
    } elseif(isset($_POST['need'])) {
      $need = $_POST['need'];
    } else {
      $need = -1;
    }

    // echo "$companyStatsId";
    if ($need != -1) {
        $stmt = $conn->stmt_init();
        // Query for a Unique VisitorId - return a single record. 
        // Add companyStatsId column into the query - ERE20201007
        if ($stmt->prepare("SELECT id, companyStatsId, visitId, visitorId, ipAddress, intervalCounter, city, region, country, postal, location, userAgent, windowLocation, specialNote, dateStamp FROM $db_tble
                            WHERE visitorId=?
                            AND companyStatsId=?
                            GROUP BY visitorId
                            ORDER BY COUNT(*) DESC
                            LIMIT 1;")) {
            $stmt->bind_param('si', $need, $companyStatsId);
            $stmt->execute();
            // Add companyStatsId column - ERE20201007
            $stmt->bind_result($row['id'], $row['companyStatsId'], $row['visitId'], $row['visitorId'], $row['ipAddress'], $row['intervalCounter'], $row['city'], $row['region'], $row['country'], $row['postal'], $row['location'], $row['userAgent'], $row['windowLocation'], $row['specialNote'], $row['dateStamp']);
            $stmt->store_result();

            $stmt->fetch();
        }
        else {
          $error[] = '0003';
        }

        if ($stmt->num_rows > 0) {
            //parse ip address of visitor
            $ipAddress = $row['ipAddress'];
            if($ipAddress == "::1" || $ipAddress == "127.0.0.1") {
                $ipAddress = "localhost";
            }

            //parse location of visitor
            $location = "";
            if($row['city'] != "" && $row['city'] != null)
                $location .= $row['city'] . ", ";
            if($row['region'] != "" && $row['region'] != null)
                $location .= $row['region'] . ", ";
            if($row['country'] != "" && $row['country'] != null)
                $location .= $row['country'];

            //parse date of visit
            $seconds = $row['dateStamp'] / 1000;
            $date = gmdate("d . m . Y", $seconds);

            //detect if visitor is mobile
            $userIsMobile = 'unknown';
            foreach ($userDeviceIsMobileInfo as $system => $type) {
                if (preg_match('/' . $system . '/i', $row['userAgent']))
                    $userIsMobile = $type;
            }

            //detect visitor's browser
            $userBrowser = 'unknown';
            foreach ($userDeviceBrowserInfo as $browser => $name) {
                if (preg_match('/' . $browser . '/i', $row['userAgent']))
                    $userBrowser = $name;
            }

            //detect visitor's operating system
            $userSystem = 'unknown';
            foreach ($userDeviceSystemInfo as $system => $name) {
                if (preg_match('/' . $system . '/i', $row['userAgent']))
                    $userSystem = $name;
            }

            //build JSON data
            $json = '{';
            $json .= '"ipAddress":"' . $ipAddress . '" , "city":"' . $row['city'] . '" , "region":"' . $row['region'] . '" , "country":"' . $row['country'] . '" , "postal":"' . $row['postal'] . '" , "coordinates":"' . $row['location'] . '" , "location":"' . $location . '" , "userAgent":"' . $row['userAgent'] . '" , "isMobile":"' . $userIsMobile . '" , "browser":"' . $userBrowser . '" , "system":"' . $userSystem . '" , "dateStamp":"' . $row['dateStamp'] . '" , "date":"' . $date . '" , ';
            $json .= '"visited":[';

                //find and build specific views into JSON data
                // Add companyStatsId into the query - ERE20201007
                // change from 'select *' to select individual columns
                if ($stmt->prepare("SELECT id, companyStatsId, visitId, visitorId, ipAddress, intervalCounter, city, region, country, postal, location, userAgent, windowLocation, specialNote, dateStamp FROM $db_tble
                                    WHERE visitorId=?
                                    AND companyStatsId=?
                                    ORDER BY dateStamp DESC")) {
                    $stmt->bind_param('si', $need, $companyStatsId);
                    $stmt->execute();
                    $stmt->bind_result($row['id'], $row['companyStatsId'], $row['visitId'], $row['visitorId'], $row['ipAddress'], $row['intervalCounter'], $row['city'], $row['region'], $row['country'], $row['postal'], $row['location'], $row['userAgent'], $row['windowLocation'], $row['specialNote'], $row['dateStamp']);
                    $stmt->store_result();
                }
                else
                    $error[] = '0003';

                $totalDuration = 0;

                if ($stmt->num_rows > 0) {
                    $counter = 0;
                    while($stmt->fetch()) {
                        $seconds = $row['dateStamp'] / 1000;
                        $date = gmdate("d . m . Y", $seconds);
                        $time = gmdate("H:i:s", $seconds);

                        $json .= '{"page":"' . $row['windowLocation'] . '" , "dateStamp":"' . $row['dateStamp'] . '" , "date":"' . $date . '" , "time":"' . $time . '" , "duration":"' . $row['intervalCounter'] . '" , "specialNote":"' . $row['specialNote'] . '"}';

                        $totalDuration += $row['intervalCounter'];

                        $counter++;
                        if ($counter < $stmt->num_rows)
                            $json .= ' , ';
                    }
                }

            $json .= ']';
            $json .= ' , "totalDuration":"' . $totalDuration . '"';
            $json .= ' , ' . $timeJSON . '}';

            $stmt->close();
        }
        else {
            $error[] = '0001';
            $json = '';
        }

        echo $json;
    }
}

/**
 * ACTION 5: Output the Included Analytics Statistics Control Panel
 *
 * If an appropriate key is provided, this answers the request with HTML and
 * JavaScript for the Simple Analytics control panel. The actual formating will
 * differ slightly depending on settings specified in *config.php*.
 */
else if ($action == 5 && (isset($permission[$key]) && (strpos($permission[$key], '5') !== false || strpos($permission[$key], '*') !== false))) {
  $enablePaginationChecked = ($paginationEnabled == 1) ? "checked" : "";

  echo "
      $docTop

      <script type='text/javascript'>
        $jsConfigData

        $JSScripting
          
        $docMiddle

        <div id='container'>
          <div id='overview'>
              <div class='overviewBox'>
                  <div class='overviewBoxInner selected' id='today' onclick='select(0);'>
                      <span class='overviewBoxTitle'>Today</span> <br><i>(last 24 hours)</i><br><br>
                      <span id='dayVisits' class='overviewBoxVisit'>0</span><br>
                      <span id='dayViews' class='overviewBoxPageView'>0</span>
                  </div>
              </div>
              <div class='overviewBox'>
                  <div class='overviewBoxInner' id='thisWeek' onclick='select(1);'>
                      <span class='overviewBoxTitle' id='thisWeek'>This Week</span> <br><i>(last 7 days)</i><br><br>
                      <span id='weekVisits' class='overviewBoxVisit'>0</span><br>
                      <span id='weekViews' class='overviewBoxPageView'>0</span>
                  </div>
              </div>
              <div class='overviewBox'>
                  <div class='overviewBoxInner' id='thisMonth' onclick='select(2);'>
                      <span class='overviewBoxTitle'>This Month</span> <br><i>(last 31 days)</i><br><br>
                      <span id='monthVisits' class='overviewBoxVisit'>0</span><br>
                      <span id='monthViews' class='overviewBoxPageView'>0</span>
                  </div>
              </div>
              <div class='overviewBox'>
                  <div class='overviewBoxInner' id='thisYear' onclick='select(3);'>
                      <span class='overviewBoxTitle'>This Year</span> <br><i>(last 365 days)</i><br><br>
                      <span id='yearVisits' class='overviewBoxVisit'>0</span><br>
                      <span id='yearViews' class='overviewBoxPageView'>0</span>
                  </div>
              </div>
              <div class='overviewBox'>
                  <div class='overviewBoxInner' id='total' onclick='select(4);'>
                      <span class='overviewBoxTitle'>Total</span><br><br><br>
                      <span id='totalVisits' class='overviewBoxVisit'>0</span><br>
                      <span id='totalViews' class='overviewBoxPageView'>0</span>
                  </div>
              </div>
          </div>

          <div id='stats' class='left'>
              Desktop<br>
              <div id='desktopBar' class='visitorsBar'>0.00%</div>
              <div id='desktopBarPageViews' class='pageViewsBar'>0.00%</div><br>

              Mobile<br>
              <div id='mobileBar' class='visitorsBar'>0.00%</div>
              <div id='mobileBarPageViews' class='pageViewsBar'>0.00%</div><br>

              Unknown<br>
              <div id='unknownBar' class='visitorsBar'>0.00%</div>
              <div id='unknownBarPageViews' class='pageViewsBar'>0.00%</div><br>
          </div>
          <div id='control' class='right'>
              <div id='colorkey'>
                  <div class='redKeyBox'>%</div>Visitors<br>
                  <div class='blueKeyBox'>%</div>Page Views
              </div>
              <div id='geosummary'>
                  The majority of visitors to this site are from...<br><br>
                  <b>n/a<br>
                  n/a</b>
              </div>
              <div id='refreshButton' onclick='refresh();'>Refresh</div>
              <!-- change file name to analytix.php - ERE20210118 -->
              <div id='printButton' onclick=\"openPopup('analytix.php?action=8&key=$key&need=' + selectedType)\">Print</div>
              <div id='settings'>
                  <input type='checkbox' name='autoRefresh' id='autoRefresh' onclick='refresh(); autoRefresh = !autoRefresh; if (autoRefresh == true) {autoRefreshTimeout = setTimeout(refresh, autoRefreshTime); } else { clearTimeout(autoRefreshTimeout); }'></input><label for='autoRefresh'>Auto-Refresh</label><br>
                  <input type='checkbox' name='enablePagination' id='enablePagination' onclick='paginationEnabled=!paginationEnabled; refresh();' $enablePaginationChecked></input><label for='enablePagination'>Enable Pagination</label><br>
                  <!--<input type='checkbox' name='nightModeSwitch' id='nightModeSwitch' onclick='document.getElementById(\"nightMode\").disabled = !document.getElementById(\"nightMode\").disabled;'></input><label for='nightModeSwitch'>Night Mode</label><br>-->
              </div>
          </div>
          <div class='clear'></div>

          <div id='visitors'>
              <div id='serverTime'>(Current Server UTC Time) <span id='serverTimeCounter'>-- . -- . -- @ --:--:--</span></div>
              <div id='visitorBox'>
                  <table id='visitorTable'>
                      <tr><th>siteId</th><th>Timestamp</th><th>IP Address</th><th>Is Mobile?</th><th>Browser</th><th>System</th></th><th>Location</th></tr>
                  </table>
                  <div class='center'><i>no visitors found</i></div>
              </div>
          </div>
        </div>

      $docBottom
  ";
}

/**
 * ACTION 6: Output the Included Analytics Visitor Statistics Panel
 *
 * If an appropriate key is provided, this answers the request with HTML and
 * JavaScript for the Simple Analytics visitor statistics control panel. The
 * actual formating will differ slightly depending on settings specified in
 * *config.php*.
 */
else if ($action == 6 && (isset($permission[$key]) && (strpos($permission[$key], '6') !== false || strpos($permission[$key], '*') !== false))) {
  if ($googleStaticMaps == true) {
      $mapImageHtml = "<a href='http://maps.google.com/maps' id='mapLink'><img src='' class='mapImage' id='map'></a>";
      $mapLoadBlankJs = "//load a blank map
                        document.getElementById('map').src = 'https://maps.googleapis.com/maps/api/staticmap?size=256x128' + googleStaticMapsAPIkeyString;";
      $mapHandles = "map:document.getElementById('map'),
                     mapLink:document.getElementById('mapLink'),";
      $mapUpdateJs = "span.map.src = \"https://maps.googleapis.com/maps/api/staticmap?center=\" + visObj.coordinates + \"&zoom=7&size=256x128&maptype=roadmap&markers=color:red%7Csize:mid%7C\" + visObj.coordinates + googleStaticMapsAPIkeyString;
                      mapHref = \"http://maps.google.com/maps?q=\" + visObj.coordinates + \"&ll=\" + visObj.coordinates + \"&z=13\";
                      span.mapLink.href = mapHref;";
  }
  else {
      $mapImageHtml = "";
      $mapLoadBlankJs = "";
      $mapHandles = "";
      $mapUpdateJs = "";
  }

  echo "
      $docTop

      <script type='text/javascript'>
          $jsConfigData

          // DECLARE VARIABLES
          var vid;
          var spanIpAddress, spanDate, spanCity, spanRegion, spanCountry, spanPostal, spanUserAgent;
          var currentServerTime;
          var autoRefresh, autoRefreshTimeout;

          // ON LOAD
          // retrieve data for the page using the JSON API and update the appropriate information
          // as necessary
          window.onload = function() {
              //current server time clock
              setInterval(serverTimer, 1000);

              //get visitor id
              vid = get('vid');

              //get site Id
              // siteId = get('siteId');

              $mapLoadBlankJs

              refresh();
          };

          // REFRESH
          function refresh() {
              if (vid != false) { //make sure a vid was given
                  if (autoRefresh == true)
                      autoRefreshTimeout = setTimeout(refresh, autoRefreshTime);

                  //get handles to all spans
                  var span = {
                      $mapHandles
                      ipAddress:document.getElementById('ipAddress'),
                      duration:document.getElementById('duration'),
                      date:document.getElementById('date'),
                      city:document.getElementById('city'),
                      region:document.getElementById('region'),
                      country:document.getElementById('country'),
                      postal:document.getElementById('postal'),
                      coordinates:document.getElementById('coordinates'),
                      userAgent:document.getElementById('userAgent')
                  };

                  // change file name to analytix.php - ERE20210118
                  ajaxCall('analytix.php', 'key=' + APIkey + '&action=4&need=' + vid, 'POST', function(resp) {
                      if (resp != \"error\") {
                          var mapHref;
                          var visObj = JSON.parse(resp);
                          // console.log('=====> Action=4 *****> ==--> visObj: ', visObj); // ERE20201120DB
                          currentServerTime = visObj.time;

                          $mapUpdateJs

                          span.ipAddress.innerHTML = visObj.ipAddress;
                          span.duration.innerHTML = parseSeconds(visObj.totalDuration);
                          span.date.innerHTML = visObj.date;
                          span.city.innerHTML = visObj.city;
                          span.region.innerHTML = visObj.region;
                          span.country.innerHTML = visObj.country;
                          span.postal.innerHTML = visObj.postal;
                          span.coordinates.innerHTML = \"<a href='\" + mapHref + \"'>\" + visObj.coordinates + \"</a>\";
                          span.userAgent.innerHTML = visObj.userAgent;

                          replacementHTML = \"<table id='visitsTable'>\";
                          replacementHTML += \"<tr><th>Page</th><th>Note</th></th><th>Timestamp</th><th>Duration</th></tr>\";

                          for (i = 0; i < visObj.visited.length; i++) {
                              replacementHTML += \"<tr><td>\" + visObj.visited[i].page + \"</td><td>\" + visObj.visited[i].specialNote + \"</td><td>\" + visObj.visited[i].date + \" @ \" + visObj.visited[i].time + \"</td><td>\" + parseSeconds(visObj.visited[i].duration) + \"</td></tr>\";
                          }

                          replacementHTML += \"</table>\";

                          if (visObj.visited.length == 0)
                              replacementHTML += \"<div class='center'><i>no visits found</i></div>\";

                          document.getElementById('visitsBox').innerHTML = \"\";
                          document.getElementById('visitsBox').innerHTML = replacementHTML;
                      }
                  });
              }
          }

          // SERVER TIMER
          function serverTimer() {
              currentServerTime++;
              document.getElementById('serverTimeCounter').innerHTML = parseTime(currentServerTime);
          }
      </script>

      $docMiddle

      <div id='container'>
          <div id='details'>
              <div class='title' id='ipAddress'>visitor not found</div><br>

              $mapImageHtml

              <b class='keyItem'>Total Duration:</b> <span id='duration'>n/a</span><br>
              <b class='keyItem'>Date Accessed:</b> <span id='date'>n/a</span><br><br>

              <b class='keyItem'>City:</b> <span id='city'>n/a</span><br>
              <b class='keyItem'>Region:</b> <span id='region'>n/a</span><br>
              <b class='keyItem'>Country:</b> <span id='country'>n/a</span><br>
              <b class='keyItem'>Postal:</b> <span id='postal'>n/a</span><br>
              <b class='keyItem'>Coordinates:</b> <span id='coordinates'>n/a</span><br><br>

              <b class='keyItem'>User Agent:</b> <span id='userAgent'>n/a</span>
          </div>

          <div id='visits'>
              <div id='serverTime'>(Current Server UTC Time) <span id='serverTimeCounter'>-- . -- . -- @ --:--:--</span></div>
              <div id='visitsBox'>
                  <table id='visitsTable'>
                      <tr><th>Page</th><th>Note</th></th><th>Timestamp</th><th>Duration</th></tr>
                  </table>
                  <div class='center'><i>no visits found</i></div>
              </div>
          </div>

          <div id='detailsControlBox'>
              <div id='refreshDetailsControlBox'>
                  <div id='refreshButton' onclick='refresh();'>Refresh</div>
              </div>
              <div id='settingsDetailsControlBox'>
                  <input type='checkbox' name='autoRefresh' id='autoRefresh' onclick='refresh(); autoRefresh = !autoRefresh; if (autoRefresh == true) {autoRefreshTimeout = setTimeout(refresh, autoRefreshTime); } else { clearTimeout(autoRefreshTimeout); }'></input><label for='autoRefresh'>Auto-Refresh</label>
              </div>
              <div class='clear'></div>
          </div>
      </div>

      $docBottom
  ";
}

/**
 * ACTION 7: Output the tracking JavaScript to be linked to on every desired page
 *
 * This answers the request with the JavaScript that allows for a visitor to a
 * page to be tracked. It should be called as "text/javascript" with an HTML
 * <script> tag.
 */
else if ($action == 7) {
  // Capture the unique Stats ID with a function - ERE20201006
  $siteID = getCompanyStatsId();
  echo "
      $jsConfigData
      var companyStatsId = $siteID; // ERE20201006 - add a unique stats ID for the current company/property

      // INITIALIZATION
      var intervalCounter, msTimestamp, specialNote, windowLocation, simpleAnalytics_data;

      window.onload = function() {
          intervalCounter = (0 - updateInterval);
          msTimestamp = Date.now();
          let saveClientTimestamp = msTimestamp; // ERE20210118
          // change file name to analytix.php - ERE20210118
          // Insert a call to the server for UTC time. Check to see if the user's system time is correct  - ERE20210112
          ajaxCall(analyticsFolder + 'analytix.php', 
            'action=9',
            'POST',
            function(resp) {
              let server = JSON.parse(resp);
              let serverTime = server.UTCTimeStamp;
              // Check that msTimestamp falls within a day's boundary of X seconds prior and X seconds in the future
              if (isInDateRange(serverTime, msTimestamp, 300)) { // Range is currently set to 5 minutes (300 seconds) // ERE20210118
                // Do nothing
              } else {
                // console.log('***> server time is being used for the date stamp. the bad stamp is: ', msTimestamp); 
                msTimestamp = serverTime;
              }
              let diff = serverTime - saveClientTimestamp 
              // console.log('*-*-*-*-*-> jsTimestamp: ' + saveClientTimestamp + ' servertime: ' + serverTime + ' <-*-*-*-*-*-> diff: ' + diff + ' milliseconds');
              specialNote = document.getElementById(scriptElementId).getAttribute('note');
              windowLocation = escape(window.location.href);
              simpleAnalytics_data = new Array();
              // console.log('-+-+-+-+-+>>>> companyStatsId: ', companyStatsId); // ERE20201118
              update();
              setInterval(update, (updateInterval * 1000));
            }
          );
      };

      // Date range check on the client's time stamp.  If the client's timestamp is outside of the specified range
      // plus or minus, then use the Server timestamp instead.
      function isInDateRange(serverTime, clientTime, timeRangeInSec) {
        const oneDayPrior = serverTime - (timeRangeInSec * 1000);
        const oneDayFuture = serverTime + (timeRangeInSec * 1000);
        if ( oneDayPrior <= clientTime && clientTime <= oneDayFuture) {
          // console.log('*****>*****> The client\'s time stamp is WITHIN range');
          return true;
        }
        // console.log('*****>>>>>>');
        // console.log('*****>*****> The client\'s time stamp is OUT of range <<<<******');
        // console.log('*****>>>>>>');
        return false; 
      }

      // UPDATE
      function update() {
          intervalCounter += updateInterval;
          // ERE20201006 - CompanyStatsId was added to the Ajax call
          
          // change file name to analytix.php - ERE20210118
          ajaxCall(analyticsFolder + 'analytix.php',
                  'windowLocation=' + windowLocation + '&msTimestamp=' + msTimestamp + '&specialNote=' + specialNote + '&intervalCounter=' + intervalCounter + '&companyStatsId=' + companyStatsId,
                  'POST',
                  function(resp) {
                      simpleAnalytics_data = JSON.parse(resp);
                      //alert(simpleAnalytics_data.visitorStamp);
                      //alert(resp);
                      // console.log('****====>>>> update() response: ', resp); // ERE20201118
                  }
              );
      }
  ";
}

/**
 * ACTION 8: Output the Print Page
 *
 * Assuming a valid API key was provided, this outputs a simply, printable HTML
 * document containing a list of visitors in the time frame specified by "need".
 */
else if ($action == 8 && (isset($permission[$key]) && (strpos($permission[$key], '8') !== false || strpos($permission[$key], '*') !== false))) {
  if (!is_numeric($_GET['need']) || !isset($_GET['need']))
      $neededContent = 0;
  else
      $neededContent = $_GET['need'];

  switch ($neededContent) {
      case 0:
          $neededString = "Today";
          break;
      case 1:
          $neededString = "This Week";
          break;
      case 2:
          $neededString = "This Month";
          break;
      case 3:
          $neededString = "This Year";
          break;
      case 4:
          $neededString = "Total";
          break;
      default:
          $neededString = "Today";
          break;
  }

  echo "
      <?DOCTYPE html>
      <html>

      <head>
          <title>Visitor Report for $neededString</title>

          <style type='text/css'>
              body {
                  font-family: sans-serif;
                  font-size: 0.75em;
              }
              a {
                  color: #555555;
                  text-decoration: none;
              }
              a:hover {
                  color: #999999;
                  text-decoration: none;
              }

              #increaseTextSize {

              }
              #decreaseTextSize {
                  font-size: 0.75em;
              }

              #visitorTable {
                  width: 100%;
                  border-collapse: collapse;
                  font-size: 1.0em;
              }
              #visitorTable tr {
                  border-bottom: 1px solid #000000;
              }
              #visitorTable td {
                  padding: .5%;
                  text-align: center;

              }

              #header {
                  margin: 0% 0% 3% 0%;
                  text-align: center;
              }
              #title {
                  font-weight: bold;
                  font-size: 2em;
              }
          </style>

          <script type='text/javascript'>
              $jsConfigData

              // INITIALIZE VARIABLES
              var currentServerTime;

              // SERVER TIMER
              function serverTimer() {
                  currentServerTime++;
                  document.getElementById('serverTimeCounter').innerHTML = parseTime(currentServerTime);
              }

              // ON LOAD
              // retrieve handles for all the bars and counters on the page, then call
              // function to load the data from database
              // change file name to analytix.php - ERE20210118
              window.onload = function() {
                  ajaxCall('analytix.php', 'key=' + APIkey + '&action=1&need=$neededContent', 'POST', function(resp) {
                      var visitorsObj = JSON.parse(resp);
                      var replacementHTML = \"\";

                      currentServerTime = visitorsObj.time;
                      serverTimer();

                      replacementHTML = \"<table id='visitorTable'>\";
                      replacementHTML += \"<tr><th>Timestamp</th><th>IP Address</th><th>Is Mobile?</th><th>Browser</th><th>System</th></th><th>Duration</th><th>Location</th></tr>\";

                      for (i = 0; i < visitorsObj.visitors.length; i++) {
                          replacementHTML += \"<tr><td>\" + visitorsObj.visitors[i].date + \"</td><td>\" + visitorsObj.visitors[i].ipAddress + \"</td><td>\" + visitorsObj.visitors[i].isMobile + \"</td><td>\" + visitorsObj.visitors[i].browser + \"</td><td>\" + visitorsObj.visitors[i].system + \"</td><td>\" + parseSeconds(visitorsObj.visitors[i].duration) + \"</td><td>\" + visitorsObj.visitors[i].location + \"</td></tr>\";
                      }

                      replacementHTML += \"</table>\";

                      if (visitorsObj.visitors.length == 0)
                          replacementHTML += \"<div class='center'><i>no visited pages found</i></div>\";

                      document.getElementById('visitorBox').innerHTML = \"\";
                      document.getElementById('visitorBox').innerHTML = replacementHTML;
                  });
              };

              // FONT SIZE
              // will change the font size of the body by a given multiplier (usually
              // 1 or -1 is perfect)
              function resizeBodyText(multiplier) {
                  if (document.body.style.fontSize == \"\") {
                      document.body.style.fontSize = \"0.75em\";
                  }
                  document.body.style.fontSize = (parseFloat(document.body.style.fontSize) + (multiplier * 0.2)) + \"em\";
              }
          </script>
      </head>

      <body>
          <div id='container'>
              <div id='header'>
                  <div id='title'>Visitor Report for $neededString</div>
                  <div id='information'><span id='serverTimeCounter'>-- . -- . -- @ --:--:--</span> UTC</div>
                  <div id='actions'><a href='#' onclick='window.print();'>Print Report</a> <a href='#' id='increaseTextSize' onclick='resizeBodyText(1)'>A</a> <a href='#' id='decreaseTextSize' onclick='resizeBodyText(-1)'>A</a></div>
              </div>

              <div id='visitors'>
                  <div id='visitorBox'>
                      <table id='visitorTable'>
                          <tr><th>Timestamp</th><th>IP Address</th><th>Is Mobile?</th><th>Browser</th><th>System</th></th><th>Location</th></tr>
                      </table>
                      <div class='center'><i>no visitors found</i></div>
                  </div>
              </div>
          </div>
      </body>

      </html>
  ";
}
// ACTION 9 
// Add a timeStamp from the server side.  Return UTC date in Milliseconds - ERE20210112
else if ($action == 9) {
  $time_arr = array("UTCTimeStamp" => round(microtime(true) * 1000));

  // set response code - 200 OK
  http_response_code(200);
 
  // make it json format
  echo json_encode($time_arr);
}

/**
 * ACTION NULL: Track a Visitor
 *
 * This records tracking data for a visitor to the website into the database
 * assuming appropriate information has been posted with the request.
 */
// Add companyStatsId to the isset test
else if (isset($_POST['windowLocation']) && isset($_POST['msTimestamp']) && isset($_POST['specialNote']) && isset($_POST['intervalCounter']) && isset($_POST['companyStatsId'])) {
  /**
   * SESSION CONFIGURATION
   */
  $debug = null;
  if (!isset($_SESSION['SIMPLY_ANALYTICS']))
      $_SESSION['SIMPLY_ANALYTICS'] = array();

  //if the session has expired (the visitor has not been tracked for the set amount of time),
  //reset everything; this forces the creation of a new uniqueIdentifier which will affect the visitorId
  if (isset($_SESSION['SIMPLY_ANALYTICS']['LAST_ACTIVITY']) && (time() - $_SESSION['SIMPLY_ANALYTICS']['LAST_ACTIVITY'] > $sessionTime)) {
      $debug = time() - $_SESSION['SIMPLY_ANALYTICS']['LAST_ACTIVITY'];

      //unset everything, INCLUDING the uniqueIdentifier
      unset(
          $_SESSION['SIMPLY_ANALYTICS']['uniqueIdentifier'],
          $_SESSION['SIMPLY_ANALYTICS']['randomNumber'],
          $_SESSION['SIMPLY_ANALYTICS']['analytics_city'],
          $_SESSION['SIMPLY_ANALYTICS']['analytics_region'],
          $_SESSION['SIMPLY_ANALYTICS']['analytics_country'],
          $_SESSION['SIMPLY_ANALYTICS']['analytics_postal'],
          $_SESSION['SIMPLY_ANALYTICS']['analytics_location'],
          $_SESSION['SIMPLY_ANALYTICS']['analytics_userAgent']
      );
  }
  $_SESSION['SIMPLY_ANALYTICS']['LAST_ACTIVITY'] = time();

  //every set amount of time, reset all variables except the uniqueIdentifier; this forces the update of data such as geoip metadata
  //and thus ensures that, at least every set amount of time, metadata is updated and accurate
  if (!isset($_SESSION['SIMPLY_ANALYTICS']['CREATED'])) {
      $_SESSION['SIMPLY_ANALYTICS']['CREATED'] = time();
  }
  else if (time() - $_SESSION['SIMPLY_ANALYTICS']['CREATED'] > $sessionTime) {
      //unset everything, EXCEPT the uniqueIdentifier
      unset(
          $_SESSION['SIMPLY_ANALYTICS']['randomNumber'],
          $_SESSION['SIMPLY_ANALYTICS']['analytics_city'],
          $_SESSION['SIMPLY_ANALYTICS']['analytics_region'],
          $_SESSION['SIMPLY_ANALYTICS']['analytics_country'],
          $_SESSION['SIMPLY_ANALYTICS']['analytics_postal'],
          $_SESSION['SIMPLY_ANALYTICS']['analytics_location'],
          $_SESSION['SIMPLY_ANALYTICS']['analytics_userAgent']
      );

      $_SESSION['SIMPLY_ANALYTICS']['CREATED'] = time();
  }

  $newID = 0;
  if (!isset($_SESSION['SIMPLY_ANALYTICS']['uniqueIdentifier'])) {
      $_SESSION['SIMPLY_ANALYTICS']['uniqueIdentifier'] = genranstr(mt_rand(128, 512));
  }
  if (!isset($_SESSION['SIMPLY_ANALYTICS']['randomNumber']))
      $_SESSION['SIMPLY_ANALYTICS']['randomNumber'] = mt_rand(0, 1000000000000000); //this random number will change on every reset, not just on expiration resets


  //retrive post data
  $windowLocation = $_POST['windowLocation'];
  $msTimestamp = $_POST['msTimestamp'];
  $specialNote = $_POST['specialNote'];
  $intervalCounter = $_POST['intervalCounter'];
  $companyStatsId = $_POST['companyStatsId'];

  //other variables
  $dayTimestamp = gmdate("dmY");
  $outputInfo = "";

  //IP address
  if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
      $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR']; //try to sniff out if client is behind a proxy
  else
      $ipAddress = $_SERVER['REMOTE_ADDR'];
  if ($ipAddress == '127.0.0.1' || $ipAddress == 'localhost' || $ipAddress == '') //if on a development server, don't use an ipAddress
      $ipAddress = '';

  //geoIP and useragent
  if (!isset($_SESSION['SIMPLY_ANALYTICS']['analytics_userAgent'])) {
      $curl = curl_init();
      $url = preg_replace('/\[ipaddr\]/', $ipAddress, $ipinfoRequestString);
      curl_setopt_array($curl, array(
          CURLOPT_RETURNTRANSFER => 1,
          CURLOPT_URL => $url,
          CURLOPT_USERAGENT => 'Analytics'
      ));

      $resp = curl_exec($curl);
      curl_close($curl);

      $geoip = json_decode($resp);

      $city = $geoip->{$ipinfoRequestResp['city']};
      $region = $geoip->{$ipinfoRequestResp['region']};
      $country = $geoip->{$ipinfoRequestResp['country']};
      $postal = $geoip->{$ipinfoRequestResp['postal']};
      if ($ipinfoRequestResp['location'] == null) {
          $location = $geoip->{$ipinfoRequestResp['latitude']} . ',' . $geoip->{$ipinfoRequestResp['longitude']};
      }
      else {
          $location = $geoip->{$ipinfoRequestResp['location']};
      }

      $userAgent = $_SERVER['HTTP_USER_AGENT'];

      $_SESSION['SIMPLY_ANALYTICS']['analytics_city'] = $city;
      $_SESSION['SIMPLY_ANALYTICS']['analytics_region'] = $region;
      $_SESSION['SIMPLY_ANALYTICS']['analytics_country'] = $country;
      $_SESSION['SIMPLY_ANALYTICS']['analytics_postal'] = $postal;
      $_SESSION['SIMPLY_ANALYTICS']['analytics_location'] = $location;
      $_SESSION['SIMPLY_ANALYTICS']['analytics_userAgent'] = $userAgent;

      $sessionCheck = 0;
  }
  else {
      $city = $_SESSION['SIMPLY_ANALYTICS']['analytics_city'];
      $region = $_SESSION['SIMPLY_ANALYTICS']['analytics_region'];
      $country = $_SESSION['SIMPLY_ANALYTICS']['analytics_country'];
      $postal = $_SESSION['SIMPLY_ANALYTICS']['analytics_postal'];
      $location = $_SESSION['SIMPLY_ANALYTICS']['analytics_location'];
      $userAgent = $_SESSION['SIMPLY_ANALYTICS']['analytics_userAgent'];

      $sessionCheck = 1;
  }

    /**
     * VISIT STAMP
     *
     * This will give the current page that is being visited by a user a unique
     * ID number to identify it when updating the length of time the user has
     * been on the page.
     */
    $visitStamp = $msTimestamp + hashcrypt($windowLocation) + hashcrypt($ipAddress);

    /**
     * VISITOR STAMP
     *
     * This will give the visitor a unique ID that will not change from page to
     * page, but will allow them to be recorded as a unique visitor once per
     * set amount of time.
     */
    $visitorStamp = hashcrypt($dayTimestamp) + hashcrypt($ipAddress) + hashcrypt($userAgent) + hashcrypt($_SESSION['SIMPLY_ANALYTICS']['uniqueIdentifier']);

    /**
     * LOG VISIT
     *
     * This checks to make sure that the user agent of the visitor does not
     * contain a filter string specified in the config file. If it does not,
     * the visit is logged into the database.
     */
    $logVisit = true;

    foreach ($userAgentFilters as $filterString) {
        if (preg_match('/' . $filterString . '/i', $userAgent))
            $logVisit = false;
    }

    if ($logVisit) {
        $stmt = $conn->stmt_init();
        // Add companyStatsId to the query -
        if ($stmt->prepare("SELECT COUNT(id) FROM $db_tble
                            WHERE visitId=? AND companyStatsId=?")) {
            $stmt->bind_param('si', $visitStamp, $companyStatsId);
            $stmt->execute();
            $stmt->bind_result($numberOfVisits);
            $stmt->store_result();

            while ($stmt->fetch()) {
                //do nothing
            }

            if ($numberOfVisits == null)
                $numberOfVisits = 0;
        }
        else
            $error[] = '0003';

        if ($numberOfVisits > 0) {
            // Add companyStatsId to the query - ERE20201007
            if ($stmt->prepare("UPDATE $db_tble
                                SET intervalCounter=?
                                WHERE visitId=? AND companyStatsId=?")) {
                $stmt->bind_param('ssi', $intervalCounter, $visitStamp, $companyStatsId);

                if ($stmt->execute() === TRUE) {
                    $outputInfo = "Record updated successfully.";
                }
                else {
                    $outputInfo = "Error updating record.";
                }
            }
            else
                $error[] = '0003';
        }
        else {
          // Add companyStatsId to the query - ERE20201006
            if ($stmt->prepare("INSERT INTO $db_tble
                                (visitId, visitorId, ipAddress, intervalCounter, city, region, country, postal, location, userAgent, windowLocation, specialNote, dateStamp, companyStatsId)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
                $stmt->bind_param('sssssssssssssi', $visitStamp, $visitorStamp, $ipAddress, $intervalCounter, $city, $region, $country, $postal, $location, $userAgent, $windowLocation, $specialNote, $msTimestamp, $companyStatsId);

                if ($stmt->execute() === TRUE) {
                    $outputInfo = "New record created successfully.";
                }
                else {
                    $outputInfo = "Error creating new record.";
                }
            }
            else
                $error[] = '0003';
        }

        $stmt->close();
    }

    /**
     * OUTPUT JSON OBJECT OF DATA
     */
    // Added companyStatsId to the JSON output - ERE20201006
    $output = array('outputInfo' => $outputInfo,
                    'uniqueIdentifier' => $_SESSION['SIMPLY_ANALYTICS']['uniqueIdentifier'],
                    'randomNumber' => $sessionCheck,
                    'visitStamp' => $visitStamp,
                    'visitorStamp' => $visitorStamp,
                    'ipAddress' => $ipAddress,
                    'intervalCounter' => $intervalCounter,
                    'city' => $city,
                    'region' => $region,
                    'country' => $country,
                    'postal' => $postal,
                    'location' => $location,
                    'userAgent' => $userAgent,
                    'windowLocation' => $windowLocation,
                    'specialNote' => $specialNote,
                    'timeStamp' => $msTimestamp,
                    'companyStatsId' => $companyStatsId,
                    'debug' => $debug
                );
    echo json_encode($output);
}
else {
    $error[] = '0000';
}


/**
 * OUTPUT ERRORS IN JSON OBJECT
 */
if ($showErrors && count($error) > 0) {
  echo '{"errors":[';
  for ($i = 0; $i < count($error); $i++) {
      echo '{"code":"' . $error[$i] . '" , "description":"' . $errors[$error[$i]] . '"}';
      if ($i < (count($error) - 1))
          echo ' , ';
  }
  echo ']}';
}


/**
* CLOSE DATABASE CONNECTION
*/
$conn->close();


/**
 * convert strings into integers
 *
 * Hashcrypt translates each ASCII character of a given string into its
 * appropriate character code, then proceeds to add up the character codes.
 *
 * @param string the string to be converted
 * @return integer the sum of the character codes of the characters in the string
 */
function hashcrypt($str) {
  $addup = 0;
  for ($i = 0; $i < strlen($str); $i++) {
      $addup += ord($str[$i]);
  }

  return $addup;
}


/**
 * generate a random string
 *
 * GenRanStr creates a random string of a specified length using characters from
 * a given character set. Characters are replaced and therefore may appear
 * multiple times in a given return value.
 *
 * @param integer length of the string to be generated
 * @param string [DEFAULT: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz01234567890`~!@#$%^&*()-_=+[{}]\|;:,<.>/?"] a string of characters to use as a charset for the generated string
 * @return string the randomly generated string
 */
function genranstr($len, $charset = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz01234567890`~!@#$%^&*()-_=+[{}]\|;:,<.>/?") {
  $str = "";
  $cnt = strlen($charset);
  while ($len--) {
      $str .= $charset[mt_rand(0, ($cnt - 1))];
  }

  return $str;
}

// ERE20201006 - Add a function which gets the company stats key for use with the stats table.  This is a unique id for this specific company/property.  
// function getCompanyStatsId() {
//   include_once("../config/connectCurrProperty.php");
//   return $companyStatsId;
// }

// ERE20201012 - Refactor getCompanyStatsId to use existing connection module
function getCompanyStatsId() {
  include_once("../config/connection.php"); // ERE20201120
  if ($hostname === null) {
    return 0;
  }
  $db_co_table = "companyDetails";
  $query_getCoStatsId = $conCreative->prepare("SELECT companyStatsId FROM $db_co_table WHERE companyDetailsID = 1");
  $query_getCoStatsId->execute();
  $row_getCoStatsID = $query_getCoStatsId->fetch();
  $totalRows_getCoStatId = $query_getCoStatsId->rowCount();
  if ($totalRows_getCoStatId > 0) {
    return $row_getCoStatsID['companyStatsId'];
  } else {
    return "Empty Value for companyStatsId";
  }
  unset($conCreative); // I think this closes the connection - ERE20201012
  
}

?>

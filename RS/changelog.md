GRO Custom Content Management System Change Log
===============================================
Below lists changes that have been made to the Content Management System per
each version. This list is to help aid in the process of upgrading

---

###Version 1.5###
- cleaned up code in most files in the *admin/* directory
    - consolidated and renamed includes
    - added appropriate tabs to make the code more elegant
- updated Simple Analytics to the latest version in order to allow for the
  duration of a visit to be shown on the main visitor list
- upgraded the comments administration system
    - added *isTagged* column to the *comments* table (*)
    - split *admin/comments.php* into *admin/comments.php* and
      *admin/flagged.php* for ease of use
    - updated the look and feel of all comments administration pages to
      appropriately use Bootstrap
    - added Bootstrap tokens to the admin menu to show when new comments have
      been added or flagged
    - created *admin/explore.php* to allow for the searching and archiving of
      comments
- commented out the right-to-left styles in *admin/Assets/css/mycontent.css* to
  make it easier to adjust the site for right-to-left languages
- removed the timezone adjustment in all comments pages (admin and front-end)
    - created new *parseDate($date)* function in *includes/inc_various.php*
    - deleted the request to *http://www.telize.com/geoip/$ip* in
      *includes/inc_various.php*
- canceled errors and warnings (by adding an *@* symbol) to the
  `@$dom->loadHTML($html_content);` in the *translate_dom()* function in
  *includes/language_func.php*
- updated various comments throughout the content management system to use the
  PHPDoc format
- fixed various errors in *admin/index.php*
    - added query to the *configurations* table to fix an error in setting the
      *$sitedir* variable to the correct value
    - put quotes around the date string for the footer copyright
- changed all image links in database to point to *local.gro.com* rather than
  *towardlife.com* (**)
- fixed glitch in *includes/head.php* that was not setting *$metatitle*
  appropriately if on a parent page
- added `$conCreative->query("SET NAMES utf8");` to *config/connection.php* to
  ensure proper encoding of foreign characters
- cleaned up *admin/include.php*
- created *analytics/custom.css* to make the Stats page in the admin panel match
  the rest of the admin panel's colors
- added text alignment classes to *admin/Assets/css/styles.css*
- added *inOutTable* class to *admin/Assets/css/styles.css* and changed the
  elements in *admin/footer.php* and *language.php* to use it
- increased height of iFrame in *admin/stats.php*
- added *includes/version.php*, included it in *includes/include.php*, and
  echo'd the `$versionString` in *includes/footer.php* to help keep track of
  versions
- added *admin/version.php*, included it in *admin/include.php*, and echo'd the
  `$versionString` in *admin/foot_include.php*

---

<sub>(\*) symbolize that the change will be automatically upgraded by running
the appropriate scripts in the */install* folder</sub>

<sub>(\*\*) change only matters for development environment</sub>

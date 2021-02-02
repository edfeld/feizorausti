<?php
/**
header.php
----------
This file creates the navigation menu at the top of every page. It pulls pages,
child pages, and sub-child pages from the database and parses them into the
proper html.
**/
/**
 * History
 * ERE20200630 - Ed Einfeld - Added Hamburger menue
 * ERE20201020 - Ed Einfeld - Bug fix.  I added isset() to one line to prevent access to an Undefined value.   
 */


echo "<div id=\"header\">";
	echo "<div id=\"header-inner\" class=\"white\">";
    // ERE20200630 - Hamburger Nav added
    echo "<nav id=\"hamnav\">";
      echo "<!-- [THE HAMBURGER] -->";
      echo "<label for=\"hamburger\">&#9776;</label>";
      echo "<input type=\"checkbox\" id=\"hamburger\"/>";
      // top-menu
      echo "<ul class=\"top-menu top-menu-anim-slide top-menu-response-to-stack\">";
        //get menu information
        $query_getMainPage = $conCreative->query("SELECT pageID, pageTitle, pageisHome, pageBelongs, pageType, pagefriendlyURL FROM pages WHERE pageBelongs=0 AND pageType!=3 ORDER BY pagetabPosition ASC");
        $totalRows_getMainPage=$query_getMainPage->rowCount();

        $counter = 1;
        if ($totalRows_getMainPage > 0) {
          while ($row_getMainPage = $query_getMainPage->fetch(PDO::FETCH_ASSOC)) {
            if ($row_getMainPage['pageType'] != 4){//
              //check what is the type of the page...if its basic or article and generate links
              switch ($row_getMainPage['pageType']) {
                // Basic page
                case "1":
                  $mainLink = "page.php?pid=".$row_getMainPage['pagefriendlyURL'];
                  break;
                // Article
                case "2":
                  $mainLink = "articles.php?pid=".$row_getMainPage['pagefriendlyURL'];
                  break;
                case "4":
                  $mainLink = $row_getMainPage['pageLink']."?pid=".$row_getMainPage['pagefriendlyURL'];
                  break;
              }

              //check if the page is set to be the Home page
              if ($row_getMainPage['pageisHome'] == 1){
                $mainLink = "index.php";
              }
              $mainLink = $sitepath . "/" . $mainLink;

              //check if page has child pages
              $currentMenuPage = $row_getMainPage['pageID'];
              $query_getChilds = $conCreative->prepare("SELECT pageID, pageTitle, pageisHome, pageBelongs, pageType, pagefriendlyURL FROM pages WHERE pageBelongs = :currentMenuPage");
              $query_getChilds->bindParam(':currentMenuPage', $currentMenuPage);
              $query_getChilds->execute();
              $totalRows_getChilds = $query_getChilds->rowCount();

              if ($totalRows_getChilds <= 0) { // ERE20201020 - Add isset() to handle null value below
                if (isset($currentPage) && $currentPage == $row_getMainPage['pageID'])
                  $dynamicInsert = "class=\"active\"";
                else
                  $dynamicInsert = "";

                echo "<li $dynamicInsert><a href=\"$mainLink\">" . $row_getMainPage['pageTitle'] . "</a>";
              }
              else { //if page has child pages generate a drop down menu
                $query_getSubPage = $conCreative->prepare("SELECT pageID, pageTitle, pageisHome, pageBelongs, pageType, pagefriendlyURL FROM pages WHERE pageBelongs = :currentMenuPage ORDER BY pagetabPosition ASC");
                $query_getSubPage->bindParam(':currentMenuPage', $currentMenuPage);
                $query_getSubPage->execute();
                $totalRows_getSubPage = $query_getSubPage->rowCount();

                if (isset($row_getSubPage['pageBelongs']) && $currentPage == $row_getSubPage['pageBelongs'])
                  $dynamicInsert = "class=\"active\"";
                else
                  $dynamicInsert = "";

                echo "<li aria-haspopup=\"true\" $dynamicInsert><a href=\"page.php?pid=" . $row_getMainPage['pagefriendlyURL'] . "\">" . $row_getMainPage['pageTitle'] . "<b class=\"caret\"></b></a>";

                  echo "<div class=\"grid-container3\">";
                    echo "<ul>";
                      $counter1 = 0;
                      if ($totalRows_getSubPage > 0) {
                        while ($row_getSubPage = $query_getSubPage->fetch(PDO::FETCH_ASSOC)) {
                          switch ($row_getSubPage['pageType']) {
                            case "1":
                              $subLink = "page.php?pid=".$row_getSubPage['pagefriendlyURL'];
                              break;
                            case "2":
                              $subLink = "articles.php?pid=".$row_getSubPage['pagefriendlyURL'];
                              break;
                            case "4":
                              $subLink = $row_getSubPage['pageLink']."?pid=".$row_getSubPage['pagefriendlyURL'];
                              break;
                          }
                          $subLink = $sitepath."/".$subLink;

                          //get third submenu if it exists
                          $query_getSubSubPage = $conCreative->prepare("SELECT pageID, pageTitle, pageisHome, pageBelongs, pageType, pagefriendlyURL FROM pages WHERE pageBelongs = :currentMenuPage ORDER BY pagetabPosition ASC");
                          $query_getSubSubPage->bindParam(':currentMenuPage', $row_getSubPage['pageID']);
                          $query_getSubSubPage->execute();
                          $totalRows_getSubSubPage = $query_getSubSubPage->rowCount();



                          if($totalRows_getSubSubPage > 0) {
                            echo "<li aria-haspopup=\"true\"><a href=\"$subLink\">" . $row_getSubPage['pageTitle'] . "</a>";
                            echo "<div class=\"grid-container2\">";
                              //echo "<div class=\"grid-column grid-column1\">";
                                echo "<ul>";
                                  while($row_getSubSubPage = $query_getSubSubPage->fetch(PDO::FETCH_ASSOC)) {
                                    switch ($row_getSubSubPage['pageType']) {
                                      case "1":
                                        $subSubLink = "page.php?pid=".$row_getSubSubPage['pagefriendlyURL'];
                                        break;
                                      case "2":
                                        $subSubLink = "articles.php?pid=".$row_getSubSubPage['pagefriendlyURL'];
                                        break;
                                      case "4":
                                        $subSubLink = $row_getSubSubPage['pageLink']."?pid=".$row_getSubSubPage['pagefriendlyURL'];
                                        break;
                                    }
                                    $subSubLink = $sitepath . "/" . $subSubLink;

                                    echo "<li><a href=\"$subSubLink\">" . $row_getSubSubPage['pageTitle'] . "</a></li>";
                                  }
                                echo "</ul>";
                              //echo "</div>";
                            echo "</div>";
                          }
                          else {
                            echo "<li><a href=\"$subLink\">" . $row_getSubPage['pageTitle'] . "</a>";
                          }
                          echo "</li>";

                          $counter1++;
                        }
                      }
                    echo "</ul>";
                  echo "</div>";
              } //end of child pages
                echo "</li>";
              $counter++;
            }
          }
        }

        //search box
        echo "
          <li class=\"search\">
            <form method=\"post\" action=\"page.php?pid=search\" onsubmit=\"\">
              <div class=\"input\">
                <input type=\"text\" name=\"search\" placeholder=\"" . translate_dom("Search") . "\">
                <button type=\"submit\" class=\"button\"></button>
              </div>
            </form>
          </li>
        ";

        //salvation button
        echo "<li style=\"\" aria-haspopup=\"true\" class=\"right\"><a href=\"meetjesus.php\">" . translate_dom("Meet Jesus") . "</a></li>";

        //contact form
        echo "<li aria-haspopup=\"true\" class=\"right\"><a href=\"contactus.php\">" . translate_dom("Contact Us") . "</a></li>";
        /*echo "
          <li style=\"\" aria-haspopup=\"true\" class=\"right\">
            <a href=\"#\">" . translate_dom("Contact Us") . "</a>
            <div class=\"grid-container6\">
              <form method=\"post\">
                <fieldset>
                  <div class=\"row\">
                    <section class=\"col col-6\">
                      <label class=\"input\">
                      <input placeholder=\"" . translate_dom("Name") . "\" name=\"name\" type=\"text\">
                      </label>
                    </section>

                    <section class=\"col col-6\">
                      <label class=\"input\">
                      <input placeholder=\"" . translate_dom("Email") . "\" type=\"email\" name=\"email\">
                      </label>
                    </section>
                  </div>

                  <section>
                    <label class=\"input\">
                    <input type=\"text\" placeholder=\"" . translate_dom("The reason you are contacting us?") . "\" name=\"reason\">
                    </label>
                  </section>

                  <section>
                    <label class=\"textarea\">
                    <textarea rows=\"4\" placeholder=\"" . translate_dom("Message") . "\" name=\"content\"></textarea>
                    </label>
                  </section>

                  <section>
                    <label class=\"input\">
                      <div class=\"captcha\">
                        " . Securimage::getCaptchaHtml() . "
                      </div>
                    </label>
                  </section>

                  <button type=\"submit\" class=\"button\" name=\"submitContact\" value=\"contact form submitted\">" . translate_dom("Send Message") . "</button>
                </fieldset>
              </form>
            </div>
          </li>
        ";*/
      echo "</ul>";
    echo "</nav>"; // ERE20200526 - Add Nav end tag
	echo "</div>";
echo "</div>";
?>

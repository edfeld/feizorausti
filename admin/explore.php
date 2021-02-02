<?php
require_once("include.php");
require_once("../includes/inc_various.php");

$currentPage = "explore";

$conCreative->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try {
	//determine set statement
	$setStatement = "";
	$commentID = -1;
	if (isset($_GET['untag'])) {
		$setStatement = "isTagged=0";
		$commentID = $_GET['untag'];
	}
	if (isset($_GET['tag'])) {
		$setStatement = "isTagged=1";
		$commentID = $_GET['tag'];
	}
	if (isset($_GET['unapprove'])) {
		$setStatement = "isApproved=0";
		$commentID = $_GET['unapprove'];
	}
	if (isset($_GET['approve'])) {
		$setStatement = "isApproved=1";
		$commentID = $_GET['approve'];
	}
	if (isset($_GET['undelete'])) {
		$setStatement = "isDeleted=0";
		$commentID = $_GET['undelete'];
	}
	if (isset($_GET['delete'])) {
		$setStatement = "isDeleted=1";
		$commentID = $_GET['delete'];
	}
	if (isset($_GET['unflag'])) {
		$setStatement = "isFlagged=0";
		$commentID = $_GET['unflag'];
	}
	if (isset($_GET['flag'])) {
		$setStatement = "isFlagged=1";
		$commentID = $_GET['flag'];
	}

	//execute comment actions
	if ($setStatement != "") {
		$query_actComment = $conCreative->prepare("UPDATE comments SET " . $setStatement . " WHERE commentID=:commentID");
		$query_actComment->bindParam(':commentID', $commentID, PDO::PARAM_INT);
		$query_actComment->execute();
	}
}
catch (PDOException $e) {
	echo $e->getMessage();
}

//get default filters
if (isset($_POST['submit']) && $_POST['submit'] == 'submit') {
	$tagged = @checkboxToBool($_POST['tagged']);
	$notTagged = @(checkboxToBool($_POST['notTagged']) == 1 ? 0 : 1);
	$deleted = @checkboxToBool($_POST['deleted']);
	$notDeleted = @(checkboxToBool($_POST['notDeleted']) == 1 ? 0 : 1);
	$approved = @checkboxToBool($_POST['approved']);
	$notApproved = @(checkboxToBool($_POST['notApproved']) == 1 ? 0 : 1);
	$flagged = @checkboxToBool($_POST['flagged']);
	$notFlagged = @(checkboxToBool($_POST['notFlagged']) == 1 ? 0 : 1);

	$searchPhrasePre = $_POST['searchString'];
	$searchPhrase = '%' . $searchPhrasePre . '%';
}
else {
	$tagged = 1;
	$notTagged = 0;
	$deleted = 1;
	$notDeleted = 0;
	$approved = 1;
	$notApproved = 0;
	$flagged = 1;
	$notFlagged = 0;

	$searchPhrasePre = '';
	$searchPhrase = '%';
}

//determine custom filter
if (isset($_POST['customFilterType'])) {
	$customFilterType = $_POST['customFilterType'];
	switch ($customFilterType) {
		case "Name":
			$queryStringAddon = "AND (commentAuthor LIKE :searchPhrase)";
			break;
		case "Email Address":
			$queryStringAddon = "AND (commentEmail LIKE :searchPhrase)";
			break;
		case "IP Address":
			$queryStringAddon = "AND (ipAddress LIKE :searchPhrase)";
			break;
		default:
			$queryStringAddon = "AND (commentContent LIKE :searchPhrase)";
			break;
	}
}
else {
	$customFilterType = "Content";
	$queryStringAddon = "AND (commentContent LIKE :searchPhrase)";
}

//retrieve comments
$queryString = "SELECT * FROM comments
				WHERE (isTagged=:tagged OR isTagged=:notTagged)
						AND (isDeleted=:deleted OR isDeleted=:notDeleted)
						AND (isApproved=:approved OR isApproved=:notApproved)
						AND (isFlagged=:flagged OR isFlagged=:notFlagged)"
						. $queryStringAddon
						. "ORDER BY commentID DESC";
try {
	$query_getComments = $conCreative->prepare($queryString);
	$query_getComments->bindParam(':tagged', $tagged, PDO::PARAM_BOOL);
	$query_getComments->bindParam(':notTagged', $notTagged, PDO::PARAM_BOOL);
	$query_getComments->bindParam(':deleted', $deleted, PDO::PARAM_BOOL);
	$query_getComments->bindParam(':notDeleted', $notDeleted, PDO::PARAM_BOOL);
	$query_getComments->bindParam(':approved', $approved, PDO::PARAM_BOOL);
	$query_getComments->bindParam(':notApproved', $notApproved, PDO::PARAM_BOOL);
	$query_getComments->bindParam(':flagged', $flagged, PDO::PARAM_BOOL);
	$query_getComments->bindParam(':notFlagged', $notFlagged, PDO::PARAM_BOOL);
	$query_getComments->bindParam(':searchPhrase', $searchPhrase, PDO::PARAM_STR);
	$query_getComments->execute();
}
catch (PDOException $e) {
	echo $e->getMessage();
}


function checkboxToBool($in) {
	if ($in == 'on')
		return 1;
	else
		return 0;
}
?>

<!DOCTYPE html>
<html>

<head>
	<?php require_once("include_head.php"); ?>
</head>

<body>
	<div class="row-fluid bodycontent">
		<?php include("include_menu.php"); ?>

		<?php include("include_message.php"); ?>

		<div class="span9 maincontainer shadow">
			<h2>Explore Comments</h2>
			Here you can explore the comments that have been posted to the website over its entire life time. This includes deleted comments and archived comments. If you want to save a comment for future reference, <em>Tag</em> it.
			<hr />

			<form id="filterForm" method="post" class="form-inline" role="form" tabindex="-1">
				<div class="form-group">
					<div class="btn-toolbar" style="margin: 0px;">
						<div class="btn-group">
				            <button type="button" tabindex="0" class="btn dropdown-toggle" data-toggle="dropdown"><span id="searchFilterBy"><?php if (isset($customFilterType)  && $searchPhrasePre != '') { echo "$customFilterType"; } else { echo "Filter by..."; } ?></span> <span class="caret"></span></button>
				            <ul class="dropdown-menu">
								<li><a href="#" onclick="changeFilterBy(this);" id='filterByDropdown'>Filter by...</a></li>
								<li class="divider"></li>
								<li><a href="#" onclick="changeFilterBy(this);">Name</a></li>
								<li><a href="#" onclick="changeFilterBy(this);">Content</a></li>
								<li><a href="#" onclick="changeFilterBy(this);">Email Address</a></li>
								<li><a href="#" onclick="changeFilterBy(this);">IP Address</a></li>
				            </ul>
			          	</div>

						<input type="hidden" name="customFilterType" id="searchFilterByHidden" class="form-control" <?php if (isset($customFilterType)) { echo "value=\"$customFilterType\""; } ?>></input>
						<input type="text" name="searchString" id="searchString" class="form-control" tabindex="1" <?php if (isset($searchPhrasePre)) { echo "value=\"$searchPhrasePre\""; } ?>></input>
						<button type="submit" name="submit" id="filterFormSubmit" value="submit" class="btn form-control" tabindex="2">Go</button>
					</div>
				</div>

				<div class="form-group commentsSearchFilters">
					<label><input type="checkbox" name='tagged' tabindex="3" <?php if ($tagged == 1) { echo "checked"; } ?>> Tagged</label>
					<label><input type="checkbox" name='notTagged' tabindex="4" <?php if ($notTagged == 0) { echo "checked"; } ?>> Not Tagged</label>
					<label><input type="checkbox" name='deleted' tabindex="5" <?php if ($deleted == 1) { echo "checked"; } ?>> Deleted</label>
					<label><input type="checkbox" name='notDeleted' tabindex="6" <?php if ($notDeleted == 0) { echo "checked"; } ?>> Not Deleted</label>
					<label><input type="checkbox" name='approved' tabindex="7" <?php if ($approved == 1) { echo "checked"; } ?>> Approved</label>
					<label><input type="checkbox" name='notApproved' tabindex="8" <?php if ($notApproved == 0) { echo "checked"; } ?>> Not Approved</label>
					<label><input type="checkbox" name='flagged' tabindex="9" <?php if ($flagged == 1) { echo "checked"; } ?>> Flagged</label>
					<label><input type="checkbox" name='notFlagged' tabindex="10" <?php if ($notFlagged == 0) { echo "checked"; } ?>> Not Flagged</label>
				</div>
			</form>

			<?php
			$counter_getComments = 0;
			if ($query_getComments->rowCount() > 0) {
				if ($searchPhrasePre != '')
					echo "<div class='alert alert-info'>" . $query_getComments->rowCount() . " comments were found matching your query (\"$searchPhrasePre\" in $customFilterType).</div>";

				while ($row = $query_getComments->fetch(PDO::FETCH_ASSOC)) {
					//parse the date
					$commentDateParsed = parseDate($row['commentDate']);

					//retrieve associated article link and title
					$query_assocArticle = $conCreative->prepare("SELECT *
																 FROM articles
																 WHERE articlefriendlyURL=:articleId");
					$query_assocArticle->bindParam(':articleId', $row['articleId'], PDO::PARAM_STR);
					$query_assocArticle->execute();
					$row_assocArticle = $query_assocArticle->fetch(PDO::FETCH_ASSOC);

					//retrieve associated page
					$query_assocPage = $conCreative->prepare("SELECT *
															  FROM pages
															  WHERE pageID=:pageID");
					$query_assocPage->bindParam(':pageID', $row_assocArticle['articleBelongs'], PDO::PARAM_INT);
					$query_assocPage->execute();
					$row_assocPage = $query_assocPage->fetch(PDO::FETCH_ASSOC);

					//create reference variables
					$articleTitle = $row_assocArticle['articleTitle'];
					$articleLink = $sitepath . '/single_article.php?pid=' . $row_assocPage['pagefriendlyURL'] . '&aid=' . $row['articleId'];
					?>

					<div class='well'>
						<a name='<?php echo $row['commentID']; ?>'><!--anchor with comment id--></a>
						<?php echo $row['commentContent']; ?>
						<div class='commentByline'>
							<?php echo $row['commentAuthor']; ?> //
							<?php if (isset($row['commentEmail']) && $row['commentEmail'] != null && $row['commentEmail'] != '') { echo $row['commentEmail'] . ' // '; } ?>
							<?php if (isset($row['ipAddress']) && $row['ipAddress'] != null && $row['ipAddress'] != '') { echo $row['ipAddress'] . ' // '; } ?>
							posted <?php echo $commentDateParsed; ?> //
							on <?php echo "<a href='$articleLink' target='_blank'>$articleTitle</a>"; ?>
						</div>
						<div class='commentTags'>
							<?php if ($row['isTagged'] == 1) { echo "<span class='label label-info'>Tagged</span>"; } ?>
							<?php if ($row['isDeleted'] == 1) { echo "<span class='label label-inverse'>Deleted</span>"; } ?>
							<?php if ($row['isApproved'] == 1) { echo "<span class='label label-success'>Approved</span>"; } ?>
							<?php if ($row['isApproved'] == 0) { echo "<span class='label label-warning'>Not Approved</span>"; } ?>
							<?php if ($row['isFlagged'] == 1) { echo "<span class='label label-important'>Flagged</span>"; } ?>
						</div>
						<div class="commentButtons btn-toolbar">
							<div class="btn-group">
								<?php
								if ($row['isTagged'] == 1) { echo "<button class='btn btn-mini' onclick='untagComment(" . $row['commentID'] . ");'>Un-Tag</button>"; }
								else { echo "<button class='btn btn-mini' onclick='tagComment(" . $row['commentID'] . ");'>Tag</button>"; }

								if ($row['isDeleted'] == 1) { echo "<button class='btn btn-mini' onclick='undeleteComment(" . $row['commentID'] . ");'>Un-Delete</button>"; }
								else { echo "<button class='btn btn-mini' onclick='deleteComment(" . $row['commentID'] . ");'>Delete</button>"; }

								if ($row['isApproved'] == 1) { echo "<button class='btn btn-mini' onclick='unapproveComment(" . $row['commentID'] . ");'>Un-Approve</button>"; }
								else { echo "<button class='btn btn-mini' onclick='approveComment(" . $row['commentID'] . ");'>Approve</button>"; }

								if ($row['isFlagged'] == 1) { echo "<button class='btn btn-mini' onclick='unflagComment(" . $row['commentID'] . ");'>Un-Flag</button>"; }
								else { echo "<button class='btn btn-mini' onclick='flagComment(" . $row['commentID'] . ");'>Flag</button>"; }
								?>
							</div>

							<div class="btn-group">
								<button class="btn btn-mini disabled">Track by...</button>
								<button class="btn btn-mini dropdown-toggle" data-toggle="dropdown">
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu">
									<li><a href="#" onclick="trackBy('<?php echo $row['commentAuthor']; ?>', 'Name');">Name</a></li>
									<li><a href="#" onclick="trackBy('<?php echo $row['commentEmail']; ?>', 'Email Address');">Email Address</a></li>
									<li><a href="#" onclick="trackBy('<?php echo $row['ipAddress']; ?>', 'IP Address');">IP Address</a></li>
								</ul>
							</div>
						</div>
					</div>

					<?php

					$counter_getComments++;
				}
			}
			else
				echo "<div class='alert'>No comments were found matching your query.</div>";
			?>
		</div>
	</div>

	<?php require_once('include_foot.php'); ?>

	<!--custom page JavaScript-->
	<script type="text/javascript">
		function changeFilterBy(optionReference) {
			document.getElementById('searchFilterBy').innerHTML = optionReference.innerHTML;
			document.getElementById('searchFilterByHidden').value = optionReference.innerHTML;
		}

		function unapproveComment(id) {
			window.location = window.location.pathname + "?unapprove=" + id;
		}
		function approveComment(id) {
			window.location = window.location.pathname + "?approve=" + id;
		}
		function deleteComment(id) {
			window.location = window.location.pathname + "?delete=" + id;
		}
		function undeleteComment(id) {
			window.location = window.location.pathname + "?undelete=" + id;
		}
		function untagComment(id) {
			window.location = window.location.pathname + "?untag=" + id;
		}
		function tagComment(id) {
			window.location = window.location.pathname + "?tag=" + id;
		}
		function unflagComment(id) {
			window.location = window.location.pathname + "?unflag=" + id;
		}
		function flagComment(id) {
			window.location = window.location.pathname + "?flag=" + id;
		}

		function trackBy(meta, type) {
			//fill out the form
			document.getElementById('searchFilterBy').innerHTML = type;
			document.getElementById('searchFilterByHidden').value = type;
			document.getElementById('searchString').value = meta;

			//submit the form
			document.getElementById('filterFormSubmit').click();
		}
	</script>
</body>

</html>

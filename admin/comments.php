<?php
require_once("include.php");
require_once("../includes/inc_various.php");

$currentPage = "comments";

$conCreative->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try {
	//approve comment
	if (isset($_GET['approve'])) {
		$query_approveComment = $conCreative->prepare("UPDATE comments SET isApproved=1,isFlagged=0 WHERE commentID=:commentID");
		$query_approveComment->bindParam(':commentID', $_GET['approve'], PDO::PARAM_INT);
		$query_approveComment->execute();
	}

	//delete comment
	if (isset($_GET['delete'])) {
		$query_deleteComment = $conCreative->prepare("UPDATE comments SET isDeleted=1 WHERE commentID=:commentID");
		$query_deleteComment->bindParam(':commentID', $_GET['delete'], PDO::PARAM_INT);
		$query_deleteComment->execute();
	}

	//retrieve comments that need to be approved
	$query_getComments = $conCreative->prepare("SELECT * FROM comments WHERE isApproved=0 AND isDeleted=0");
	$query_getComments->execute();
	$totalRows_getComments = $query_getComments->rowCount();
}
catch (PDOException $e) {
	echo $e->getMessage();
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

		<div class="span9 maincontainer shadow">
			<?php include("include_message.php"); ?>

			<h2>Submitted Comments</h2>
			Brand new comments that need your approval are listed below. If you opt to approve them, they will be displayed on the website publically. If you decide to delete them, they will be archived but hidden from the public.
			<hr />

			<?php
			$counterComments = 0;
			if ($totalRows_getComments > 0) {
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
						<!--<div class='commentTags'>
							<?php if ($row['isTagged'] == 1) { echo "<span class='label label-info'>Tagged</span>"; } ?>
							<?php if ($row['isDeleted'] == 1) { echo "<span class='label label-inverse'>Deleted</span>"; } ?>
							<?php if ($row['isApproved'] == 1) { echo "<span class='label label-success'>Approved</span>"; } ?>
							<?php if ($row['isApproved'] == 0) { echo "<span class='label label-warning'>Not Approved</span>"; } ?>
							<?php if ($row['isFlagged'] == 1) { echo "<span class='label label-important'>Flagged</span>"; } ?>
						</div>-->
						<div class="commentButtons btn-toolbar">
							<div class="btn-group">
								<button class='btn btn-mini' onclick='approveComment(<?php echo $row['commentID']; ?>);'>Approve</button>
								<button class='btn btn-mini' onclick='deleteComment(<?php echo $row['commentID']; ?>);'>Delete</button>
							</div>

							<!--<div class="btn-group">
								<button class="btn btn-mini disabled">Track this visitor by...</button>
								<button class="btn btn-mini dropdown-toggle" data-toggle="dropdown">
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu">
									<li><a href="#">Name</a></li>
									<li><a href="#">Email Address</a></li>
									<li><a href="#">IP Address</a></li>
								</ul>
							</div>-->
						</div>
					</div>

					<?php

					$counterComments++;
				}
			}
			if($counterComments <= 0){
				echo "<div class='alert'>No new comments were found.</div>";
			}
			?>
		</div>
	</div>

	<?php require_once('include_foot.php'); ?>

	<!--custom page JavaScript-->
	<script type="text/javascript">
	function approveComment(id){
		window.location = window.location.pathname + "?approve=" + id;
	}
	function deleteComment(id){
		window.location = window.location.pathname + "?delete=" + id;
	}
	</script>
</body>

</html>

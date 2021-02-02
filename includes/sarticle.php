<?php
/**
sarticle.php
------------
This file is used to display the content and comments, as well as processes
new comment posts, for single articles.
**/
?>


<div id="articleContainer">
	<?php if($row_getArticle['articleImage'] != "" && $row_getArticle['articleImage'] != null){ ?>
	<div class="articleImageHolder"><img src="<?php echo $row_getArticle['articleImage']; ?>" alt="<?php echo $row_getArticle['articleTitle']; ?>" class="articleImage articleImageFull"></div>
	<div class="articleTitle"><?php echo $row_getArticle['articleTitle']; ?></div>
	<div class="articleDate"><?php echo translate_dom(strftime("%d %B %Y", strtotime($row_getArticle['articleDate']))); ?></div>
	<?php echo $row_getArticle['articleDescription']; ?>
	<?php
	}
	else{
	?>
	<div class="articleTitle"><?php echo $row_getArticle['articleTitle']; ?></div>
	<div class="articleDate"><?php echo translate_dom(strftime("%d %B %Y", strtotime($row_getArticle['articleDate']))); ?></div>
	<?php echo $row_getArticle['articleDescription']; ?>
	<?php
	}
	?>
	<div class="clearboth"></div>

	<?php
	//flag comment and display message if a user clicked flag
	if(isset($_GET['flag']) && $_GET['flag'] > 0){
		$commentId = $_GET['flag'];
		$flagCommentQuery = $conCreative->prepare("UPDATE comments SET isFlagged=1 WHERE commentID=" . $commentId);
		if($flagCommentQuery->execute()){
			$message     = "Thank you, the comment has been flagged for screening.";
			$messageType = "normal";
			echo "<script type='text/javascript'>window.location.assign(\"$sitepath/single_article.php?pid=" . $pid . "&aid=" . $aid . "&msg=" . $message . "&msgType=" . $messageType . "\");</script>";
		}
		else{
			$message     = "Error: The comment you attempted to flag does not seem to exist.";
			$messageType = "normal";
			echo "<script type='text/javascript'>window.location.assign(\"$sitepath/single_article.php?pid=" . $pid . "&aid=" . $aid . "&msg=" . $message . "&msgType=" . $messageType . "\");</script>";
		}
	}

	echo "<br>";

	//post comment so that it can be approved
	try{
	if (isset($_GET['action']) && $_GET['action'] == 1) {

		$postingError = 0;

		if (!isset($_POST['commentAuthor']) || $_POST['commentAuthor'] == null || trim($_POST['commentAuthor']) == "" || strlen(trim($_POST['commentAuthor'])) <= 0 || !isset($_POST['commentContent']) || $_POST['commentContent'] == null || trim($_POST['commentContent']) == "" || strlen(trim($_POST['commentContent'])) <= 0) {
			$postingError = 1;
		}

		//verify the captcha
		if ($_POST['honeypot'] != "" || $_POST['submitSlider'] < 75) {
			$postingError = 2;
		}

		//get post data, make sure the necessary is entered, and make it safe
		$commentAuthor  = $conCreative->quote(htmlspecialchars(trim($_POST['commentAuthor'])));
		$commentEmail   = $conCreative->quote(htmlspecialchars(trim($_POST['commentEmail'])));
		$commentContent = $conCreative->quote(htmlspecialchars(trim($_POST['commentContent'])));
		$commentReply   = htmlspecialchars(trim($_POST['commentReply']));
		if ($_POST['hideEmail'])
			$hideEmail = 1;
		else
			$hideEmail = 0;
		date_default_timezone_set("UTC");
		$commentDate = date('Y-m-d H:i:s');

		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR']; //try to sniff out if client is behind a proxy
		else
			$ipAddress = $_SERVER['REMOTE_ADDR'];

		$conCreative->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$postCommentQuery = $conCreative->prepare("INSERT Into comments (articleID, commentAuthor, commentContent, commentEmail, commentDate, hideEmail, commentReply, ipAddress)
													VALUES ('$aid', $commentAuthor, $commentContent, $commentEmail, '$commentDate', $hideEmail, $commentReply, '$ipAddress')");

		if ($postingError == 0 && $postCommentQuery->execute()) {
			$message     = "Thank you! Your comment has been submitted and is awaiting approval.";
			$messageType = "success";
			echo "<script type='text/javascript'>window.location.assign(\"$sitepath/single_article.php?pid=" . $pid . "&aid=" . $aid . "&msg=" . $message . "&msgType=" . $messageType . "\");</script>";
		}
		else if($postingError == 1){
			echo "<script type='text/javascript'>document.getElementById('message').innerHTML=\"<div class='error'>" . trim(translate_dom("Neither your name nor the content of your comment can be left blank.")) . "</div>\";</script>";
		}
		else if($postingError == 2){
			echo "<script type='text/javascript'>document.getElementById('message').innerHTML=\"<div class='error'>" . trim(translate_dom("Enter the characters in the image below correctly.")) . "</div>\";</script>";
		}
	}
	}
	catch (PDOException $e) {
		echo "<div class='articleCommentsError'>" . $e->getMessage() . "</div>";
	}
	?>

  <?php if($row_getArticle['allowComments'] == 1){ ?>
  <div class="articleTitle"><?php echo translate_dom("Comments..."); ?></div>
  <?php
  $counterComments=0;
  if($totalRows_getComments > 0){
	while($row = $query_getComments->fetch(PDO::FETCH_ASSOC)){
		if ($row['commentReply'] == -1) {
			//style the comment box
			echo "<div class='articleComments'>";

			//echo the comment and the author
			echo nl2br($row['commentContent']) . "<br>";
			echo "<span style='font-size: 10px;'>by " . $row['commentAuthor'];

			//echo email if one was given
			if($row['commentEmail'] != null && $row['hideEmail'] == 0){
				echo " // <a style='color: #888888; text-decoration: none;' href='mailto:" . $row['commentEmail'] . "'>" . $row['commentEmail'] . "</a>";
				}

			//echo the date
			$commentDate = parseDate($row['commentDate']);
			echo translate_dom(" // posted " . $commentDate);

			echo translate_dom(" // <a style='color: #ff3300; text-decoration: none;' href='single_article.php?pid=" . $pid . "&aid=" . $aid . "&flag=" . $row['commentID'] . "'>Flag as Inappropriate</a>");

			echo translate_dom(" // <a style='color: #0066ff; text-decoration: none;' href='" . $requesturl . "#postComment' onClick=\"resetReply(); document.getElementById('commentReply').value=" . $row['commentID'] . "; document.getElementById('commentInformation').innerHTML='<div class=\'articleComments\'><strong>Note:</strong> You are currently replying to a comment. <a onClick=resetReply(); style=cursor:pointer;>Click here to cancel your reply and post a normal comment.</a></div>';\">Reply</a>");

			//end the styling
			echo "</span></div>";

			$counterComments++;

			//display any replies to the above comment
			$query_getCommentReplies = $conCreative->prepare("SELECT * FROM comments WHERE commentReply=:cr AND isDeleted=0 AND isApproved=1");
			$query_getCommentReplies->bindParam(':cr', $row['commentID']);
			$query_getCommentReplies->execute();
			$totalRows_getCommentReplies = $query_getComments->rowCount();
			if($totalRows_getCommentReplies > 0){
				while($row = $query_getCommentReplies->fetch(PDO::FETCH_ASSOC)){
					echo "<div class='articleReplyComments'>";

					//echo the comment and the author
					echo nl2br($row['commentContent']) . "<br>";
					echo "<span style='font-size: 10px;'>by " . $row['commentAuthor'];

					//echo email if one was given
					if($row['commentEmail'] != null && $row['hideEmail'] == 0){
						echo " // <a style='color: #888888; text-decoration: none;' href='mailto:" . $row['commentEmail'] . "'>" . $row['commentEmail'] . "</a>";
					}

					//echo the date
					$commentDate = parseDate($row['commentDate']);
					echo translate_dom(" // posted " . $commentDate);

					echo translate_dom(" // <a style='color: #ff3300; text-decoration: none;' href='single_article.php?pid=" . $pid . "&aid=" . $aid . "&flag=" . $row['commentID'] . "'>Flag as Inappropriate</a>");

					//end the styling
					echo "</span></div>";
				}
			}
		}
	}
}
if($counterComments <= 0){
	echo translate_dom("<div class='articleComments'>There are no comments to display.</div><br>");
}
  ?>

	<div class="articleTitle" style='margin-top: 32px;'><a id='postComment'></a><?php echo translate_dom("Post Comment..."); ?></div>
	<div id='commentInformation'></div>

	<form id="commentForm" name="comment" class="form">
		<input type="hidden" name="commentReply" id="commentReply" value=-1></input>

		<div class="row">
			<section class="col col-6">
				<label class="input">
					<input type="text" name="commentAuthor" placeholder="<?php echo translate_dom("Your Name"); ?>" value="<?php if(isset($_POST['commentAuthor']) && isset($postingError) && $postingError != 0){ echo $_POST['commentAuthor']; } ?>">
				</label>
			</section>

			<section class="col col-6">
				<label class="input">
					<input type="text" name="commentEmail" placeholder="<?php echo translate_dom("Your Email Address"); ?>" value="<?php if(isset($_POST['commentEmail']) && isset($postingError) && $postingError != 0){ echo $_POST['commentEmail']; } ?>">
				</label>
			</section>
		</div>

		<section>
			<label for="hideEmail"><input type="checkbox" name="hideEmail" value="hideEmail" style="display: inline-block;" <?php if(!isset($_POST['hideEmail']) && isset($postingError) && $postingError != 0){ echo "unchecked"; } else { echo "checked"; } ?>></input> <?php echo translate_dom("Keep Email Private (Only admins of website will be able to see it)"); ?></label>
		</section>

		<section>
			<label class="textarea">
				<textarea name="commentContent" placeholder="<?php echo translate_dom("Your Comment"); ?>"><?php if(isset($_POST['commentContent']) && isset($postingError) && $postingError != 0){ echo $_POST['commentContent']; } ?></textarea>
			</label>
		</section>
	</form>

<?php } ?>

</div>

<script type="text/javascript" src="js/honeypot-and-submit-slider.js"></script>
<script type="text/javascript">
	/* resetReply simply resets all of the fields in the comment form to be
	blank. */
	function resetReply() {
		document.getElementById('commentInformation').innerHTML = '';
		document.getElementById('commentReply').value = -1;
	}
	
	/* Setup the comment form */
	setupForm('#commentForm', "<?php echo "single_article.php?pid=" . $pid . "&aid=" . $aid . "&action=1"; ?>", "POST");
	addHoneypotAndSubmitSlider('#commentForm', "<?php echo translate_dom("> > > > >"); ?>");
</script>

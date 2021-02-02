<?php
//retrieve count of comments that need to be approved
if ($currentPage != "comments") {
	$query_getCommentsCount = $conCreative->prepare("SELECT COUNT(*) as total FROM comments WHERE isApproved=0 AND isDeleted=0");
	$query_getCommentsCount->execute();
	$fetch_getCommentsCount = $query_getCommentsCount->fetch(PDO::FETCH_ASSOC);
	$total_getCommentsCount = $fetch_getCommentsCount['total'];
}
else
	$total_getCommentsCount = $totalRows_getComments;

//retrieve count of comments that are flagged
if ($currentPage != "flagged") {
	$query_getFlaggedCommentsCount = $conCreative->prepare("SELECT COUNT(*) as total FROM comments WHERE isFlagged=1 AND isDeleted=0");
	$query_getFlaggedCommentsCount->execute();
	$fetch_getFlaggedCommentsCount = $query_getFlaggedCommentsCount->fetch(PDO::FETCH_ASSOC);
	$total_getFlaggedCommentsCount = $fetch_getFlaggedCommentsCount['total'];
}
else
	$total_getFlaggedCommentsCount = $totalRows_getFlaggedComments;

//retrieve recieve salvations that need to be responded to
if ($currentPage != "recieves") {
	$query_getRecievesCount = $conCreative->prepare("SELECT COUNT(*) as total FROM salvations WHERE contacted=0 AND type=0");
	$query_getRecievesCount->execute();
	$fetch_getRecievesCount = $query_getRecievesCount->fetch(PDO::FETCH_ASSOC);
	$total_getRecievesCount = $fetch_getRecievesCount['total'];
}
else {
	$total_getRecievesCount = $totalRows_getSalvations;
}

//retrieve request discipleships that need to be responded to
if ($currentPage != "requests") {
	$query_getRequestsCount = $conCreative->prepare("SELECT COUNT(*) as total FROM salvations WHERE contacted=0 AND type=1");
	$query_getRequestsCount->execute();
	$fetch_getRequestsCount = $query_getRequestsCount->fetch(PDO::FETCH_ASSOC);
	$total_getRequestsCount = $fetch_getRequestsCount['total'];
}
else {
	$total_getRequestsCount = $totalRows_getSalvations;
}
?>

<div class="span2 menucontainer shadow">
	<!--display logged in user-->
	<div style="text-align: center; color: white; margin-top: 4px; margin-bottom: 8px;"><?php echo "Hello, " . $_SESSION['MM_Username'] . "!"; ?></div>

	<!--display menu-->
	<ul class="nav">
		<li <?php if ($currentPage=="admin") { ?>class="active" <?php } ?>><a href="admin.php"><i class="icon-home icon-white"></i> Dashboard</a></li>
		<li <?php if ($currentPage=="stats") { ?>class="active" <?php } ?>><a href="stats.php"><i class="icon-signal icon-white"></i> Stats</a></li>
		<li <?php if (($currentPage=="banners") || ($currentPage=="navigation")) { ?>class="active" <?php } ?> onclick="displayDiv('#catdiv2')"><a href="#"><i class="icon-arrow-up icon-white"></i> Header</a>
			<ul id="catdiv2" class="nav submenu" <?php  if(($currentPage=="banners")||($currentPage=="navigation")){ ?> style="display:block;" <?php } ?>>
				<li <?php if ($currentPage=="navigation") { ?>class="active" <?php } ?>><a href="navigation.php"><i class="icon-tasks icon-white"></i> Navigation Order</a></li>
				<li <?php if ($currentPage=="banners") { ?>class="active" <?php } ?>><a href="banners.php"><i class="icon-picture icon-white"></i> Banners</a></li>
			</ul>
		</li>
		<li <?php if ($currentPage=="pages") { ?>class="active" <?php } ?>><a href="pages.php"><i class="icon-list-alt icon-white"></i> Pages</a></li>
		<li <?php if ($currentPage=="articles") { ?>class="active" <?php } ?>><a href="articles.php"><i class="icon-align-justify icon-white"></i> Articles</a></li>
		<li <?php if (($currentPage == "comments") || ($currentPage == "explore") || ($currentPage == "flagged")) { ?>class="active" <?php } ?> onclick="displayDiv('#catdiv3')"><a href="#"><i class="icon-comment icon-white"></i> Comments <span class='badge'><?php echo ($total_getCommentsCount + $total_getFlaggedCommentsCount); ?></a>
			<ul id="catdiv3" class="nav submenu" <?php  if(($currentPage == "comments") || ($currentPage == "explore") || ($currentPage == "flagged")) { ?> style="display:block;" <?php } ?>>
				<li <?php if ($currentPage == "comments") { ?>class="active" <?php } ?>><a href="comments.php"><i class="icon-star icon-white"></i> New <span class='badge badge-info'><?php echo $total_getCommentsCount; ?></span></a></li>
				<li <?php if ($currentPage == "flagged") { ?>class="active" <?php } ?>><a href="flagged.php"><i class="icon-flag icon-white"></i> Flagged <span class='badge badge-important'><?php echo $total_getFlaggedCommentsCount; ?></span></a></li>
				<li <?php if ($currentPage == "explore") { ?>class="active" <?php } ?>><a href="explore.php"><i class="icon-eye-open icon-white"></i> Explore Comments</a></li>
			</ul>
		</li>
		<li <?php if (($currentPage == "salvations") || ($currentPage == "recieves") || ($currentPage == "requests")) { ?>class="active" <?php } ?> onclick="displayDiv('#catdiv4')"><a href="#"><i class="icon-leaf icon-white"></i> Salvations <span class='badge'><?php echo ($total_getRecievesCount + $total_getRequestsCount); ?></span></a>
			<ul id="catdiv4" class="nav submenu" <?php if (($currentPage == "salvations") || ($currentPage == "recieves") || ($currentPage == "requests")) { ?> style="display:block;" <?php } ?>>
				<li <?php if ($currentPage == "recieves") { ?>class="active" <?php } ?>><a href="recieves.php"><i class="icon-bullhorn icon-white"></i> Recieve Jesus <span class='badge badge-success'><?php echo $total_getRecievesCount; ?></span></a></li>
				<li <?php if ($currentPage == "requests") { ?>class="active" <?php } ?>><a href="requests.php"><i class="icon-book icon-white"></i> Request Discipleship <span class='badge badge-warning'><?php echo $total_getRequestsCount; ?></span></a></li>
				<li <?php if ($currentPage == "salvations") { ?>class="active" <?php } ?>><a href="salvations.php"><i class="icon-eye-open icon-white"></i> Explore Salvations</a></li>
			</ul>
		</li>
		<li <?php if ($currentPage=="footer") { ?>class="active" <?php } ?>><a href="footer.php"><i class="icon-arrow-down icon-white"></i> Footer</a></li>
		<li <?php if (($currentPage=="details") || ($currentPage=="users") || ($currentPage=="analytics") || ($currentPage=="config")) { ?>class="active" <?php } ?> onclick="displayDiv('#catdiv1')"><a href="#"><i class="icon-cog icon-white"></i> Tools</a>
			<ul id="catdiv1" class="nav submenu" <?php  if (($currentPage=="details") || ($currentPage=="users") || ($currentPage=="analytics") || ($currentPage=="config") || ($currentPage=="language")) { ?> style="display:block;" <?php } ?>>
				<li><a href="#" onclick="openFilemanager(0,'');"><i class="icon-folder-open icon-white"></i> File Manager</a></li>
				<li <?php if($currentPage=="users"){ ?>class="active" <?php } ?>><a href="users.php"><i class="icon-user icon-white"></i> Users</a></li>
				<li <?php if ($currentPage=="details") { ?>class="active" <?php } ?>><a href="details.php"><i class="icon-info-sign icon-white"></i> Site Details</a></li>
				<!--<li <?php if ($currentPage=="analytics") { ?>class="active" <?php } ?>><a href="analytics.php"><i class="icon-asterisk icon-white"></i> Code</a></li>-->
				<li <?php if ($currentPage=="language") { ?>class="active" <?php } ?>><a href="language.php"><i class="icon-globe icon-white"></i> Language</a></li>
				<li <?php if ($currentPage=="config") { ?>class="active" <?php } ?>><a href="config.php"><i class="icon-wrench icon-white"></i> Settings</a></li>
			</ul>
		</li>
		<li <?php if ($currentPage=="help") { ?>class="active" <?php } ?>><a href="help.php"><i class="icon-question-sign icon-white"></i> Help</a></li>
		<li><a href="<?php echo $logoutAction; ?>"><i class="icon-user icon-white"></i> Sign Out</a></li>
	</ul>
</div>

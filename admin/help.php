<?php
require_once("include.php");

$currentPage="help";
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

			<div>
				<h2>Help</h2>
				Below are a handful of videos to help demonstrate use of the site and admin panel.<br><br>

				<h2>F.A.Q.</h2>
				Below are answers to a variety of frequently asked questions about the operation of this website.<br><br>
				<div class="help">
					<div class="helptitle" onclick="displayDiv('#help9')">How do comments work?</div>
					<div class="helpcontent" id="help9">When a visitor posts a comment on an article that has been created for the website, it will be shown inside the Comments page of the admin panel. In order for the comment to be displayed to the public, an admin of the website must go to this page and approve it. Prior to approving it, the comment can be edited if it contains offensive or sensitive language.<br><br>Once a comment has been approved, other visitors of the site will be able to read it below an article. If one of them finds it offensive, they can choose to "flag as innapropriate". This will remove the comment from the public and make it so that an admin of the website, once again, must locate the comment in the Comments section of the admin panel and approve it.<br><br>At any point in this process, admins of this website also have the ability to delete submitted and flagged comments if deemed necessary.</div>
				</div>
				<div class="help">
					<div class="helptitle" onclick="displayDiv('#help1')">What is a Parent Page?</div>
					<div class="helpcontent" id="help1">Pages that have subpages. This way you can easily create a menu with a submenu.</div>
				</div>
				<div class="help">
					<div class="helptitle" onclick="displayDiv('#help2')">Type of pages</div>
					<div class="helpcontent" id="help2">
						<strong>Basic Page</strong>
						<p>Standard pages with title and description.</p>
						<strong>Article Page</strong>
						<p>A page to which you can assign articles/posts. A simple example of an article page is a latest news page.</p>
						<!--<strong>Footer Page</strong>
						<p>Pages that appear on the footer of your site. A simple example of a footer page is Terms & Conditions, Privacy Policy etc.</p>-->
						<strong>Stub Page</strong>
						<p>A page that will not show up in the site's navigation, but can be linked to manually.</p>
					</div>
				</div>
				<div class="help">
					<div class="helptitle" onclick="displayDiv('#help3')">How can I change the navigation menu page ordering?</div>
					<div class="helpcontent" id="help3">The navigation menu page ordering can be changed be selecting "Header -> Navigation Order" from the navigation on the left-hand side of the admin panel.</div>
				</div>
				<div class="help">
					<div class="helptitle" onclick="displayDiv('#help4')">What is a friendly URL?</div>
					<div class="helpcontent" id="help4">Friendly URL is the value we use to call a page or article on the frontend. By using this value (i.e. ?pid=create-a-friendly-search-engine-website) is much more friendlier for search engines rather using an id number.</div>
				</div>
				<div class="help">
					<div class="helptitle" onclick="displayDiv('#help5')">How to I remove an image?</div>
					<div class="helpcontent" id="help5">
					<p>To remove an image from Articles or Banners, simply clear the path from the input box</p>
					</div>
				</div>
				<div class="help">
					<div class="helptitle" onclick="displayDiv('#help7')">How can I create a page that includes articles</div>
					<div class="helpcontent" id="help7">
						<p>In order to create a page that includes articles (such as a blog page), you must...</p>
						<ul>
							<li>Navigate to Pages and Create a New Page</li>
							<li>Under Page Type, choose "Article Page" and press add page</li>
							<li>Navigate to Articles</li>
							<li>Create a New Article</li>
							<li>Choose the name of the page you just created as the article's Parent Page</li>
						</ul>
					</div>
				</div>
				<div class="help">
					<div class="helptitle" onclick="displayDiv('#help8')">Can I add a Google analytics code?</div>
					<div class="helpcontent" id="help8">
					<p>After you create your Google analytics profile, go to <strong>Analytics</strong> under <strong>Tools</strong> and paste your code. Remember to include &lt;script&gt; &lt;/script&gt; tags</p>
					</div>
				</div>

			</div>


		</div>
	</div>

	<?php require_once("include_foot.php"); ?>
</body>

</html>

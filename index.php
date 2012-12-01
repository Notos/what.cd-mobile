<?
	require '../classes/config.php';
?>
<!doctype html>
<!--[if lt IE 7 ]> <html lang="en" class="ie6"> <![endif]--> <!--[if IE 7 ]>		<html lang="en" class="ie7"> <![endif]--> <!--[if IE 8 ]> 	 <html lang="en" class="ie8"> <![endif]--> <!--[if IE 9 ]>		<html lang="en" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html lang="en"> <!--<![endif]-->
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title>What.CD</title>
	<meta name="description" content="What.CD Mobile">
	<meta name="author" content="What.CD">

	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="shortcut icon" href="/favicon.ico">
	<link rel="apple-touch-icon" href="/apple-touch-icon.png">
	<link href="css/bootstrap.css" rel="stylesheet" media="screen">
	<link href="css/bootstrap-responsive.css" rel="stylesheet" media="screen">
	<link rel="stylesheet" href="css/style.css?v=2">

	<!--
		[if lt IE 9]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]
	-->
</head>
<body>
<script type="text/x-handlebars" data-template-name="application">
	{{#if App.ApplicationController.loggedIn}}
	{{/if}}
	<div id="app-container" class="container-fluid">
		{{outlet}}
		<?
			if (constant("DEBUG_MODE")) {
				ob_start();
				exec("git log -1 --pretty=format:'%h'", $result);
				if (isset($result[0])) {
					$result = $result[0];
				} else {
					$result = "";
				}
				ob_clean();
				print '<div class="version">' . $result . '</div>';
			}
		?>
	</div>
</script>

<script type="text/x-handlebars" data-template-name="navbar">
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container-fluid">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<a class="brand" href="#/">What.CD</a>

				<div class="nav-collapse collapse">
					<ul class="nav">
						{{#view view.NavItemView item="index" }}
						 <a href="#" >Home</a>
						{{/view}}
						{{#view view.NavItemView item="torrents" }}
						 <a href="#/torrents" >Torrents</a>
						{{/view}}
						{{#view view.NavItemView item="forums" }}
						 <a href="#/forums" >Forums</a>
						{{/view}}
						{{#if App.ApplicationController.info.subscriptions}}
							<li><a href=""><strong>Subscriptions</strong></a></li>
						{{else}}
							{{#view view.NavItemView item="subscriptions" }}
								<a href="#/subscriptions" >Subscriptions</a>
							{{/view}}
						{{/if}}
						{{#view view.NavItemView item="inbox" }}
						 <a href="#/inbox" >Inbox</a>
						{{/view}}
					</ul>
					<ul class="nav pull-right">
						<li id="fat-menu" class="dropdown">
							<a href="#" id="user_menu" role="button" class="dropdown-toggle" data-toggle="dropdown">{{App.ApplicationController.user.username}}<b class="caret"></b></a>
							<ul class="dropdown-menu" role="menu">
								<li><a href="#/logout">Logout</a></li>
							</ul>
						</li>
					</ul>
				</div>
				<!--/.nav-collapse -->
			</div>
		</div>
	</div>
</script>
<script type="text/x-handlebars" data-template-name="index">
	{{#if App.ApplicationController.loggedIn}}
		{{view App.NavbarView}}
		{{#if App.ApplicationController.info.messages}}
			{{view App.AlertsView}}
		{{/if}}
		{{view App.NewsView}}
	{{else}}
		<h1>What.CD</h1>
		<a href="#/login">Log In</a>
	{{/if}}
</script>

<script type="text/x-handlebars" data-template-name="login-form">
	<h1>Login</h1>
	<form id="login-form">
		<input type="text" name="username" id="login-username" placeholder="username"/><br/>
		<input type="password" name="password" id="login-password" placeholder="password"/><br/>
		<input class="btn btn-large btn-primary" type="submit" value="Login"/>
	</form>
</script>

<script type="text/x-handlebars" data-template-name="alerts">
	<div class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert">×</button>
		{{#if App.ApplicationController.info.messages}}
			<strong>{{App.ApplicationController.info.messages}}</strong> new message.
		{{/if}}
		{{#if App.ApplicationController.info.notifications}}
			<strong>{{App.ApplicationController.info.notifications}}</strong> notification.
		{{/if}}
	</div>
</script>

<script type="text/x-handlebars" data-template-name="news">
	{{#if App.ApplicationController.news.announcements}}
		<h3>Announcements</h3>
		<ul class="news">
				{{#each announcement in App.ApplicationController.news.announcements}}
				<li class="news-item">
					<h4>{{announcement.title}}</h4>
					<div class="news-body">{{{announcement.body}}}</div>
				</li>
				{{/each}}
		</ul>
	{{/if}}
		{{#if App.ApplicationController.news.blogs}}
		<h3>Blog</h3>
		<ul class="news">
			{{#each blog in App.ApplicationController.news.blogs}}
			<li class="news-item">
				<h4>{{blog.title}}</h4>
				<div class="news-body">{{{blog.body}}}</div>
			</li>
			{{/each}}
		</ul>
	{{/if}}
</script>

<script type="text/x-handlebars" data-template-name="inbox">
	{{view App.NavbarView}}
	{{#if App.ApplicationController.hasMessages}}
		<h2>Inbox</h2>
		<ul class="inbox">
			{{#each message in App.ApplicationController.inbox.messages}}
				<li class="inbox-item-{{#if message.unread}}un{{/if}}read">
					<div class="inbox-from">{{message.username}}{{#if message.donor}}<img src="static/common/symbols/donor.png" alt="Donor" />{{/if}}</div>
					<div class="inbox-subject">{{message.subject}}</div>
					<div class="inbox-date">{{message.date}}</div>
				</li>
			{{/each}}
		</ul>
	{{else}}
		<h2>Your inbox is empty, sir.</h2>
	{{/if}}
</script>

<script type="text/x-handlebars" data-template-name="torrents">
	{{view App.NavbarView}}
	<h2>Torrents Page</h2>
</script>

<script type="text/x-handlebars" data-template-name="forums">
	{{view App.NavbarView}}
	<h2>Forums Page</h2>
</script>

<script type="text/x-handlebars" data-template-name="subscriptions">
	{{view App.NavbarView}}
	<h2>Subscriptions Page</h2>
</script>

<!-- The missing protocol means that it will match the current protocol, either http or https. If running locally, we use the local jQuery. -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="js/libs/jquery-1.7.2.min.js"><\/script>')</script>
<script src="js/libs/handlebars-1.0.0.beta.6.js"></script>
<script src="js/libs/ember-1.0.0-pre.2.min.js"></script>
<script src="js/libs/bootstrap.min.js"></script>
<script src="js/whatcd.js"></script>
<script src="js/app.js"></script>
<script src="js/libs/bootstrap-alert.js"></script>

</body>
</html>

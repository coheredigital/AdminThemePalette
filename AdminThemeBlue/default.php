<?php

$searchForm = $user->hasPermission('page-edit') ? $modules->get('ProcessPageSearch')->renderSearchForm() : '';
$bodyClass = $input->get->modal ? 'modal ' : '';
$bodyClass .= "id-{$page->id} template-{$page->template->name}";
if(!isset($content)) $content = '';



if(is_file(dirname(__FILE__) . "/styles/main-$colors.css")) $session->adminThemeColors = $colors;
	else $session->adminThemeColors = $defaultColorTheme;
$config->styles->prepend($config->urls->adminTemplates . "styles/main-default.css?v=6");
$config->styles->prepend($config->urls->adminTemplates . "styles/jquery-ui.css");


if ($adminTheme->disable_dots) {
	$config->styles->prepend($config->urls->adminTemplates . "styles/nodots.css?v=6");
}




$config->styles->append($config->urls->root . "wire/templates-admin/styles/font-awesome/css/font-awesome.min.css");


include("includes/theme.inc");

$config->scripts->append($config->urls->root . "wire/templates-admin/scripts/inputfields.js?v=5");
$config->scripts->append($config->urls->adminTemplates . "scripts/main.js?v=5");

$browserTitle = wire('processBrowserTitle');
if(!$browserTitle) $browserTitle = __(strip_tags($page->get('title|name')), __FILE__) . ' &bull; ProcessWire';





/*
 * Dynamic phrases that we want to be automatically translated
 *
 * These are in a comment so that they register with the parser, in place of the dynamic __() function calls with page titles.
 *
 * __("Pages");
 * __("Setup");
 * __("Modules");
 * __("Access");
 * __("Admin");
 * __("Site");
 * __("Languages");
 * __("Users");
 * __("Roles");
 * __("Permissions");
 * __("Templates");
 * __("Fields");
 * __("Add New");
 * __("Not yet configured: See template family settings.");
 *
 */

?>
<!DOCTYPE html>
<html lang="<?php echo __('en', __FILE__); // HTML tag lang attribute
	/* this intentionally on a separate line */ ?>">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex, nofollow" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title><?php echo $browserTitle; ?></title>

	<script type="text/javascript">
		<?php

		$jsConfig = $config->js();
		$jsConfig['debug'] = $config->debug;
		$jsConfig['urls'] = array(
			'root' => $config->urls->root,
			'admin' => $config->urls->admin,
			'modules' => $config->urls->modules,
			'core' => $config->urls->core,
			'files' => $config->urls->files,
			'templates' => $config->urls->templates,
			'adminTemplates' => $config->urls->adminTemplates,
			);

		if(!empty($jsConfig['JqueryWireTabs'])) $bodyClass .= ' hasWireTabs';
		?>

		var config = <?php echo json_encode($jsConfig); ?>;
	</script>

	<?php foreach($config->styles->unique() as $file) echo "\n\t<link type='text/css' href='$file' rel='stylesheet' />"; ?>


	<!--[if IE]>
	<link rel="stylesheet" type="text/css" href="<?php echo $config->urls->adminTemplates; ?>styles/ie.css" />
	<![endif]-->

	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo $config->urls->adminTemplates; ?>styles/ie7.css" />
	<![endif]-->

	<?php foreach($config->scripts->unique() as $file) echo "\n\t<script type='text/javascript' src='$file'></script>"; ?>


	<?php if ($adminTheme->custom_css): ?>
		<style>
			<?php echo $adminTheme->custom_css ?>
		</style>
	<?php endif ?>
</head>
<!-- adminTheme id used for overrides -->
<body id="adminTheme" <?php if($bodyClass) echo " class='$bodyClass'"; ?>>

	<?php if(count($notices)) include($config->paths->adminTemplates . "includes/notices.inc"); ?>

	<?php if(!$user->isGuest()): ?>



	<?php endif; ?>

	<div id="masthead" class="masthead ui-helper-clearfix">
		<div class="container">


			<?php echo tabIndent($searchForm, 3); ?>

			<ul id='topnav'>
				<?php include($config->paths->adminTemplates . "includes/topnav.inc"); ?>
				<?php if(!$user->isGuest()): ?>
				<li>
					<?php $class = $page->name == "profile" ? "on" : "" ?>
					<a class='dropdown-toggle <?php echo $class ?>' href='<?php echo $config->urls->admin?>profile/'><i class='fa fa-user'></i></a>
					<ul class='dropdown-menu topnav' data-my='left-1 top' data-at='left bottom'>
						<?php if($user->hasPermission('profile-edit')): ?>
						<li><a href='<?php echo $config->urls->admin?>profile/'><?php echo __('Profile', __FILE__); ?> <small><?php echo $user->name?></small></a></li>
						<?php endif; ?>
						<li><a href='<?php echo $config->urls->admin?>login/logout/'><?php echo __('Logout', __FILE__); ?> <i class='fa fa-sign-out'></i></a></li>
					</ul>
				</li>
				<?php endif; ?>
			</ul>

		</div>
	</div>

	<div class="container">
		<div id="wrapper">
			<div id="headline">
				<div class="container">
					<div id='breadcrumbs'>
						<ul class='nav'>
							<?php
							echo "<li><a class='sitelink' href='{$config->urls->root}'><i class='fa fa-home'></i></a><i class='fa fa-angle-right'></i></li>";
							foreach($this->fuel('breadcrumbs') as $breadcrumb) {
								$title = __($breadcrumb->title, __FILE__);
								echo "<li><a href='{$breadcrumb->url}'>{$title}</a><i class='fa fa-angle-right'></i></li>";
							}
							unset($title);
							echo "<li class='title'>" . __(strip_tags($this->fuel->processHeadline ? $this->fuel->processHeadline : $page->get("title|name")), __FILE__) . "</li>";
							?>
						</ul>
					</div>

					<?php
					if(in_array($page->id, array(2,3,8))) { // page-list
						echo "<div id='head_button'>";
						include($config->paths->adminTemplates . "includes/shortcuts.inc");
						echo "</div>";
					}
					?>


				</div>
			</div>
			<div id="content" class="content fouc_fix">

					<?php if(trim($page->summary)) echo "<h2>{$page->summary}</h2>"; ?>

					<?php if($page->body) echo $page->body; ?>

					<?php echo $content?>
			</div>
		</div>

	</div>


	<?php if($config->debug && $this->user->isSuperuser()) include($config->paths->adminTemplates . "includes/debug.inc"); ?>
	<div id="footer" class="footer">
		<div class="container">

			<?php if(!$user->isGuest()): ?>



			<span id='userinfo'>
				<i class='fa fa-user'></i>
				<?php
				if($user->hasPermission('profile-edit')): ?>
				<a class='action' href='<?php echo $config->urls->admin; ?>profile/'><?php echo $user->name; ?></a> |
				<?php endif; ?>

				<a class='action' href='<?php echo $config->urls->admin; ?>login/logout/'><?php echo __('Logout', __FILE__); ?></a> <i class='fa fa-sign-out'></i>
			</span>
			<!-- <a id='logo' href='<?php echo $config->urls->admin?>'><img width='130' src="<?php echo $config->urls->adminTemplates?>styles/images/logo.png" alt="ProcessWire" /></a> -->
			<?php endif; ?>
			<p>
				ProcessWire <?php echo $config->version . ' <!--v' . $config->systemVersion; ?>--> &copy; <?php echo date("Y"); ?> Ryan Cramer
			</p>



			
		</div>
	</div>
	

	
</body>
</html>

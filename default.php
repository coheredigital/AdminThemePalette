<?php

/**
 * default.php
 *
 * Main markup template file for AdminThemeDefault
 *
 * __('FOR TRANSLATORS: please translate the file /wire/templates-admin/default.php rather than this one.');
 *
 *
 */

if(!defined("PROCESSWIRE")) die();

if(!isset($content)) $content = '';

$searchForm = $user->hasPermission('page-edit') ? $modules->get('ProcessPageSearch')->renderSearchForm() : '';
$version = $adminTheme->version;

$config->styles->append($config->urls->root . "wire/templates-admin/styles/font-awesome/css/font-awesome.min.css");
$config->styles->prepend("{$config->urls->AdminThemePalette}styles/main.css");
$config->styles->append("{$config->urls->AdminThemePalette}styles/theme.css");
if ($adminTheme->disable_dots) $config->styles->append($config->urls->adminTemplates . "styles/nodots.css?v=6");

$config->scripts->append($config->urls->root . "wire/templates-admin/scripts/inputfields.js?v=$version");
$config->scripts->append($config->urls->adminTemplates . "scripts/main.js?v=$version");

require_once(dirname(__FILE__) . "/AdminThemePaletteHelpers.php");
$helpers = new AdminThemePaletteHelpers();
$extras = $adminTheme->getExtraMarkup();

?><!DOCTYPE html>
<html lang="<?php echo $helpers->_('en');
	/* this intentionally on a separate line */ ?>">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex, nofollow" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title><?php echo $helpers->renderBrowserTitle(); ?></title>

	<script type="text/javascript"><?php echo $helpers->renderJSConfig(); ?></script>

	<?php foreach($config->styles as $file) echo "\n\t<link type='text/css' href='$file' rel='stylesheet' />"; ?>

	<?php foreach($config->scripts as $file) echo "\n\t<script type='text/javascript' src='$file'></script>"; ?>

	<?php echo $extras['head']; ?>

</head>


<body class='<?php echo $helpers->renderBodyClass(); ?>'>
<div id="wrapper">
<?php
echo $helpers->renderAdminNotices($notices);
echo $extras['notices'];
?>

<?php if($user->isGuest()): ?>
	<div id="login-box">
		<div id="logo">
        	<img src="<?php echo $config->urls->adminTemplates ?>styles/images/pw-logo.png">
        </div>
        <div class="login-form">
        	<?php echo $content?>
        </div>
	</div>
	<script>
	$(document).ready(function() {
		$(".Inputfields > .Inputfield > .InputfieldHeader").unbind('click');
	});
	</script>

<?php else: ?>
		<div id="header">
			<div id="masthead" class="masthead ui-helper-clearfix">
				<div class="container">
					<?php
					if($user->isLoggedin()) {
						echo $searchForm;
						echo "\n\n<ul id='topnav'>" . $helpers->renderTopNavItems() . "</ul>";
					}
					echo $extras['masthead'];
					?>
				</div>
			</div><!--/#masthead-->
			<div id="dropdowns"></div>
			<div id='breadcrumbs'>
				<div class='container'>
					<?php
					if($page->process == 'ProcessPageList' || ($page->name == 'lister' && $page->parent->name == 'page')) {
						echo $helpers->renderAdminShortcuts();
					}
					?>
					<ul class='nav'><?php echo $helpers->renderBreadcrumbs(); ?></ul>
				</div>
			</div><!--/#breadcrumbs-->
		</div>


		<div id="content" class="content fouc_fix">
			<div class="container">

				<?php
				if(trim($page->summary)) echo "<h2>$page->summary</h2>";
				if($page->body) echo $page->body;
				echo $content;
				echo $extras['content'];
				?>

			</div>
		</div><!--/#content-->

		<?php include($config->paths->adminTemplates . "includes/footer.inc"); ?>





		<?php echo $extras['body']; ?>

	<?php endif; ?>
	</div><!--/#wrapper-->
</body>
</html>

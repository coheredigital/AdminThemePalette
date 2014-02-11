<?php

	// var_dump($adminTheme->dropdown_depth);

function renderTopNavItem(Page $p, $level = 0) {


	$showItem = wire('user')->isSuperuser() ? true : false;
	$info = array();
	$children = $p->numChildren && !$level && $p->name != 'page' ? $p->children("check_access=0") : array();
	$out = '';

	if(!$level && !$showItem) { 
		$checkPages = count($children) ? $children : array($p); 
		foreach($checkPages as $child) {
			if($child->viewable()) {
				$showItem = true;
				break;
			}
		}
	}

	if(!$showItem) return '';

	$class = strpos(wire('page')->path, $p->path) === 0 ? 'on' : '';
	$title = strip_tags((string)$p->get('title|name')); 
	$title = __($title, dirname(__FILE__) . '/default.php'); // translate from context of default.php
	$out .= "<li>";



	if(wire("adminTheme")->dropdown_depth > 0 && !$level && count($children)) {

		$class = trim("$class dropdown-toggle"); 
		$out .= "<a href='$p->url' class='$class'>$title</a>"; 
		$out .= "<ul class='dropdown-menu topnav' data-my='left-1 top' data-at='left bottom'>";

		foreach($children as $c) {
			
			$items = null;

			if(wire('user')->isSuperuser()) {
				if($c->id == 11) {
					$items = wire('templates'); 
				} else if($c->id == 16) {
					$items = wire('fields'); 
				}
				if(count($items) > 20) $items = null; // don't build excessively large lists
			}


			if(wire("adminTheme")->dropdown_depth > 1 && $items) {
				$addLabel = __('Add New', dirname(__FILE__) . '/default.php');
				$out .= "<li><a href='$c->url'>$c->title</a><ul>" . 
					"<li class='add'><a href='{$c->url}add'><i class='icon-plus-sign'></i> $addLabel</a></li>";
				foreach($items as $item) {
					$out .= "<li><a href='{$c->url}edit?id=$item->id'>$item->name</a></li>";
				}
				$out .= "</ul></li>";
			} else {
				$out .= renderTopNavItem($c, $level+1);
			}
		}

		$out .= "</ul>";

	} else {
		$class = $class ? " class='$class'" : '';
		$out .= "<a href='$p->url'$class>$title</a>"; 
	}

	$out .= "</li>";

	return $out; 
}

foreach($pages->get($wire->config->adminRootPageID)->children("check_access=0") as $p) {
	if(!$p->viewable()) continue; 
	echo renderTopNavItem($p);
}

if(!$user->isGuest()): ?>
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
<?php endif;
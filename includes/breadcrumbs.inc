<div id='breadcrumbs'>
	<ul class='nav'>
		<?php
		foreach($this->fuel('breadcrumbs') as $breadcrumb) {
			$title = __($breadcrumb->title, __FILE__);
			echo "<li><a href='{$breadcrumb->url}'>{$title}</a><i class='fa fa-angle-right'></i></li>";
		}
		unset($title);
		echo "<li class='title'>" . __(strip_tags($this->fuel->processHeadline ? $this->fuel->processHeadline : $page->get("title|name")), __FILE__) . "</li>";
		?>
	</ul>
</div>
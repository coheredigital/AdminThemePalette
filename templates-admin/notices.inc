<div id="notices">
	<div class="container notices-remove-container">
		<a href="#" class="notices-remove"><span class="icon-remove"></span></a>
	</div>
	<ul>

	<?php
		foreach($notices as $notice) {
			$text = $notice->text;
			if($notice->class && $config->debug) $text = $notice->class." : ".$text;
			if($notice instanceof NoticeError) {
				$class = "notice-error";
			}
			else{
				$class = 'notice-success';
			}
			echo "<li class='".$class."'><div class='container'>".$text."</div></li>";
		}
	?>
	</ul>	
</div>
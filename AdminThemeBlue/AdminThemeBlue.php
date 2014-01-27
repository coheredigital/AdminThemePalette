<?php

class AdminThemeBlue extends AdminTheme implements Module {

	public static function getModuleInfo() {
		return array(
			'title' => 'Blue Admin Theme',
			'version' => 060,
			'summary' => 'Beautiful, simple, sensible admin theme.',
			'autoload' => 'template=admin'
			); 
	}



}


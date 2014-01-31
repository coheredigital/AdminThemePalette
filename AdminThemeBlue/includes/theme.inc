<?php 


require "lessphp/lessc.inc.php";
$less = new lessc;


$theme_color = $adminTheme->theme_color ? $adminTheme->theme_color : "#555";
$theme_color_name = $adminTheme->theme_color ? $sanitizer->pageName($adminTheme->theme_color, true) : "default";


$less->setVariables(array(
  "theme_color" => $theme_color
));
$theme_css = $less->checkedCompile("{$config->paths->AdminThemeBlue}styles/theme_colors/theme.less", "{$config->paths->AdminThemeBlue}styles/theme_colors/theme_{$theme_color_name}.css");
$config->styles->append("{$config->urls->AdminThemeBlue}styles/theme_colors/theme_{$theme_color_name}.css");
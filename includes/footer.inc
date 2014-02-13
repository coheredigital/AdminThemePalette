<div id="footer" class="footer">
	<div class="container">
		<?php 
		if (!$adminTheme->disable_gravatar){
			$gravatar = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( $user->email ) ) ) . "?d=mm&s=50";
		}
		if ($gravatar){
			$edit = __('profile', __FILE__);
			$user_image = "<div class='user_image'><img class='gravatar' src='{$gravatar}' alt=''></div>";			
			$userinfo_class = "has-gravatar";
		}
		?>
		
		<div id='userinfo' class="<?php echo $userinfo_class ?>">
			
			<?php if($user_image) echo $user_image; ?>
			
			
			<div class="user_details">
				<?php 
				echo "<div class='user_name'>{$user->name}</div>";
				if ($user->email) 
					echo "<div class='user_email'>{$user->email}</div>";
				?>
				<div class="user_actions">
					<?php if ($user->hasPermission('profile-edit')): ?>
					<a href='<?php echo $config->urls->admin ?>profile/'><?php echo __("Profile") ?></a> 
					<?php endif ?>
					| <a class="button-logout" href='<?php echo $config->urls->admin; ?>login/logout/'><?php echo __('Logout', __FILE__); ?></a> <!-- <i class='fa fa-sign-out'></i> -->
				</div>
				
			</div>

		</div>
		<div class="brand">
			<div class="logo">
				<img src="<?php echo $config->urls->AdminThemePalette ?>styles/images/logo_mask_eee.png" alt="">
				<p class="copy">
					ProcessWire <?php echo $config->version . ' <!--v' . $config->systemVersion; ?>--> &copy; <?php echo date("Y"); ?> Ryan Cramer
				</p>
			</div>
		</div>


		<div class="view-site" >
			VIEW SITE<br>
			<a href="<?php echo $config->urls->root ?>">
				<?php echo "http&#58;&#47;&#47;{$config->httpHost}&#47" ?>;
			</a> 
		</div>



		
	</div>
</div>
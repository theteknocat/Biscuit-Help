<p>Welcome to the <?php echo SITE_TITLE; ?> Help System. This page provides general help and information for how to use the functional features of your website.</p>
<div id="help-tabs">
	<ul>
		<li><a href="#help-general-info">General Functionality</a></li>
		<li><a href="#help-admin-menu">The Admin Menu</a></li>
		<?php
		if (!empty($other_help_items)) {
			?>
		<li><a href="#help-more-help">More Help</a></li>
			<?php
		}
		?>
	</ul>
	<div id="help-general-info">
		<p>Functionality and content management features can be found right in the pages of your site when you are logged in. There is no separate "back end" area. Rather, you will see buttons in appropriate places in any given page for performing the functions you have permission to access.</p>
		<p>As such, for the most part if you want to edit the content of any given page, go to that page and you will see buttons for performing all operations you have permission for. Here are some examples of administration buttons you may see on any given page when logged in:</p>
		<ul>
			<li><strong>Module administration bar:</strong><br>
				<img src="/framework/modules/help/images/module-admin-bar.jpg" alt="Module administration bar"><br>
				This type of bar may appear depending on which page you are on and what modules are installed on the page. This example is the page content management module bar. It provides buttons for general functions of the module, which vary from one module to another.</li>
			<li><strong>In-line administration buttons:</strong><br>
				<img src="/framework/modules/help/images/inline-buttons-1.jpg" alt="In-line admin buttons"><img src="/framework/modules/help/images/inline-buttons-2.jpg" alt="In-line administration buttons"><br>
				Buttons like these may appear next to individual items on a page that you can edit or delete, depending on your permissions. These are examples from the news and events module and the photo gallery module.</li>
		</ul>
	</div>
	<div id="help-admin-menu">
		<p>When logged in you should see this icon in the top left-hand corner of your website: <img src="/framework/modules/help/images/admin-menu-icon.jpg" alt="Admin menu icon" style="vertical-align: middle"></p>
		<p>Clicking on this icon gives you access to an administration menu with links to functionality that may not otherwise be accessible through buttons within the page. At the very least, this menu will provide access to the help system. If you see nothing other than help in this menu, that means there is no functionality you need, or have permission, to access beyond what you can see in the page when you are logged in.</p>
		<p>Here is an example of what you might see in the menu if you were logged in to a site with a variety of modules installed:<br>
			<img src="/framework/modules/help/images/admin-menu-1.jpg" alt="Super admin menu"></p>
		<p>Clicking on any main menu item will expand it to provide you with links to specific functions, for example:<br>
			<img src="/framework/modules/help/images/admin-menu-2.jpg" alt="Banner ads admen menu"></p>
	</div>
	<?php
	if (!empty($other_help_items)) {
		?>
	<div id="help-more-help">
		<p>Additional help is available for the following items:</p>
		<ul>
		<?php
		foreach ($other_help_items as $human_name => $url) {
			?>
			<li><a href="<?php echo $url; ?>"><?php echo $human_name; ?></a></li>
			<?php
		}
		?>
		</ul>
	</div>
		<?php
	}
	?>
</div>

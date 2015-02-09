<?php
function cdash_admin_menus() {
	$welcome_page_title = __('Welcome to Chamber Dashboard Business Directory', 'cdash');
	// About
	$about = add_dashboard_page($welcome_page_title, $welcome_page_title, 'manage_options', 'cdash-about', 'cdash_about_screen');
}
add_action('admin_menu', 'cdash_admin_menus');
	
// remove dashboard page links.
function cdash_admin_head() {
	remove_submenu_page( 'index.php', 'cdash-about' );
}
add_action('admin_head', 'cdash_admin_head');

	
// Display the welcome page
function cdash_about_screen() 
	{
		?>
		<div class="wrap about-wrap">

		<h1><?php _e('Welcome to Chamber Dashboard Business Directory', 'cdash'); ?></h1>

		<div class="about-text cdash-about-text">
			<?php
				_e('Power your Chamber of Commerce with Chamber Dashboard', 'cdash');
			?>
		</div>
		<p class="cdash-actions">
			<a href="<?php echo esc_url(admin_url('admin.php?page=chamber-dashboard-business-directory/options.php')); ?>" class="button button-primary"><?php _e('Settings', 'cdash'); ?></a>
			<a href="http://chamberdashboard.com/support/documentation/" class="button button-primary" target="_blank"><?php _e('Documentation', 'cdash'); ?></a>
			<a href="http://chamberdashboard.com/support/" class="button button-primary" target="_blank"><?php _e('Support', 'cdash'); ?></a>
		</p>

		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php if ($_GET['page'] == 'cdash-about') echo 'nav-tab-active'; ?>" href="<?php echo esc_url(admin_url(add_query_arg( array( 'page' => 'cdash-about'), 'index.php'))); ?>">
				<?php _e('About Chamber Dashboard', 'cdash'); ?>		
			</a>
		</h2>

			<div class="changelog">

				<h3><?php _e('Main Features', 'cdash'); ?></h3>

				<div class="feature-section col three-col">

					<div>
						<h4><?php _e('Create Your Directory', 'cdash'); ?></h4>
						<p><?php _e('Build a website to showcase your members\' businesses more effectively.  Customize your listings to meet member needs.', 'cdash'); ?></p>
					</div>

					<div>
						<h4><?php _e('Import Existing Member Listings', 'cdash'); ?></h4>
						<p><?php _e('Easily import listings from or export listings to a CSV file.  Customize your listings to meet member needs.', 'cdash'); ?></p>
					</div>

					<div class="last-feature">
						<h4><?php _e('Display Directory on your Website', 'cdash'); ?></h4>
						<p><?php _e('Display member businesses as a complete, searchable directory or single listing. Turn your site into a community resource of local business connections.', 'cdash'); ?></p>
					</div>

				</div>
				
			</div>

			<div class="changelog">
			
				<h3><?php _e('Other Chamber Dashboard Plugins', 'cdash'); ?></h3>
				<p><?php _e('The Business Directory is just one in a suite of plugins designed to make your Chamber or organization an easy to manage, central source of information regarding local businesses, event management and professional communication.', 'cdash'); ?></p>

				<div class="feature-section col three-col">
					<div>
						<h4><a href="https://wordpress.org/plugins/chamber-dashboard-crm/" target="_blank"><?php _e('Chamber Dashboard CRM', 'cdash'); ?></a></h4>
						<p><?php _e('Customer Relationship Management has evolved. Organizations today need tools that enhance customer interaction from marketing to sales to follow up services. This plugin creates a directory of the people associated with your organization and allows you to track the progression of your relationship with them.', 'cdash'); ?></p>
					</div>
					<div>
						<h4><?php _e('Chamber Dashboard Events Calendar - Coming Soon!', 'cdash'); ?></h4>
						<p><?php _e('Create an events calendar on your site. Track event registrations and attendees, connect and encourage members to grow their professional networks. ', 'cdash'); ?></p>
					</div>
					
					<div class='last-feature'>
						<h4><?php _e('Chamber Dashboard Member Management - Coming Soon!', 'cdash'); ?></h4>
						<p><?php _e('Streamline management of your organization. The Member Management plugin allows you to track membership levels, accept online member payments, and more, freeing you up to work with your members on your core mission.', 'cdash'); ?></p>
					</div>
					
				</div>

			</div>

			<div class="return-to-dashboard">
				<a href="<?php echo esc_url(admin_url('admin.php?page=chamber-dashboard-business-directory/options.php')); ?>"><?php _e( 'Go to Chamber Dashboard Settings', 'cdash' ); ?></a>
			</div>
		</div>
		<?php
	}
	
// Redirect to welcome page after activation
function cdash_welcome() 
{

	// Bail if no activation redirect transient is set
    if (!get_transient('_cdash_activation_redirect'))
		return;

	// Delete the redirect transient
	delete_transient('_cdash_activation_redirect');		
	
	// Bail if activating from network, or bulk, or within an iFrame
	if (is_network_admin() || isset($_GET['activate-multi']) || defined('IFRAME_REQUEST'))
		return;

	if ((isset($_GET['action']) && 'upgrade-plugin' == $_GET['action']) && (isset($_GET['plugin']) && strstr($_GET['plugin'], 'cdash-business-directory.php')))
		return;

	wp_safe_redirect(admin_url('index.php?page=cdash-about'));
	exit;
}
add_action('admin_init', 'cdash_welcome');
	
?>
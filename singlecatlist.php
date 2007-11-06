<?php
/*
Plugin Name: Single Bookmark Category List
Description: List bookmarks from one bookmark category. Licensed under <a href="http://www.gnu.org/licenses/gpl.txt">GPL v2</a>.
Author: Azmeen (HTNet)
Version: 1.1
Author URI: http://www.heritage-tech.net
*/

/*
Version Info
-----------------
1.0	[5 Feb 2007] :	Initial public release
1.1	[18 May 2007] :	Fixes to provide compatibility with WP2.2
*/

function widget_sclw_init()
{
	// Check for the required API functions
	if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
		return;

	function widget_sclw($args, $number = 1) {
		extract($args);
		$options = get_option('widget_sclw');
		$catid = $options[$number]['catid'];

		echo $before_widget;
		$thecat = get_category($catid, ARRAY_A);
		echo $before_title . $thecat['cat_name'] . $after_title;
		echo "<ul>\n";
		get_links($catid, '<li>', "</li>", "\n", true, 'name', false);
		echo "</ul>\n";
		echo $after_widget;
	}

	function widget_sclw_control($number) {
			$options = $newoptions = get_option('widget_sclw');
			if ( !is_array($options) )
				$options = $newoptions = array();
			if ( $_POST["sclw-submit-$number"] ) {
				$newoptions[$number]['catid'] = stripslashes($_POST["sclw-catid-$number"]);
			}
			if ( $options != $newoptions ) {
				$options = $newoptions;
				update_option('widget_sclw', $options);
			}
			$catid = attribute_escape($options[$number]['catid']);
	?>
					<p style="text-align: center">Select Category:</p>
					<p style="text-align: center">
					<?php
					$categories = get_categories("hide_empty=1&type=link");
					$select_cat = "<select name=\"sclw-catid-$number\">\n";
					foreach ((array) $categories as $cat)
						$select_cat .= '<option value="' . $cat->cat_ID . '"' . (($cat->cat_ID == $catid) ? " selected='selected'" : '') . '>' . wp_specialchars($cat->cat_name) . "</option>\n";
					$select_cat .= "</select>\n";
					echo $select_cat;
					?>
					<input type="hidden" id="sclw-submit-<?php echo "$number"; ?>" name="sclw-submit-<?php echo "$number"; ?>" value="1" />
					</p>
	<?php
	}

	function widget_sclw_setup() {
			$options = $newoptions = get_option('widget_sclw');
			if ( isset($_POST['sclw-number-submit']) ) {
				$number = (int) $_POST['sclw-number'];
				if ( $number > 9 ) $number = 9;
				if ( $number < 1 ) $number = 1;
				$newoptions['number'] = $number;
			}
			if ( $options != $newoptions ) {
				$options = $newoptions;
				update_option('widget_sclw', $options);
				widget_sclw_register($options['number']);
			}
	}

	function widget_sclw_page() {
			$options = $newoptions = get_option('widget_sclw');
	?>
			<div class="wrap">
				<form method="POST">
					<h2><?php _e('Single Bookmark Category Widgets'); ?></h2>
					<p style="line-height: 30px;"><?php _e('How many single bookmark category widgets would you like?'); ?>
					<select id="sclw-number" name="sclw-number" value="<?php echo $options['number']; ?>">
	<?php for ( $i = 1; $i < 10; ++$i ) echo "<option value='$i' ".($options['number']==$i ? "selected='selected'" : '').">$i</option>"; ?>
					</select>
					<span class="submit"><input type="submit" name="sclw-number-submit" id="sclw-number-submit" value="<?php echo attribute_escape(__('Save')); ?>" /></span></p>
				</form>
			</div>
	<?php
	}

	function widget_sclw_register() {
			$options = get_option('widget_sclw');
			$number = $options['number'];
			if ( $number < 1 ) $number = 1;
			if ( $number > 9 ) $number = 9;
			$dims = array('width' => 380, 'height' => 100);
			$class = array('classname' => 'widget_sclw');
			for ($i = 1; $i <= 9; $i++) {
				$name = sprintf(__('Bookmarks From Category %d'), $i);
				$id = "sclw-$i"; // Never never never translate an id
				wp_register_sidebar_widget($id, $name, $i <= $number ? 'widget_sclw' : /* unregister */ '', $class, $i);
				wp_register_widget_control($id, $name, $i <= $number ? 'widget_sclw_control' : /* unregister */ '', $dims, $i);
			}
			add_action('sidebar_admin_setup', 'widget_sclw_setup');
			add_action('sidebar_admin_page', 'widget_sclw_page');
	}
	// Delay plugin execution to ensure Dynamic Sidebar has a chance to load first
	widget_sclw_register();
}

	
// Tell Dynamic Sidebar about our new widget and its control
add_action('plugins_loaded', 'widget_sclw_init');

?>

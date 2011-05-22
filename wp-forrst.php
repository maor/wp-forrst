<?php
/*
Plugin Name: WP-Forrst
Plugin URI: http://github.com/maor
Description: Add your Forrst profile to your WordPress sidebar.
Author: Maor Chazen - <a href='http://forrst.com/people/maor'>@maor</a> on forrst
Author URI: http://360signals.com
Version: 1.0.0
*/


class WP_Forrst extends WP_Widget {
	
	var $plugin_path;
	var $plugin_URL;
	
	function WP_Forrst()
	{
		$widget_options = array(
			'classname' => 'forrst_w',
			'description' => 'Share with the world your Forrst profile'
		);
		$control_options = array(
			'user' => 'maor',
			'show_photo' => 1,
			'show_bio' => 0
		);
		
		
		parent::WP_Widget('forrst_widget', 'Forrst', $widget_options, $control_options);
		
		// Set Plugin Path
		$this->plugin_path = dirname(__FILE__);
	
		// Set Plugin URL
		$this->plugin_URL = WP_PLUGIN_URL . '/wp-forrst';
	}
	

	function form($instance) {
		
		// outputs the options form on admin
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'user' => 'maor', 'show_photo' => 1, 'show_bio' => 0 ) );
		
		$title = strip_tags($instance['title']);
		$user = strip_tags($instance['user']);
		$show_photo = strip_tags($instance['show_photo']);
		$show_bio = strip_tags($instance['show_bio']);
	?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_id('user'); ?>">Username:</label>
				<input class="widefat" id="<?php echo $this->get_field_id('user'); ?>" name="<?php echo $this->get_field_name('user'); ?>" type="text" value="<?php echo attribute_escape($user); ?>" />
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_id( 'show_photo' ); ?>">Show avatar?</label>
				<input class="checkbox" type="checkbox" <?php echo ($show_photo == 'on') ? 'checked="checked"' : ''; ?> id="<?php echo $this->get_field_id( 'show_photo' ); ?>" name="<?php echo $this->get_field_name( 'show_photo' ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'show_bio' ); ?>">Show Bio?</label>
				<input class="checkbox" type="checkbox" <?php echo ($show_bio == 'on') ? 'checked="checked"' : ''; ?> id="<?php echo $this->get_field_id( 'show_bio' ); ?>" name="<?php echo $this->get_field_name( 'show_bio' ); ?>" />
			</p>
			
	<?php
	}

	function update($new_instance, $old_instance) {
		
		$instance = $old_instance;
		
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['user'] = strip_tags($new_instance['user']);
		$instance['show_photo'] = $new_instance['show_photo'];
		$instance['show_bio'] = $new_instance['show_bio'];
		
		return $instance;
	}

	function widget($args, $instance) {
		// outputs the content of the widget
		extract($args, EXTR_SKIP);
		
		echo $before_widget;
		
		$title = apply_filters('widget_title', $instance['title'] );
		
		$user = $instance['user'];
		$show_photo = isset( $instance['show_photo'] ) ? $instance['show_photo'] : false;
		$show_bio = isset( $instance['show_bio'] ) ? $instance['show_bio'] : false;
		
		
		// information
		$info = $this->get_user_info($user);
		
		
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };
		?>
		
		<div class="container">
		
			<div class="box_em">
				<div class="photo">
					<?php if ($show_photo == true) : ?>
					<a href="<?php echo $info['url'] ?>">
						<img src="<?php echo $info['photos']['thumb_url']?>" alt="<?php echo $info['username']?>" class="fr_avatar"/>
					</a>
					<?php endif; ?>
				</div>
				
				<div class="meta">
					<h3 class="username"><a href="<?php echo $info['url'] ?>"><?php echo $info['name']?> <span><?php echo $info['username']?></span></a></h3>
					<p class="metadata">is a <?php echo $info['is_a'] ?></p>
				</div>
			</div>
			
			<?php if ($show_bio) : ?>
			<div class="bio">
				<?php echo $info['bio'] ?>
			</div>
			<?php endif; ?>
			
			<div class="follow-block">
				<a class="unfollow" href="<?php echo $info['url'] ?>/follow">Follow</a>            
			</div>
			
		</div>
		
		<?php
		echo $after_widget;
	}
	
	public function get_user_info($user)
	{
		include 'Forrst_API.php';
		
		//$WP_Forrst = new WP_Forrst;
		
		$Forrst_API = new Forrst_API($user);
		
		$results = $Forrst_API->get_user_info();
		
		return $results;
	}
	
	function css_handler()
	{
		$css_file = WP_PLUGIN_URL . '/wp-forrst/wp-forrst-styles.css';
		
        wp_enqueue_style( 'forrst', $css_file);

	}
	
}

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'forrst_load_widgets' );

/**
 * Register our widget.
 * 'Example_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function forrst_load_widgets() {
	register_widget( 'WP_Forrst' );
}

// add_action('wp_head', array('WP_Forrst', 'css_handler'));
add_action( 'wp_print_styles', array('WP_Forrst', 'css_handler'));
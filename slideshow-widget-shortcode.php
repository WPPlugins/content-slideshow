<?php
/**
 * Widget & shortcode that display random images from your media library as an embedded iframe of the Content Slideshow plugin's page.
*/

// Register 'Content Slideshow' widget.
function content_slideshow_widget_init() {
	return register_widget( 'Content_Slideshow_Widget' );
}
add_action( 'widgets_init', 'content_slideshow_widget_init' );

// Add the Content Slideshow shortcode.
add_shortcode( 'content_slideshow', 'content_slideshow_do_shortcode' );
function content_slideshow_do_shortcode( $atts ){
	extract( shortcode_atts( array(
		'size'  => 'auto',
		'year'  => '',
		'month' => '',
	), $atts ) );

	return content_slideshow_get_embed( $size, $year, $month );
}

class Content_Slideshow_Widget extends WP_Widget {
	/* Constructor */
	function __construct() {
		parent::__construct( 'Content_Slideshow_Widget', __( 'Content Slideshow', 'content-slideshow' ), array( 
			'customize_selective_refresh' => true,
		) );
	}

	/* This is the Widget */
	function widget( $args, $instance ) {
		global $post;
		extract( $args );

		if ( ! array_key_exists( 'size', $instance ) ) {
			$instance['size'] = 'medium';
		}

		if ( ! array_key_exists( 'title', $instance ) ) {
			$instance['title'] = '';
		}

		// Widget options
		$title = apply_filters('widget_title', $instance['title'] ); // Title
		$size  = ( in_array( $instance['size'], array( 'thumbnail', 'medium', 'large', 'full' ) ) ? $instance['size'] : 'medium' );

		// Output
		echo $before_widget;

		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		echo content_slideshow_get_embed( $size, '', '' );

		echo $after_widget;
	}

	/* Widget control update */
	function update( $new_instance, $old_instance ) {
		$instance    = $old_instance;
		$instance['title']  = strip_tags( $new_instance['title'] );
		$instance['size'] = ( in_array( $new_instance['size'], array( 'thumbnail', 'medium', 'large', 'full' ) ) ? $new_instance['size'] : 'medium' );

		return $instance;
	}

	/* Widget settings */
	function form( $instance ) {
	    if ( $instance ) {
			$title = $instance['title'];
			$size  = $instance['size'];
	    }
		else {
		    // These are the defaults.
			$title = '';
			$size = 'medium';
	    }

		// The widget form. ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __( 'Title:', 'content-slideshow' ); ?></label>
			<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" class="widefat" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('size'); ?>"><?php echo __( 'Image Size:', 'content-slideshow' ); ?></label>
			<select name="<?php echo $this->get_field_name('size'); ?>" id="<?php echo $this->get_field_id('size'); ?>" class="widefat">
				<option value="thumbnail" <?php if( $size == 'thumbnail' ) { echo 'selected="selected"'; } ?>>Thumbnail</option>
				<option value="medium" <?php if( $size == 'medium' ) { echo 'selected="selected"'; } ?>>Medium</option>
				<option value="large" <?php if( $size == 'large' ) { echo 'selected="selected"'; } ?>>Large</option>
				<option value="full" <?php if( $size == 'full' ) { echo 'selected="selected"'; } ?>>Full</option>
			</select>
		</p>
	<?php 
	}

} // class Content_Slideshow_Widget

function content_slideshow_get_embed( $size = 'auto', $year = '', $month = '' ) {
	$url = '/slideshow';
	$args = array(
		'size'  => $size,
		'year'  => $year,
		'month' => $month,
	);
	$url = add_query_arg( $args, $url );

	$html = '<a href="' . home_url( remove_query_arg( 'size', $url ) ) . '" target="_blank"><div class="content-slideshow-widget-container" style="position: relative; width: 100%; height: 0; padding-bottom: 66.67%;">';
	$html .= '<iframe src="' . home_url( $url ) . '" style="position: absolute; top:0; left: 0; width: 100%; height: 100%; border: none;"></iframe>';
	$html .= '</div></a>';

	return $html;
}

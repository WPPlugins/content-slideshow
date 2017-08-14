<?php
/*
 * Template that displays a slideshow of random pictures from the media library.
 * Please note that it is not possible to pause the slideshow or go back;
 * this template works best when you need to display pictures related to
 * your business/organization in the background at an event or in your office.
 *
 * Parameters are controlled via query string.
 *
 * `size` is the size of the image to load, either `thumbnail` (discouraged), `medium`, `large`, `full`, or `auto`, which uses medium or large depending on wp_is_mobile()
 * `year` is the 4-digit numeric year in which the images were published.
 * `month` is the numeric month in which the images were published (between 1 and 12).
 *
 * Using all custom options, for example:
 * http://example.com/slideshow?hidpi&year=2013&month=12
 */


do_action( 'wp_enqueue_scripts' );
wp_enqueue_script( 'content-slideshow', plugins_url( '/content-slideshow.js', __FILE__ ), array( 'jquery', 'wp-util' ), null, true );

// Get options, via query string.

// Size of image to load.
$size = ( array_key_exists( 'size', $_GET ) ? $_GET['size'] : 'auto' );

// Year uploaded, in 4-digit numeric form.
$year = ( array_key_exists( 'year', $_GET ) ? absint( $_GET['year'] ) : false );

// Month uploaded, as an int between 1 and 12.
$month = ( array_key_exists( 'month', $_GET ) ? absint( $_GET['month'] ) : false );

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<title><?php printf( __( 'Slideshow :: %s', 'content-slideshow' ), get_bloginfo( 'title' ) ); ?></title>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width" />
	<style type="text/css">
		* { box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; }
		img, figure, figcaption { transition: .32s opacity ease-in-out; }
		html { background: #222; }
		body { margin: 0; padding: 0; overflow: hidden; color: #fff; font-family: sans-serif; }
		figure { width: 100%; height: 100%; margin: 0; padding: 0; text-align: center; vertical-align: middle; position: fixed; left: 0; top: 100%; opacity: .1; background: #222; }
		img { width: auto; height: auto; margin: auto; }
		img.landscape { width: 100%; }
		img.portrait { height: 100%; }
		figcaption { font-family: sans-serif; font-size: 28px; font-weight: normal; font-style: normal; position: absolute; bottom: 0; text-align: center; width: 100%; padding: 12px 15%; margin: 0; background: rgba(0,0,0,.5); color: #fff; text-shadow: 1px 1px 1px #000; }
		@-ms-viewport { width: device-width; }
		@viewport { width: device-width; }
		@media screen and (max-width: 1024px) { figcaption { font-size: 22px; } }
		@media screen and (max-width: 600px) { figcaption { font-size: 16px; } }
		@media screen and (max-width: 400px) { figcaption { font-size: 12px; } }
	</style>
<?php 
	/*
	 * Action in the <head> of the Content Slideshow page template.
	 *
	 * wp_head() is NOT called on this page because it is independent of the rest of the site.
	 * However, this hook is provided so that custom styling or functionality can be applied.
	 */
	do_action( 'content_slideshow_head' );
?>
</head>
<body>
<?php
	// Get all images, searching only for image/jpeg: this should return pictures but not graphics, as long as files are uploaded in the appropriate format
	$query_image_args = array(
		'post_type' => 'attachment',
		'post_mime_type' =>'image/jpeg',
		'post_status' => 'inherit', // required for images, which are children of posts
		'posts_per_page' => 500,
		'max_num_pages' => 1,
		'orderby' => 'rand',
		// custom options
		'year' => $year,
		'monthnum' => $month,
	);

	$images = new WP_Query( $query_image_args );

	if ( ! $images->have_posts() ) {
		echo '<h1>' . __( 'Sorry, there are no images to display here.', 'content-slideshow' ) . '</h1></figure></body></html>';
		exit;
	}

	$sizes = array( 'thumbnail', 'medium', 'large', 'full' );
	if( in_array( $size, $sizes ) ) {
		$size = $size;
	} elseif( wp_is_mobile() ) {
		$size = 'medium';
	} else {
		$size = 'large';
	}
	
	$urls = array();
	foreach( $images->posts as $image ) {
		// caption
		if ( $image->post_excerpt ) { // caption field
			$caption = $image->post_excerpt;
		} elseif ( $image->post_content ) { // description field
			$caption = $image->post_content;
		} elseif ( $image->post_title ) { // title field
			$caption = $image->post_title;
		} else {
			$caption = '';
		}
		$id = $image->ID;
		$img = wp_get_attachment_image_src( $id, $size );
		$urls[] = array(
			'id' => $id,
			'src' => $img[0],
			'url' => get_attachment_link( $id ),
			'caption' => $caption,
			'orientation' => ( $img[1] > $img[2] ? 'landscape' : 'portrait' ),
		);
	}
?>
	<script type="text/javascript">var contentSlideshowData = <?php echo wp_json_encode( $urls ); ?></script>
	<script type="text/html" id="tmpl-single-image-figure">
		<figure id="figure-{{ data.id }}">
		<figcaption>{{ data.caption }}</figcaption>
			<a href="{{ data.url }}" target="_blank" tabindex="-1">
				<img src="{{ data.src }}" class="{{ data.orientation }}" id="image-{{ data.id }}">
			</a>
		</figure>
	</script>
<?php
	/* 
	 * Action just before </body> of the Content Slideshow page template.
	 *
	 * wp_footer() is NOT called on this page because it is independent of the rest of the site.
	 * However, this hook is provided so that custom markup or functionality can be applied.
	 */
	do_action( 'content_slideshow_footer' );
	print_footer_scripts();
?>
</body>
</html>
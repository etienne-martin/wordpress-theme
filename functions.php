<?php 
	
// Enqueues scripts and styles
function twentysixteen_scripts() {
	
	// Remove wp-embed.min.js
	wp_deregister_script( 'wp-embed' );
	
	// Theme css
	wp_enqueue_style( 'twentysixteen-style', get_stylesheet_uri() );
	// Theme javascript
	wp_enqueue_script( 'twentysixteen-script', get_template_directory_uri() . '/build/build.js', array( 'jquery' ), '20160816', true );
}
add_action( 'wp_enqueue_scripts', 'twentysixteen_scripts' );
	
// Disable the admin bar	
add_filter('show_admin_bar', '__return_false');

// Add svg support in the media uploader
function cc_mime_types($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');
// Fix svg thumbnail preview in the media uploader
function common_svg_media_thumbnails($response, $attachment, $meta){
    if($response['type'] === 'image' && $response['subtype'] === 'svg+xml' && class_exists('SimpleXMLElement'))
    {
        try {
            $path = get_attached_file($attachment->ID);
            if(@file_exists($path))
            {
                $svg = new SimpleXMLElement(@file_get_contents($path));
                $src = $response['url'];
                $width = (int) $svg['width'];
                $height = (int) $svg['height'];

                //media gallery
                $response['image'] = compact( 'src', 'width', 'height' );
                $response['thumb'] = compact( 'src', 'width', 'height' );

                //media single
                $response['sizes']['full'] = array(
                    'height'        => $height,
                    'width'         => $width,
                    'url'           => $src,
                    'orientation'   => $height > $width ? 'portrait' : 'landscape',
                );
            }
        }
        catch(Exception $e){}
    }

    return $response;
}
add_filter('wp_prepare_attachment_for_js', 'common_svg_media_thumbnails', 10, 3);

// Add theme option page
if( function_exists('acf_add_options_page') ) {
	acf_add_options_page();	
	acf_add_options_sub_page('Header');
	//acf_add_options_sub_page('Placeholders');	
	//acf_add_options_sub_page('Favicons');	
	//acf_add_options_sub_page('404');	
	acf_add_options_sub_page('Footer');	
}

// Add custom image sizes
add_action( 'after_setup_theme', 'setup' );
function setup() {
    //add_image_size('fullHD', 1920, 1080, array('center','center'));
    //add_image_size('800x600', 800, 600, array('center','center'));
}

// Removes comments from admin menu
add_action( 'admin_menu', 'my_remove_admin_menus' );
function my_remove_admin_menus() {
    remove_menu_page( 'edit-comments.php' );
}
// Removes comments from post and pages
add_action('init', 'remove_comment_support', 100);
function remove_comment_support() {
    remove_post_type_support( 'post', 'comments' );
    remove_post_type_support( 'page', 'comments' );
}
// Removes comments from admin bar
function mytheme_admin_bar_render() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
}
add_action( 'wp_before_admin_bar_render', 'mytheme_admin_bar_render' );

// Change the Login Logo
function my_login_logo() { ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/IMG/loginLogo.svg);
            width: 150px;
            height: 72px;
            background-size: cover;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );

// Remove unwanted HTML comments from SVG
function remove_html_comments($content = ''){
	$openingTag = strpos($content, "<svg");
	$closingTag = strrpos($content, "</svg>");		
    return substr($content, $openingTag, $closingTag);
}
	
?>
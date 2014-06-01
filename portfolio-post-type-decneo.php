<?php
/**
 * Portfolio Post Type
 *
 * @package   PortfolioPostType
 * @author    Decneo
 * @license   GPL-2.0+
 * @link      http://themeforest.net/user/Decneo
 * @copyright 2013 Decneo
 *
 * @wordpress-plugin
 * Plugin Name: Portfolio Post Type By Decneo
 * Plugin URI:  http://themeforest.net/user/Decneo
 * Description: Enables a portfolio post type and taxonomies.
 * Version:     1.0
 * Author:      Decneo
 * Author URI:  http://themeforest.net/user/Decneo
 * Text Domain: portfolioposttype
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 */

if ( ! class_exists( 'Portfolio_Post_Type' ) ) :

class Portfolio_Post_Type {

	public function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_textdomain' ) );

		// Give the portfolio menu item a unique icon
		add_action( 'admin_head', array( $this, 'portfolio_icon' ) );
		
		add_action('init', 'create_portfolio');

		function create_portfolio() {
			$portfolio_args = array(
				'label' => __('Portfolio', 'multipixels'),
				'singular_label' => __('Portfolio', 'multipixels'),
				'public' => true,
				'show_ui' => true,
				'capability_type' => 'post',
				'hierarchical' => false,
				'rewrite' => array('slug' =>'Portfolio', 'with_front' => true),
				'supports' => array('title', 'editor', 'thumbnail', 'author', 'comments', 'excerpt')
				);
			register_post_type('portfolio', $portfolio_args);
			
		}

		add_action('init', 'portfoliocategory', 0);
		add_theme_support('post-thumbnails');

		function portfoliocategory() {
			register_taxonomy(
					'portfoliocategory',
					'portfolio',
					array(
						'hierarchical' => true,
						'label' => 'Categories',
						'query_var' => true,
						'rewrite' => true
					)
			);
		}


		add_action("template_redirect", 'redirecttosingle');
		function redirecttosingle(){
		global $wp_query;
		$query_post_type = $wp_query->query_vars["post_type"];
		if ($query_post_type == "portfolio"){
				  if (have_posts()){
						global $decneo_postcat;
						$postcat = "portfoliocategory";
						require(TEMPLATEPATH . '/single_portfolio.php');
						die();
					}else{
						$wp_query->is_404 = true;
					}
		}
		}

	}

	/**
	 * Load the plugin text domain for translation.
	 */
	public function load_textdomain() {

		$domain = 'portfolioposttype';
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages' );
	}


	/**
	 * Display the custom post type icon in the dashboard.
	 */
	public function portfolio_icon() {
		$plugin_dir_url = plugin_dir_url( __FILE__ );
		?>
		<style>
			#menu-posts-portfolio .wp-menu-image {
				background: url(<?php echo $plugin_dir_url; ?>images/portfolio-icon.png) no-repeat 6px 6px !important;
			}
			#menu-posts-portfolio:hover .wp-menu-image, #menu-posts-portfolio.wp-has-current-submenu .wp-menu-image {
				background-position: 6px -16px !important;
			}
			#icon-edit.icon32-posts-portfolio {
				background: url(<?php echo $plugin_dir_url; ?>images/portfolio-32x32.png) no-repeat;
			}
		</style>
		<?php
	}

}

new Portfolio_Post_Type;

endif;


/*    portfolio Option  */

add_action('admin_init', 'port_init');

function port_init() {
	add_meta_box("page-options", __( 'Page Options', 'multipixels' ), "page_options", "portfolio", "normal", "high");
	add_action('save_post','update_page_data');
}

/* portfolio Options */

add_action("admin_init", "add_portfolio_option");
add_action('save_post', 'update_portfolio_option');

function add_portfolio_option(){
add_meta_box("portfolio-options", "Portfolio Options", "portfolio_options", "portfolio", "normal", "high");
}

function update_portfolio_option(){
	global $post;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }
	if($post){
		if( isset($_POST["website_url"]) ) {
			update_post_meta($post->ID, "website_url", $_POST["website_url"]);
		}
		if( isset($_POST["video_link"]) ) {
			update_post_meta($post->ID, "video_link", $_POST["video_link"]);
		}	
	}
}

function portfolio_options(){
	global $post;
	$custom = get_post_custom($post->ID);
	if (isset($custom["website_url"][0])){
		$website_url = $custom["website_url"][0];
	}else{
		$website_url = "";
	}
	if (isset($custom["video_link"][0])){
		$video_link = $custom["video_link"][0];
	}else{
		$video_link = "";
	}
?>

    <div id="portfolio-details">
        <table cellpadding="15" cellspacing="15">
            <tr>
            	<td><label>Your Project Link: <i style="color: #ff0000;"><br/>(Optional)</i></label></td><td><input name="website_url" style="width:300px" value="<?php echo $website_url; ?>" /></td>
            </tr>
            <tr>
                <td><label>Youtube,Vimeo or another custom link: <i style="color: #ff0000;"><br/>(For portfolio video view)</i></label></td><td><input name="video_link" style="width:300px" value="<?php echo $video_link; ?>" /></td>	
            </tr>
        </table>
    </div>
      
<?php
}
?>

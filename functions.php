<?php

/**
 * Timber starter-theme
 * https://github.com/timber/starter-theme
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

use function PHPSTORM_META\map;

/**
 * If you are installing Timber as a Composer dependency in your theme, you'll need this block
 * to load your dependencies and initialize Timber. If you are using Timber via the WordPress.org
 * plug-in, you can safely delete this block.
 */
$composer_autoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($composer_autoload)) {
	require_once $composer_autoload;
	$timber = new Timber\Timber();
}

/**
 * This ensures that Timber is loaded and available as a PHP class.
 * If not, it gives an error message to help direct developers on where to activate
 */
if (!class_exists('Timber')) {

	add_action(
		'admin_notices',
		function () {
			echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url(admin_url('plugins.php#timber')) . '">' . esc_url(admin_url('plugins.php')) . '</a></p></div>';
		}
	);

	add_filter(
		'template_include',
		function ($template) {
			return get_stylesheet_directory() . '/static/no-timber.html';
		}
	);
	return;
}

/**
 * Sets the directories (inside your theme) to find .twig files
 */
Timber::$dirname = array('templates', 'views');

/**
 * By default, Timber does NOT autoescape values. Want to enable Twig's autoescape?
 * No prob! Just set this value to true
 */
Timber::$autoescape = false;


/**
 * We're going to configure our theme inside of a subclass of Timber\Site
 * You can move this to its own file and include here via php's include("MySite.php")
 */
class StarterSite extends Timber\Site
{
	/** Add timber support. */
	public function __construct()
	{

		add_action('after_setup_theme', array($this, 'theme_supports'));

		add_filter('timber/context', array($this, 'add_to_context'));
		add_filter('timber/twig', array($this, 'add_to_twig'));

		add_action('init', array($this, 'register_post_types'));
		add_action('init', array($this, 'register_taxonomies'));
		add_action('init', array($this, 'custom_post_type_formation'));
		add_action('wp_enqueue_scripts', array($this, 'styles_wordpress'));



		add_action('admin_head', [$this, 'adminStyle']);
		add_action('acf/init', [$this, 'acfBlocks']);
		parent::__construct();
	}
	/** This is where you can register custom post types. */
	public function register_post_types()
	{
	}
	/** This is where you can register custom taxonomies. */
	public function register_taxonomies()
	{
	}

	/** Activate the SVG Support
	 *
	 * 
	 */



	/** This is where you add some context
	 *
	 * @param string $context context['this'] Being the Twig's {{ this }}.
	 */
	public function add_to_context($context)
	{

		$context['menu']  = new Timber\Menu('menu-principal');
		$context['menu-footer']  = new Timber\Menu('menu-footer');
		$context['site']  = $this;
		$context['stylesheet_directory'] = get_stylesheet_directory() . '/static';
		$args = array(
			// Get post type project
			'post_type' => 'formations',
			// Get all posts
			'posts_per_page' => 3,

			// Order by post date
			'orderby' => array(
				'date' => 'ASC'
			)
		);

		$context['formations'] = Timber::get_posts($args);
		return $context;
	}



	public function theme_supports()
	{
		// Add default posts and comments RSS feed links to head.
		add_theme_support('automatic-feed-links');

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support('title-tag');

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support('post-thumbnails');

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		/*
		 * Enable support for Post Formats.
		 *
		 * See: https://codex.wordpress.org/Post_Formats
		 */
		add_theme_support(
			'post-formats',
			array(
				'aside',
				'image',
				'video',
				'quote',
				'link',
				'gallery',
				'audio',
			)
		);

		add_theme_support('menus');

		register_nav_menus(
			array(
				'menu-principal' => esc_html__('Menu principal', '_s')
			)
		);
	}



	public function acfBlocks()
	{
		if (!function_exists('acf_register_block')) {
			return;
		}

		$blocks = [
			[
				'name' => 'hero',
				'title' => 'hero'
			],
			[
				'name' => 'icons-list',
				'title' => 'icons-list'
			],
			[
				'name' => 'course-list',
				'title' => 'course-list'
			],
			[
				'name' => 'text-image',
				'title' => 'text-image'
			]
		];

		foreach ($blocks as $perKey => $perBlock) {
			acf_register_block(
				[
					'name'            => $perBlock['name'],
					'title'           => $perBlock['title'],
					'description'     => '',
					'render_callback' => [$this, 'renderAcfCallback'],
					'category'        => 'formatting',
					'icon'            => 'format-aside',
					'keywords'        => [str_replace(' ', ',', '')],
					
				]
			);
		}
	}

	/**
	 * Render Dynamic Gutenberg block template
	 * @version 1.0
	 */

	public function renderAcfCallback($block, $content = '', $is_preview = false)
	{

		$blockName = $block['name'];

		$blockName = str_replace('acf/', '', $blockName);
		$context                = Timber::context();
		$context['block']       = $block;
		$context['fields']      = get_fields();
		$context['is_preview']  = $is_preview;


		Timber::render('blocks/' . $blockName . '-block.twig', $context);
	}


	/** This Would return 'foo bar!'.
	 *
	 * @param string $text being 'foo', then returned 'foo bar!'.
	 */
	public function apiCall($url, $filter = "")
	{

		$array = array();
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		//for debug only!
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		$resp = curl_exec($curl);
		curl_close($curl);

		$respJSON = json_decode($resp);



		$array = array_filter($respJSON, function ($k) use ($filter) {
			if (str_contains($k->title, $filter)) {
				return $k;
			}
		});

		$final_array = array_values($array);
		return $final_array;
	}

	/** This is where you can add your own functions to twig.
	 *
	 * @param string $twig get extension.
	 */
	public function add_to_twig($twig)
	{
		$twig->addExtension(new Twig\Extension\StringLoaderExtension());
		$twig->addFilter(new Twig\TwigFilter('myfoo', array($this, 'myfoo')));
		return $twig;
	}
	public function styles_wordpress()
	{
		/* JAVASCRIPT */
		// wp_deregister_script( 'jquery' );
		wp_dequeue_style('wp-block-library');
		wp_dequeue_style('wp-block-library-theme');
		wp_dequeue_style('wc-block-style');
		wp_enqueue_script('jquery');
		wp_enqueue_script('bootstrap', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js', '', null, true);
		wp_enqueue_script('slickCarousel', 'http://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', '', null, true);
		//wp_enqueue_script('lightbox', 'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js', '', null, true);
		//wp_enqueue_script('masonryjs', 'https://cdnjs.cloudflare.com/ajax/libs/masonry/4.2.2/masonry.pkgd.min.js', '', null, true);
		wp_enqueue_script('main', get_template_directory_uri() . '/static/main.js', '', null, true);

		/* CSS */

		wp_enqueue_style('bootstrap', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css');
		wp_enqueue_style('slickCarousel', 'http://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css');
		wp_enqueue_style('hamburgers', 'https://cdnjs.cloudflare.com/ajax/libs/hamburgers/1.1.3/hamburgers.min.css');
		//wp_enqueue_style('lightbox', 'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css');
		wp_enqueue_style('style', get_stylesheet_uri());
	}
	// Register Custom Post Type
	public function custom_post_type_formation()
	{

		$labels = array(
			'name'                  => _x('Formations', 'Post Type General Name', 'savoirdessiner'),
			'singular_name'         => _x('Formation', 'Post Type Singular Name', 'savoirdessiner'),
			'menu_name'             => __('Formations', 'savoirdessiner'),
			'name_admin_bar'        => __('Formations', 'savoirdessiner'),
			'archives'              => __('Archives', 'savoirdessiner'),
			'attributes'            => __('Attributs', 'savoirdessiner'),
			'parent_item_colon'     => __('Parent', 'savoirdessiner'),
			'all_items'             => __('Toutes les formations', 'savoirdessiner'),
			'add_new_item'          => __('Ajouter une formation', 'savoirdessiner'),
			'add_new'               => __('Ajouter', 'savoirdessiner'),
			'new_item'              => __('Nouveau', 'savoirdessiner'),
			'edit_item'             => __('Editer', 'savoirdessiner'),
			'update_item'           => __('Mettre à jour', 'savoirdessiner'),
			'view_item'             => __('Voir', 'savoirdessiner'),
			'view_items'            => __('Tout voir', 'savoirdessiner'),
			'search_items'          => __('Rechercher', 'savoirdessiner'),
			'not_found'             => __('Not found', 'savoirdessiner'),
			'not_found_in_trash'    => __('Not found in Trash', 'savoirdessiner'),
			'featured_image'        => __('Image mis en avant', 'savoirdessiner'),
			'set_featured_image'    => __('Mettre une image en avant', 'savoirdessiner'),
			'remove_featured_image' => __('Supprimer', 'savoirdessiner'),
			'use_featured_image'    => __('Utiliser en tant qu\'image en avant', 'savoirdessiner'),
			'insert_into_item'      => __('Insert into item', 'savoirdessiner'),
			'uploaded_to_this_item' => __('Uploaded to this item', 'savoirdessiner'),
			'items_list'            => __('Items list', 'savoirdessiner'),
			'items_list_navigation' => __('Items list navigation', 'savoirdessiner'),
			'filter_items_list'     => __('Filter items list', 'savoirdessiner'),
		);
		$args = array(
			'label'                 => __('Formation', 'savoirdessiner'),
			'description'           => __('Liste des formations proposées sur Savoir dessiner', 'savoirdessiner'),
			'labels'                => $labels,
			'supports'              => array('title', 'editor', 'thumbnail'),
			'taxonomies'            => array('category', 'post_tag'),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'menu_icon'             => 'dashicons-art',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
		);
		register_post_type('formations', $args);
	}
}

if (function_exists('acf_add_options_page')) {

	acf_add_options_page();
}

/* functions.php */
add_filter('timber_context', 'mytheme_timber_context');

function mytheme_timber_context($context)
{
	$context['options'] = get_fields('option');
	return $context;
}

/**
 * Add Mime Types
 */
function svg_upload($mimes = array())
{


	// allow SVG file upload
	$mimes['svg'] = 'image/svg+xml';
	$mimes['svgz'] = 'image/svg+xml';

	return $mimes;
	
}

add_filter('upload_mimes', 'svg_upload', 99);



function my_wc_cart_count()
{
	global $woocommerce;

?>

	<div class="header-right_wc">

		<ul class="header-right__entry">
			<li class="header-right__entry-single"><a href="<?php echo wc_get_page_permalink( 'shop' )?>"><?php _e('Le shop', 'savoirdessiner'); ?></a></li>
			<li class="header-right__entry-single icon">
				
				<?php echo file_get_contents(get_stylesheet_directory().'/static/shop.svg');?>
				<a href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e('View your shopping cart', 'savoirdessiner'); ?>"><?php _e('Mon panier', 'savoirdessiner'); ?></a>
			</li>

		<?php if (is_user_logged_in()) { ?>
				<li class="header-right__entry-single"><a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>" title="<?php _e('My Account', 'savoirdessiner'); ?>"><?php _e('Mon compte', 'savoirdessiner'); ?></a></li>
			<?php } else { ?>
				<li class="header-right__entry-single"><a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>" title="<?php _e('Login / Register', 'savoirdessiner'); ?>"><?php _e('Se connecter', 'savoirdessiner'); ?></a></li>
			<?php } ?>
		</ul>
		<a href="#" class="btn-fill"><?php _e('Commencer', 'savoirdessiner'); ?></a>
	</div>
<?php


}

add_action('mytheme_header_action', 'my_wc_cart_count');

add_action( 'admin_init', 'bootstrap_editor' );

function bootstrap_editor(){

	add_theme_support( 'editor-styles' ); // if you don't add this line, your stylesheet won't be added
    add_editor_style('https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css');
    add_editor_style('style.css');

}



new StarterSite();

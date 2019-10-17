<?php
/**
 * Plugin Name: Category Landing Page
 * Plugin URI:	https://github.com/billerickson/Category-Landing-Page/
 * Description: Turn your category archives into rich, engaging, SEO friendly landing pages.
 * Version:     1.0.0
 * Author:      Bill Erickson
 * Author URI:  https://www.billerickson.net
 * Requires at least: 5.0
 * License:     MIT
 * License URI: http://www.opensource.org/licenses/mit-license.php
 */

 class Category_Landing_Page {

 	/**
 	 * Instance of the class.
 	 * @var object
 	 */
 	private static $instance;

 	/**
 	 * Supported taxonomies
 	 * @var array
 	 */
 	public $supported_taxonomies;

	/**
	 * Post type
	 * @var string
	 */
	public $post_type = 'be_landing_page';

 	/**
 	 * Class Instance.
 	 * @return Category_Landing_Page
 	 */
 	public static function instance() {
 		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Category_Landing_Page ) ) {
 			self::$instance = new Category_Landing_Page();

			add_action( 'init', [ self::$instance, 'supported_taxonomies' ], 4 );
 			add_action( 'init', [ self::$instance, 'register_cpt' ], 12 );
			add_action('acf/init', [ self::$instance, 'register_metabox' ] );
 			add_action( 'template_redirect', [ self::$instance, 'redirect_single' ] );
 			add_action( 'admin_bar_menu', [ self::$instance, 'admin_bar_link' ], 90 );
 		}
 		return self::$instance;
 	}

	/**
	 * Supported Taxonomies
	 *
	 */
	function supported_taxonomies() {
		$this->supported_taxonomies = apply_filters( 'category_landing_page_taxonomies', [ 'category' ] );
	}

 	/**
 	 * Register the custom post type
 	 *
 	 */
 	function register_cpt() {

 		$labels = array(
 			'name'               => 'Landing Pages',
 			'singular_name'      => 'Landing Page',
 			'add_new'            => 'Add New',
 			'add_new_item'       => 'Add New Landing Page',
 			'edit_item'          => 'Edit Landing Page',
 			'new_item'           => 'New Landing Page',
 			'view_item'          => 'View Landing Page',
 			'search_items'       => 'Search Landing Pages',
 			'not_found'          => 'No Landing Pages found',
 			'not_found_in_trash' => 'No Landing Pages found in Trash',
 			'parent_item_colon'  => 'Parent Landing Page:',
 			'menu_name'          => 'Landing Pages',
 		);

 		$args = array(
 			'labels'              => $labels,
 			'hierarchical'        => false,
 			'supports'            => array( 'title', 'editor', 'revisions' ),
 			'public'              => false,
 			'show_ui'             => true,
 			'show_in_rest'	      => true,
 			'exclude_from_search' => true,
 			'has_archive'         => false,
 			'query_var'           => true,
 			'can_export'          => true,
 			'rewrite'             => array( 'slug' => 'landing-page', 'with_front' => false ),
 			'menu_icon'           => 'dashicons-welcome-widgets-menus',
 		);

 		register_post_type( $this->post_type, $args );
 	}

	/**
	 * Register metabox
	 *
	 */
	function register_metabox() {

		$taxonomies = $tax_fields = [];
		foreach( $this->supported_taxonomies as $i => $tax_slug ) {
			$tax = get_taxonomy( $tax_slug );
			$taxonomies[ $tax_slug ] = $tax->labels->singular_name;

			$tax_fields[] = [
				'key'					=> 'field_10' . $i,
				'label'					=> $tax->labels->name,
				'name'					=> 'be_connected_' . $tax_slug,
				'type'					=> 'taxonomy',
				'taxonomy'				=> $tax_slug,
				'field_type'			=> 'select',
				'conditional_logic'		=> [
					[
						[
							'field'		=> 'field_5da8747adb0bf',
							'operator'	=> '==',
							'value'		=> $tax_slug,
						]
					]
				]
			];
		}

		$taxonomy_select_field = [[
			'key'		=> 'field_5da8747adb0bf',
			'label'		=> 'Taxonomy',
			'name'		=> 'be_connected_taxonomy',
			'type'		=> 'select',
			'choices'	=> $taxonomies,
		]];

		$settings = [
			'title' => 'Appears On',
			'fields' => array_merge( $taxonomy_select_field, $tax_fields ),
			'location' => array(
				array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => $this->post_type,
					),
				),
			),
			'position' => 'side',
			'active' => true,
		];

		acf_add_local_field_group( $settings );
	}

 	/**
 	 * Redirect single landing page
 	 *
 	 */
 	function redirect_single() {
 		if( ! is_singular( $this->post_type ) )
 			return;

 		$supported = $this->supported_taxonomies;
 		$taxonomy = get_post_meta( get_the_ID(), 'be_connected_taxonomy', true );
 		$term = get_post_meta( get_the_ID(), 'be_connected_' . $taxonomy, true );


 		if( empty( $term ) ) {
 			$redirect = home_url();
 		} else {
 			$term = get_term_by( 'term_id', $term, $taxonomy );
 			$redirect = get_term_link( $term, $taxonomy );
 		}

 		wp_redirect( $redirect );
 		exit;

 	}

 	/**
 	 * Show landing page
 	 *
 	 */
 	function show( $location = '' ) {
 		if( ! $location )
 			$location = $this->get_archive_id();

		if( empty( $location ) )
			return;

 		$args = [ 'post_type' => $this->post_type, 'posts_per_page' => 1 ];
 		if( is_int( $location ) )
 			$args['p'] = intval( $location );
 		else
 			$args['name'] = sanitize_key( $location );

 		$loop = new WP_Query( $args );

 		if( $loop->have_posts() ): while( $loop->have_posts() ): $loop->the_post();
 			echo '<div class="block-area block-area-' . sanitize_key( get_the_title() ) . '">';
 				the_content();
 			echo '</div>';
 		endwhile; endif; wp_reset_postdata();
 	}

 	/**
 	 * Get taxonomy
 	 *
 	 */
 	function get_taxonomy() {
 		$taxonomy = is_category() ? 'category' : ( is_tag() ? 'post_tag' : get_query_var( 'taxonomy' ) );
 		if( in_array( $taxonomy, $this->supported_taxonomies ) )
 			return $taxonomy;
 		else
 			return false;
 	}

 	/**
 	 * Get Archive ID
 	 *
 	 */
 	function get_archive_id() {
 		$taxonomy = $this->get_taxonomy();
 		if( empty( $taxonomy ) || ! is_archive() )
 			return false;

 		$meta_key = 'be_connected_' . str_replace( '-', '_', $taxonomy );

 		$loop = new WP_Query( array(
 			'post_type' => $this->post_type,
 			'posts_per_page' => 1,
 			'fields' => 'ids',
 			'no_found_rows' => true,
 			'update_post_term_cache' => false,
 			'update_post_meta_cache' => false,
 			'meta_query' => array(
 				array(
 					'key' => $meta_key,
 					'value' => get_queried_object_id(),
 				)
 			)
 		));

 		if( empty( $loop->posts ) )
 			return false;
 		else
 			return $loop->posts[0];

 	}

 	/**
 	 * Admin Bar Link
 	 *
 	 */
 	 function admin_bar_link( $wp_admin_bar ) {
 		 $taxonomy = $this->get_taxonomy();
 		 if( ! $taxonomy )
 		 	return;

 		if( ! ( is_user_logged_in() && current_user_can( 'edit_post' ) ) )
 			return;

 		$archive_id = $this->get_archive_id();
 		if( !empty( $archive_id ) ) {
 			$wp_admin_bar->add_node( array(
 				'id' => 'category_landing_page',
 				'title' => 'Edit Landing Page',
 				'href'  => get_edit_post_link( $archive_id ),
 			) );

 		} else {
 			$wp_admin_bar->add_node( array(
 				'id' => 'category_landing_page',
 				'title' => 'Add Landing Page',
 				'href'  => admin_url( 'post-new.php?post_type=' . $this->post_type )
 			) );
 		}
 	 }
 }

 /**
  * The function provides access to the class methods.
  *
  * Use this function like you would a global variable, except without needing
  * to declare the global.
  *
  * @return object
  */
 function category_landing_page() {
 	return Category_Landing_Page::instance();
 }
 category_landing_page();

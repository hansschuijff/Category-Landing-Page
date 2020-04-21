<?php
/**
 * Plugin Name: Category Landing Page
 * Plugin URI:	https://github.com/billerickson/Category-Landing-Page/
 * Description: Turn your category archives into rich, engaging, SEO friendly landing pages.
 * Version:     1.1.0
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
	 * Supported CPT Archives
	 * @var array
	 */
	public $supported_cpt_archives;

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
			add_action( 'init', [ self::$instance, 'supported_cpt_archives' ], 4 );
			add_action( 'init', [ self::$instance, 'register_cpt' ], 12 );
			add_action('acf/init', [ self::$instance, 'register_metabox' ] );
			add_action( 'admin_bar_menu', [ self::$instance, 'admin_bar_link_front' ], 90 );
			add_action( 'admin_bar_menu', [ self::$instance, 'admin_bar_link_back' ], 90 );

			// Built-in support for Genesis and Theme Hook Alliance
			add_action( 'tha_content_while_before', [ self::$instance, 'show' ], 20 );
			add_action( 'genesis_before_while', [ self::$instance, 'show' ], 20 );
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
	 * Supported Post Type Archives
	 *
	 */
	function supported_cpt_archives() {
		$this->supported_cpt_archives = apply_filters( 'category_landing_page_cpt_archives', [] );
	}

	/**
	 * Icon
	 *
	 */
	function icon( $name = '', $size = 20 ) {
		$output = '';
		switch( $name ) {
			case 'cultivatewp-menu':
				$output = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 75 75"><path fill="#A0A5AA" fill-rule="evenodd" d="M37.155,-2.13162821e-14 C57.675,-2.13162821e-14 74.31,16.635 74.31,37.155 C74.31,57.675 57.675,74.31 37.155,74.31 C16.635,74.31 -1.70530257e-13,57.675 -1.70530257e-13,37.155 C-1.70530257e-13,16.635 16.635,-2.13162821e-14 37.155,-2.13162821e-14 Z M17.6385,17.6393 C6.8785,28.3993 6.8785,45.9103 17.6395,56.6713 C21.7385,60.7703 26.8185,63.3043 32.1305,64.2803 C32.1375,64.2813 32.1435,64.2833 32.1505,64.2853 C32.1805,64.2913 32.2095,64.3023 32.2395,64.3083 C32.2465,64.3103 32.2525,64.3123 32.2595,64.3133 L32.6135,64.3613 L32.6135,64.3613 C33.2355,64.4643 33.8585,64.5533 34.4845,64.6133 C34.7315,64.6373 34.9765,64.6493 35.2225,64.6673 C35.6705,64.6983 36.1185,64.7193 36.5665,64.7283 C36.7625,64.7323 36.9605,64.7553 37.1555,64.7553 C40.3015,64.7553 43.3235,64.2183 46.1425,63.2453 C46.2145,63.2203 46.2865,63.1973 46.3575,63.1713 C46.6425,63.0703 46.9225,62.9633 47.2035,62.8533 C47.3725,62.7873 47.5415,62.7223 47.7095,62.6533 C47.8915,62.5783 48.0705,62.4983 48.2495,62.4183 C48.5115,62.3043 48.7725,62.1883 49.0315,62.0653 C49.1135,62.0253 49.1945,61.9843 49.2765,61.9443 C51.9485,60.6403 54.4545,58.8883 56.6715,56.6713 C57.2875,56.0543 57.8605,55.4053 58.4105,54.7413 C58.4305,54.7173 58.4495,54.6943 58.4685,54.6713 C58.7295,54.3533 58.9835,54.0323 59.2285,53.7053 L59.3425,53.5493 L59.3425,53.5493 C59.9805,52.6863 60.5645,51.7903 61.0935,50.8663 C61.2295,50.6303 61.3655,50.3943 61.4945,50.1543 C61.5865,49.9803 61.6745,49.8043 61.7635,49.6293 C61.9195,49.3223 62.0705,49.0133 62.2155,48.7003 C62.2765,48.5673 62.3375,48.4343 62.3965,48.3003 L62.656625,47.69305 L62.656625,47.69305 L62.9665,46.9183 L62.9665,46.9183 C63.5335,45.4243 63.9715,43.8693 64.2735,42.2653 C64.2825,42.2163 64.2905,42.1673 64.2995,42.1183 C64.3835,41.6573 64.4565,41.1933 64.5165,40.7253 L64.5525,40.4493 L64.5525,40.4493 C64.6035,40.0193 64.6445,39.5873 64.6755,39.1523 C64.6835,39.0443 64.6935,38.9363 64.6995,38.8283 C64.7275,38.3683 64.7435,37.9053 64.7485,37.4393 C64.7495,37.3723 64.7525,37.3053 64.7535,37.2373 L64.7555,37.1553 L64.7555,37.1553 C64.7555,37.0853 64.7465,37.0143 64.7455,36.9443 C64.7415,36.2813 64.7075,35.6163 64.6555,34.9513 C64.6435,34.8053 64.6315,34.6613 64.6175,34.5153 C64.5455,33.7623 64.4545,33.0093 64.3195,32.2583 C64.3065,32.1843 64.2785,32.1173 64.2585,32.0463 C64.2415,31.9823 64.2265,31.9183 64.2045,31.8563 C64.1765,31.7763 64.1415,31.7013 64.1045,31.6253 C64.0785,31.5703 64.0505,31.5153 64.0195,31.4623 C63.9785,31.3913 63.9375,31.3223 63.8905,31.2563 C63.8515,31.2003 63.8065,31.1483 63.7625,31.0963 C63.7155,31.0413 63.6725,30.9833 63.6215,30.9333 C63.5275,30.8393 63.4255,30.7543 63.3185,30.6763 L63.2465,30.6263 L63.2465,30.6263 C63.2145,30.6053 63.1815,30.5893 63.1485,30.5703 C63.0805,30.5283 63.0115,30.4893 62.9395,30.4543 C62.9005,30.4353 62.8635,30.4153 62.8235,30.3983 C62.7945,30.3863 62.7665,30.3773 62.7375,30.3663 C62.6665,30.3393 62.5935,30.3183 62.5195,30.2983 C62.4675,30.2833 62.4155,30.2663 62.3625,30.2553 C62.3455,30.2513 62.3295,30.2473 62.3125,30.2443 C62.3065,30.2433 62.3005,30.2403 62.2935,30.2393 C60.6595,29.9473 59.0135,29.7993 57.3985,29.7993 C42.1805,29.7993 29.7985,42.1803 29.7985,57.3983 C29.7985,57.7673 29.8335,58.1403 29.8495,58.5113 C26.6805,57.4333 23.6975,55.6583 21.1745,53.1363 C12.3635,44.3243 12.3635,29.9853 21.1745,21.1743 C29.9865,12.3633 44.3235,12.3633 53.1365,21.1743 C54.1115,22.1503 55.6955,22.1503 56.6715,21.1743 C57.6475,20.1973 57.6475,18.6143 56.6715,17.6393 C45.9105,6.8773 28.4005,6.8773 17.6385,17.6393 Z M59.7155,38.3743 C59.7125,38.4243 59.7135,38.4753 59.7095,38.5253 C59.7055,38.6113 59.6965,38.6973 59.6905,38.7843 C59.6645,39.1423 59.6315,39.4993 59.5885,39.8533 C59.5805,39.9273 59.5705,39.9993 59.5615,40.0723 C59.5105,40.4583 59.4515,40.8433 59.3815,41.2253 L59.3615,41.3333 L59.3615,41.3333 C59.1165,42.6353 58.7575,43.9133 58.2895,45.1513 C58.2735,45.1923 58.2565,45.2333 58.2415,45.2743 C58.1105,45.6133 57.9705,45.9493 57.8235,46.2823 C57.7755,46.3913 57.7265,46.4993 57.6755,46.6083 C57.5585,46.8643 57.4335,47.1163 57.3065,47.3683 C57.2325,47.5123 57.1615,47.6573 57.0845,47.8003 C56.9815,47.9933 56.8715,48.1833 56.7615,48.3743 C56.3205,49.1423 55.8395,49.8833 55.3145,50.5913 L55.2345,50.7013 L55.2345,50.7013 C55.0295,50.9743 54.8185,51.2423 54.6025,51.5053 L54.5685,51.5453 L54.5685,51.5453 C54.0285,52.1973 53.4495,52.8143 52.8415,53.4023 C52.4585,53.7713 52.0675,54.1273 51.6655,54.4643 C51.4775,54.6223 51.2855,54.7753 51.0915,54.9273 C50.6575,55.2683 50.2165,55.5973 49.7635,55.9023 C49.6085,56.0073 49.4475,56.1053 49.2895,56.2073 C48.8045,56.5153 48.3135,56.8103 47.8115,57.0783 C47.6915,57.1433 47.5685,57.2033 47.4465,57.2653 C46.8845,57.5533 46.3145,57.8223 45.7345,58.0593 C45.6765,58.0823 45.6175,58.1053 45.5585,58.1283 C43.2605,59.0453 40.8415,59.5503 38.4045,59.6843 L59.7155,38.3743 Z M56.1865,34.8323 L34.8325,56.1863 C35.4425,44.6873 44.6875,35.4433 56.1865,34.8323 Z"/></svg>';
				break;

			case 'cultivate':
				$output = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 72 72"><g fill="none"><circle cx="36" cy="36" r="36" fill="#283E4C"/><path fill="#6FC05D" d="M57,51.75 L57.13,51.58 C57.7227354,50.7743556 58.270246,49.9363973 58.77,49.07 L59.17,48.36 C59.25,48.2 59.33,48.04 59.42,47.88 C59.51,47.72 59.71,47.28 59.85,46.98 C59.91,46.85 59.97,46.73 60.02,46.61 C60.19,46.22 60.36,45.83 60.51,45.43 C60.5156528,45.3835045 60.5156528,45.3364955 60.51,45.29 C61.0560229,43.8529303 61.4743084,42.3705267 61.76,40.86 C61.7648167,40.8134576 61.7648167,40.7665424 61.76,40.72 C61.84,40.28 61.91,39.84 61.97,39.4 L61.97,39.13 C62.0166667,38.7166667 62.0566667,38.3066667 62.09,37.9 L62.09,37.59 C62.09,37.15 62.09,36.71 62.09,36.27 C62.09,36.21 62.09,36.14 62.09,36.08 L62.09,36 C62.09,35.93 62.09,35.86 62.09,35.8 C62.09,35.1733333 62.06,34.5433333 62,33.91 L62,33.48 C61.93,32.77 61.84,32.05 61.71,31.34 C61.6769136,31.1507448 61.6127116,30.968276 61.52,30.8 L61.52,30.8 C61.3707078,30.5211457 61.1541603,30.2839747 60.89,30.11 L60.89,30.11 L60.86,30 L60.86,30 L60.72,29.93 L60.66,29.93 L60.37,29.85 L60.37,29.85 C52.7123625,28.4307469 44.8200205,30.4877578 38.8293245,35.4642276 C32.8386285,40.4406974 29.369139,47.8219574 29.36,55.61 C29.36,56.21 29.36,56.81 29.44,57.41 C20.5325737,54.6815519 14.2453113,46.7227212 13.6528928,37.4256404 C13.0604744,28.1285595 18.2867,19.4360789 26.7757719,15.59911 C35.2648438,11.7621411 45.2427647,13.5825117 51.83,20.17 C52.3102885,20.7020119 53.0447777,20.9258669 53.74015,20.7521687 C54.4355224,20.5784705 54.9784705,20.0355224 55.1521687,19.34015 C55.3258669,18.6447777 55.1020119,17.9102885 54.57,17.43 C46.3710212,9.22624248 33.7611114,7.37297751 23.5484271,12.8707926 C13.3357428,18.3686077 7.93941056,29.9152096 10.2729384,41.2765272 C12.6064663,52.6378448 22.1171007,61.1226696 33.67,62.15 L33.67,62.15 C34.43,62.22 35.2,62.26 35.97,62.26 C36.42,62.26 36.86,62.26 37.31,62.26 L37.49,62.26 C37.88,62.26 38.26,62.21 38.65,62.17 L38.65,62.17 C44.6466458,61.5590823 50.2469947,58.8914082 54.5,54.62 C55.08,54.03 55.62,53.42 56.14,52.79 L56.21,52.7 C56.53,52.36 56.76,52.06 57,51.75 Z M58.35,37.37 C58.35,37.44 58.35,37.51 58.35,37.57 C58.35,37.95 58.29,38.32 58.24,38.69 C58.2454863,38.7465339 58.2454863,38.8034661 58.24,38.86 C58.1933333,39.26 58.1333333,39.66 58.06,40.06 L58.06,40.12 C57.810402,41.4176629 57.4524826,42.6921236 56.99,43.93 L56.99,44.01 C56.85,44.36 56.7,44.71 56.55,45.06 C56.55,45.15 56.47,45.25 56.42,45.34 C56.3,45.61 56.17,45.88 56.03,46.14 C55.89,46.4 55.9,46.4 55.83,46.53 C55.76,46.66 55.6,46.95 55.48,47.16 C55.0298122,47.89263 54.5355701,48.597259 54,49.27 L53.88,49.43 C53.69,49.69 53.49,49.94 53.28,50.2 L53.22,50.27 C52.6840816,50.9109776 52.1132144,51.5219056 51.51,52.1 C51.18,52.42 50.83,52.74 50.51,53.04 L49.81,53.61 C49.49,53.86 49.17,54.1 48.81,54.33 C48.45,54.56 48.23,54.73 47.94,54.91 C47.65,55.09 47.31,55.3 47,55.48 L46,55.99 C45.66,56.16 45.31,56.33 45,56.49 L44.13,56.85 C43.69,57.02 43.25,57.18 42.8,57.32 L42.17,57.51 C41.62,57.67 41.07,57.81 40.51,57.93 L40.16,57.99 C39.54,58.1 38.91,58.2 38.27,58.27 L38.11,58.27 C37.42,58.33 36.72,58.37 36.02,58.37 L36,58.37 L58.38,36 L58.38,36.16 C58.38,36.56 58.36,37 58.34,37.37 L58.35,37.37 Z M55.62,33.23 L55.62,33.23 L33.23,55.64 L33.23,55.64 C33.23,43.275005 43.2450161,33.2465529 55.61,33.23 L55.62,33.23 Z"/></g></svg>';
				break;
		}
		return $output;
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
			'rewrite'             => false,
			'menu_icon'           => 'data:image/svg+xml;base64,' . base64_encode( $this->icon( 'cultivatewp-menu' ) ),
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
	 * Show landing page
	 *
	 */
	function show( $location = '' ) {
		if( ! $location )
			$location = $this->get_landing_id();

		if( empty( $location ) || get_query_var( 'paged' ) )
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
			if( is_archive() )
				echo '<header id="recent" class="archive-recent-header"><h2>Newest ' . get_the_archive_title() . '</h2></header>';
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
	 * Get Landing Page ID
	 *
	 */
	function get_landing_id() {

		if( is_post_type_archive() && in_array( get_post_type(), $this->supported_cpt_archives ) ) {
			$loop = new WP_Query( array(
				'post_type' => $this->post_type,
				'posts_per_page' => 99,
				'fields' => 'ids',
				'no_found_rows' => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'post_name__in' => [ 'cpt-' . get_post_type() ]
			));

		} else {

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
		}

		if( empty( $loop->posts ) )
			return false;
		else
			return $loop->posts[0];

	}

	/**
	 * Get term link
	 *
	 */
	function get_term_link( $archive_id = false ) {

		if( empty( $archive_id ) )
			return false;

		$taxonomy = get_post_meta( $archive_id, 'be_connected_taxonomy', true );
		$term = get_post_meta( $archive_id, 'be_connected_' . $taxonomy, true );

		if( empty( $term ) )
			return false;

		$term = get_term_by( 'term_id', $term, $taxonomy );
		return get_term_link( $term, $taxonomy );
	}

	/**
	 * Admin Bar Link, Frontend
	 *
	 */
	 function admin_bar_link_front( $wp_admin_bar ) {
		 $taxonomy = $this->get_taxonomy();
		 if( ! ( $taxonomy || is_post_type_archive( $this->supported_cpt_archives ) ) )
		 	return;

		if( ! ( is_user_logged_in() && current_user_can( 'manage_categories' ) ) )
			return;

		$archive_id = $this->get_landing_id();
		$icon = '<span style="display: block; float: left; margin: 5px 5px 0 0;">' . $this->icon( 'cultivatewp-menu' ) . '</span>';
		if( !empty( $archive_id ) ) {
			$wp_admin_bar->add_node( array(
				'id' => 'category_landing_page',
				'title' => $icon . 'Edit Landing Page',
				'href'  => get_edit_post_link( $archive_id ),
			) );

		} else {
			$wp_admin_bar->add_node( array(
				'id' => 'category_landing_page',
				'title' => $icon . 'Add Landing Page',
				'href'  => admin_url( 'post-new.php?post_type=' . $this->post_type )
			) );
		}
	 }

	/**
	 * Admin Bar Link, Backend
	 *
	 */
	function admin_bar_link_back( $wp_admin_bar ) {
		if( ! is_admin() )
			return;

		$screen = get_current_screen();
		if( empty( $screen->id ) || $this->post_type !== $screen->id )
			return;

		$archive_id = !empty( $_GET['post'] ) ? intval( $_GET['post'] ) : false;
		if( ! $archive_id )
			return;

		$term_link = $this->get_term_link( $archive_id );
		if( empty( $term_link ) )
			return;

		$icon = '<span style="display: block; float: left; margin: 5px 5px 0 0;">' . $this->icon( 'cultivatewp-menu' ) . '</span>';
		$wp_admin_bar->add_node( array(
			'id'	=> 'category_landing_page',
			'title'	=> $icon . 'View Landing Page',
			'href'	=> $term_link,
		));
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

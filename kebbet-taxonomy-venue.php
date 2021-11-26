<?php
/**
 * Plugin Name:       Kebbet plugins - custom taxonomy: venue
 * Plugin URI:        https://github.com/kebbet/kebbet-taxonomy-venue
 * Description:       Register the custom taxonomy venue
 * Version:           20211111.01
 * Author:            Erik Betshammar
 * Author URI:        https://verkan.se
 * Requires at least: 5.7
 * Requires PHP:      7.4
 * Update URI:        false
 *
 * @package kebbet-taxonomy-venue
 * @author Erik Betshammar
 */

namespace kebbet\taxonomy\venue;

const TAXONOMY   = 'venue';
const POST_TYPES = array( 'post', 'event-archive' );
const HIDE_SLUG  = false;

/**
 * Hook into the 'init' action
 */
function init() {
	load_textdomain();
	register();
}
add_action( 'init', __NAMESPACE__ . '\init', 0 );

/**
 * Flush rewrite rules on registration.
 */
function rewrite_flush() {
	// First, we "add" the custom taxonomy via the above written function.
	register();

	// ATTENTION: This is *only* done during plugin activation hook in this example!
	// You should *NEVER EVER* do this on every page load!!
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, __NAMESPACE__ . '\rewrite_flush' );

/**
 * Load plugin textdomain.
 */
function load_textdomain() {
	load_plugin_textdomain( 'kebbet-taxonomy-venue', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

/**
 * Register the taxonomy
 */
function register() {

	$tax_labels = array(
		'name'                       => _x( 'Venues', 'taxonomy general name', 'kebbet-taxonomy-venue' ),
		'menu_name'                  => __( 'Venues', 'kebbet-taxonomy-venue' ),
		'singular_name'              => _x( 'Venue', 'taxonomy singular name', 'kebbet-taxonomy-venue' ),
		'all_items'                  => __( 'All venue tags', 'kebbet-taxonomy-venue' ),
		'edit_item'                  => __( 'Edit tag', 'kebbet-taxonomy-venue' ),
		'view_item'                  => __( 'View tag', 'kebbet-taxonomy-venue' ),
		'update_item'                => __( 'Update tag', 'kebbet-taxonomy-venue' ),
		'add_new_item'               => __( 'Add new tag', 'kebbet-taxonomy-venue' ),
		'new_item_name'              => __( 'New tag name', 'kebbet-taxonomy-venue' ),
		'separate_items_with_commas' => __( 'Separate venue tags with commas', 'kebbet-taxonomy-venue' ),
		'search_items'               => __( 'Search tags', 'kebbet-taxonomy-venue' ),
		'add_or_remove_items'        => __( 'Add or remove tags', 'kebbet-taxonomy-venue' ),
		'choose_from_most_used'      => __( 'Choose from the most used venue tags', 'kebbet-taxonomy-venue' ),
		'not_found'                  => __( 'No tags found.', 'kebbet-taxonomy-venue' ),
		'popular_items'              => __( 'Popular tags', 'kebbet-taxonomy-venue' ),
		'parent_item'                => __( 'Parent tag', 'kebbet-taxonomy-venue' ),
		'parent_item_colon'          => __( 'Parent tag:', 'kebbet-taxonomy-venue' ),
		'back_to_items'              => __( '&larr; Back to tags', 'kebbet-taxonomy-venue' ),
	);

	$capabilities = array(
		'manage_terms' => 'publish_posts', // Previous 'manage_options'.
		'edit_terms'   => 'publish_posts', // Previous 'manage_options'.
		'delete_terms' => 'manage_categories', // Previous 'manage_options'.
		'assign_terms' => 'publish_posts',
	);

	$tax_args = array(
		'capabilities'          => $capabilities,
		'hierarchical'          => false,
		'has_archive'           => false,
		'labels'                => $tax_labels,
		'public'                => false,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => false,
		'show_in_rest'          => true,
		'rewrite'               => false,
		'description'           => __( 'Venue tags.', 'kebbet-taxonomy-venue' ),
	);

	register_taxonomy( TAXONOMY, POST_TYPES, $tax_args );
}

/**
 * Remove the 'slug' column from the table in 'edit-tags.php'
 */
function remove_column_slug( $columns ) {
    if ( isset( $columns['slug'] ) )
        unset( $columns['slug'] );   

    return $columns;
}

/**
 * Run filter only if constant says so.
 */
if ( true === HIDE_SLUG ) {
	add_filter( 'manage_edit-' . TAXONOMY . '_columns', __NAMESPACE__ . '\remove_column_slug' );
}

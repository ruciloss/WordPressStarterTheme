<?php
/**
 * The main class of WPDE.
 *
 * @package WordPress Development Environment (WPDE)
 * @author Ruciloss
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Taxonomy functions class.
 */
class WPDE_Taxonomy {
    /**
     * The name for the taxonomy.
     *
     * @var     string
     */
    public $taxonomy;

    /**
     * The plural name for the taxonomy terms.
     *
     * @var     string
     */
    public $plural;

    /**
     * The singular name for the taxonomy terms.
     *
     * @var     string
     */
    public $single;

    /**
     * The array of post types to which this taxonomy applies.
     *
     * @var     array
     */
    public $post_types;

    /**
     * The array of taxonomy arguments
     *
     * @var     array
     */
    public $taxonomy_args;

    /**
     * Taxonomy constructor.
     *
     * @param string $taxonomy Taxonomy variable nnam.
     * @param string $plural Taxonomy plural name.
     * @param string $single Taxonomy singular name.
     * @param array  $post_types Affected post types.
     * @param array  $tax_args Taxonomy additional args.
     */
    public function __construct($taxonomy = '', $plural = '', $single = '', $post_types = [], $tax_args = [])
    {

        if (! $taxonomy || ! $plural || ! $single) {
            return;
        }

        // Post type name and labels.
        $this->taxonomy = $taxonomy;
        $this->plural   = $plural;
        $this->single   = $single;
        if (! is_array($post_types)) {
            $post_types = [ $post_types ];
        }
        $this->post_types    = $post_types;
        $this->taxonomy_args = $tax_args;

        // Register taxonomy.
        add_action('init', [ $this, 'register_taxonomy' ]);
    }

    /**
     * Register new taxonomy
     *
     * @return void
     */
    public function register_taxonomy()
    {
        //phpcs:disable
        $labels = [
            'name'                       => $this->plural,
            'singular_name'              => $this->single,
            'menu_name'                  => $this->plural,
            'all_items'                  => sprintf(__('All %s', 'wpde'), $this->plural),
            'edit_item'                  => sprintf(__('Edit %s', 'wpde'), $this->single),
            'view_item'                  => sprintf(__('View %s', 'wpde'), $this->single),
            'update_item'                => sprintf(__('Update %s', 'wpde'), $this->single),
            'add_new_item'               => sprintf(__('Add New %s', 'wpde'), $this->single),
            'new_item_name'              => sprintf(__('New %s Name', 'wpde'), $this->single),
            'parent_item'                => sprintf(__('Parent %s', 'wpde'), $this->single),
            'parent_item_colon'          => sprintf(__('Parent %s:', 'wpde'), $this->single),
            'search_items'               => sprintf(__('Search %s', 'wpde'), $this->plural),
            'popular_items'              => sprintf(__('Popular %s', 'wpde'), $this->plural),
            'separate_items_with_commas' => sprintf(__('Separate %s with commas', 'wpde'), $this->plural),
            'add_or_remove_items'        => sprintf(__('Add or remove %s', 'wpde'), $this->plural),
            'choose_from_most_used'      => sprintf(__('Choose from the most used %s', 'wpde'), $this->plural),
            'not_found'                  => sprintf(__('No %s found', 'wpde'), $this->plural),
        ];
        //phpcs:enable
        $args = [
            'label'                 => $this->plural, // Sets the plural label for the taxonomy.
            'labels'                => apply_filters($this->taxonomy . '_labels', $labels), // Filters and sets the labels for the taxonomy.
            'hierarchical'          => true, // The taxonomy is hierarchical (like categories).
            'public'                => true, // The taxonomy is public.
            'show_ui'               => true, // Shows the taxonomy in the admin UI.
            'show_in_nav_menus'     => true, // The taxonomy can be added to navigation menus.
            'show_tagcloud'         => true, // Shows the taxonomy in tag clouds.
            'meta_box_cb'           => null, // Sets a callback function for the meta box display, null for default.
            'show_admin_column'     => true, // Shows the taxonomy in the admin columns.
            'show_in_quick_edit'    => true, // Shows the taxonomy in the quick edit panel.
            'update_count_callback' => '', // Callback function to update the count of terms in the taxonomy.
            'show_in_rest'          => true, // Makes the taxonomy available in the REST API.
            'rest_base'             => $this->taxonomy, // Sets the base URL for REST API requests.
            'rest_controller_class' => 'WP_REST_Terms_Controller', // Uses the WP_REST_Terms_Controller class for REST API requests.
            'query_var'             => $this->taxonomy, // Sets the query variable for the taxonomy.
            'rewrite'               => true, // Enables URL rewriting for the taxonomy.
            'sort'                  => '', // Allows sorting of terms in the taxonomy.
        ];

        $args = array_merge($args, $this->taxonomy_args);

        register_taxonomy($this->taxonomy, $this->post_types, apply_filters($this->taxonomy . '_register_args', $args, $this->taxonomy, $this->post_types));
    }

} // WPDE_Taxonomy::

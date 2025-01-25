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
 * Post type declaration class.
 */
class WPDE_Post_Type {
    /**
     * The name for the custom post type.
     *
     * @var     string
     */
    public $post_type;

    /**
     * The plural name for the custom post type posts.
     *
     * @var     string
     */
    public $plural;

    /**
     * The singular name for the custom post type posts.
     *
     * @var     string
     */
    public $single;

    /**
     * The description of the custom post type.
     *
     * @var     string
     */
    public $description;

    /**
     * The options of the custom post type.
     *
     * @var     array
     */
    public $options;

    /**
     * Constructor
     *
     * @param string $post_type Post type.
     * @param string $plural Post type plural name.
     * @param string $single Post type singular name.
     * @param string $description Post type description.
     * @param array  $options Post type options.
     */
    public function __construct($post_type = '', $plural = '', $single = '', $description = '', $options = [])
    {

        if (! $post_type || ! $plural || ! $single) {
            return;
        }

        // Post type name and labels.
        $this->post_type   = $post_type;
        $this->plural      = $plural;
        $this->single      = $single;
        $this->description = $description;
        $this->options     = $options;

        // Regsiter post type.
        add_action('init', [ $this, 'register_post_type' ]);

        // Display custom update messages for posts edits.
        add_filter('post_updated_messages', [ $this, 'updated_messages' ]);
        add_filter('bulk_post_updated_messages', [ $this, 'bulk_updated_messages' ], 10, 2);
    }

    /**
     * Register new post type
     *
     * @return void
     */
    public function register_post_type()
    {
        //phpcs:disable
        $labels = [
            'name'               => $this->plural,
            'singular_name'      => $this->single,
            'name_admin_bar'     => $this->single,
            'add_new'            => _x('Add New', $this->post_type, 'wpde'),
            'add_new_item'       => sprintf(__('Add New %s', 'wpde'), $this->single),
            'edit_item'          => sprintf(__('Edit %s', 'wpde'), $this->single),
            'new_item'           => sprintf(__('New %s', 'wpde'), $this->single),
            'all_items'          => sprintf(__('All %s', 'wpde'), $this->plural),
            'view_item'          => sprintf(__('View %s', 'wpde'), $this->single),
            'search_items'       => sprintf(__('Search %s', 'wpde'), $this->plural),
            'not_found'          => sprintf(__('No %s Found', 'wpde'), $this->plural),
            'not_found_in_trash' => sprintf(__('No %s Found In Trash', 'wpde'), $this->plural),
            'parent_item_colon'  => sprintf(__('Parent %s'), $this->single),
            'menu_name'          => $this->plural,
        ];
        //phpcs:enable

        $args = [
            'labels'                => apply_filters($this->post_type . '_labels', $labels), // Filters and sets the labels for the custom post type.
            'description'           => $this->description, // Sets a description for the custom post type.
            'public'                => true, // The post type is public.
            'publicly_queryable'    => true, // The post type can be queried publicly.
            'exclude_from_search'   => false, // Includes the post type in search results.
            'show_ui'               => true, // Shows the post type in the admin UI.
            'show_in_menu'          => true, // Shows the post type in the admin menu.
            'show_in_nav_menus'     => true, // The post type can be added to navigation menus.
            'query_var'             => true, // Enables querying the post type with a query variable.
            'can_export'            => true, // Allows the post type to be exportable.
            'rewrite'               => true, // Enables URL rewriting for the post type.
            'capability_type'       => 'post', // Uses the capabilities of a 'post' for this custom post type.
            'has_archive'           => true, // Enables an archive page for the post type.
            'hierarchical'          => true, // Allows the post type to be hierarchical (like pages).
            'show_in_rest'          => true, // Makes the post type available in the REST API.
            'rest_base'             => $this->post_type, // Sets the base URL for REST API requests.
            'rest_controller_class' => 'WP_REST_Posts_Controller', // Uses the WP_REST_Posts_Controller class for REST API requests.
            'supports'              => [ 'title', 'editor', 'excerpt', 'comments', 'thumbnail' ], // Supports specific features for the post type.
            'menu_position'         => 5, // Sets the position of the post type in the admin menu.
            'menu_icon'             => 'dashicons-admin-post', // Sets the icon for the post type in the admin menu.
        ];

        $args = array_merge($args, $this->options);

        register_post_type($this->post_type, apply_filters($this->post_type . '_register_args', $args, $this->post_type));
    }

    /**
     * Set up admin messages for post type
     *
     * @param  array $messages Default message.
     * @return array           Modified messages.
     */
    public function updated_messages($messages = [])
    {
        global $post, $post_ID;
        //phpcs:disable
        $messages[ $this->post_type ] = [
            0  => '',
            1  => sprintf(__('%1$s updated. %2$sView %3$s%4$s.', 'wpde'), $this->single, '<a href="' . esc_url(get_permalink($post_ID)) . '">', $this->single, '</a>'),
            2  => __('Custom field updated.', 'wpde'),
            3  => __('Custom field deleted.', 'wpde'),
            4  => sprintf(__('%1$s updated.', 'wpde'), $this->single),
            5  => isset($_GET['revision']) ? sprintf(__('%1$s restored to revision from %2$s.', 'wpde'), $this->single, wp_post_revision_title((int) $_GET['revision'], false)) : false,
            6  => sprintf(__('%1$s published. %2$sView %3$s%4$s.', 'wpde'), $this->single, '<a href="' . esc_url(get_permalink($post_ID)) . '">', $this->single, '</a>'),
            7  => sprintf(__('%1$s saved.', 'wpde'), $this->single),
            8  => sprintf(__('%1$s submitted. %2$sPreview post%3$s%4$s.', 'wpde'), $this->single, '<a target="_blank" href="' . esc_url(add_query_arg('preview', 'true', get_permalink($post_ID))) . '">', $this->single, '</a>'),
            9  => sprintf(__('%1$s scheduled for: %2$s. %3$sPreview %4$s%5$s.', 'wpde'), $this->single, '<strong>' . date_i18n(__('M j, Y @ G:i', 'wpde'), strtotime($post->post_date)) . '</strong>', '<a target="_blank" href="' . esc_url(get_permalink($post_ID)) . '">', $this->single, '</a>'),
            10 => sprintf(__('%1$s draft updated. %2$sPreview %3$s%4$s.', 'wpde'), $this->single, '<a target="_blank" href="' . esc_url(add_query_arg('preview', 'true', get_permalink($post_ID))) . '">', $this->single, '</a>'),
        ];
        //phpcs:enable

        return $messages;
    }

    /**
     * Set up bulk admin messages for post type
     *
     * @param  array $bulk_messages Default bulk messages.
     * @param  array $bulk_counts   Counts of selected posts in each status.
     * @return array                Modified messages.
     */
    public function bulk_updated_messages($bulk_messages = [], $bulk_counts = [])
    {

        //phpcs:disable
        $bulk_messages[ $this->post_type ] = [
            'updated'   => sprintf(_n('%1$s %2$s updated.', '%1$s %3$s updated.', $bulk_counts['updated'], 'wpde'), $bulk_counts['updated'], $this->single, $this->plural),
            'locked'    => sprintf(_n('%1$s %2$s not updated, somebody is editing it.', '%1$s %3$s not updated, somebody is editing them.', $bulk_counts['locked'], 'wpde'), $bulk_counts['locked'], $this->single, $this->plural),
            'deleted'   => sprintf(_n('%1$s %2$s permanently deleted.', '%1$s %3$s permanently deleted.', $bulk_counts['deleted'], 'wpde'), $bulk_counts['deleted'], $this->single, $this->plural),
            'trashed'   => sprintf(_n('%1$s %2$s moved to the Trash.', '%1$s %3$s moved to the Trash.', $bulk_counts['trashed'], 'wpde'), $bulk_counts['trashed'], $this->single, $this->plural),
            'untrashed' => sprintf(_n('%1$s %2$s restored from the Trash.', '%1$s %3$s restored from the Trash.', $bulk_counts['untrashed'], 'wpde'), $bulk_counts['untrashed'], $this->single, $this->plural),
        ];
        //phpcs:enable

        return $bulk_messages;
    }

} // END WPDE_Post_Type::

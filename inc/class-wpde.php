<?php
/**
 * The main class of WPDE.
 *
 * @package WordPress Development Environment (WPDE)
 * @author Ruciloss
 */

if (!defined('ABSPATH')) {
    exit();
}

class WPDE {
    /**
     * The single instance of WordPress Development Environment (WPDE).
     *
     * @var     object
     * @access private
     */
    private static $_instance = null; //phpcs:ignore

    /**
     * The version number.
     *
     * @var     string
     */
    public $_version; //phpcs:ignore

    /**
     * The token.
     *
     * @var     string
     */
    public $_token; //phpcs:ignore

    /**
     * The main plugin file.
     *
     * @var     string
     */
    public $file;

    /**
     * The theme dist URL.
     *
     * @var     string
     */
    public $dist_url;

    /**
     * Suffix for scripts and styles.
     *
     * @var     string
     */
    public $script_suffix;

    /**
     * Lang domain.
     * @var string
     */
    public $text_domain;

    /**
     * Lang folder.
     * @var string
     */
    public $lang;

    /**
     * Style onload.
     * @var string
     */
    public $onload;

    /**
     * Script async
     * @var string
     */
    public $async;

    /**
     * Script defer.
     * @var string
     */
    public $defer;

    /**
     * Settings URL.
     * @var string
     */
    public $settings_url;

    /**
     * The cdn.
     * @var string
     */
    public $cdn;

    /**
     * Constructor
     *
     * Initializes the theme object with the specified file and version.
     * This constructor sets up various properties and hooks for the theme.
     *
     * @param  string $file    File constructor
     * @param  string $version Theme version
     */
    public function __construct($file = '', $version = '1.0.0')
    {
        $this->_version = $version;
        $this->_token = 'wpde';
        $this->text_domain = 'wpde';
        $this->lang = get_template_directory() . '/lang';

        $this->async = '-async';
        $this->defer = '-defer';
        $this->onload = '-onload';

        $this->file = $file;
        $this->dist_url = get_template_directory_uri() . '/dist/';
        $this->settings_url = admin_url() . 'admin.php?page=' . $this->_token;

        $this->cdn = 'https://cdn.jsdelivr.net/npm/';

        $this->script_suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

        register_activation_hook($this->file, [$this, 'install']);

        // Load async or defer
        add_filter('script_loader_tag', [$this, 'add_asyncdefer_attr'], 10, 2);
        add_filter('style_loader_tag', [$this, 'add_defer_attr'], 10, 2);

        // Load frontend CSS | JS
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles'], 10);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts'], 10);

        // Load admin CSS | JS
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts'], 10, 1);
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_styles'], 10, 1);

        // Load login CSS | JS
        add_action('login_enqueue_scripts', [$this, 'login_enqueue_scripts'], 10, 1);
        add_action('login_enqueue_scripts', [$this, 'login_enqueue_styles'], 10, 1);

        // ACF Notice
        if (!$this->is_acf()) {
            add_action('admin_notices', [$this, 'acf_notice']);
        }

        // Setup
        add_action('after_setup_theme', [$this, 'theme_setup']);

        // Load body classes
        add_filter('body_class', [$this, 'public_body_classes']);
        add_filter('admin_body_class', [$this, 'admin_body_classes']);
        add_filter('login_body_class', [$this, 'login_body_classes']);

        // Load admin components
        add_action('admin_menu', [$this, 'add_options_page']);

        if($this->is_wpde()) {
            add_action('admin_head', [$this, 'add_admin_navbar']);
            add_action('admin_footer_text', [$this, 'admin_footer_text'], 10, 1);
            add_action('update_footer', [$this, 'admin_footer_version'], 9999, 1);
        }
        add_action('wp_before_admin_bar_render', [$this, 'add_adminbar_tabs'], 999);
        add_action('admin_head', [$this, 'add_help_tabs']);
        add_action('wp_dashboard_setup', [$this, 'add_dashboard_metabox']);

        // Load SMTP
        add_action('phpmailer_init', [$this, 'setup_smtp']);

        // Under construction
        add_action('template_redirect', [$this, 'under_construction']);

        // Clone post
        add_action('admin_action_clone_post', [$this, 'clone_post']);
        add_filter('post_row_actions', [$this, 'clone_post_link'], 10, 2);
        add_filter('page_row_actions', [$this, 'clone_post_link'], 10, 2);
    } // END __construct()

    /**
     * Register post type function.
     *
     * @param string $post_type Post Type.
     * @param string $plural Plural Label.
     * @param string $single Single Label.
     * @param string $description Description.
     * @param array  $options Options array.
     *
     * @return bool|string|WPDE_Post_Type
     */
    public function register_post_type($post_type = '', $plural = '', $single = '', $description = '', $options = [])
    {

        if (! $post_type || ! $plural || ! $single) {
            return false;
        }

        $post_type = new WPDE_Post_Type($post_type, $plural, $single, $description, $options);

        return $post_type;
    }

    /**
     * Wrapper function to register a new taxonomy.
     *
     * @param string $taxonomy Taxonomy.
     * @param string $plural Plural Label.
     * @param string $single Single Label.
     * @param array  $post_types Post types to register this taxonomy for.
     * @param array  $taxonomy_args Taxonomy arguments.
     *
     * @return bool|string|WPDE_Taxonomy
     */
    public function register_taxonomy($taxonomy = '', $plural = '', $single = '', $post_types = [], $taxonomy_args = [])
    {

        if (! $taxonomy || ! $plural || ! $single) {
            return false;
        }

        $taxonomy = new WPDE_Taxonomy($taxonomy, $plural, $single, $post_types, $taxonomy_args);

        return $taxonomy;
    }

    /**
     * Add async or defer attributes to script enqueues.
     *
     * This method adds async or defer attributes to script enqueues based on their handle.
     * If the handle contains 'async', it adds the async attribute.
     * If the handle contains 'defer', it adds the defer attribute.
     * If neither 'async' nor 'defer' is found in the handle, it returns the original HTML output.
     *
     * @param  string $html   The HTML output for the enqueued script.
     * @param  string $handle The script's registered handle.
     * @return string        The modified HTML output.
     */
    public function add_asyncdefer_attr($html, $handle)
    {
        if (strpos($handle, 'async') !== false) {
            return str_replace('<script ', '<script async ', $html);
        } elseif (strpos($handle, 'defer') !== false) {
            return str_replace('<script ', '<script defer ', $html);
        } else {
            return $html;
        }
    } // END add_asyncdefer_attr()

    /**
     * Add defer attribute to style enqueues.
     *
     * This method adds the defer attribute to style enqueues with 'onload' in their handle.
     * It modifies the HTML output to include the defer attribute for such styles.
     *
     * @param  string $html   The HTML output for the enqueued style.
     * @param  string $handle The style's registered handle.
     * @return string        The modified HTML output.
     */
    public function add_defer_attr($html, $handle)
    {
        if (strpos($handle, 'onload') !== false) {
            return str_replace('media=\'all\'', 'media="print" onload="this.media=\'all\'"', $html);
        }
        return $html;
    } // END add_defer_attr()

    /**
     * Load frontend stylesheets.
     *
     * This method loads necessary styles for the frontend of the website.
     *
     * @return void
     */
    public function enqueue_styles()
    {
        wp_register_style($this->_token . 'public', esc_url($this->dist_url) . 'css/public_styles' . $this->script_suffix . '.css', [], $this->_version);
        wp_enqueue_style($this->_token . 'public');

        wp_register_style($this->_token . '-mfp' . $this->onload, esc_url($this->cdn) . 'magnific-popup@1.1.0/dist/' . 'magnific-popup' . $this->script_suffix . '.css', [], $this->_version);
        wp_enqueue_style($this->_token . '-mfp' . $this->onload);
        
        wp_register_style($this->_token . '-c' . $this->onload, esc_url($this->cdn) . 'vanilla-cookieconsent@3.0.1/dist/' . 'cookieconsent' . $this->script_suffix . '.css', [], $this->_version);
        wp_enqueue_style($this->_token . '-c' . $this->onload);
    } // END enqueue_styles ()

    /**
     * Load frontend Javascript.
     *
     * This method registers and enqueues scripts specifically for the front-end of the website.
     * It also localizes scripts for passing data from PHP to JavaScript.
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        wp_register_script($this->_token . '-bs' . $this->defer, esc_url($this->cdn) . 'bootstrap@5.3.3/dist/js/' . 'bootstrap.bundle' . $this->script_suffix . '.js', ['jquery'], $this->_version, true);
        wp_enqueue_script($this->_token . '-bs' . $this->defer);

        wp_register_script($this->_token . '-c' . $this->defer, esc_url($this->cdn) . 'vanilla-cookieconsent@3.0.1/dist/' . 'cookieconsent.umd' . $this->script_suffix . '.js', ['jquery'], $this->_version, true);
        wp_enqueue_script($this->_token . '-c' . $this->defer);

        wp_register_script($this->_token . '-mfp' . $this->defer, esc_url($this->cdn) . 'magnific-popup@1.1.0/dist/' . 'jquery.magnific-popup' . $this->script_suffix . '.js', ['jquery'], $this->_version, true);
        wp_enqueue_script($this->_token . '-mfp' . $this->defer);

        wp_register_script($this->_token . '-icons' . $this->defer, esc_url($this->cdn) . '@fortawesome/fontawesome-free@6.5.2/js/' . 'all' . $this->script_suffix . '.js', ['jquery'], $this->_version, true);
        wp_enqueue_script($this->_token . '-icons' . $this->defer);

        wp_register_script($this->_token . $this->defer, esc_url($this->dist_url) . 'js/public' . $this->script_suffix . '.js', ['jquery'], $this->_version, true);
        wp_enqueue_script($this->_token . $this->defer);

        wp_register_script($this->_token . '-cc' . $this->defer, esc_url($this->dist_url) . 'js/cc' . $this->script_suffix . '.js', ['jquery'], $this->_version, true);
        wp_enqueue_script($this->_token . '-cc' . $this->defer);

        wp_localize_script($this->_token . $this->defer, $this->_token, [
            // code goes here
        ]);
    } // END enqueue_scripts()

    /**
     * Load admin stylesheets.
     *
     * This method registers and enqueues stylesheets specifically for WordPress admin pages.
     *
     * @param  string $hook Hook parameter.
     * @return void
     */
    public function admin_enqueue_styles($hook = '')
    {
        wp_register_style($this->_token . '-admin', esc_url($this->dist_url) . 'css/admin_styles' . $this->script_suffix . '.css', [], $this->_version);
        wp_enqueue_style($this->_token . '-admin');

        if($this->is_wpde()) {
            wp_register_style($this->_token . '-settings', esc_url($this->dist_url) . 'css/settings_styles' . $this->script_suffix . '.css', [], $this->_version);
            wp_enqueue_style($this->_token . '-settings'); 
        }
    } // END admin_enqueue_styles()

    /**
     * Load admin Javascript.
     *
     * This method registers and enqueues scripts specifically for WordPress admin pages.
     * It also localizes scripts for passing data from PHP to JavaScript.
     *
     * @param  string $hook Hook parameter
     * @return void
     */
    public function admin_enqueue_scripts($hook = '')
    {
        wp_register_script($this->_token . '-admin', esc_url($this->dist_url) . 'js/admin' . $this->script_suffix . '.js', ['jquery'], $this->_version, true);
        wp_enqueue_script($this->_token . '-admin');

        if($this->is_wpde()) {
            wp_register_script($this->_token . '-settings', esc_url($this->dist_url) . 'js/settings' . $this->script_suffix . '.js', ['jquery'], $this->_version, true);
            wp_enqueue_script($this->_token . '-settings');
        }

        wp_localize_script($this->_token . '-admin', $this->_token, []);
    } // END admin_enqueue_scripts()

    /**
     * Load login stylesheets.
     *
     * This method registers and enqueues stylesheets specifically for the WordPress login page.
     *
     * @param  string $hook Hook parameter.
     * @return void
     */
    public function login_enqueue_styles($hook = '')
    {
        wp_register_style($this->_token . '-login', esc_url($this->dist_url) . 'css/login_styles' . $this->script_suffix . '.css', [], $this->_version);
        wp_enqueue_style($this->_token . '-login');
    } // END login_enqueue_styles()

    /**
     * Login enqueue scripts.
     *
     * This method registers and enqueues scripts specifically for the WordPress login page.
     * It also localizes scripts for passing data from PHP to JavaScript.
     *
     * @param  string $hook Hook parameter
     * @return void
     */
    public function login_enqueue_scripts($hook = '')
    {
        wp_register_script($this->_token . '-login', esc_url($this->dist_url) . 'js/login' . $this->script_suffix . '.js', ['jquery'], $this->_version, true);
        wp_enqueue_script($this->_token . '-login');

        wp_localize_script($this->_token . '-login', $this->_token, []);
    } // END login_enqueue_scripts ()

    /**
     * Load front body classes.
     *
     * This method adds custom classes to the body tag on the front-end of the website.
     *
     * @param  string $classes Classes body tag
     * @return string $classes
     */
    public function public_body_classes($classes)
    {
        $classes[] = $this->_token . '-public';
        return $classes;
    } // END public_body_class()

    /**
     * Load admin body classes.
     *
     * This method adds custom classes to the body tag on WordPress admin pages.
     *
     * @param string $classes The Class
     * @return string
     */
    public function admin_body_classes($classes)
    {
        $this->is_wpde() ? ($css = $this->_token . '-settings ' . $this->_token . '-admin') : ($css = $this->_token . '-admin');
        $class = $css . ' ' . $classes;
        return $class;
    } // END admin_body_class()

    /**
     * Load login body classes.
     *
     * This method adds custom classes to the body tag on the WordPress login page.
     *
     * @param  string $classes Classes body tag
     */
    public function login_body_classes($classes)
    {
        $classes[] = $this->_token . '-login';
        return $classes;
    } // END login_body_class()

    /**
     * Theme setup.
     *
     * This method initializes various theme supports and features,
     * which are essential components for the proper functioning of the theme.
     *
     *
     * @return void
     */
    public function theme_setup()
    {
        load_theme_textdomain($this->text_domain, $this->lang);

        $post_formats = ['aside', 'image', 'gallery', 'video', 'audio', 'link', 'quote', 'status', 'chat', 'code', 'review', 'recipe', 'event', 'poll', 'tutorial', 'faq'];
        add_theme_support('post-formats', $post_formats);

        add_theme_support('automatic-feed-links');
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('customize-selective-refresh-widgets');
        add_theme_support('align-wide');
        add_theme_support('wp-block-styles');
        add_theme_support('responsive-embeds');

        add_theme_support('woocommerce');

        add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);

        add_theme_support('editor-styles');
        add_post_type_support('page', 'excerpt');

        register_nav_menus([
            'menu-1' => esc_html__('Navbar', 'wpde'),
            'menu-2' => esc_html__('Footer 1', 'wpde'),
            'menu-3' => esc_html__('Footer 2', 'wpde'),
            'menu-4' => esc_html__('Footer 3', 'wpde'),
        ]);

        add_image_size('small-sm', 50, 50, true);
        add_image_size('medium-md', 450, 600, true);
        add_image_size('large-lg', 1920, 600, true);
        add_image_size('thumb', 200, 200, true);
        add_image_size('thumb-rectangle', 500, 300, true);

        require_once get_template_directory() . '/inc/class-bootstrap-nav-walker.php';
    } // END theme_setup()

    /**
     * SMTP setup for sending emails.
     *
     * This method configures PHPMailer's SMTP settings, allowing the theme
     * to send emails using an external SMTP server. It initializes SMTP
     * authentication, sets the SMTP host, port, and security settings,
     * and defines the sender's email address and name for outgoing emails.
     *
     * @param PHPMailer $phpmailer The PHPMailer instance used to send emails.
     * @return void
     */
    public function setup_smtp($phpmailer)
    {
        $smtp = get_field('wpde_smtp', 'option');
    
        if ($smtp && isset($smtp['host'], $smtp['username'], $smtp['password'])) {
            $host = $smtp['host'];
            $username = $smtp['username'];
            $password = $smtp['password'];
            $email = !empty($smtp['email']) ? $smtp['email'] : get_option('admin_email');
            $name = !empty($smtp['name']) ? $smtp['name'] : get_bloginfo('name');
            
            $port = !empty($smtp['port']) ? $smtp['port'] : 587;
            $encryption = !empty($smtp['encryption']) ? $smtp['encryption'] : 'tls';
    
            switch ($encryption) {
                case 'ssl':
                    $encryption_type = 'ssl';
                    break;
                case 'tls':
                case 'none':
                default:
                    $encryption_type = 'tls';
                    break;
            }
    
            $phpmailer->isSMTP();
            $phpmailer->Host = $host;
            $phpmailer->SMTPAuth = true;
            $phpmailer->Username = $username;
            $phpmailer->Password = $password;
            $phpmailer->SMTPSecure = $encryption_type;
            $phpmailer->Port = $port;
            $phpmailer->setFrom($email, $name);
            $phpmailer->addReplyTo($email, $name);
        }
    }

    /**
     * Add admin options page.
     *
     * This method adds an admin options page to the WordPress admin area.
     * If the Advanced Custom Fields plugin is available, it creates a options page for theme options.
     *
     * @return  void
     */
    public function add_options_page()
    {
        if ($this->is_acf()) {
            $parent = acf_add_options_page([
                'page_title' => 'WPDE',
                'menu_title' => 'WPDE',
                'menu_slug'  => $this->_token,
                'capability' => 'manage_options',
                'icon_url'   => get_template_directory_uri() . '/img/admin/icon.svg',
                'position'   => -1,
            ]);
        }
    } // END add_options_page

    /**
     * Add admin navbar.
     *
     * This method adds a custom navbar to the WordPress admin area.
     * It is displayed when the current screen matches the top-level admin page associated with the plugin's unique token.
     * The navbar is designed to enhance the admin interface with additional navigation options.
     *
     * @return  void
     */
    public function add_admin_navbar()
    {
        $screen = get_current_screen();
        if ($screen->id === 'toplevel_page_' . $this->_token) {
            $html  = '<nav id="' . $this->_token . '-navbar" class="' . $this->_token . '-navbar">';
                $html .= '<div class="navbar-start">';
                    $html .= '<div class="navbar-brand">';
                        $html .= '<a href="' . admin_url() . '" class="navbar-logo">';
                            $html .= '<img src="' . get_template_directory_uri() . '/img/admin/logo.svg" width="50" height="auto" alt="Logo">';
                        $html .= '</a>';
                    $html .= '</div>';
                    $html .= '<a href="admin.php?page=' . $this->_token . '" class="navbar-brand-text">';
                        $html .= 'WordPress Development Environment (WPDE) <span>' . __('Settings page', 'wpde') . '</span>';
                    $html .= '</a>';
                $html .= '</div>';

                $html .= '<div class="navbar-end">';
                    $html .= '<ul class="navbar-collapse">';
                        $html .= '<li class="nav-item">';
                            $html .= '<a href="' . home_url() . '" class="nav-link navbar-brand-text" target="_blank">';
                                $html .= '<div>Visit site <span>' . home_url() . '</span></div>';
                            $html .= '</a>';
                        $html .= '</li>';
                    $html .= '</ul>';
                $html .= '</div>';
            $html .= '</nav>';

            echo $html;
        }
    }

    /**
     * Modify the admin footer text.
     *
     * This method allows you to append or modify the content of the WordPress admin footer text.
     * You can use this to include additional messages or links relevant to the plugin.
     *
     * @param   string $text The existing footer text.
     * @return  void
     */
    public function admin_footer_text($text)
    {
        echo $text . ' <i>' . __('We appreciate your choice of', 'wpde') . ' <a href="https://ruciloss.github.io" target="_blank">WPDE</a>.</i>';
    }

    /**
     * Display custom version information in the admin footer.
     *
     * This method outputs custom version information in the WordPress admin footer.
     * It includes the WordPress version and the plugin's version.
     *
     * @return  string A formatted string with version information.
     */
    public function admin_footer_version()
    {
        $wp_version = get_bloginfo('version');
        return 'WP ' . $wp_version . ' | WPDE ' . $this->_version;
    }

    /**
     * Add admin bar tabs.
     *
     * This method adds custom tabs to the WordPress admin bar.
     * It includes a link to the template settings.
     * Additionally, if not in the admin area, it adds tabs displaying the current template file name and post type.
     *
     * @param  object  $wp_admin_bar The WordPress admin bar object.
     * @return void
     */
    public function add_adminbar_tabs($wp_admin_bar)
    {
        global $wp_admin_bar;
        global $template;

        if ($this->is_acf()) {
            $wp_admin_bar->add_node([
                'id' => $this->_token,
                'title' => "<span class='ab-icon'></span><span class='ab-label'>" . __('WPDE', 'wpde') . '</span>',
                'href' => $this->settings_url,
            ]);
        }

        if (!is_admin()) {
            $wp_admin_bar->add_node([
                'id' => $this->_token . '-template',
                'parent' => $this->_token,
                'title' => __('Template:', 'wpde') . ' ' . basename($template),
                'href' => false,
            ]);
            $wp_admin_bar->add_node([
                'id' => $this->_token . '-post-type',
                'parent' => $this->_token,
                'title' => __('Post Type:', 'wpde') . ' ' . get_post_type(),
                'href' => false,
            ]);
        }
    } // END add_adminbar_tabs()

    /**
     * Adds admin help tabs.
     *
     * This method adds custom help tabs to the current screen in the WordPress admin area.
     * It provides information about the WordPress Development Environment (WPDE).
     *
     * @return array Empty array.
     */
    public function add_help_tabs()
    {
        $screen = get_current_screen();

        $content = '<p><strong>WordPress Development Environment (WPDE)</strong></p>';
        $content .= '<p>' . __('Version:', 'wpde') . ' ' . esc_html($this->_version). '</p>';
        $content .= '<div>';
            $content .= '<a href="' . esc_url($this->settings_url) . '">' . __('Settings', 'wpde') . '</a> ';
            $content .= '<a href="https://ruciloss.github.io">' . __('Documentation', 'wpde') . '</a> ';
        $content .= '</div>';
        $content .= '<p>WordPress Development Environment (WPDE) ' . __('is a fantastic starting point for creating a WordPress template. It includes responsive and accessibility design, necessary files, and features needed for proper template functioning, along with a settings page.', 'wpde') . '</p>';

        $screen->add_help_tab([
            'id' => $this->_token,
            'title' => 'WPDE',
            'content' => $content,
            'callback' => '',
            'priority' => 0,
        ]);
    } // END add_help_tabs()

    /**
     * Adds or remove metaboxes to the admin dashboard.
     *
     * This method adds a custom metabox to the WordPress dashboard.
     * It includes a link to a document specified in the theme options.
     *
     * @return void
     */
    public function add_dashboard_metabox()
    {
        add_meta_box(
            $this->_token . '-dashboard-metabox',
            'WordPress Development Environment (WPDE)',
            [$this, 'render_dashboard_metabox'],
            'dashboard',
            'side',
            'high'
        );
    }

    /**
     * Renders the dashboard metabox content.
     *
     * @return void
     */
    public function render_dashboard_metabox()
    {
        $required_plugins = [
            'ACF PRO' => [
                'is_active' => $this->is_acf(),
                'description' => __('Version 5.7.0 or higher is required for the theme to function correctly.', 'wpde'),
            ],
        ];

        $supported_plugins = [
            'WooCommerce' => [
                'is_active' => $this->is_woo(),
                'description' => __('WooCommerce is fully compatible with the theme.', 'wpde'),
            ],
        ];

        echo '<div class="main">';

            echo '<p class="title">' . __('Required plugins:', 'wpde') . '</p>';
            echo '<ul class="wpde-plugins separator">';
            foreach ($required_plugins as $plugin => $data) {
                $status_class = $data['is_active'] ? 'active' : 'required';
                $status_icon = $data['is_active']
                    ? get_template_directory_uri() . '/img/admin/active.svg'
                    : get_template_directory_uri() . '/img/admin/inactive.svg';
                $description = $data['description'];

                echo sprintf(
                    '<li class="%s">
                        <img src="%s" width="20px" height="auto" alt="%s icon"/> 
                        <div>
                            <p class="title">%s</p>
                            <p class="description">%s</p>
                        </div>
                    </li>',
                    $status_class,
                    esc_url($status_icon),
                    esc_html($plugin),
                    esc_html($plugin),
                    esc_html($description)
                );
            }
            echo '</ul>';

            echo '<p class="title">' . __('Supported plugins:', 'wpde') . '</p>';
            echo '<ul class="wpde-plugins">';

            foreach ($supported_plugins as $plugin => $data) {
                $status_class = $data['is_active'] ? 'active' : 'inactive';
                $status_icon = $data['is_active']
                    ? get_template_directory_uri() . '/img/admin/active.svg'
                    : get_template_directory_uri() . '/img/admin/inactive.svg';
                $description = $data['description'];

                echo sprintf(
                    '<li class="%s">
                        <img src="%s" width="20px" height="auto" alt="%s icon"/> 
                        <div>
                            <p class="title">%s</p>
                            <p class="description">%s</p>
                        </div>
                    </li>',
                    $status_class,
                    esc_url($status_icon),
                    esc_html($plugin),
                    esc_html($plugin),
                    esc_html($description)
                );
            }

            echo '</ul>';
        echo '</div>';
    }

    /**
     * Display a notice in the WordPress admin area.
     *
     * This method displays an admin notice to indicate that the ACF PRO plugin is required
     * for the theme to function correctly.
     *
     * @return  void
     */
    public function acf_notice()
    {
        $html = '<div id="' . $this->_token . '-notice" class="notice notice-error is-dismissible">';
            $html .= '<p><strong> WordPress Development Environment (WPDE)</strong> ' . __('theme requires the', 'wpde') . ' <a href="https://www.advancedcustomfields.com/" target="_blank" rel="noopener noreferrer"><strong>' . __('ACF PRO', 'wpde') . '</strong></a> ' . __('plugin, with a minimum version of 5.7.0, to function correctly.', 'wpde') . '</p>';
        $html .= '</div>';

        echo $html;
    }

    /**
     * Checks the activation of the ACF plugin.
     *
     * This method checks if the required plugin is active.
     * If it's active, it returns true; otherwise, it returns false.
     *
     * @param bool $pro Whether to check for ACF PRO version.
     *                  If true, it checks for ACF_PRO class existence; otherwise, it checks for any ACF class existence.
     *
     * @return bool
     */
    public function is_acf()
    {
        return class_exists('acf_pro');
    }

    /**
     * Checks the activation of the WooCommerce plugin.
     *
     * This method checks if the WooCommerce plugin is active.
     * If it's active, it returns true; otherwise, it returns false.
     *
     * @return bool
     */
    public function is_woo()
    {
        return class_exists('WooCommerce');
    }

    /**
     * Checks if the current page is a WPDE (WordPress Development Environment) page.
     *
     * This function determines whether the current page is part of the WordPress Development Environment (WPDE)
     * based on the presence of a specific token in the page URL. It is typically used within the admin area.
     *
     * @return boolean True if the current page is a WPDE page, false otherwise.
     */
    public function is_wpde()
    {
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
            $token = $this->_token;
            if (is_admin() && strpos($page, $token) !== false) {
                return true;
            }
        }
    } // END is_wpde()

    /**
     * Generate theme selector dropdown with Bootstrap 5 styles.
     *
     * This function generates a dropdown menu for selecting themes with Bootstrap 5 styles.
     * It provides options for selecting different themes such as Auto, Light, and Dark.
     *
     * @return void
     */
    public function theme()
    {
        $html = '<div class="dropdown d-flex align-items-center justify-content-center" id="' . $this->_token . '-theme">';
            $html .= '<button type="button" class="navbar-toggler d-block border-0 dropdown-toggle p-0 shadow-none" data-bs-toggle="dropdown" aria-expanded="false">';
                $html .= '<i class="fa-solid fa-circle-half-stroke"></i> <span class="visually-hidden">Auto</span>';
            $html .= '</button>';
            $html .= '<ul class="dropdown-menu" style="left:-70px;margin-top: 1.3rem;">';
                $html .= '<li><a class="dropdown-item active" href="#!" data-value="auto"><i class="fa-solid fa-circle-half-stroke"></i> Auto</a></li>';
                $html .= '<li><a class="dropdown-item" href="#!" data-value="light"><i class="fa-solid fa-sun"></i> ' . __('Light', 'wpde') . '</a></li>';
                $html .= '<li><a class="dropdown-item" href="#!" data-value="dark"><i class="fa-solid fa-moon"></i> ' . __('Dark', 'wpde') . '</a></li>';
            $html .= '</ul>';
        $html .= '</div>';

        echo $html;
    } // END theme()

    /**
     * Load pagination with Bootstrap 5 styles.
     *
     * This function generates pagination links with Bootstrap 5 styles.
     * It extends the default WordPress pagination and displays the pagination links along with information
     * about the number of results being shown on the current page out of total results.
     *
     * @param   array $args {
     *     Optional. Array of arguments for customizing the pagination output.
     *
     *     @type int    $mid_size   How many numbers to either side of the current page.
     *     @type string $prev_text  Text to display for the previous page link.
     *     @type string $next_text  Text to display for the next page link.
     *     @type string $type       Controls format of the returned value. Possible values are 'array', 'list', or 'echo'.
     * }
     * @return  string HTML output of the paginated links with Bootstrap 5 styles.
     */
    public function pagination($args = [])
    {
        $defaults = [
            'mid_size' => 2,
            'prev_text' => __('Previous', 'wpde'),
            'next_text' => __('Next', 'wpde'),
            'type' => 'array',
        ];

        $args = wp_parse_args($args, $defaults);
        $links = paginate_links($args);

        if ($links) {
            global $wp_query;
            $total_posts = $wp_query->found_posts;
            $posts_per_page = get_query_var('posts_per_page');
            $current_page = max(1, get_query_var('paged'));

            $start = ($current_page - 1) * $posts_per_page + 1;
            $end = min($total_posts, $current_page * $posts_per_page);

            $html = '<div class="d-flex align-items-center justify-content-between my-4 pt-3 border-top">';
            $html .= '<small class="text-muted">' . __('Showing', 'wpde') . ' ' . $start . ' ' . __('to', 'wpde') . ' ' . $end . ' ' . __('of', 'wpde') . ' ' . $total_posts . ' ' . __('results', 'wpde') . '</small>';
            $html .= '<nav aria-label="Pagination">';
            $html .= '<ul class="pagination mb-0">';

            foreach ($links as $link) {
                if (strpos($link, 'current') !== false) {
                    $html .= "<li class='page-item active' aria-current='page'>" . str_replace('page-numbers', 'page-link', $link) . '</li>';
                } else {
                    $html .= "<li class='page-item'>" . str_replace('page-numbers', 'page-link', $link) . '</li>';
                }
            }

            $html .= '</ul>';
            $html .= '</nav>';
            $html .= '</div>';

            echo $html;
        }
    }

    /**
     * Generate and display a formatted title section.
     *
     * This method creates an HTML structure for a title section, which includes
     * a subtitle, main title, and description. It also applies optional layout
     * and custom styles based on the provided options array.
     *
     * @param   string $title       The main title to be displayed.
     * @param   string $subtitle    The subtitle to be displayed.
     * @param   string $description A description to be displayed below the title.
     * @param   array  $options     Optional parameters for layout and border settings.
     * @return  string              The generated HTML for the title section.
     */
    public function the_title($title, $subtitle, $description, $options = [])
    {
        if (empty($title)) {
            return;
        }
    
        $html = '';
        $custom_class = !empty($options['class']) ? esc_attr($options['class']) : '';
    
        $html .= '<div class="pb-5 mb-3 border-bottom ' . esc_attr($custom_class) . '">';
            $html .= '<small class="text-primary"><strong>' . esc_html($subtitle) . '</strong></small>';
            $html .= '<h1 class="mb-2">' . esc_html($title) . '</h1>';
            $html .= '<p class="mb-0 text-muted">' . esc_html($description) . '</p>';
        $html .= '</div>';
    
        echo $html;
    }

    /**
     * Under Construction mode.
     *
     * This function checks the settings in the site's admin to determine if the page should be marked as "under construction".
     * If this option is enabled, it displays a message informing users that the site is undergoing maintenance and halts 
     * the page rendering with an HTTP 503 (Service Unavailable) response.
     *
     * @return void Stops the page rendering and displays the under construction message.
     */
    public function under_construction() 
    {
        $options = get_field('wpde_options', 'option');
        if ($options) {
            $under_construction = $options['under_construction'];
        }
        if (!empty($under_construction) && !is_user_logged_in() && !is_admin()) {
            wp_die(
                sprintf(
                    '<h1>%s</h1><p>%s</p>',
                    __('We are updating the website, please check back later', 'wpde'),  
                    __('This website is currently undergoing maintenance. We will be back soon!', 'wpde') 
                ),
                __('Under Construction', 'wpde'), 
                array('response' => 503)
            );
        }
    }

    /**
     * Load breadcrumbs for navigation.
     *
     * This function generates breadcrumb navigation for pages and posts, providing users with a trail
     * of links to navigate back to parent pages, categories, archives, or search results.
     *
     * @return string HTML output of the breadcrumb navigation.
     */
    public function breadcrumbs()
    {

        if (is_front_page()) {
            return;
        }

        global $post;
        $custom_taxonomy = '';

        $defaults = [
            'home' => esc_html(__('Home', 'wpde')),
            'id' => $this->_token . '-breadcrumbs',
        ];

        echo '<div class="mb-6">'; 

        // Start breadcrumb with a link to your homepage
        echo '<nav id="' . $defaults['id'] . '" aria-label="breadcrumb" class="d-none d-md-flex">';
        echo '<ul class="breadcrumb mb-0">';

        // Creating home link
        echo "<li class='breadcrumb-item'><a href='" . get_home_url() . "'>" . $defaults['home'] . '</a></li>';

        // Single
        if (is_single()) {
            $post_type = get_post_type();

            // If post type is not 'post'
            if ($post_type != 'post') {
                $post_type_object = get_post_type_object($post_type);
                $post_type_link = get_post_type_archive_link($post_type);
                echo "<li class='breadcrumb-item'><a href='" . $post_type_link . "'>" . $post_type_object->labels->name . '</a></li>';
            }

            // Get categories
            $category = get_the_category($post->ID);

            // If category not empty
            if (!empty($category)) {
                // Arrange category parent to child
                $category_values = array_values($category);
                $get_last_category = end($category_values);
                $get_parent_category = rtrim(get_category_parents($get_last_category->term_id, true, ','), ',');
                $cat_parent = explode(',', $get_parent_category);

                // Store category in $display_category
                $display_category = '';
                foreach ($cat_parent as $p) {
                    $display_category .= "<li class='breadcrumb-item'>" . $p . '</li>';
                }
            }

            // If it's a custom post type within a custom taxonomy
            $taxonomy_exists = taxonomy_exists($custom_taxonomy);

            if (empty($get_last_category) && !empty($custom_taxonomy) && $taxonomy_exists) {
                $taxonomy_terms = get_the_terms($post->ID, $custom_taxonomy);
                $cat_id = $taxonomy_terms[0]->term_id;
                $cat_link = get_term_link($taxonomy_terms[0]->term_id, $custom_taxonomy);
                $cat_name = $taxonomy_terms[0]->name;
            }

            // Check if the post is in a category
            if (!empty($get_last_category)) {
                echo $display_category;
                echo "<li class='breadcrumb-item'>" . get_the_title() . '</li>';
            } elseif (!empty($cat_id)) {
                echo "<li class='breadcrumb-item'><a href='" . $cat_link . "'>" . $cat_name . '</a></li>';
                echo "<li class='breadcrumb-item'>" . get_the_title() . '</li>';
            } else {
                echo '<li>' . get_the_title() . '</li>';
            }

            // Archive product
        } elseif (is_post_type_archive('product')) {
            $post_type = get_post_type();
            $post_type_object = get_post_type_object($post_type);
            echo "<li class='breadcrumb-item'>" . $post_type_object->labels->name . '</li>';

            // Archive
        } elseif (is_archive()) {
            // Taxonomy
            if (is_tax()) {
                $post_type = get_post_type();

                // If post type is not post
                if ($post_type != 'post') {
                    $post_type_object = get_post_type_object($post_type);
                    $post_type_link = get_post_type_archive_link($post_type);
                    echo "<li class='breadcrumb-item'><a href='" . $post_type_link . "'>" . $post_type_object->labels->name . '</a></li>';
                }

                $custom_tax_name = get_queried_object()->name;
                echo "<li class='breadcrumb-item'>" . $custom_tax_name . '</li>';

                // Category
            } elseif (is_category()) {
                $parent = get_queried_object()->category_parent;
                if ($parent !== 0) {
                    $parent_category = get_category($parent);
                    $category_link = get_category_link($parent);
                    echo "<li class='breadcrumb-item'><a href='" . esc_url($category_link) . "'>" . $parent_category->name . '</a></li>';
                }
                echo "<li class='breadcrumb-item'>" . single_cat_title('', false) . '</li>';

                // Tag
            } elseif (is_tag()) {
                // Get tag information
                $term_id = get_query_var('tag_id');
                $taxonomy = 'post_tag';
                $args = 'include=' . $term_id;
                $terms = get_terms($taxonomy, $args);
                $get_term_name = $terms[0]->name;

                // Display the tag name
                echo "<li class='breadcrumb-item'>" . $get_term_name . '</li>';
            } elseif (is_day()) {
                // Day archive
                // Year link
                echo "<li><a href='" . get_year_link(get_the_time('Y')) . "'>" . get_the_time('Y') . ' Archiv</a></li>';
                // Month link
                echo "<li><a href='" . get_month_link(get_the_time('Y'), get_the_time('m')) . "'>" . get_the_time('M') . ' Archiv</a></li>';
                // Day display
                echo "<li class='breadcrumb-item'>" . get_the_time('jS') . ' ' . get_the_time('M') . ' Archiv</li>';
            } elseif (is_month()) {
                // Month archive
                // Year link
                echo "<li class='breadcrumb-item'><a href='" . get_year_link(get_the_time('Y')) . "'>" . get_the_time('Y') . ' Archiv</a></li>';
                // Month Display
                echo "<li class='breadcrumb-item'>" . get_the_time('M') . ' Archiv</li>';
            } elseif (is_year()) {
                // Year Display
                echo "<li class='breadcrumb-item'>" . get_the_time('Y') . ' Archiv</li>';
            } elseif (is_author()) {
                // Auhor archive
                // Get the author information
                global $author;
                $userdata = get_userdata($author);
                // Display author name
                echo "<li class='breadcrumb-item'>" . $userdata->display_name . '</li>';
            } else {
                echo "<li class='breadcrumb-item'>" . post_type_archive_title() . '</li>';
            }

            // Page
        } elseif (is_page()) {
            // Standard page
            if ($post->post_parent) {
                // If child page, get parents
                $anc = get_post_ancestors($post->ID);

                // Get parents in the right order
                $anc = array_reverse($anc);

                // Parent page loopfa-2x
                if (!isset($parents)) {
                    $parents = null;
                }
                foreach ($anc as $ancestor) {
                    $parents .= "<li class='breadcrumb-item'><a href='" . get_permalink($ancestor) . "'>" . get_the_title($ancestor) . '</a></li>';
                }
                // Display parent pages
                echo $parents;

                // Current page
                echo "<li class='breadcrumb-item'>" . get_the_title() . '</li>';
            } else {
                // Just display current page if not parents
                echo "<li class='breadcrumb-item'>" . get_the_title() . '</li>';
            }

            // Search
        } elseif (is_search()) {
            echo "<li class='breadcrumb-item'>" . __('Search results for:', 'wpde') . " " . "<strong>" . get_search_query() . '</strong></li>';
            // 404
        } elseif (is_404()) {
            echo "<li class='breadcrumb-item'>" . 'Error 404' . '</li>';
        }

        // End breadcrumbs
        echo '</ul>';
        echo '</nav>';
        echo '</div>';
    }

    /**
     * Adds a "Clone" link to the list of actions for posts.
     *
     * This function appends a "Clone" option to the actions available for posts in the admin panel. 
     * The link allows users with appropriate permissions to duplicate a post.
     *
     * @param array $actions The existing array of actions for the post.
     * @param WP_Post $post The current post object.
     * @return array Updated array of actions including the "Clone" option.
     */
    public function clone_post_link($actions, $post) {
        if (current_user_can('edit_posts')) {
            $url = wp_nonce_url(
                admin_url('admin.php?action=clone_post&post=' . $post->ID),
                'clone_post_' . $post->ID
            );
            $actions['duplicate'] = '<a href="' . esc_url($url) . '" title="' . __('Clone post', 'wpde') . '">' . __('Clone', 'wpde') . '</a>';
        }
        return $actions;
    }

    /**
     * Handles the cloning of a post.
     *
     * This function duplicates a post and its metadata, taxonomies, and other related data.
     * It verifies user permissions and redirects back to the post list after creating the duplicate.
     *
     * @return void Stops execution to handle redirection or display an error message.
     */
    public function clone_post() 
    {
        // Verifying that the user has rights
        if (!isset($_GET['post']) || !isset($_GET['_wpnonce']) || 
            !wp_verify_nonce($_GET['_wpnonce'], 'clone_post_' . $_GET['post'])) {
            wp_die('You do not have permission for this action.');
        }

        $post_id = intval($_GET['post']);
        $post = get_post($post_id);

        if (!$post) {
            wp_die('Post not found.');
        }

        // Create post
        $new_post = array(
            'post_title'   => $post->post_title,
            'post_content' => $post->post_content,
            'post_status'  => 'draft',
            'post_type'    => $post->post_type,
            'post_author'  => get_current_user_id(),
        );

        $new_post_id = wp_insert_post($new_post);

        // Taxonomy
        $taxonomies = get_object_taxonomies($post->post_type);
        foreach ($taxonomies as $taxonomy) {
            $terms = wp_get_post_terms($post_id, $taxonomy, array('fields' => 'ids'));
            wp_set_object_terms($new_post_id, $terms, $taxonomy);
        }

        // Metadata
        $meta_data = get_post_meta($post_id);
        foreach ($meta_data as $key => $values) {
            foreach ($values as $value) {
                add_post_meta($new_post_id, $key, maybe_unserialize($value));
            }
        }

        // Redirect
        wp_redirect(admin_url('edit.php?post_type=' . $post->post_type));
        exit;
    }

    /**
     * Main WPDE Instance.
     *
     * Ensures that only one instance of WPDE is loaded or can be loaded.
     * This method is responsible for creating and returning the singleton instance of the WPDE class.
     *
     * @param  string $version The version parameter.
     * @static
     * @return Object|WPDE The WPDE instance.
     * @see    WPDE()
     */
    public static function instance($version = '1.0.0')
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($version);
        }

        return self::$_instance;
    } // END instance()

    /**
     * Prevents cloning of the WPDE instance.
     *
     * This magic method is invoked when an attempt is made to clone the WPDE instance,
     * and it throws an error indicating that cloning of the WPDE instance is forbidden.
     *
     * @return void
     */
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, esc_html(__('Cloning of WordPress Development Environment (WPDE) is forbidden')), esc_attr($this->_version));
    } // END __clone()

    /**
     * Prevents unserializing instances of the WPDE class.
     *
     * This magic method is invoked when an attempt is made to unserialize an instance of the WPDE class,
     * and it throws an error indicating that unserializing instances of the WPDE class is forbidden.
     *
     * @return void
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, esc_html(__('Unserializing instances of WordPress Development Environment (WPDE) is forbidden')), esc_attr($this->_version));
    } // END __wakeup()

    /**
     * Installation method, runs on activation.
     *
     * This method is called during plugin or theme activation to perform installation tasks.
     * It typically includes actions like logging the version number.
     *
     * @return void
     */
    public function install()
    {
        $this->_log_version_number();
    } // END install()

    /**
     * Log the theme version number.
     *
     * This method updates the theme version number in the WordPress options table.
     * It is intended to be called internally within the theme or plugin code.
     *
     * @return void
     */
    private function _log_version_number()
    {
        //phpcs:ignore
        update_option($this->_token . '_version', $this->_version);
    } // END _log_version_number()
} // END WPDE::

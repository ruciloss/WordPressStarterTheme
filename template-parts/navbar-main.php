<?php
$navbar = get_field('wpde_navbar', 'option');
if($navbar) {
    $navbar_position = $navbar['position'];
    $navbar_layout = $navbar['layout'];
    $navbar_link = $navbar['link'];
    $navbar_btn = $navbar['link_2'];
    $navbar_cart = $navbar['cart'];

    switch ($navbar_position) {
        case 'Fixed':
            $navbar_class = 'position-fixed top-0 start-0 end-0';
            break;
        case 'Headroom':
            $navbar_class = 'position-fixed top-0 start-0 end-0 headroom';
            break;
        case 'Static':
        case '':
        default:
            $navbar_class = '';
            break;
    }

    switch ($navbar_layout) {
        case 'Container':
            $container_class = '';
            break;
        case 'Full':
        case '':
        default:
            $container_class = '-fluid';
            break;
    }
}

$logo = get_field('wpde_logo', 'option');
if ($logo) {
    $logo_image = $logo['image'];
    $logo_width = $logo['width'] ? $logo['width'] : 100;
    $logo_text = $logo['text'];
}

$options = get_field('wpde_options', 'option');
if ($options) {
    $search_form = $options['search_form'];
    $theme_toggler = $options['theme_toggler'];
}
?>
<nav class="navbar navbar-expand-lg bg-blur border-bottom shadow-sm z-2 <?php echo esc_attr($navbar_class); ?>">
	<div class="container<?php echo $container_class; ?>">
		<a class="navbar-brand d-flex align-items-center gap-1" href="<?php echo esc_url(home_url()); ?>">
        <?php if (!empty($logo_image)) { ?>
            <img class="img-fluid" src="<?php echo esc_url($logo_image['url']); ?>" alt="Logo" width="<?php echo esc_attr($logo_width); ?>" height="auto"/>
        <?php
        } elseif(!empty($logo_text)) {
            echo esc_html($logo_text);
        } else {
            echo esc_html(get_bloginfo('name'));
        }
        ?>
		</a>
		<div class="collapse navbar-collapse" id="navbar">
            <small class="text-primary d-block d-lg-none fw-bold my-4"><?php _e('Navigation', 'wpde'); ?></small>
		<?php
        wp_nav_menu([
            'theme_location' => 'menu-1',
            'container' => false,
            'menu_class' => 'me-3',
            'fallback_cb' => '__return_false',
            'items_wrap' => '<ul id="%1$s" class="navbar-nav gap-2 ms-3 ms-lg-auto mb-3 mb-lg-0 %2$s">%3$s</ul>',
            'depth' => 2,
            'walker' => new bootstrap_5_wp_nav_menu_walker(),
        ]);
        ?>
		</div>
        <div class="d-flex align-items-center justify-content-center gap-2 ms-4" id="navbar-actions">
            <?php 
            if(empty($theme_toggler)) { 
                WPDE()->theme(); 
            } 
            ?>
            <?php if (empty($search_form)) { ?>
			<button type="button" class="navbar-toggler d-block border-0 p-0 shadow-none" data-bs-toggle="modal" data-bs-target="#modal-searchform">
			    <i class="fa-solid fa-magnifying-glass"></i>
			</button>
            <?php } ?>
            <?php if (empty($navbar_cart) && WPDE()->is_woo()) { ?>
			<a href="<?php echo wc_get_cart_url(); ?>" class="navbar-toggler d-flex align-items-center justify-content-center border-0 p-0 shadow-none">
                <i class="fa-solid fa-basket-shopping"></i>
			</a>
            <?php } ?>
			<button type="button" class="navbar-toggler border-0 p-0 shadow-none" data-bs-toggle="collapse" data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
                <i id="navbar-toggler-icon" class="fa-solid fa-bars fa-lg"></i>
			</button>
		</div>
		<div class="d-none d-lg-flex">
            <?php if(!empty($navbar_link) || !empty($navbar_btn)) { ?>
            <ul class="navbar-nav border-start align-items-center ps-4 ms-4">
            <?php
            if($navbar_link) {
                $link_url = $navbar_link['url'];
                $link_title = $navbar_link['title'];
                $link_target = $navbar_link['target'] ? $navbar_link['target'] : '_self';
            ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>"><?php echo esc_html($link_title); ?></a>
                </li>
            <?php } ?>
            <?php
            if($navbar_btn) {
                $link_url = $navbar_btn['url'];
                $link_title = $navbar_btn['title'];
                $link_target = $navbar_btn['target'] ? $navbar_btn['target'] : '_self';
            ?>
                <li class="nav-item ms-3">
                    <a class="btn btn-primary text-uppercase" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>">
                        <small>
                            <?php echo esc_html($link_title); ?>
                            <i class="fa-solid fa-arrow-right ms-1"></i> 
                        </small>
                    </a>
                </li>
            <?php } ?>
            </ul>
            <?php } ?>
        </div>
	</div>
</nav>

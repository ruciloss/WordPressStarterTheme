<?php
$logo = get_field('wpde_logo', 'option');
if ($logo) {
    $logo_image = $logo['image'];
    $logo_width = $logo['width'] ? intval($logo['width']) : 100;
    $logo_text = $logo['text'];
}

$footer = get_field('wpde_footer', 'option');
if ($footer) {
    $left_block = $footer['left_block'];
}

$metadata = get_field('wpde_metadata', 'option');
if ($metadata) {
    $email = $metadata['email'];
    $phone = $metadata['phone'];
}
?>
<div class="container-fluid border-top px-0 pt-6 pb-5">
	<div class="container">
		<div class="row">
			<div class="col-md-6 mb-5 mb-md-0">
				<a class="d-block" href="<?php echo esc_url(home_url()); ?>">
				<?php if (!empty($logo_image)) { ?>
					<img class="img-fluid mb-2" src="<?php echo esc_url($logo_image['url']); ?>" alt="Logo" width="<?php echo esc_attr($logo_width); ?>" height="auto"/>
				<?php
				} elseif(!empty($logo_text)) {
				    echo esc_html($logo_text);
				} else {
				    echo esc_html(get_bloginfo('name'));
				}
?>
				</a>
				<small class="text-muted"><?php echo wp_kses_post($left_block); ?></small>
				<?php if (!empty($email) && !empty($phone)) { ?>
				<ul class="navbar-nav list-unstyled d-flex flex-row align-items-center flex-wrap flex-md-nowrap column-gap-3 mt-3">
					<?php if (!empty($email)) { ?>
						<li class="nav-item">
							<a href="mailto:<?php echo esc_attr($email); ?>">
								<i class="fa-solid fa-envelope"></i>
								<small><?php echo esc_html($email); ?></small>
							</a>
						</li>
					<?php } ?>
					<?php if (!empty($phone)) { ?>
						<li class="nav-item">
							<a href="tel:<?php echo esc_attr($phone); ?>">
								<i class="fa-solid fa-phone-alt"></i>
								<small><?php echo esc_html($phone); ?></small>
							</a>
						</li>
					<?php } ?>
				</ul>
				<?php } ?>
			</div>

			<?php
            $menus = [
                'footer-1' => 'menu-2',
                'footer-2' => 'menu-3',
                'footer-3' => 'menu-4',
            ];

foreach ($menus as $footer_menu => $theme_location) {
    $menu_items = wp_get_nav_menu_items($footer_menu);
    if (!empty($menu_items)) {
        ?>
					<div class="col-4 col-md-2 mb-3 <?php echo ($theme_location === 'menu-2') ? 'ms-md-auto' : ''; ?>">
						<h5><?php echo ($theme_location === 'menu-2') ? 'Menu' : 'Section'; ?></h5>
						<?php
            wp_nav_menu([
                'theme_location' => $theme_location,
                'container' => false,
                'menu_class' => '',
                'fallback_cb' => '__return_false',
                'items_wrap' => '<ul id="%1$s" class="navbar-nav me-auto mb-2 mb-md-0 text-muted %2$s">%3$s</ul>',
                'depth' => 2,
                'walker' => new bootstrap_5_wp_nav_menu_walker(),
            ]);
        ?>
					</div>
					<?php
    }
}
?>

		</div>
	</div>
</div>

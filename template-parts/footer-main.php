<?php
$logo = get_field('wpde_logo', 'option');
if ($logo) {
    $logo_text = !empty($logo['text']) ? $logo['text'] : get_bloginfo('name');
} else {
    $logo_text = esc_html(get_bloginfo('name'));
}
?>
<footer class="container-fluid px-0">
	<div class="container">
		<div class="d-flex flex-column-reverse flex-md-row justify-content-start justify-content-md-between gap-3 py-4">
			<div>
				<small class="text-muted">© <?php echo esc_html(date('Y')) . ' ' . esc_html($logo_text) . '. ' . __('All rights reserved.', 'wpde'); ?></small>
			</div>
			<div class="d-flex align-items-center gap-4">
				<ul class="navbar-nav align-items-center flex-row column-gap-3 mb-0">
					<li class="nav-item">
						<a href="#!" class="nav-link py-0 py-md-2 text-muted" onclick="CookieConsent.showPreferences(); return false;">
							<?php _e('Privacy settings', 'wpde'); ?>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?php echo esc_url(get_permalink(3)); ?>" class="nav-link py-0 py-md-2 text-muted">
							<?php echo esc_html(get_the_title(3)); ?>
						</a>
					</li>
				</ul>
				<?php get_template_part('template-parts/social', 'media'); ?>
			</div>
		</div>
	</div>
</footer>

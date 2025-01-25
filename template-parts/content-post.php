<a class="card h-100 border-0" href="<?php echo esc_url(get_permalink()); ?>">
	<div class="position-relative">
		<?php
        if (has_post_thumbnail()) {
            the_post_thumbnail('thumb-rectangle', ['class' => 'card-img-top rounded-4 border']);
        } else {
            $default_image_url = get_template_directory_uri() . '/img/default-thumbnail.webp';
            echo '<img src="' . esc_url($default_image_url) . '" class="card-img-top rounded-4 border" alt="' . __('Default Thumbnail', 'wpde') . '">';
        }
        ?>
	</div>
	<div class="card-body px-0">
        <div class="d-flex items-center gap-3 mb-3">
        <?php
            $time = get_the_time('U');
            $modified_time = get_the_modified_time('U');
            if ($modified_time >= $time + 86400) {
                echo '<small> ' . esc_html(get_the_modified_time('F jS, Y')) . '</small>';
            } else {
                echo '<small> ' . esc_html(get_the_date('F jS, Y')) . '</small>';
            }
            if (has_category()) {
                $categories = get_the_category();
                foreach ($categories as $category) {
                    echo '<small class="bg-body-secondary border rounded-3 px-2">' . esc_html($category->name) . '</small>';
                }
            }
            if (get_post_type() === 'page') {
                echo '<small class="bg-body-secondary border rounded-3 px-2">' . __('Page', 'wpde') . '</small>';
            }
        ?>
        </div>
		<h4 class="mb-3"><?php echo esc_html(get_the_title()); ?></h4>
		<p class="mb-0"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 15, '')); ?></p> 
	</div>
</a>

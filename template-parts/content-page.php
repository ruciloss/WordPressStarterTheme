<div class="col-md-12">     
    <?php
    WPDE()->the_title(get_the_title(), __('Page', 'wpde'), wp_trim_words(get_the_excerpt(), 15, ''));
    get_template_part('template-parts/content', 'breadcrumbs');

    if (has_post_thumbnail()) {
        $image_size_custom = wp_is_mobile() ? 'thumb' : 'large-lg';
        the_post_thumbnail($image_size_custom, ['class' => 'img-fluid mb-6 shadow-lg']);
    }

    the_content();
    ?>		
</div>

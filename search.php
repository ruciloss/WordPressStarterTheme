<?php
/**
 * The template for displaying search results pages.
 *
 * @package WordPress Development Environment (WPDE)
 * @author Ruciloss
 * 
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 */
?>

<?php get_header(); ?>

<div class="container-fluid px-0 py-6">
    <div class="container">
        <?php
        $search_query = get_search_query();
        $title = sprintf(__('Content matching your query: "%s"', 'wpde'), esc_html($search_query));
        WPDE()->the_title($title, __('Search results', 'wpde'), __('Explore our latest articles and resources matching your search query.', 'wpde'));
        get_template_part('template-parts/content', 'breadcrumbs');
        ?>
        <div class="row mt-6 gy-4">
            <?php if (have_posts()) {
                while (have_posts()) {
                    the_post();
                    $grid = get_field('wpde_grid', 'option');
                    if ($grid) {
                        $grid_size = intval($grid['search']);
                        $card = $grid['search_card'];
                    } else {
                        $grid_size = 3;
                        $card = 'post';
                    }
                    echo '<div class="col-md-' . esc_attr($grid_size) . '">';
                        get_template_part('template-parts/content', $card);
                    echo '</div>';
                }
                get_template_part('template-parts/content', 'pagination');
                wp_reset_postdata();
            } else {
                $html = '<div class="col-lg-12">';
                    $html .= '<p class="text-danger mb-0">' . esc_html(__('Sorry, no data was found matching your search terms. Please try again with different keywords.', 'wpde')) . '</p>';
                $html .= '</div>';

                echo $html;
            } ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>

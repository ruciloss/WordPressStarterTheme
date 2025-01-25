<?php
/**
 * The template for displaying taxonomy pages.
 *
 * @package WordPress Development Environment (WPDE)
 * @author Ruciloss
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#custom-taxonomies
 */
?>

<?php get_header(); ?>

<div class="container px-0 py-6">
    <div class="container">
        <?php
        $current_term = get_queried_object();
        WPDE()->the_title($current_term->name, __('Taxonomy', 'wpde'), wp_kses_post(term_description()));
        get_template_part('template-parts/content', 'breadcrumbs');
        ?>
        <div class="row mt-6 gy-4">
            <?php
            if (have_posts()) {
                while (have_posts()) {
                    the_post();
                    $grid = get_field('wpde_grid', 'option');
                    if ($grid) {
                        $grid_size = intval($grid['taxonomy']);
                        $card = $grid['taxonomy_card'];
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
                    $html .= '<p class="text-danger mb-0">' . esc_html(__('Sorry, no data was found in this taxonomy.', 'wpde')) . '</p>';
                $html .= '</div>';

                echo $html;
            }
            ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>

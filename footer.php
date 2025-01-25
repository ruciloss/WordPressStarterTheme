<?php
/**
 * The template for displaying the footer.
 *
 * @package WordPress Development Environment (WPDE)
 * @author Ruciloss
 * 
 * @link https://developer.wordpress.org/reference/functions/get_footer/
 */
?>

</main>

<?php
if (!is_404()) {
    $footer = get_field('wpde_footer', 'option');
    if ($footer) {
        $footer_layout = $footer['layout'];
    } else {
        $footer_layout = 'Simple';
    }

    switch ($footer_layout) {
        case 'Advanced':
            get_template_part('template-parts/footer', 'top');
            get_template_part('template-parts/footer', 'main');
            break;
        case 'Simple':
        case '':
        default:
            get_template_part('template-parts/footer', 'main');
            break;
    }
}

get_template_part('template-parts/modal', 'search');
get_template_part('template-parts/content', 'alert');

wp_footer();
?>

</body>
</html>

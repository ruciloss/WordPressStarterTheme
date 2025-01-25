<?php
$options = get_field('wpde_options', 'option');
if ($options) {
    $breadcrumbs = $options['breadcrumbs'];
}

if (empty($breadcrumbs)) {
    WPDE()->breadcrumbs();
}

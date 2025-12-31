<?php
/**
 * Template Name: Smart Blocks Full Width
 *
 * @link https://developer.wordpress.org/themes/template-files-section/page-template-files/
 *
 * @package Smart Blocks
 */

if (Smart_Blocks::is_fse_template()) {
    ?>
    <!doctype html>
    <html <?php language_attributes(); ?>>

        <head>
            <meta charset="<?php bloginfo('charset'); ?>" />
            <meta name="viewport" content="width=device-width, initial-scale=1" />
            <link rel="profile" href="https://gmpg.org/xfn/11" />
            <?php wp_head(); ?>
        </head>

        <body <?php body_class(); ?>>
            <?php
} else {
    get_header();
}
/* Start the Loop */
while (have_posts()):
    the_post();
    the_content();
endwhile;
// End of the loop.
if (Smart_Blocks::is_fse_template()) {
    wp_footer();
    ?>
        </body>

    </html>
    <?php
} else {
    get_footer();
}
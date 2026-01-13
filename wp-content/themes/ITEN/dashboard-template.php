<?php
/**
 * Template Name: ENERG Dashboard (No Header/Footer)
 */

$session = energ_get_session();

if (!$session || empty($session['member_id'])) {
    wp_redirect(site_url('/login'));
    exit;
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body style="background:#f5f6fa;">
    <div id="energ-dashboard-wrapper">
        <?php echo do_shortcode('[energ_dashboard]'); ?>
    </div>

    <?php wp_footer(); ?>
</body>
</html>

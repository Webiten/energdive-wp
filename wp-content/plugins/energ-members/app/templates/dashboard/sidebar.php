<?php
defined('ABSPATH') || exit;
$base_url = site_url('/dashboard');
$active = function($name) use($tab){
    return $tab === $name ? 'energ-s-active' : '';
};
?>

<aside class="energ-sidebar">
    <div class="energ-brand">
        <h3>ENERGD</h3>
        <small>iTen Media</small>
    </div>

    <nav class="energ-nav">
        <a class="<?php echo $active('dashboard'); ?>" href="<?php echo esc_url( $base_url . '?tab=dashboard' ); ?>">Dashboard</a>
        <a class="<?php echo $active('my-read'); ?>" href="<?php echo esc_url( $base_url . '?tab=my-read' ); ?>">My Read</a>
        <a class="<?php echo $active('saved'); ?>" href="<?php echo esc_url( $base_url . '?tab=saved' ); ?>">My Saved Articles</a>
        <a class="<?php echo $active('early-access'); ?>" href="<?php echo esc_url( $base_url . '?tab=early-access' ); ?>">Early Access</a>
        <a class="<?php echo $active('events'); ?>" href="<?php echo esc_url( $base_url . '?tab=events' ); ?>">Events / Sale</a>
        <a class="<?php echo $active('subscription'); ?>" href="<?php echo esc_url( $base_url . '?tab=subscription' ); ?>">Subscription</a>
        <a class="<?php echo $active('purchase-history'); ?>" href="<?php echo esc_url( $base_url . '?tab=purchase-history' ); ?>">Purchased History</a>
        <a class="<?php echo $active('membership'); ?>" href="<?php echo esc_url( $base_url . '?tab=membership' ); ?>">Membership</a>
    </nav>

    <div class="energ-sidebar-footer">
        <button id="energ-toggle-compact" class="energ-btn">Toggle</button>
    </div>
</aside>

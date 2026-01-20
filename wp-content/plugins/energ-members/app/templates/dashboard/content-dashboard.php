<?php
defined('ABSPATH') || exit;
?>

<section class="energ-grid">
    <div class="energ-card stats">
        <h4>Total Visitors</h4>
        <div class="energ-stat">5.9M</div>
    </div>

    <div class="energ-card stats">
        <h4>Bounce Rate</h4>
        <div class="energ-stat">62.11%</div>
    </div>

    <div class="energ-card stats">
        <h4>Conversion</h4>
        <div class="energ-stat">21.91%</div>
    </div>

    <div class="energ-card stats">
        <h4>Active Referrals</h4>
        <div class="energ-stat">470</div>
    </div>

    <div class="energ-card wide">
        <h3>Welcome back, <?php echo esc_html( $member->first_name ?: $member->phone ); ?></h3>
        <p>Here is a quick overview of your account and latest activity.</p>
    </div>

    <div class="energ-card">
        <h4>Profile</h4>
        <p><strong>Name:</strong> <?php echo esc_html(trim($member->first_name . ' ' . $member->last_name)); ?></p>
        <p><strong>Email:</strong> <?php echo esc_html($member->email ?: 'Not provided'); ?></p>
    </div>

    <div class="energ-card">
        <h4>Subscription</h4>
        <p>No active subscription.</p>
    </div>
</section>

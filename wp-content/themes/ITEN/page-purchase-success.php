<?php
/* Template Name: Purchase Success */
get_header();

$post_id = intval($_GET['article'] ?? 0);
$payment_id = sanitize_text_field($_GET['payment_id'] ?? '');
?>
<div class="purchase-success">
    <div class="tick" style="font-size:80px;color:green;">✓</div>
    <h1>Payment successful!</h1>
    <p>Payment id: <?php echo esc_html($payment_id); ?></p>
    <p>Thank you. Your article has been unlocked.</p>

    <?php if ($post_id): ?>
        <a class="continue-btn" href="<?php echo get_permalink($post_id); ?>">Continue to read →</a>
    <?php endif; ?>
</div>

<?php get_footer(); ?>

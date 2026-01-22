<?php
/* Template Name: Buy Article */
get_header();

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink())); exit;
}

$post_id = intval($_GET['id'] ?? 0);
if (!$post_id) { echo "Invalid article ID"; get_footer(); exit; }

$price = get_field('paid_price', $post_id);
if (!$price) { echo "Price not set for this article."; get_footer(); exit; }

$receipt = 'article_' . $post_id . '_user_' . get_current_user_id() . '_' . time();

// create order via server
$order = energ_rzp_create_order($price, 'INR', $receipt);
if ($order['code'] !== 200 && $order['code'] !== 201) {
    echo 'Error creating order'; get_footer(); exit;
}
$order_data = $order['body'];
?>
<div class="buy-article-page">
    <h1>Buy article: <?php echo get_the_title($post_id); ?></h1>
    <p>Amount: ₹<?php echo esc_html($price); ?></p>

    <button id="rzp-pay">Pay Now</button>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.getElementById('rzp-pay').addEventListener('click', function(){
    var options = {
        "key": "<?php echo defined('RZP_KEY_ID') ? RZP_KEY_ID : ''; ?>",
        "amount": "<?php echo esc_js($order_data['amount']); ?>",
        "currency": "<?php echo esc_js($order_data['currency']); ?>",
        "name": "<?php echo esc_js(get_bloginfo('name')); ?>",
        "description": "<?php echo esc_js(get_the_title($post_id)); ?>",
        "order_id": "<?php echo esc_js($order_data['id']); ?>",
        "handler": function (response){
            // On success, server will be informed by webhook; but we create a quick redirect to success page with payment id
            window.location.href = "<?php echo site_url('/purchase-success'); ?>?article=<?php echo $post_id; ?>&payment_id=" + response.razorpay_payment_id + "&order_id=" + response.razorpay_order_id + "&signature=" + response.razorpay_signature;
        },
        "prefill": {
            "name": "<?php echo esc_js(wp_get_current_user()->display_name); ?>",
            "email": "<?php echo esc_js(wp_get_current_user()->user_email); ?>"
        }
    };
    var rzp1 = new Razorpay(options);
    rzp1.open();
});
</script>

<?php get_footer(); ?>
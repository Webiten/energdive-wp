<?php
/* Template Name: Subscribe Page */
get_header();

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink())); exit;
}

$current_user = wp_get_current_user();
?>
<div class="subscribe-page">
    <h1>Subscribe</h1>

    <div class="plans">
        <div class="plan">
            <h3>Monthly</h3>
            <p>₹199 / month</p>
            <button id="rzp-sub-month" data-plan="plan_Rh6UQX339y9BlV">Subscribe Monthly</button>
        </div>

        <div class="plan">
            <h3>Yearly</h3>
            <p>₹1499 / year</p>
            <button id="rzp-sub-year" data-plan="plan_Rh6TwQPMWoGgcc">Subscribe Yearly</button>
        </div>
    </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.addEventListener('click', function(e){
    if (e.target && e.target.matches('button[data-plan]')) {
        const planId = e.target.getAttribute('data-plan');

        // Create subscription on server
        fetch("<?php echo admin_url('admin-ajax.php'); ?>?action=energ_create_subscription", {
            method: 'POST',
            credentials: 'same-origin',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify({ plan_id: planId })
        }).then(r => r.json()).then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            // data.subscription contains subscription.id and razorpay_checkout fields
            const options = {
                "key": "<?php echo defined('RZP_KEY_ID') ? RZP_KEY_ID : ''; ?>",
                "subscription_id": data.subscription_id,
                "name": "<?php echo get_bloginfo('name'); ?>",
                "description": "Subscription",
                "handler": function (response){
                    // we'll validate via webhook; but also record on client
                    window.location.href = "<?php echo site_url('/subscription-success/'); ?>?sub_id=" + response.razorpay_subscription_id;
                },
                "prefill": {
                    "name": "<?php echo esc_js($current_user->display_name); ?>",
                    "email": "<?php echo esc_js($current_user->user_email); ?>"
                }
            };
            const rzp = new Razorpay(options);
            rzp.open();
        }).catch(err => {
            console.error(err); alert('Something went wrong.');
        });
    }
});
</script>

<?php get_footer(); ?>
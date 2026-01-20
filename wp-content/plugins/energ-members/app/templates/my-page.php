<?php
// CHECK CUSTOM SESSION
$session = energ_get_session();

if (!$session || empty($session['member_id'])) {
    wp_redirect(site_url('/login'));
    exit;
}

global $wpdb;
$table = $wpdb->prefix . 'energ_members';

$member = $wpdb->get_row(
    $wpdb->prepare("SELECT * FROM $table WHERE id = %d", $session['member_id'])
);

if (!$member) {
    energ_clear_session();
    wp_redirect(site_url('/login'));
    exit;
}
?>

<div id="energ-dashboard" style="max-width:600px;margin:30px auto;padding:20px;border:1px solid #ccc;border-radius:10px;">
    <h2>Welcome, <?php echo esc_html($member->first_name ?: $member->phone); ?> 👋</h2>

    <p><strong>Phone:</strong> <?php echo esc_html($member->phone); ?></p>

    <?php if (!empty($member->email)): ?>
        <p><strong>Email:</strong> <?php echo esc_html($member->email); ?></p>
    <?php else: ?>
        <p style="color:red;">Email not verified yet.</p>
        <a href="<?php echo site_url('/complete-registration?phone='.$member->phone); ?>" class="button">Verify Email</a>
    <?php endif; ?>

    <hr>

    <h3>Your Profile</h3>
    <ul>
        <li><strong>Name:</strong> <?php echo esc_html(trim($member->first_name . ' ' . $member->last_name)); ?></li>
        <li><strong>Date of Birth:</strong> <?php echo esc_html($member->dob); ?></li>
        <li><strong>Community:</strong> <?php echo esc_html($member->community); ?></li>
        <li><strong>Sub Community:</strong> <?php echo esc_html($member->sub_community); ?></li>
    </ul>

    <hr>

    <button id="energ-logout" style="background:#d00;color:#fff;padding:10px 18px;border:none;border-radius:5px;cursor:pointer;">
        Logout
    </button>
</div>

<script>
jQuery(function($){
    $("#energ-logout").on("click", function(){
        $.post(ENERG_AJAX.ajax_url, {
            action: "energ_logout",
            nonce: ENERG_AJAX.nonce
        }, function(res){
            if (res.success) {
                window.location.href = "<?php echo site_url('/login'); ?>";
            }
        });
    });
});
</script>

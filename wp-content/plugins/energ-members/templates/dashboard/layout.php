<?php
// layout.php
defined('ABSPATH') || exit;

// SESSION CHECK
$session = function_exists('energ_get_session') ? energ_get_session() : false;
if (!$session || empty($session['member_id'])) {
    wp_redirect(site_url('/login'));
    exit;
}

global $wpdb;
$table = $wpdb->prefix . 'energ_members';

$member = $wpdb->get_row(
    $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $session['member_id'])
);

if (!$member) {
    if (function_exists('energ_clear_session')) energ_clear_session();
    wp_redirect(site_url('/login'));
    exit;
}

/**
 * Helper: determine term id from stored value (id, slug or name)
 */
if (!function_exists('_energ_term_id_from_value')) {
    function _energ_term_id_from_value($val, $taxonomy) {
        if (empty($val)) return '';
        if (is_numeric($val)) return intval($val);
        $t = get_term_by('slug', sanitize_title($val), $taxonomy);
        if ($t && !is_wp_error($t)) return (int)$t->term_id;
        $t2 = get_term_by('name', $val, $taxonomy);
        if ($t2 && !is_wp_error($t2)) return (int)$t2->term_id;
        return '';
    }
}

$member_comm_id    = _energ_term_id_from_value($member->community ?? '', 'sector');
$member_subcomm_id = _energ_term_id_from_value($member->sub_community ?? '', 'sector');

$member_ind_id     = _energ_term_id_from_value($member->industry ?? '', 'industry');
$member_subind_id  = _energ_term_id_from_value($member->sub_industry ?? '', 'industry');

$tab = sanitize_text_field($_GET['tab'] ?? 'dashboard');

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>ENERG Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
/* ---------------- basic styles - keep as needed ---------------- */
body { background:#f8f9fc; font-family: "Inter", sans-serif; margin:0; }
.energ-wrapper { display:flex; width:100%; min-height:100vh; }
.energ-sidebar { width:240px; background:#fff; border-right:1px solid #e4e6ef; padding:25px 0; position:fixed; height:100vh; overflow-y:auto; }
.energ-sidebar h2{ text-align:center; font-size:22px; font-weight:700; margin-bottom:25px; color:#111827; }
.energ-menu{ list-style:none; padding:0; margin:0; }
.energ-menu li a{ display:flex; align-items:center; gap:12px; padding:12px 25px; font-size:15px; color:#374151; text-decoration:none; transition:0.25s; }
.energ-menu li a:hover, .energ-menu li a.active { background:#eef2ff; color:#3b5bdb; font-weight:600; }
.energ-content { margin-left:240px; padding:28px; width:calc(100% - 240px); }
.energ-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
.energ-header h1 { font-size:36px; font-weight:700; margin:0; color:#111827; }
.logout-btn { background:#ef4444; border:none; padding:8px 18px; border-radius:6px; color:white; font-size:14px; cursor:pointer; }

/* cards */
.card-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:22px; margin-top:18px; }
.aurora-card { background:white; padding:22px; border-radius:12px; border:1px solid #e5e7eb; box-shadow:0 4px 12px rgba(0,0,0,0.03); }
.aurora-title{ font-size:14px; color:#6b7280; margin-bottom:8px; font-weight:600; }
.aurora-value{ font-size:28px; font-weight:700; color:#2563eb; }

/* account layout */
.account-wrapper{ display:flex; gap:28px; }
.account-menu{ width:280px; background:#fff; border:1px solid #e5e7eb; padding:20px; border-radius:10px; height:max-content; }
.account-item{ display:flex; align-items:center; gap:12px; padding:10px 12px; border-radius:8px; cursor:pointer; font-size:15px; margin-bottom:8px; transition:0.15s; }
.account-item:hover, .account-item.active{ background:#eef2ff; color:#3b5bdb; }
.account-content{ flex:1; background:#fff; border:1px solid #e5e7eb; padding:26px; border-radius:12px; }
.account-section-title{ font-size:22px; font-weight:700; margin-bottom:18px; }
.account-box{ padding:16px; background:#f9fafb; border-radius:10px; border:1px solid #e5e7eb; margin-bottom:18px; }
.account-grid{ display:grid; grid-template-columns:1fr 1fr; gap:16px; }
label { font-weight:600; font-size:13px; margin-bottom:6px; display:block; }
.form-control, .form-select { height:44px; }

@media (max-width:900px){
  .energ-sidebar{ display:none; }
  .energ-content{ margin-left:0; width:100%; padding:18px; }
  .card-grid{ grid-template-columns:1fr; }
  .account-grid{ grid-template-columns:1fr; }
}
</style>
<?php wp_head(); ?>
</head>
<body>

<div class="energ-wrapper">
    <aside class="energ-sidebar">
        <h2>ENERGD</h2>
        <ul class="energ-menu">
            <li><a href="<?php echo esc_url(site_url('/dashboard/?tab=dashboard')); ?>" class="<?php echo $tab === 'dashboard' ? 'active' : ''; ?>"><i class='bx bx-grid-alt'></i> Dashboard</a></li>
            <li><a href="<?php echo esc_url(site_url('/dashboard/?tab=myread')); ?>" class="<?php echo $tab === 'myread' ? 'active' : ''; ?>"><i class='bx bx-book'></i> My Read</a></li>
            <li><a href="<?php echo esc_url(site_url('/dashboard/?tab=saved')); ?>" class="<?php echo $tab === 'saved' ? 'active' : ''; ?>"><i class='bx bx-bookmark'></i> Saved Articles</a></li>
            <li><a href="<?php echo esc_url(site_url('/dashboard/?tab=early')); ?>" class="<?php echo $tab === 'early' ? 'active' : ''; ?>"><i class='bx bx-lock-open'></i> Early Access</a></li>
            <li><a href="<?php echo esc_url(site_url('/dashboard/?tab=events')); ?>" class="<?php echo $tab === 'events' ? 'active' : ''; ?>"><i class='bx bx-calendar-event'></i> Events / Sale</a></li>
            <li><a href="<?php echo esc_url(site_url('/dashboard/?tab=subscription')); ?>" class="<?php echo $tab === 'subscription' ? 'active' : ''; ?>"><i class='bx bx-credit-card'></i> Subscription</a></li>
            <li><a href="<?php echo esc_url(site_url('/dashboard/?tab=purchases')); ?>" class="<?php echo $tab === 'purchases' ? 'active' : ''; ?>"><i class='bx bx-receipt'></i> Purchase History</a></li>
            <li><a href="<?php echo esc_url(site_url('/dashboard/?tab=membership')); ?>" class="<?php echo $tab === 'membership' ? 'active' : ''; ?>"><i class='bx bx-shield'></i> Membership</a></li>
            <li><a href="<?php echo esc_url(site_url('/dashboard/?tab=account')); ?>" class="<?php echo $tab === 'account' ? 'active' : ''; ?>"><i class='bx bx-user'></i> My Account</a></li>
        </ul>
    </aside>

    <main class="energ-content">
        <div class="energ-header">
            <h1>Welcome, <?php echo esc_html($member->first_name ?: $member->phone); ?></h1>
            <div>
                <button id="energ-logout" class="logout-btn">Logout</button>
            </div>
        </div>

        <?php
        // include parts based on tab
        switch ($tab):
            case 'dashboard':
                include __DIR__ . '/parts/dashboard.php';
                break;

            case 'account':
                include __DIR__ . '/parts/account.php';
                break;

            // simple placeholders for other tabs (you can create parts later)
            case 'myread':
                echo '<p>No reads yet.</p>';
                break;
            case 'saved':
                echo '<p>No saved articles.</p>';
                break;
            case 'early':
                echo '<p>Early access content coming soon.</p>';
                break;
            case 'events':
                echo '<p>Events / Sale list.</p>';
                break;
            case 'subscription':
                echo '<p>No active subscription.</p>';
                break;
            case 'purchases':
                echo '<p>Purchase history.</p>';
                break;
            case 'membership':
                echo '<p>Your membership level.</p>';
                break;

        endswitch;
        ?>

    </main>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
jQuery(function($){
    $('#energ-logout').on('click', function(){
        $.post( (typeof ENERG_AJAX !== 'undefined' ? ENERG_AJAX.ajax_url : '<?php echo admin_url('admin-ajax.php'); ?>' ), {
            action: "energ_logout",
            nonce: (typeof ENERG_AJAX !== 'undefined' ? ENERG_AJAX.nonce : '')
        }, function(res){
            if (res && res.success) {
                window.location.href = "<?php echo esc_js(site_url('/login')); ?>";
            } else {
                window.location.href = "<?php echo esc_js(site_url('/login')); ?>";
            }
        }, 'json');
    });
});
</script>

<?php wp_footer(); ?>
</body>
</html>

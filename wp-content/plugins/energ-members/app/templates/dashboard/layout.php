<?php
// SESSION CHECK
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

// Fetch READ count
$read_count = (int) $wpdb->get_var(
    $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}energ_reads WHERE member_id = %d", $member->id)
);

// Fetch SAVED count
$saved_count = (int) $wpdb->get_var(
    $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}energ_saved WHERE member_id = %d", $member->id)
);

// Latest News Post
$latest_news = get_posts([
    'post_type' => 'news',
    'post_status' => 'publish',
    'posts_per_page' => 1,
]);

// Latest Opinions (Carousel)
$latest_opinions = get_posts([
    'post_type' => 'opinion',
    'post_status' => 'publish',
    'posts_per_page' => 3,
]);


if (!$member) {
    energ_clear_session();
    wp_redirect(site_url('/login'));
    exit;
}

/**
 * Helpers to determine term IDs for pre-selection even if DB stores name
 */
function _energ_term_id_from_value($val, $taxonomy) {
    if (empty($val)) return '';
    if (is_numeric($val)) return intval($val);
    $t = get_term_by('slug', sanitize_title($val), $taxonomy);
    if ($t && !is_wp_error($t)) return (int)$t->term_id;
    $t2 = get_term_by('name', $val, $taxonomy);
    if ($t2 && !is_wp_error($t2)) return (int)$t2->term_id;
    return '';
}

$member_comm_id = _energ_term_id_from_value($member->community ?? '', 'sector');
$member_subcomm_id = _energ_term_id_from_value($member->sub_community ?? '', 'sector');

$member_ind_id = _energ_term_id_from_value($member->industry ?? '', 'industry');
$member_subind_id = _energ_term_id_from_value($member->sub_industry ?? '', 'industry');

?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
/* --- Basic page layout --- */
body { background:#f6f7fb !important; font-family:"Inter",sans-serif !important; margin:0; padding:0; color:#111827; }
.energ-wrapper { display:flex; width:100%; min-height:100vh; }

/* --- Sidebar --- */
.energ-sidebar { width:260px; background:#fff; border-right:1px solid #e6e9ef; padding:30px 18px; position:fixed; height:100vh; overflow-y:auto; }
.energ-sidebar h2 { font-size:20px; font-weight:700; margin:0 0 18px; color:#0f172a; }
.energ-menu { list-style:none; padding:0; margin:0; }
.energ-menu li { margin-bottom:8px; }
.energ-menu li a { display:block; padding:10px 12px; color:#334155; text-decoration:none; border-radius:8px; transition:all .15s; }
.energ-menu li a:hover { background:#eef2ff; color:#1e40af; font-weight:600; }

/* --- Main content area --- */
.energ-content { margin-left:260px; padding:24px 32px; width:calc(100% - 260px); }

/* --- Aurora topbar --- */
.aurora-topbar { display:flex; align-items:center; justify-content:space-between; gap:18px; margin-bottom:22px; }
.aurora-left { display:flex; align-items:center; gap:18px; }
.aurora-search { position:relative; min-width:360px; }
.aurora-search input { width:100%; height:44px; padding:10px 38px 10px 16px; border-radius:10px; border:1px solid #e6e9ef; background:#fff; box-shadow:0 1px 2px rgba(16,24,40,0.02); }
.aurora-search .bx-search { position:absolute; right:12px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:18px; }

/* center greeting (for large header) */
.aurora-greeting { flex:1; padding-left:10px; }
.aurora-greeting h1 { margin:0; font-size:28px; font-weight:700; color:#0f172a; }
.aurora-greeting p { margin:3px 0 0; color:#64748b; font-size:13px; }

/* right icons */
.aurora-right { display:flex; align-items:center; gap:12px; }
.icon-btn { display:inline-flex; align-items:center; justify-content:center; width:44px; height:44px; border-radius:10px; background:#fff; border:1px solid #e6e9ef; cursor:pointer; position:relative; }
.icon-btn .badge { position:absolute; top:6px; right:6px; background:#ef4444; color:#fff; font-size:11px; padding:2px 6px; border-radius:999px; }

/* avatar dropdown */
.aurora-avatar { display:flex; align-items:center; gap:10px; cursor:pointer; padding:8px 10px; border-radius:10px; background:#fff; border:1px solid #e6e9ef; }
.aurora-avatar img { width:36px; height:36px; border-radius:999px; object-fit:cover; }

/* --- Dashboard cards area (Aurora style) --- */
.aurora-metrics { display:grid; grid-template-columns: repeat(3, 1fr); gap:20px; margin-bottom:22px; }
.metric-card { background:#fff; border:1px solid #e6e9ef; border-radius:12px; padding:18px; box-shadow:0 6px 18px rgba(2,6,23,0.03); }
.metric-title { color:#475569; font-size:13px; margin-bottom:8px; font-weight:600; }
.metric-value { font-size:26px; font-weight:700; color:#0f172a; }
.metric-sub { color:#10b981; font-size:12px; margin-top:10px; }

/* --- Account layout block (kept similar to previous) --- */
.account-wrapper { display:flex; gap:30px; }
.account-menu { width:300px; background:#fff; border:1px solid #e6e9ef; padding:22px; border-radius:10px; }
.account-item { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:8px; cursor:pointer; margin-bottom:8px; color:#374151; }
.account-item.active, .account-item:hover { background:#eef2ff; color:#1e40af; font-weight:600; }
.account-content { flex:1; background:#fff; border:1px solid #e6e9ef; padding:24px; border-radius:12px; }

.account-section-title { font-size:20px; font-weight:700; margin-bottom:18px; }

/* Responsive */
@media (max-width:1000px) {
    .aurora-search { min-width:220px; }
    .aurora-greeting { display:none; }
    .aurora-metrics { grid-template-columns:1fr; }
    .energ-sidebar { display:none; position:relative; width:100%; height:auto; }
    .energ-content { margin-left:0; padding:18px; width:100%; }
}
</style>

<?php
$tab = $_GET['tab'] ?? 'dashboard';
?>
<div class="energ-wrapper">

    <!-- SIDEBAR -->
    <aside class="energ-sidebar">
        <h2>ENERGD</h2>
        <ul class="energ-menu">
            <li><a href="/dashboard/?tab=dashboard"><i class='bx bx-grid-alt'></i> Dashboard</a></li>
            <li><a href="/dashboard/?tab=myread"><i class='bx bx-book'></i> My Read</a></li>
            <li><a href="/dashboard/?tab=saved"><i class='bx bx-bookmark'></i> Saved Articles</a></li>
            <li><a href="/dashboard/?tab=early"><i class='bx bx-lock-open'></i> Early Access</a></li>
            <li><a href="/dashboard/?tab=events"><i class='bx bx-calendar-event'></i> Events / Sale</a></li>
            <li><a href="/dashboard/?tab=subscription"><i class='bx bx-credit-card'></i> Subscription</a></li>
            <li><a href="/dashboard/?tab=purchases"><i class='bx bx-receipt'></i> Purchase History</a></li>
            <li><a href="/dashboard/?tab=membership"><i class='bx bx-shield'></i> Membership</a></li>
            <li><a href="/dashboard/?tab=account"><i class='bx bx-user'></i> My Account</a></li>
        </ul>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="energ-content">

        <!-- TOPBAR -->
        <div class="aurora-topbar">
            <div class="aurora-left">
                <div class="aurora-search">
                    <input id="aurora_search_input" placeholder="Search articles, topics, people..." />
                    <i class="bx bx-search"></i>
                </div>
            </div>

            <div class="aurora-right">

                <div class="icon-btn" id="aurora_notify_btn" title="Notifications">
                    <i class="bx bx-bell" style="font-size:18px;color:#0f172a;"></i>
                    <span class="badge">2</span>
                </div>

                <div class="aurora-avatar" id="aurora_avatar_btn" title="Account">
                    <img src="<?php echo esc_url(get_avatar_url($member->email ?: $member->phone, ['size'=>80])); ?>" alt="avatar" />
                    <div style="font-size:14px;">
                        <div style="font-weight:700;"><?php echo esc_html($member->first_name ?: 'Member'); ?></div>
                        <div style="font-size:12px;color:#64748b;"><?php echo esc_html($member->email ?: $member->phone); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <?php switch ($tab): ?>
<?php case 'dashboard': ?>
<?php
// Featured Stories
$featured_stories = get_posts([
    'post_type' => 'articles',
    'posts_per_page' => 3,
    'tax_query' => [[
        'taxonomy' => 'content_type',
        'field'    => 'name',
        'terms'    => 'Featured Stories'
    ]]
]);

// Latest Article
$latest_article = get_posts([
    'post_type' => 'reports',
    'posts_per_page' => 1,
]);
$latest_article = $latest_article ? $latest_article[0] : null;

// Latest News (6 items)
$latest_news = get_posts([
    'post_type' => 'news',
    'posts_per_page' => 6,
]);

$placeholder = "https://via.placeholder.com/600x350/EEE/AAA?text=No+Image";
?>

<style>
/* STOP FULL PAGE SHAKE */
html, body {
    overflow-x: hidden !important;
}

/* RIGHT COLUMN SHOULD NOT EXPAND */
.dashboard-grid > div:last-child {
    min-width: 0; /* IMPORTANT FIX */
}
/* ---- FIXED GRID ---- */
.dashboard-grid {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 24px;
}

/* ---- PANELS ---- */
.aurora-panel {
    background: #fff;
    border: 1px solid #e6e9ef;
    border-radius: 14px;
    padding: 20px;
}

/* ---- SCROLLABLE NEWS ---- */
.news-scroll-box {
    max-height: 420px;
    overflow-y: auto;
    margin-top: 14px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    padding-right: 4px;
}
.news-card {
    display: flex;
    gap: 14px;
    padding: 12px;
    border-radius: 12px;
    background: #fff;
    border: 1px solid #e6e9ef;
    text-decoration: none;
    color: #0f172a;
    transition: 0.2s;
}
.news-card:hover {
    background: #f8fafc;
}
.news-thumb {
    width: 105px;
    height: 70px;
    border-radius: 10px;
    object-fit: cover;
}
.news-title {
    font-size: 15px;
    font-weight: 600;
}
.news-excerpt {
    font-size: 13px;
    color: #64748b;
}

/* ---- FEATURED STORIES ---- */
.fs-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px;
    border: 1px solid #e6e9ef;
    border-radius: 12px;
    background: #fff;
    text-decoration: none;
    color: #111;
}
.fs-img {
    width: 70px;
    height: 55px;
    border-radius: 10px;
    object-fit: cover;
}

/* ---- LATEST ARTICLE ---- */
.article-box img {
    width: 100%;
    height: 230px;
    border-radius: 12px;
    object-fit: cover;
}

/* ---- OPINION SLIDER ---- */
/* OPINION WRAPPER – FIXED & SAFE */
.opinion-slider-wrapper {
    position: relative;
    overflow: hidden;
    width: 100%;
    padding: 0 35px; /* safe padding so arrows don't overflow */
    box-sizing: border-box;
}

/* OPINION SLIDER ROW */
.opinion-slider {
    display: flex;
    gap: 16px;
    transition: transform 0.35s ease;
    will-change: transform;
}

/* Ensure cards don't break the layout */
.opinion-card {
    min-width: 260px;
    max-width: 260px;
    flex-shrink: 0;
    background: white;
    border: 1px solid #e6e9ef;
    border-radius: 12px;
    padding: 12px;
}

/* FIX: Arrow buttons inside wrapper */
.opinion-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 34px;
    height: 34px;
    border-radius: 10px;
    background: #fff;
    border: 1px solid #ddd;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    z-index: 20;
}

/* SAFE POSITIONS */
.opinion-prev { left: 5px; }
.opinion-next { right: 5px; }

/* FIX: Prevent right column from stretching */
.right-top {
    min-width: 0 !important;
    overflow: hidden !important;
}
</style>

<div class="dashboard-grid">

    <!-- LEFT COLUMN -->
    <div>
        <div class="aurora-panel">
            <div class="aurora-title-sm"><?php echo date_i18n("l, M d, Y"); ?></div>
            <h3>Good morning, <?php echo esc_html($member->first_name); ?>!</h3>

            <p style="color:#64748b;font-size:13px;">Updates from your activity.</p>

            <p><b><?php echo $read_count; ?></b> Reads</p>
            <p><b><?php echo $saved_count; ?></b> Saved Articles</p>
        </div>

        <!-- Latest News (Scrollable 6 items) -->
        <div class="aurora-panel">
            <div class="aurora-title-sm">Latest News</div>

            <div class="news-scroll-box">
                <?php foreach($latest_news as $n): ?>
                    <a class="news-card" href="<?= get_permalink($n->ID) ?>">
                        <img class="news-thumb" src="<?= get_the_post_thumbnail_url($n->ID,'medium') ?: $placeholder ?>">
                        <div>
                            <div class="news-title"><?= esc_html($n->post_title); ?></div>
                            <div class="news-excerpt"><?= wp_trim_words($n->post_content, 18); ?></div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN -->
    <div>

        <!-- ROW: Featured Stories + Latest Article -->
        <div style="display:grid;grid-template-columns:1fr 1.3fr;gap:24px;">

            <!-- Featured Stories -->
            <div class="aurora-panel">
                <div class="aurora-title-sm">Latest Featured Stories</div>

                <div style="display:flex;flex-direction:column;gap:12px;margin-top:12px;">
                    <?php foreach($featured_stories as $fs): ?>
                        <a class="fs-item" href="<?= get_permalink($fs->ID) ?>">
                            <img class="fs-img" src="<?= get_the_post_thumbnail_url($fs->ID,'medium') ?: $placeholder ?>">
                            <div><b><?= $fs->post_title ?></b></div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Latest Article -->
            <div class="aurora-panel article-box">
                <div class="aurora-title-sm">Latest Article</div>

                <?php if($latest_article): ?>
                    <img src="<?= get_the_post_thumbnail_url($latest_article->ID,'large') ?: $placeholder ?>">
                    <h3 style="margin-top:10px;"><?= $latest_article->post_title ?></h3>
                <?php endif; ?>
            </div>
        </div>

        <!-- Opinions Slider -->
        <div class="aurora-panel">
            <div class="aurora-title-sm">Latest Opinions</div>

            <div class="opinion-slider-wrapper">
                <div class="opinion-arrow opinion-prev"><i class="bx bx-chevron-left"></i></div>
                <div class="opinion-arrow opinion-next"><i class="bx bx-chevron-right"></i></div>

                <div id="opSlider" class="opinion-slider">
                    <?php foreach($latest_opinions as $op): ?>
                        <a class="opinion-card" href="<?= get_permalink($op->ID) ?>">
                            <img src="<?= get_the_post_thumbnail_url($op->ID,'medium') ?: $placeholder ?>">
                            <h4 style="font-size:14px;margin-top:8px;"><?= $op->post_title ?></h4>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
let s=document.getElementById("opSlider"),o=0;
function w(){return s.children[0].offsetWidth+16;}
document.querySelector(".opinion-next").onclick=()=>{o-=w();s.style.transform=`translateX(${o}px)`};
document.querySelector(".opinion-prev").onclick=()=>{o+=w();if(o>0)o=0;s.style.transform=`translateX(${o}px)`};
</script>

<?php break; ?>


        <!-- other cases (myread, saved ...) you can re-add similar blocks from your previous file if needed -->

        <?php case 'account': ?>

            <div class="account-wrapper">
                <!-- LEFT MENU -->
                <div class="account-menu">
                    <h3>Account Settings</h3>

                    <div class="account-item" onclick="showAccTab('personal', this)"><i class="bx bx-user"></i> Personal Information</div>
                    <div class="account-item" onclick="showAccTab('work', this)"><i class="bx bxs-graduation"></i> Work & Education</div>
                    <div class="account-item" onclick="showAccTab('privacy', this)"><i class="bx bx-lock-alt"></i> Privacy & Protection</div>
                    <div class="account-item" onclick="showAccTab('notifications', this)"><i class="bx bx-bell"></i> Notifications & Alerts</div>
                </div>

                <!-- RIGHT PANEL -->
                <div class="account-content">

                    <!-- PERSONAL INFO -->
                    <div id="acc_tab_personal" class="acc-tab">
                        <h2 class="account-section-title">Personal Information</h2>

                        <div class="account-box">
                            <div class="account-grid">
                                <div>
                                    <label>Salutation</label>
                                    <select id="acc_salutation" class="form-select">
                                        <option value="">Select</option>
                                        <option value="Mr." <?php selected($member->salutation ?? '', 'Mr.'); ?>>Mr.</option>
                                        <option value="Ms." <?php selected($member->salutation ?? '', 'Ms.'); ?>>Ms.</option>
                                        <option value="Mrs." <?php selected($member->salutation ?? '', 'Mrs.'); ?>>Mrs.</option>
                                        <option value="Dr." <?php selected($member->salutation ?? '', 'Dr.'); ?>>Dr.</option>
                                    </select>
                                </div>
                                <div>
                                    <label>Country</label>
                                    <input id="acc_country" type="text" class="form-control" value="<?php echo esc_attr($member->country); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="account-box">
                            <div class="account-grid">
                                <div>
                                    <label>First Name</label>
                                    <input id="acc_firstname" type="text" class="form-control" value="<?php echo esc_attr($member->first_name); ?>">
                                </div>

                                <div>
                                    <label>Last Name</label>
                                    <input id="acc_lastname" type="text" class="form-control" value="<?php echo esc_attr($member->last_name); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="account-box">
                            <div class="account-grid">
                                <div>
                                    <label>Phone Number</label>
                                    <input id="acc_phone" type="text" class="form-control" value="<?php echo esc_attr($member->phone); ?>">
                                </div>

                                <div>
                                    <label>Email</label>
                                    <input id="acc_email" type="email" class="form-control" value="<?php echo esc_attr($member->email); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="account-box">
                            <div class="account-grid">
                                <div>
                                    <label>Community (Sector)</label>
                                    <select id="acc_community_personal" class="form-select">
                                        <option value="">Select Community</option>
                                        <?php
                                        $parents = get_terms(['taxonomy'=>'sector','hide_empty'=>false,'parent'=>0]);
                                        foreach ($parents as $p): ?>
                                            <option value="<?php echo esc_attr($p->term_id); ?>" <?php selected($member_comm_id, $p->term_id); ?>><?php echo esc_html($p->name); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div>
                                    <label>Sub Community</label>
                                    <select id="acc_sub_community_personal" class="form-select">
                                        <option value="">Select Sub Community</option>
                                        <?php
                                        if ($member_comm_id) {
                                            $children = get_terms(['taxonomy'=>'sector','hide_empty'=>false,'parent'=>$member_comm_id]);
                                            foreach ($children as $c): ?>
                                                <option value="<?php echo esc_attr($c->term_id); ?>" <?php selected($member_subcomm_id, $c->term_id); ?>><?php echo esc_html($c->name); ?></option>
                                            <?php endforeach;
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button id="acc_save_personal" class="btn btn-primary mt-2">Save Personal</button>
                    </div>

                    <!-- WORK -->
                    <div id="acc_tab_work" class="acc-tab" style="display:none;">
                        <h2 class="account-section-title">Work & Education</h2>

                        <div class="account-box">
                            <div class="account-grid">
                                <div>
                                    <label>Organization</label>
                                    <input id="acc_organization" type="text" class="form-control" value="<?php echo esc_attr($member->organization); ?>">
                                </div>

                                <div>
                                    <label>Designation</label>
                                    <input id="acc_designation" type="text" class="form-control" value="<?php echo esc_attr($member->designation); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="account-box">
                            <div class="account-grid">
                                <div>
                                    <label>Industry</label>
                                    <select id="acc_industry_work" class="form-select">
                                        <option value="">Select Industry</option>
                                        <?php
                                        $ind_parents = get_terms(['taxonomy'=>'industry','hide_empty'=>false,'parent'=>0]);
                                        foreach ($ind_parents as $ip): ?>
                                            <option value="<?php echo esc_attr($ip->term_id); ?>" <?php selected($member_ind_id, $ip->term_id); ?>><?php echo esc_html($ip->name); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div>
                                    <label>Sub Industry</label>
                                    <select id="acc_sub_industry_work" class="form-select">
                                        <option value="">Select Sub Industry</option>
                                        <?php
                                        if ($member_ind_id) {
                                            $ind_children = get_terms(['taxonomy'=>'industry','hide_empty'=>false,'parent'=>$member_ind_id]);
                                            foreach ($ind_children as $ic): ?>
                                                <option value="<?php echo esc_attr($ic->term_id); ?>" <?php selected($member_subind_id, $ic->term_id); ?>><?php echo esc_html($ic->name); ?></option>
                                            <?php endforeach;
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="account-box">
                            <div class="account-grid">
                                <div>
                                    <label>Community (Sector) — (Work)</label>
                                    <select id="acc_community_work" class="form-select">
                                        <option value="">Select Community</option>
                                        <?php foreach ($parents as $p): ?>
                                            <option value="<?php echo esc_attr($p->term_id); ?>" <?php selected($member_comm_id, $p->term_id); ?>><?php echo esc_html($p->name); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div>
                                    <label>Sub Community (Work)</label>
                                    <select id="acc_sub_community_work" class="form-select">
                                        <option value="">Select Sub Community</option>
                                        <?php
                                        if ($member_comm_id) {
                                            $children = get_terms(['taxonomy'=>'sector','hide_empty'=>false,'parent'=>$member_comm_id]);
                                            foreach ($children as $c): ?>
                                                <option value="<?php echo esc_attr($c->term_id); ?>" <?php selected($member_subcomm_id, $c->term_id); ?>><?php echo esc_html($c->name); ?></option>
                                            <?php endforeach;
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button id="acc_save_work" class="btn btn-primary mt-2">Save Work</button>
                    </div>

                </div>
            </div>

        <?php break; ?>

        <?php endswitch; ?>

    </main>
</div>

<!-- small avatar dropdown template (hidden) -->
<div id="aurora_avatar_dropdown" style="display:none;">
    <div style="padding:12px;">
        <div style="display:flex;gap:10px;align-items:center;margin-bottom:8px;">
            <img src="<?php echo esc_url(get_avatar_url($member->email ?: $member->phone, ['size'=>80])); ?>" style="width:56px;height:56px;border-radius:999px;">
            <div>
                <div style="font-weight:700;"><?php echo esc_html($member->first_name ?: 'Member'); ?></div>
                <div style="font-size:13px;color:#64748b;"><?php echo esc_html($member->email ?: $member->phone); ?></div>
            </div>
        </div>
        <hr />
        <div style="padding:8px 0;">
            <a href="<?php echo site_url('/dashboard/?tab=account'); ?>" style="display:block;padding:8px 0;color:#0f172a;">Account Settings</a>
            <a id="aurora_profile_logout" href="#" style="display:block;padding:8px 0;color:#ef4444;">Logout</a>
        </div>
    </div>
</div>

<script>
function showAccTab(tab, el) {
    document.querySelectorAll('.acc-tab').forEach(t => t.style.display = 'none');
    var target = document.getElementById("acc_tab_" + tab);
    if (target) target.style.display = 'block';
    document.querySelectorAll('.account-item').forEach(i => i.classList.remove('active'));
    if (el) el.classList.add('active');
}

document.addEventListener('DOMContentLoaded', function(){
    var first = document.querySelector('.account-item');
    if(first) {
        first.classList.add('active');
        showAccTab('personal', first);
    }

    // Avatar dropdown (simple)
    var avatarBtn = document.getElementById('aurora_avatar_btn');
    var dropdown = null;
    if (avatarBtn) {
        avatarBtn.addEventListener('click', function(e){
            e.preventDefault();
            // remove old
            var existing = document.getElementById('aurora_dropdown_box');
            if (existing) { existing.parentNode.removeChild(existing); return; }
            var box = document.createElement('div');
            box.id = 'aurora_dropdown_box';
            box.style.position = 'absolute';
            box.style.right = '32px';
            box.style.top = (avatarBtn.getBoundingClientRect().bottom + window.scrollY + 8) + 'px';
            box.style.zIndex = 9999;
            box.style.boxShadow = '0 6px 18px rgba(2,6,23,0.08)';
            box.style.borderRadius = '10px';
            box.style.background = '#fff';
            box.style.border = '1px solid #e6e9ef';
            box.innerHTML = document.getElementById('aurora_avatar_dropdown').innerHTML;
            document.body.appendChild(box);

            // attach logout handler
            var logoutLink = box.querySelector('#aurora_profile_logout');
            if (logoutLink) {
                logoutLink.addEventListener('click', function(ev){
                    ev.preventDefault();
                    jQuery.post(ENERG_AJAX.ajax_url, { action: 'energ_logout', nonce: ENERG_AJAX.nonce }, function(res){
                        if (res && res.success) window.location.href = "<?php echo site_url('/login'); ?>";
                    });
                });
            }
        });
    }

    // close dropdown when clicking outside
    document.addEventListener('click', function(e){
        var ex = document.getElementById('aurora_dropdown_box');
        if (!ex) return;
        if (e.target.closest('#aurora_dropdown_box') || e.target.closest('#aurora_avatar_btn')) return;
        ex.parentNode.removeChild(ex);
    });

});
</script>

<script>
jQuery(function($){
    // Logout from fixed button (if you keep a logout button)
    $("#energ-logout").on("click", function(){
        $.post(ENERG_AJAX.ajax_url, { action: "energ_logout", nonce: ENERG_AJAX.nonce }, function(res){
            if (res && res.success) window.location.href = "<?php echo site_url('/login'); ?>";
        });
    });

    function loadSubTerms(taxonomy, parentId, $targetSelect, placeholderText) {
        placeholderText = placeholderText || 'Select';
        $targetSelect.html('<option value="">' + 'Loading…' + '</option>');
        $.post(ENERG_AJAX.ajax_url, { action: 'energ_get_sub_terms', nonce: ENERG_AJAX.nonce, taxonomy: taxonomy, parent: parentId }, function(res){
            if (res && res.success) {
                var html = '<option value="">' + placeholderText + '</option>';
                res.data.forEach(function(t){ html += '<option value="'+t.term_id+'">'+t.name+'</option>'; });
                $targetSelect.html(html);
            } else {
                $targetSelect.html('<option value="">'+placeholderText+'</option>');
            }
        }, 'json').fail(function(){ $targetSelect.html('<option value="">'+placeholderText+'</option>'); });
    }

    $('#acc_community_personal').on('change', function(){ loadSubTerms('sector', $(this).val(), $('#acc_sub_community_personal'), 'Select Sub Community'); });
    $('#acc_community_work').on('change', function(){ loadSubTerms('sector', $(this).val(), $('#acc_sub_community_work'), 'Select Sub Community'); });
    $('#acc_industry_work').on('change', function(){ loadSubTerms('industry', $(this).val(), $('#acc_sub_industry_work'), 'Select Sub Industry'); });

    $('#acc_save_personal').on('click', function(e){
        e.preventDefault();
        var payload = {
            action: 'energ_save_account',
            nonce: ENERG_AJAX.nonce,
            member_id: "<?php echo (int)$member->id; ?>",
            first_name: $('#acc_firstname').val(),
            last_name: $('#acc_lastname').val(),
            email: $('#acc_email').val(),
            phone: $('#acc_phone').val(),
            salutation: $('#acc_salutation').val(),
            country: $('#acc_country').val(),
            community: $('#acc_community_personal').val() || '',
            sub_community: $('#acc_sub_community_personal').val() || ''
        };
        $.post(ENERG_AJAX.ajax_url, payload, function(res){
            if (res && res.success) { alert('Personal info saved.'); location.reload(); }
            else alert('Save failed: ' + (res.data || 'unknown'));
        }, 'json').fail(function(xhr){ alert('Request failed: ' + xhr.status); });
    });

    $('#acc_save_work').on('click', function(e){
        e.preventDefault();
        var payload = {
            action: 'energ_save_account',
            nonce: ENERG_AJAX.nonce,
            member_id: "<?php echo (int)$member->id; ?>",
            organization: $('#acc_organization').val(),
            designation: $('#acc_designation').val(),
            industry: $('#acc_industry_work').val() || '',
            sub_industry: $('#acc_sub_industry_work').val() || '',
            community: $('#acc_community_work').val() || '',
            sub_community: $('#acc_sub_community_work').val() || ''
        };
        $.post(ENERG_AJAX.ajax_url, payload, function(res){
            if (res && res.success) { alert('Work info saved.'); location.reload(); }
            else alert('Save failed: ' + (res.data || 'unknown'));
        }, 'json').fail(function(xhr){ alert('Request failed: ' + xhr.status); });
    });

    // Quick search behaviour (client-side demo)
    $('#aurora_search_input').on('keypress', function(e){
        if (e.which === 13) {
            var q = $(this).val();
            if (!q) return;
            window.location.href = "<?php echo site_url('/search'); ?>?q=" + encodeURIComponent(q);
        }
    });

});
</script>

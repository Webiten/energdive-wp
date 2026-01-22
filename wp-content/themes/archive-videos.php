<?php
/**
 * Archive template for CPT: videos
 * Features:
 * - Premium UI grid
 * - YouTube thumbnail fallback (meta key: youtube_url)
 * - Quick preview modal (YouTube embed)
 * - Social share buttons (X, LinkedIn, Facebook, WhatsApp, Email)
 */

get_header();

/**
 * Extract YouTube ID from common URL formats.
 */
function energ_videos_get_youtube_id($url) {
  if (!$url) return '';

  $url = trim($url);
  $parts = wp_parse_url($url);
  if (empty($parts['host'])) return '';

  $host = strtolower($parts['host']);

  // youtu.be/VIDEO_ID
  if (strpos($host, 'youtu.be') !== false) {
    $path = isset($parts['path']) ? trim($parts['path'], '/') : '';
    return preg_match('/^[a-zA-Z0-9_-]{6,15}$/', $path) ? $path : '';
  }

  // youtube.com/watch?v=VIDEO_ID
  if (!empty($parts['query'])) {
    parse_str($parts['query'], $q);
    if (!empty($q['v']) && preg_match('/^[a-zA-Z0-9_-]{6,15}$/', $q['v'])) return $q['v'];
  }

  // youtube.com/embed/VIDEO_ID
  if (!empty($parts['path']) && strpos($parts['path'], '/embed/') !== false) {
    $id = str_replace('/embed/', '', $parts['path']);
    $id = trim($id, '/');
    return preg_match('/^[a-zA-Z0-9_-]{6,15}$/', $id) ? $id : '';
  }

  return '';
}

/**
 * Get best thumbnail: Featured image > YouTube thumb (from youtube_url meta) > placeholder.
 */
function energ_videos_get_card_thumb_url($post_id) {
  if (has_post_thumbnail($post_id)) {
    $img = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'large');
    if (!empty($img[0])) return $img[0];
  }

  $yt = get_post_meta($post_id, 'youtube_url', true);
  $id = energ_videos_get_youtube_id($yt);
  if ($id) {
    // High quality default
    return "https://img.youtube.com/vi/{$id}/hqdefault.jpg";
  }

  // Lightweight inline SVG placeholder
  $svg = rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="675"><rect width="100%" height="100%" fill="#0b1220"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#7aa2ff" font-family="Arial" font-size="48">Videos</text></svg>');
  return "data:image/svg+xml;charset=utf-8,{$svg}";
}

/**
 * Build share URLs.
 */
function energ_share_links($permalink, $title) {
  $u = rawurlencode($permalink);
  $t = rawurlencode($title);

  return [
    'x'        => "https://twitter.com/intent/tweet?url={$u}&text={$t}",
    'linkedin' => "https://www.linkedin.com/sharing/share-offsite/?url={$u}",
    'facebook' => "https://www.facebook.com/sharer/sharer.php?u={$u}",
    'whatsapp' => "https://api.whatsapp.com/send?text={$t}%20{$u}",
    'email'    => "mailto:?subject={$t}&body={$u}",
  ];
}

$archive_title = post_type_archive_title('', false);
$archive_desc  = get_the_archive_description();
?>

<style>
  :root{
    --bg: #fff;
    --panel: rgba(0,0,0,.05);
    --panel2: rgba(0,0,0,.08);
    --stroke: rgba(0,0,0,.10);
    --stroke2: rgba(0,0,0,.16);
    --text: rgba(0,0,0,.92);
    --muted: rgba(0,0,0,.68);
    --muted2: rgba(0,0,0,.52);
    --brand: #7aa2ff;
    --brand2: #9f7aff;
    --shadow: 0 20px 20px rgba(0,0,0,.25);
    --radius: 10px;
  }

  .energ-videos-wrap{
    background:#fff;
    color: var(--text);
    min-height: 70vh;
    padding: clamp(18px, 3vw, 34px) 0;
  }

  .energ-container{
    width: min(1180px, calc(100% - 32px));
    margin: 0 auto;
  }

  .energ-hero{
    border: 1px solid var(--stroke);
    background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.03));
    border-radius: calc(var(--radius) + 6px);
    padding: clamp(18px, 3vw, 28px);
    box-shadow: var(--shadow);
    overflow: hidden;
    position: relative;
  }

  .energ-hero:before{
    content:"";
    position:absolute;
    inset:-2px;
    background: radial-gradient(600px 220px at 25% 0%, rgba(122,162,255,.18), transparent 60%),
                radial-gradient(520px 240px at 80% 0%, rgba(159,122,255,.14), transparent 60%);
    pointer-events:none;
    opacity:.8;
  }

  .energ-hero > *{ position: relative; }

  .energ-title{
    font-size: clamp(22px, 2.2vw, 34px);
    line-height: 1.1;
    margin: 0 0 10px;
    letter-spacing: -0.02em;
  }

  .energ-sub{
    color: var(--muted);
    margin: 0 0 18px;
    max-width: 72ch;
  }

  .energ-toolbar{
    display:flex;
    gap: 12px;
    flex-wrap: wrap;
    align-items:center;
    justify-content: space-between;
    border-top: 1px solid var(--stroke);
    margin-top: 14px;
    padding-top: 14px;
  }

  .energ-search{
    display:flex;
    gap: 10px;
    flex: 1 1 420px;
    max-width: 720px;
  }

  .energ-search input[type="search"]{
    flex:1;
    padding: 12px 14px;
    border-radius: 999px;
    border: 1px solid var(--stroke);
    background: rgba(255,255,255,.04);
    color: var(--text);
    outline: none;
  }

  .energ-search input[type="search"]::placeholder{ color: var(--muted2); }

  .energ-btn{
    padding: 12px 14px;
    border-radius: 999px;
    border: 1px solid var(--stroke2);
    background: linear-gradient(180deg, rgba(122,162,255,.20), rgba(122,162,255,.12));
    color: var(--text);
    cursor: pointer;
    transition: transform .15s ease, background .15s ease, border-color .15s ease;
    white-space: nowrap;
  }
  .energ-btn:hover{ transform: translateY(-1px); border-color: rgba(122,162,255,.35); }

  .energ-count{
    color: var(--muted);
    font-size: 14px;
    flex: 0 0 auto;
  }

  .energ-grid{
    margin-top: 18px;
    display:grid;
    grid-template-columns: repeat(12, 1fr);
    gap: 40px;
  }

  .energ-card{
    grid-column: span 4;
    border: 1px solid var(--stroke);
    background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.03));
    border-radius: var(--radius);
    overflow:hidden;
    box-shadow: 0 10px 15px rgba(0,0,0,0.15);
    transition: transform .18s ease, border-color .18s ease;
    position: relative;
    margin-bottom:30px;
  }
  .energ-card:hover{ transform: translateY(-4px); border-color: rgba(122,162,255,.28); }

  @media (max-width: 980px){
    .energ-card{ grid-column: span 6; }
  }
  @media (max-width: 640px){
    .energ-card{ grid-column: span 12; }
  }

  .energ-thumb{
    position: relative;
    aspect-ratio: 16/9;
    background: rgba(255,255,255,.03);
    overflow: hidden;
  }

  .energ-thumb img{
    width:100%;
    height:100%;
    object-fit: cover;
    display:block;
    transform: scale(1.02);
    filter: saturate(1.05) contrast(1.05);
  }

  .energ-play{
    position:absolute;
    inset:0;
    display:flex;
    align-items:center;
    justify-content:center;
    background: linear-gradient(180deg, rgba(0,0,0,.10), rgba(0,0,0,.35));
    opacity: 1;
  }

  .energ-play .bubble{
    width: 64px;
    height: 64px;
    border-radius: 999px;
    border: 1px solid rgba(255,255,255,.22);
    background: rgba(0,0,0,.35);
    backdrop-filter: blur(8px);
    display:flex;
    align-items:center;
    justify-content:center;
    box-shadow: 0 10px 25px rgba(0,0,0,.45);
  }
  .energ-play .triangle{
    width: 0; height: 0;
    border-left: 16px solid rgba(255,255,255,.90);
    border-top: 10px solid transparent;
    border-bottom: 10px solid transparent;
    margin-left: 4px;
  }

  .energ-card-body{
    padding: 14px 14px 12px;
  }

  .energ-card-title{
    margin: 0 0 6px;
    font-size: 16px;
    line-height: 1.25;
    letter-spacing: -0.01em;
  }

  .energ-card-title a{
    color: var(--text);
    text-decoration:none;
  }
  .energ-card-title a:hover{
    text-decoration: underline;
    text-decoration-thickness: 2px;
    text-underline-offset: 3px;
  }

  .energ-meta{
    display:flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items:center;
    color: var(--muted2);
    font-size: 13px;
    margin-bottom: 10px;
  }
  .energ-dot{
    width:4px; height:4px;
    border-radius: 999px;
    background: rgba(255,255,255,.25);
    display:inline-block;
    transform: translateY(-1px);
  }

  .energ-excerpt{
    color: var(--muted);
    font-size: 14px;
    line-height: 1.55;
    margin: 0 0 12px;
  }

  .energ-actions{
    display:flex;
    align-items:center;
    justify-content: space-between;
    gap: 10px;
    border-top: 1px solid var(--stroke);
    padding-top: 10px;
  }

  .energ-open{
    display:inline-flex;
    align-items:center;
    gap: 8px;
    padding: 8px 12px;
    border-radius: 999px;
    border: 1px solid var(--stroke2);
    background: rgba(255,255,255,.04);
    color: var(--text);
    text-decoration:none;
    font-size: 13px;
    transition: transform .15s ease, border-color .15s ease;
  }
  .energ-open:hover{ transform: translateY(-1px); border-color: rgba(122,162,255,.35); }

  .energ-share{
    display:flex;
    gap: 8px;
    align-items:center;
  }

  .energ-share a{
    width: 34px;
    height: 34px;
    border-radius: 999px;
    border: 1px solid var(--stroke2);
    background: rgba(255,255,255,.04);
    display:inline-flex;
    align-items:center;
    justify-content:center;
    text-decoration:none;
    color: var(--text);
    font-size: 12px;
    transition: transform .15s ease, border-color .15s ease;
  }
  .energ-share a:hover{ transform: translateY(-1px); border-color: rgba(122,162,255,.35); }

  /* Pagination */
  /* ==============================
   ENERG PAGINATION – REFINED
============================== */

/* ==============================
   ENERG PAGINATION – FINAL
============================== */

.energ-pagination {
    margin: 32px 0 0;
    display: flex;
    justify-content: center;
}

/* wrapper */
.energ-pagination .page-numbers {
    display: inline-flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    padding: 12px;
    border-radius: 10px;
    color:#000;
}

/* links + spans */
.energ-pagination .page-numbers a,
.energ-pagination .page-numbers span {
    min-width: 40px;
    

    display: inline-flex;
    align-items: center;
    justify-content: center;

    padding: 0 14px;
    border-radius: 10px;

    
    background: #fff;

    color: #333;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;

    transition: 
        background-color .2s ease,
        border-color .2s ease,
        color .2s ease,
        box-shadow .2s ease;
}

/* hover */
.energ-pagination .page-numbers a:hover {
    
}

/* active page */
.energ-pagination .page-numbers.current {
    background: #000000;
    color: #fff;
    box-shadow: 0 6px 18px rgba(11,28,45,0.25);
}

/* prev / next */
.energ-pagination .page-numbers.prev,
.energ-pagination .page-numbers.next {
    padding: 0 18px;
    font-weight: 600;
}



/* ==============================
   RESPONSIVE
============================== */

@media (max-width: 576px) {
    .energ-pagination {
        margin-top: 24px;
    }

    .energ-pagination .page-numbers {
        gap: 6px;
    }

    .energ-pagination .page-numbers a,
    .energ-pagination .page-numbers span {
        min-width: 36px;
        height: 36px;
        font-size: 13px;
    }
}


/* ==============================
   RESPONSIVE
============================== */

@media (max-width: 576px) {
    .energ-pagination {
        margin-top: 24px;
    }

    .energ-pagination .page-numbers {
        gap: 6px;
    }

    .energ-pagination .page-numbers a,
    .energ-pagination .page-numbers span {
        min-width: 36px;
        height: 36px;
        font-size: 13px;
    }
}


  /* Modal */
  .energ-modal{
    position: fixed;
    inset: 0;
    display:none;
    align-items:center;
    justify-content:center;
    background: rgba(0,0,0,.72);
    z-index: 9999;
    padding: 18px;
  }
  .energ-modal[aria-hidden="false"]{ display:flex; }

  .energ-modal-panel{
    width: min(980px, 100%);
    border-radius: calc(var(--radius) + 8px);
    background: #060a14;
    border: 1px solid rgba(255,255,255,.14);
    box-shadow: var(--shadow);
    overflow:hidden;
  }

  .energ-modal-top{
    display:flex;
    align-items:center;
    justify-content: space-between;
    gap: 10px;
    padding: 12px 14px;
    border-bottom: 1px solid rgba(255,255,255,.10);
    color: var(--muted);
    font-size: 13px;
  }

  .energ-modal-close{
    border: 1px solid rgba(255,255,255,.18);
    background: rgba(255,255,255,.04);
    color: var(--text);
    border-radius: 999px;
    padding: 8px 12px;
    cursor:pointer;
  }

  .energ-modal-iframe{
    aspect-ratio: 16/9;
    width: 100%;
    background: #000;
  }
  .energ-modal-iframe iframe{
    width:100%;
    height:100%;
    border:0;
    display:block;
  }
</style>

<section class="energ-videos-wrap">
  <div class="energ-container">

    <!--<header class="energ-hero">-->
    <!--  <h1 class="energ-title"><?php echo esc_html($archive_title ?: 'Videos'); ?></h1>-->

    <!--  <?php if (!empty($archive_desc)) : ?>-->
    <!--    <div class="energ-sub"><?php echo wp_kses_post($archive_desc); ?></div>-->
    <!--  <?php else : ?>-->
    <!--    <p class="energ-sub">Browse the latest videos with quick previews, descriptions, and one-click sharing.</p>-->
    <!--  <?php endif; ?>-->

    <!--  <div class="energ-toolbar">-->
    <!--    <form class="energ-search" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">-->
    <!--      <input type="hidden" name="post_type" value="videos" />-->
    <!--      <input-->
    <!--        type="search"-->
    <!--        name="s"-->
    <!--        value="<?php echo isset($_GET['s']) ? esc_attr(wp_unslash($_GET['s'])) : ''; ?>"-->
    <!--        placeholder="Search videos…"-->
    <!--        aria-label="Search videos"-->
    <!--      />-->
    <!--      <button class="energ-btn" type="submit">Search</button>-->
    <!--    </form>-->

    <!--    <div class="energ-count">-->
    <!--      <?php-->
    <!--        global $wp_query;-->
    <!--        $count = isset($wp_query->found_posts) ? (int) $wp_query->found_posts : 0;-->
    <!--        echo esc_html($count . ' video' . ($count === 1 ? '' : 's'));-->
    <!--      ?>-->
    <!--    </div>-->
    <!--  </div>-->
    <!--</header>-->

    <?php if (have_posts()) : ?>
      <div class="energ-grid" aria-live="polite">
        <?php while (have_posts()) : the_post();
          $id         = get_the_ID();
          $title      = get_the_title();
          $permalink  = get_permalink();
          $thumb      = energ_videos_get_card_thumb_url($id);
          $yt_url     = get_post_meta($id, 'youtube_url', true);
          $yt_id      = energ_videos_get_youtube_id($yt_url);
          $share      = energ_share_links($permalink, $title);

          $excerpt = get_the_excerpt();
          if (!$excerpt) {
            $excerpt = wp_trim_words(wp_strip_all_tags(get_the_content(null, false, $id)),15);
          }
        ?>
          <article class="energ-card">
            <div class="energ-thumb">
              <a
                href="<?php echo esc_url($permalink); ?>"
                class="energ-thumb-link"
                <?php if ($yt_id) : ?>
                  data-yt="<?php echo esc_attr($yt_id); ?>"
                  data-title="<?php echo esc_attr($title); ?>"
                <?php endif; ?>
                aria-label="<?php echo esc_attr('Open ' . $title); ?>"
              >
                <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" />
                <div class="energ-play" aria-hidden="true">
                  <div class="bubble"><div class="triangle"></div></div>
                </div>
              </a>
            </div>

            <div class="energ-card-body">
              <h2 class="energ-card-title">
                <a href="<?php echo esc_url($permalink); ?>">
                  <?php echo esc_html($title); ?>
                </a>
              </h2>

              <div class="energ-meta">
                <span><?php echo esc_html(get_the_date()); ?></span>
                <span class="energ-dot"></span>
                <span><?php echo esc_html(get_the_author()); ?></span>
                <?php if ($yt_id) : ?>
                  <span class="energ-dot"></span>
                  <span>Preview available</span>
                <?php endif; ?>
              </div>

              <p class="energ-excerpt"><?php echo esc_html($excerpt); ?></p>

              <div class="energ-actions">
                <a class="energ-open" href="<?php echo esc_url($permalink); ?>">View details</a>

                <div class="energ-share" aria-label="Share">
                  <a href="<?php echo esc_url($share['linkedin']); ?>" target="_blank" rel="noopener" title="Share on LinkedIn">in</a>
                  <a href="<?php echo esc_url($share['x']); ?>" target="_blank" rel="noopener" title="Share on X">x</a>
                  <a href="<?php echo esc_url($share['facebook']); ?>" target="_blank" rel="noopener" title="Share on Facebook">f</a>
                  <a href="<?php echo esc_url($share['whatsapp']); ?>" target="_blank" rel="noopener" title="Share on WhatsApp">wa</a>
                  <a href="<?php echo esc_url($share['email']); ?>" title="Share via Email">@</a>
                </div>
              </div>
            </div>
          </article>
        <?php endwhile; ?>
      </div>

      <div class="energ-pagination">
        <?php
          echo paginate_links([
            'type'      => 'plain',
            'prev_text' => '← Prev',
            'next_text' => 'Next →',
          ]);
        ?>
      </div>

    <?php else : ?>
      <div class="energ-container" style="margin-top:18px;">
        <p style="color: rgba(255,255,255,.7);">No videos found.</p>
      </div>
    <?php endif; ?>

  </div>
</section>

<!-- Preview Modal -->
<div class="energ-modal" id="energVideoModal" aria-hidden="true" role="dialog" aria-label="Video preview">
  <div class="energ-modal-panel">
    <div class="energ-modal-top">
      <div id="energModalTitle">Preview</div>
      <button class="energ-modal-close" type="button" id="energModalClose" aria-label="Close preview">Close</button>
    </div>
    <div class="energ-modal-iframe" id="energModalFrame"></div>
  </div>
</div>

<script>
  (function(){
    const modal = document.getElementById('energVideoModal');
    const frame = document.getElementById('energModalFrame');
    const title = document.getElementById('energModalTitle');
    const closeBtn = document.getElementById('energModalClose');

    function openModal(ytId, videoTitle){
      const src = "https://www.youtube.com/embed/" + encodeURIComponent(ytId) + "?autoplay=1&rel=0";
      title.textContent = videoTitle || "Preview";
      frame.innerHTML = '<iframe src="'+src+'" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>';
      modal.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    }

    function closeModal(){
      modal.setAttribute('aria-hidden', 'true');
      frame.innerHTML = '';
      document.body.style.overflow = '';
    }

    document.addEventListener('click', function(e){
      const a = e.target.closest('.energ-thumb-link');
      if (!a) return;

      const yt = a.getAttribute('data-yt');
      if (!yt) return; // If no YouTube ID, allow normal navigation

      e.preventDefault();
      openModal(yt, a.getAttribute('data-title') || 'Preview');
    });

    closeBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', function(e){
      if (e.target === modal) closeModal();
    });

    document.addEventListener('keydown', function(e){
      if (e.key === 'Escape' && modal.getAttribute('aria-hidden') === 'false') closeModal();
    });
  })();
</script>

<?php get_footer(); ?>

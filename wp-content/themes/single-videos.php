<?php
/**
 * Single template for CPT: videos
 * Features:
 * - Premium UI layout
 * - YouTube embed from meta key: youtube_url
 * - Sticky share bar
 * - Meta row (date, author, reading time)
 * - Taxonomy chips (if assigned)
 * - Prev/Next navigation
 * - "More Videos" section
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
 * Reading time estimate from content length.
 */
function energ_estimated_read_time_minutes($content) {
  $words = str_word_count(wp_strip_all_tags($content));
  $wpm = 200; // typical reading speed
  $mins = max(1, (int) ceil($words / $wpm));
  return $mins;
}

/**
 * Share URLs.
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

  .energ-single-wrap{
    background:#fff;
    color: var(--text);
    padding: clamp(18px, 3vw, 34px) 0;
  }

  .energ-container{
    width: min(1180px, calc(100% - 32px));
    margin: 0 auto;
  }

  .energ-breadcrumbs{
    display:flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items:center;
    color: var(--muted2);
    font-size: 13px;
    margin-bottom: 12px;
  }
  .energ-breadcrumbs a{ color: var(--muted); text-decoration:none; }
  .energ-breadcrumbs a:hover{ text-decoration: underline; }
  .energ-crumb-dot{
    width:4px;height:4px;border-radius:999px;background: rgba(0,0,0,.25);display:inline-block;
  }

  .energ-hero{
    /*border: 1px solid var(--stroke);
    background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.03));
    border-radius: calc(var(--radius) + 6px);*/
    padding: clamp(18px, 3vw, 28px);
    /*box-shadow: var(--shadow);*/
    overflow:hidden;
    position: relative;
  }

  /*.energ-hero:before{
    content:"";
    position:absolute;
    inset:-2px;
    background: radial-gradient(640px 240px at 22% 0%, rgba(122,162,255,.18), transparent 60%),
                radial-gradient(520px 240px at 82% 0%, rgba(159,122,255,.14), transparent 60%);
    pointer-events:none;
    opacity:.85;
  }*/

  .energ-hero > *{ position: relative; }

  .energ-title{
    font-size: clamp(22px, 2.4vw, 38px);
    line-height: 1.08;
    margin: 0 0 10px;
    letter-spacing: -0.02em;
  }

  .energ-meta{
    display:flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items:center;
    color: var(--muted);
    font-size: 14px;
    margin: 0 0 10px;
  }
  .energ-dot{
    width:4px;height:4px;border-radius:999px;background: rgba(255,255,255,.25);display:inline-block;
    transform: translateY(-1px);
  }

  .energ-chip-row{
    display:flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items:center;
    margin-top: 12px;
  }
  .energ-chip{
    display:inline-flex;
    align-items:center;
    gap: 8px;
    padding: 8px 12px;
    border-radius: 999px;
    border: 1px solid var(--stroke);
    background: rgba(255,255,255,.04);
    color: var(--muted);
    text-decoration:none;
    font-size: 13px;
  }
  .energ-chip:hover{ border-color: rgba(122,162,255,.30); color: var(--text); }

  .energ-layout{
    display:grid;
    grid-template-columns: 1fr 320px;
    gap: 35px;
    margin-top: 16px;
    align-items:start;
  }
  @media (max-width: 980px){
    .energ-layout{ grid-template-columns: 1fr; }
  }

  .energ-main{
    border: 1px solid var(--stroke);
    background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.03));
    border-radius: var(--radius);
    overflow:hidden;
    box-shadow: 0 10px 10px rgba(0,0,0,.10);
  }

  .energ-player{
    position: relative;
    aspect-ratio: 16/9;
    background: #000;
  }
  .energ-player iframe{
    width:100%;
    height:100%;
    border:0;
    display:block;
  }

  .energ-content{
    padding: 16px 16px 18px;
  }

  .energ-content p{ color: rgba(0,0,0,.82); line-height: 1.8; }
  .energ-content h2, .energ-content h3{ color: var(--text); }
  .energ-content a{ color: var(--brand); }

  .energ-side{
    position: sticky;
    top: 18px;
    border: 1px solid var(--stroke);
    background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.03));
    border-radius: var(--radius);
    overflow:hidden;
    box-shadow: 0 10px 10px rgba(0,0,0,.10);
  }
  @media (max-width: 980px){
    .energ-side{ position: relative; top: auto; }
  }

  .energ-side-head{
    padding: 14px 14px 10px;
    border-bottom: 1px solid var(--stroke);
  }

  .energ-side-title{
    margin: 0;
    font-size: 14px;
    letter-spacing: .02em;
    color: var(--muted);
    text-transform: uppercase;
  }

  .energ-share{
    padding: 12px 14px 14px;
    display:grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
  }

  .energ-share a{
    display:flex;
    align-items:center;
    justify-content:center;
    gap: 8px;
    padding: 10px 12px;
    border-radius: 14px;
    border: 1px solid var(--stroke2);
    background: rgba(255,255,255,.04);
    color: var(--text);
    text-decoration:none;
    font-size: 13px;
    transition: transform .15s ease, border-color .15s ease;
  }
  .energ-share a:hover{
    transform: translateY(-1px);
    border-color: rgba(122,162,255,.35);
  }

  .energ-side-block{
    padding: 14px;
    border-top: 1px solid var(--stroke);
  }

  .energ-kv{
    display:flex;
    justify-content: space-between;
    gap: 10px;
    color: var(--muted);
    font-size: 13px;
    padding: 8px 0;
    border-bottom: 1px dashed rgba(255,255,255,.10);
  }
  .energ-kv:last-child{ border-bottom: 0; padding-bottom: 0; }

  .energ-nav{
    margin-top: 16px;
    display:flex;
    gap: 12px;
    flex-wrap: wrap;
  }

  .energ-nav a{
    flex: 1 1 240px;
    border: 1px solid var(--stroke);
    background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.03));
    border-radius: var(--radius);
    padding: 14px;
    text-decoration:none;
    color: var(--text);
    box-shadow: 0 14px 36px rgba(0,0,0,.32);
    transition: transform .15s ease, border-color .15s ease;
  }
  .energ-nav a:hover{ transform: translateY(-2px); border-color: rgba(122,162,255,.28); }

  .energ-nav small{ display:block; color: var(--muted2); margin-bottom: 6px; }
  .energ-nav strong{ display:block; font-weight: 600; line-height: 1.3; }

  .energ-more{
    margin-top: 30px;
    /*border: 1px solid var(--stroke);
    background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.03));
    border-radius: var(--radius);
    padding: 14px;
    box-shadow: 0 14px 36px rgba(0,0,0,.32);*/
  }

  .energ-more h3{
    margin: 0 0 10px;
    color: var(--muted);
    font-size: 14px;
    letter-spacing: .02em;
    text-transform: uppercase;
  }

  .energ-more-grid{
    display:grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
  }
  @media (max-width: 980px){ .energ-more-grid{ grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: 640px){ .energ-more-grid{ grid-template-columns: 1fr; } }

  .energ-mini{
    border: 1px solid var(--stroke);
    background: rgba(255,255,255,.04);
    border-radius: 14px;
    overflow:hidden;
    text-decoration:none;
    color: var(--text);
    transition: transform .15s ease, border-color .15s ease;
        margin-bottom: 20px;
  }
  .energ-mini:hover{ transform: translateY(-2px); border-color: rgba(122,162,255,.28); }

  .energ-mini-thumb{
    aspect-ratio: 16/9;
    background: rgba(255,255,255,.03);
  }
  .energ-mini-thumb img{
    width:100%; height:100%; object-fit: cover; display:block;
  }
  .energ-mini-body{ padding: 10px 10px 12px; }
  .energ-mini-body strong{ display:block; font-size: 14px; line-height: 1.25; }
  .energ-mini-body span{ display:block; margin-top: 6px; color: var(--muted2); font-size: 12px; }
</style>

<section class="energ-single-wrap">
  <div class="energ-container">

    <?php if (have_posts()) : while (have_posts()) : the_post();
      $id        = get_the_ID();
      $title     = get_the_title();
      $permalink = get_permalink();
      $content   = get_the_content();

      $yt_url = get_post_meta($id, 'youtube_url', true);
      $yt_id  = energ_videos_get_youtube_id($yt_url);

      $read_mins = energ_estimated_read_time_minutes($content);
      $share = energ_share_links($permalink, $title);

      // Breadcrumb basics (Home > Videos > Title)
      $archive_url = get_post_type_archive_link('videos');

      // Taxonomy chips (if you use taxonomies)
      // Replace these taxonomy names with yours if needed.
      $tax_names = ['category', 'post_tag']; // add custom tax like 'video_topic'
      $chips = [];
      foreach ($tax_names as $tax) {
        $terms = get_the_terms($id, $tax);
        if (!is_wp_error($terms) && !empty($terms)) {
          foreach ($terms as $t) {
            $chips[] = [
              'name' => $t->name,
              'url'  => get_term_link($t),
            ];
          }
        }
      }

      // Prev/Next (same post type)
      $prev = get_previous_post(true, '', ''); // doesn't restrict to CPT, so use adjacent query below if needed
      $next = get_next_post(true, '', '');

      // Safer: get adjacent post within same post type
      $prev_post = get_adjacent_post(false, '', true, '');
      $next_post = get_adjacent_post(false, '', false, '');

    ?>

      <nav class="energ-breadcrumbs" aria-label="Breadcrumb">
        <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
        <span class="energ-crumb-dot"></span>
        <a href="<?php echo esc_url($archive_url ?: home_url('/videos/')); ?>">Videos</a>
        <span class="energ-crumb-dot"></span>
        <span><?php echo esc_html(wp_trim_words($title, 10)); ?></span>
      </nav>

      <header class="energ-hero">
        <h1 class="energ-title"><?php echo esc_html($title); ?></h1>

        <div class="energ-meta">
          <span><?php echo esc_html(get_the_date()); ?></span>
          <span class="energ-dot"></span>
          <span><?php echo esc_html(get_the_author()); ?></span>
          <?php if ($yt_id) : ?>
            <span class="energ-dot"></span>
            <span><?php echo esc_html('YouTube'); ?></span>
          <?php endif; ?>
        </div>

        <?php if (!empty($chips)) : ?>
          <div class="energ-chip-row" aria-label="Topics">
            <?php foreach (array_slice($chips, 0, 8) as $chip) : ?>
              <a class="energ-chip" href="<?php echo esc_url($chip['url']); ?>">
                <?php echo esc_html($chip['name']); ?>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </header>

      <div class="energ-layout">
        <!-- MAIN -->
        <article class="energ-main">
          <div class="energ-player">
            <?php if ($yt_id) : ?>
              <iframe
                src="<?php echo esc_url('https://www.youtube.com/embed/' . $yt_id . '?rel=0'); ?>"
                title="<?php echo esc_attr($title); ?>"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
              ></iframe>
            <?php else : ?>
              <div style="display:flex;align-items:center;justify-content:center;height:100%;color:rgba(255,255,255,.75);padding:18px;text-align:center;">
                No YouTube URL found. Please add <strong style="margin-left:6px;">youtube_url</strong> meta to this post.
              </div>
            <?php endif; ?>
          </div>

          <div class="energ-content">
            <?php
              // Render post content (description)
              the_content();
            ?>
          </div>
        </article>

        <!-- SIDE -->
        <aside class="energ-side" aria-label="Share and details">
          <div class="energ-side-head">
            <p class="energ-side-title">Share this video</p>
          </div>

          <div class="energ-share">
            <a href="<?php echo esc_url($share['linkedin']); ?>" target="_blank" rel="noopener">LinkedIn</a>
            <a href="<?php echo esc_url($share['x']); ?>" target="_blank" rel="noopener">X</a>
            <a href="<?php echo esc_url($share['facebook']); ?>" target="_blank" rel="noopener">Facebook</a>
            <a href="<?php echo esc_url($share['whatsapp']); ?>" target="_blank" rel="noopener">WhatsApp</a>
            <a href="<?php echo esc_url($share['email']); ?>">Email</a>
            <a href="<?php echo esc_url($permalink); ?>" id="energCopyLink">Copy link</a>
          </div>

          <!--<div class="energ-side-block">-->
          <!--  <div class="energ-kv">-->
          <!--    <span>Published</span>-->
          <!--    <span><?php echo esc_html(get_the_date()); ?></span>-->
          <!--  </div>-->
          <!--  <div class="energ-kv">-->
          <!--    <span>Author</span>-->
          <!--    <span><?php echo esc_html(get_the_author()); ?></span>-->
          <!--  </div>-->
          <!--  <div class="energ-kv">-->
          <!--    <span>Reading time</span>-->
          <!--    <span><?php echo esc_html($read_mins . ' min'); ?></span>-->
          <!--  </div>-->
          <!--  <?php if ($yt_url) : ?>-->
          <!--    <div class="energ-kv">-->
          <!--      <span>YouTube URL</span>-->
          <!--      <span style="max-width:170px; text-align:right; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">-->
          <!--        <a href="<?php echo esc_url($yt_url); ?>" target="_blank" rel="noopener" style="color: var(--brand); text-decoration:none;">-->
          <!--          Open-->
          <!--        </a>-->
          <!--      </span>-->
          <!--    </div>-->
          <!--  <?php endif; ?>-->
          <!--</div>-->
        </aside>
      </div>

      <!-- Prev/Next -->
      <div class="energ-nav" aria-label="Navigation">
        <?php if ($prev_post) : ?>
          <a href="<?php echo esc_url(get_permalink($prev_post->ID)); ?>">
            <small>Previous</small>
            <strong><?php echo esc_html(get_the_title($prev_post->ID)); ?></strong>
          </a>
        <?php endif; ?>

        <?php if ($next_post) : ?>
          <a href="<?php echo esc_url(get_permalink($next_post->ID)); ?>">
            <small>Next</small>
            <strong><?php echo esc_html(get_the_title($next_post->ID)); ?></strong>
          </a>
        <?php endif; ?>
      </div>

      <!-- More Videos -->
      <?php
$more_query = new WP_Query([
  'post_type'      => 'videos',
  'posts_per_page' => 6,
  'post__not_in'   => [get_the_ID()],
  'orderby'        => 'date',
  'order'          => 'DESC',
]);

if ($more_query instanceof WP_Query && $more_query->have_posts()) : ?>
  <section class="energ-more" aria-label="More videos">
    <h3>More videos</h3>
    <div class="energ-more-grid">
      <?php while ($more_query->have_posts()) : $more_query->the_post(); ?>
        <a class="energ-mini" href="<?php the_permalink(); ?>">
          <div class="energ-mini-thumb">
            <?php
              // Thumbnail fallback (Featured > YouTube > Placeholder)
              $pid = get_the_ID();
              $thumb = '';

              if (has_post_thumbnail($pid)) {
                $img = wp_get_attachment_image_src(get_post_thumbnail_id($pid), 'large');
                $thumb = !empty($img[0]) ? $img[0] : '';
              }

              if (!$thumb) {
                $yt = get_post_meta($pid, 'youtube_url', true);
                $yid = energ_videos_get_youtube_id($yt);
                if ($yid) $thumb = "https://img.youtube.com/vi/{$yid}/hqdefault.jpg";
              }

              if (!$thumb) {
                $svg = rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="675"><rect width="100%" height="100%" fill="#0b1220"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#7aa2ff" font-family="Arial" font-size="48">Video</text></svg>');
                $thumb = "data:image/svg+xml;charset=utf-8,{$svg}";
              }
            ?>
            <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy">
          </div>

          <div class="energ-mini-body">
            <strong><?php echo esc_html(get_the_title()); ?></strong>
            <span><?php echo esc_html(get_the_date()); ?></span>
          </div>
        </a>
      <?php endwhile; ?>
    </div>
  </section>
<?php
endif;

wp_reset_postdata();
?>


      <script>
        (function(){
          const btn = document.getElementById('energCopyLink');
          if(!btn) return;

          btn.addEventListener('click', function(e){
            e.preventDefault();
            const url = btn.getAttribute('href');

            if (navigator.clipboard && window.isSecureContext) {
              navigator.clipboard.writeText(url).then(() => {
                btn.textContent = 'Copied';
                setTimeout(() => btn.textContent = 'Copy link', 1400);
              }).catch(() => {
                window.prompt('Copy this link:', url);
              });
            } else {
              window.prompt('Copy this link:', url);
            }
          });
        })();
      </script>

    <?php endwhile; endif; ?>

  </div>
</section>

<?php get_footer(); ?>

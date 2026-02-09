<?php get_header(); ?>

<style>
/* ======== ADDED: TOP NEWS BANNER (LIKE YOUR REFERENCE) ======== */
.news-hero {
    width: 100%;
    height: 320px;
    background: url('https://your-image-url-here.com/banner.jpg') center/cover no-repeat;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.news-hero::before {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.45);
}

.news-hero-content {
    position: relative;
    z-index: 2;
    text-align: center;
    color: white;
    max-width: 700px;
    padding: 0 20px;
}

.news-hero-content h1 {
    font-family: "Playfair Display", serif;
    font-size: 42px;
    margin-bottom: 10px;
    letter-spacing: 1.2px;
}

.news-hero-content p {
    font-family: "Roboto Flex", sans-serif;
    font-size: 16px;
    line-height: 1.6;
}

/* ===== YOUR EXISTING CSS (UNCHANGED) ===== */

* { box-sizing: border-box; }
html, body { overflow-x: hidden; }
a { text-decoration: none !important; }
img { max-width: 100%; height: auto; }

/* PAGE HEADING */
h1.sec-head {
    font-family: "Playfair Display", serif;
    margin: 30px auto;
    padding: 20px 0;
    border-top: 1px solid #dfdfdf;
    border-bottom: 1px solid #dfdfdf;
    width: 100%;
    max-width: 1200px;
    font-size: 26px;
    letter-spacing: 1.6px;
    font-weight: 500;
    text-align: left;
    padding-left: 20px;
    padding-right: 20px;
}

/* TABS WRAPPER */
.community-tabs-section {
    width: 100%;
    max-width: 100%;
    margin: 10px auto 40px auto;
    padding: 0px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}

.community-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 25px;
}

.community-tabs .tablinks {
    background: transparent;
    border: 1px solid #000;
    padding: 8px 18px;
    font-size: 15px;
    cursor: pointer;
    transition: 0.3s ease;
    color: #000;
    border-radius: 4px;
}

.community-tabs .tablinks:hover,
.community-tabs .tablinks.active {
    background: #000;
    color: #fff;
}

.tabcontent { animation: fadeIn 0.3s ease-in; }

/* GRID (Tabs) */
.community-post-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
    width: calc(100% - 280px);
    margin: 0 auto;
}

.community-post-card { background: #fff; transition: 0.3s ease; }
.community-post-card:hover { transform: translateY(-4px); }

.community-post-thumb img {
    width: 100%;
    height: 240px;
    object-fit: cover;
    border-radius: 4px;
}

.community-post-title {
    font-family: "Playfair Display", serif;
    font-size: 20px;
    margin-top: 12px;
    color: #000;
    font-weight: 500;
}

.community-meta {
    font-size: 13px;
    color: #777;
    margin: 5px 0;
    font-family: "Roboto Flex", sans-serif;
}

.community-meta a{
    color: #777;
}

.community-excerpt {
    font-size: 15px;
    color: #444;
    line-height: 1.4em;
}

/* DEFAULT RESULTS GRID */
section.dfr {
    width: 100%;
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
}

.default-results {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
    width: 100%;
}

article.archive-post {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    transition: 0.3s ease;
}

article.archive-post:hover {
    box-shadow: 0px 5px 18px rgba(0,0,0,0.1);
}

article.archive-post img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

h3.sph {
    font-family: "Playfair Display", serif;
    font-size: 18px;
    padding: 12px 15px 0;
    color: #000;
    font-weight: 500;
}

.dmp {
    font-size: 13px;
    color: #555;
    padding: 0 15px 15px;
}
.dmp a{
    color: #000;
}

h3.sector-title {
    padding: 10px 0px 10px 0px;
    border-top: 1px solid;
    border-bottom: 1px solid;
    width: 81px;
    font-size: 25px;
    letter-spacing: 1.23px;
}

/* DEFAULT HEADING */
.default-heading {
    font-family: "Playfair Display", serif;
    margin: 30px auto;
    padding: 20px 0;
    border-top: 1px solid #dfdfdf;
    border-bottom: 1px solid #dfdfdf;
    width: 100%;
    max-width: 1200px;
    font-size: 26px;
    letter-spacing: 1.6px;
    font-weight: 500;
    text-align: left;
    padding-left: 20px;
    padding-right: 20px;
}

/* RESPONSIVE */

/* TABLET */
@media (max-width: 991px) {

    .news-hero { height: 260px; }

    .news-hero-content h1 { font-size: 34px; }

    .community-post-thumb img { height: 200px; }

    .default-results {
        grid-template-columns: repeat(2, 1fr);
    }

    .community-post-grid {
        grid-template-columns: repeat(2, 1fr);
        width: 100%;
        margin: 0;
    }
}

/* MOBILE */
@media (max-width: 600px) {

    .news-hero { height: 220px; }

    .news-hero-content h1 { font-size: 28px; }

    h1.sec-head { font-size: 20px; }

    .community-post-thumb img { height: 180px; }

    section.dfr,
    .community-tabs-section {
        padding: 0 15px;
    }

    .default-results {
        grid-template-columns: 1fr;
    }

    .community-post-grid {
        grid-template-columns: 1fr;
        width: 100%;
        margin: 0;
    }
}
</style>

<!-- ======== NEW BANNER (ADDED ON TOP) ======== -->
<?php 
$term = get_queried_object();
$banner = '';

if (function_exists('get_field') && isset($term->term_id)) {
    $banner = get_field('sector_banner', 'sector_' . $term->term_id);
}
?>

<div class="news-hero" 
<?php if (!empty($banner)) : ?>
style="background:url('<?php echo esc_url($banner); ?>') center/cover no-repeat;"
<?php else : ?>
style="background:#111;"
<?php endif; ?>
>
   <div class="news-hero-content">
      <h1><?php echo esc_html($term->name); ?></h1>
      <p>
        Explore oil & gas intelligence from ENERGDIVE, bringing you insights on policy,
        markets, infrastructure, technology, and developments shaping the sector’s future.
      </p>
   </div>
</div>

<!-- ======== BANNER ENDS ======== -->

<div class="container">

<?php
$current_term = get_queried_object();

if ($current_term && isset($current_term->term_id)) :

    $child_terms = get_terms([
        'taxonomy'   => 'sector',
        'parent'     => $current_term->term_id,
        'hide_empty' => false
    ]);

    if (!empty($child_terms)) :
?>

<?php endif; endif; ?>

</div> <!-- container END -->

<script>
function openSectorTab(evt, slug) {
    document.querySelectorAll(".tabcontent").forEach(t => t.style.display = "none");
    document.querySelectorAll(".tablinks").forEach(b => b.classList.remove("active"));

    document.getElementById(slug).style.display = "block";
    evt.currentTarget.classList.add("active");
}
</script>

<!-- DEFAULT POSTS -->
<section class="dfr">
    <div class="default-results">

        <?php
       $sector_query = new WP_Query([
    'post_type'      => ['news','articles','case-study'],
    'posts_per_page' => 40,
    'meta_query'     => [
        'relation' => 'OR',
        [
            'key'     => 'priority',
            'compare' => 'EXISTS',
            'type'    => 'NUMERIC'
        ],
        [
            'key'     => 'priority',
            'compare' => 'NOT EXISTS'
        ]
    ],
    'orderby' => 'date',
    'order'   => 'DESC',
    'tax_query' => [[
        'taxonomy' => 'sector',
        'field'    => 'slug',
        'terms'    => $current_term->slug
    ]]
]);

        while ($sector_query->have_posts()) : $sector_query->the_post();

      $ctype_terms = get_the_terms(get_the_ID(), 'content_type');
      $ctype_html  = '';
      if (!is_wp_error($ctype_terms) && !empty($ctype_terms)) {
        $links = [];
        foreach ($ctype_terms as $t) {
          $url = get_term_link($t);
          if (!is_wp_error($url)) {
            $links[] = '<a href="' . esc_url($url) . '">' . esc_html($t->name) . '</a>';
          }
        }
        if (!empty($links)) {
          $ctype_html = implode(', ', $links);
        }
      }
        ?>

        <article class="archive-post">
  <a href="<?php the_permalink(); ?>">
    <?php the_post_thumbnail('medium'); ?>
    <h3 class="sph"><?php the_title(); ?></h3>

    <p class="dmp">
      <?php
        $author = function_exists('get_field') ? get_field('select_author') : null;
        if ($author):
      ?>
        <span class="author">
          <a href="<?php echo esc_url(get_permalink($author->ID)); ?>">
            <?php echo esc_html(get_the_title($author->ID)); ?>
          </a>
        </span>
      <?php else: ?>
        <span class="author">By <?php echo esc_html(get_the_author()); ?></span>
      <?php endif; ?>

      <span class="sep"> | </span>
      <span class="date"><?php echo esc_html(get_the_date()); ?></span>

      <?php if (!empty($ctype_html)) : ?>
        <span class="sep"> | </span>
        <span class="ctype"><?php echo $ctype_html; ?></span>
      <?php endif; ?>
    </p>
  </a>
</article>

        <?php endwhile; wp_reset_postdata(); ?>

    </div>
</section>

<section class="dfr">
    <h3 class="sector-title">Videos</h3>
    <div class="default-results">
        <?php
        $sector_query = new WP_Query([
            'post_type' => ['videos'],
            'posts_per_page' => 40,
            'tax_query' => [[
                'taxonomy' => 'sector',
                'field'    => 'slug',
                'terms'    => $current_term->slug
            ]]
        ]);

        while ($sector_query->have_posts()) : $sector_query->the_post();

      $ctype_terms = get_the_terms(get_the_ID(), 'content_type');
      $ctype_html  = '';
      if (!is_wp_error($ctype_terms) && !empty($ctype_terms)) {
        $links = [];
        foreach ($ctype_terms as $t) {
          $url = get_term_link($t);
          if (!is_wp_error($url)) {
            $links[] = '<a href="' . esc_url($url) . '">' . esc_html($t->name) . '</a>';
          }
        }
        if (!empty($links)) {
          $ctype_html = implode(', ', $links);
        }
      }
        ?>

        <article class="archive-post">
  <a href="<?php the_permalink(); ?>">
    <?php the_post_thumbnail('medium'); ?>
    <h3 class="sph"><?php the_title(); ?></h3>

    <p class="dmp">
      <?php
        $author = function_exists('get_field') ? get_field('select_author') : null;
        if ($author):
      ?>
        <span class="author">
          <a href="<?php echo esc_url(get_permalink($author->ID)); ?>">
            <?php echo esc_html(get_the_title($author->ID)); ?>
          </a>
        </span>
      <?php else: ?>
        <span class="author">By <?php echo esc_html(get_the_author()); ?></span>
      <?php endif; ?>

      <span class="sep"> | </span>
      <span class="date"><?php echo esc_html(get_the_date()); ?></span>

      <?php if (!empty($ctype_html)) : ?>
        <span class="sep"> | </span>
        <span class="ctype"><?php echo $ctype_html; ?></span>
      <?php endif; ?>
    </p>
  </a>
</article>

        <?php endwhile; wp_reset_postdata(); ?>

    </div>
</section>

<?php get_footer(); ?>

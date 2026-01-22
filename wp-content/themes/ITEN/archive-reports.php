<?php
/**
 * Template Name: Reports Filter Page
 */

get_header();?>
<style>

@import url('https://fonts.googleapis.com/css2?family=Abhaya+Libre:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap');

   /* ============================================================
   GLOBAL ARTICLE STYLES — CLEAN SPACING & TYPOGRAPHY
============================================================ */
:root {
    --article-max-width: 760px;
    --line: 1.3;
    --space: 15px;
    
    --text-color: #404040;
    --accent: #7b9f35;
    
    
    --theme-color: #7b9f35;
	--theme-color2: #678036;
	--title-color: #080809;
	--title-dark: #000000;
	--body-color: #404040;
	--smoke-color: #F5F5F5;
	--smoke-color2: #EFF3FA;
	--black-color: #000000;
	--black-color2: #080E1C;
	--gray-color: #B5B5B5;
	--white-color: #ffffff;
	--light-color: #bdbdbd;
	--body-bg: #fff;
	--yellow-color: #FFB539;
	--success-color: #28a745;
	--error-color: #dc3545;
	--border-color: #EFEFEF;
	--title-font:  "Playfair Display", serif;
	--body-font: "Abhaya Libre", serif;
	--icon-font: "Font Awesome 6 Pro";
	--main-container: 1280px;
	--container-gutters: 24px;
	--section-space: 40px;
	--section-space-mobile: 30px;
	--section-title-space: 30px;
	--ripple-ani-duration: 5s;
	--black:#0b0b0b;
  --white:#fff;
  --muted:#666;
    --topbar-bg:#0b0b0b;
    --transition-fast:180ms;
    --container-width:1200px;
}

body {
	font-family: var(--body-font);
	font-size: 16px;
	font-weight: 500;
	color: var(--body-color);
	line-height: 26px;
	overflow-x: hidden;
	-webkit-font-smoothing: antialiased;
	background-color: var(--body-bg)
}

iframe {
	border: none;
	width: 100%
}
.container-widdthhh{
            max-width: calc(var(--main-container) + var(--container-gutters));
        padding-left: calc(var(--container-gutters) / 2);
        padding-right: calc(var(--container-gutters) / 2);
        margin:0 auto;
}




/* Main Banner Section */
.breadcrumb-section {
  width: 100%;
  height: 300px;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;

  display: flex;
  justify-content: center;
  align-items: center;

  text-align: center;
  position: relative;
}

/* Dark overlay for readability */
.breadcrumb-section::before {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.3);
}

/* Content */
.breadcrumb-content {
  position: relative;
  color: #fff;
  padding: 0 20px;
}

.breadcrumb-content h1 {
  font-size: 38px;
  margin: 0;
  font-weight: 700;
  font-family:"Playfair Display", serif;
}

.breadcrumb-content p {
  margin-top: 15px;
  font-size: 16px;
  opacity: 0.9;
}


/* ===== REPORT LIST ===== */
.filtered-list {
  display: flex;
  flex-direction: column;
  gap: 32px;
  padding:60px 0;
}

/* ===== CARD ===== */
.news-card {
  display: grid;
  grid-template-columns: 140px 1fr auto;
  gap: 32px;
  align-items: center;

  background: #fff;
  border-radius: 18px;
  padding: 28px 32px;

  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
}

/* ===== IMAGE ===== */
/* ===== IMAGE ===== */
.news-thumb {
  width: 140px;
  aspect-ratio: 3 / 4;   /* Portrait ratio */
  overflow: hidden;
  border-radius: 14px;
}

.news-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;    /* Prevent stretching */
  display: block;
}


/* ===== CONTENT ===== */
.news-content {
  display: flex;
  flex-direction: column;
}

.news-title {
  font-family: "Playfair Display", serif;
  font-size: 24px;
  font-weight: 700;
  line-height: 1.4;
  margin: 0 0 2px;
  color: #000;
  padding: 0;
}

.news-meta {
  font-family: "Abhaya Libre", serif;
  font-size: 15px;
  color: #666;
  padding: 0;
}

.news-text p{
    font-family: "Abhaya Libre", serif;
  font-size: 16px;
  color: #333;
}

/* ===== BUTTON ===== */
.btn-report {
  display: inline-block;
  padding: 4px 24px;
  border-radius: 10px;

  background: #d7dbc8;
  border: 1px solid #a8be73;

  font-size: 14px;
  font-weight: 600;
  text-decoration: none;
  color: #000;

  transition: all 0.25s ease;
}

.btn-report:hover {
  background: #a8be73;
  color: #fff;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
  .news-card {
    grid-template-columns: 1fr;
    text-align: center;
  }

  .news-thumb img {
    margin: 0 auto;
  }

  .news-cta {
    margin-top: 14px;
  }
}


</style>

<section class="breadcrumb-section" 
    style="background-image: url('https://energ.energdive.com/wp-content/uploads/2025/12/reportsbreadcrumb.jpg'); 
           background-size: cover; 
           background-position: center;">
 <div class="breadcrumb-content">
    <h1>Reports</h1>
    <p>Explore in-depth reports featuring data-driven analysis, sector insights, policy reviews, and market intelligence shaping India’s evolving energy landscape.</p>
  </div>
</section>

<section class="container-widdthhh">
    
    


<div class="container">
  <div class="default-results">

    <?php
    $args = [
      'post_type'      => 'reports',
      'posts_per_page' => -1,
      'post_status'    => 'publish'
    ];

    $default = new WP_Query($args);

    if ($default->have_posts()):
      echo '<div class="filtered-list">';

      while ($default->have_posts()): $default->the_post();

        $thumbnail      = get_the_post_thumbnail_url(get_the_ID(), 'medium_large');
        $date           = get_the_date('F j, Y');
        $categories     = get_the_terms(get_the_ID(), 'sector');
        $category_name  = !empty($categories) ? esc_html($categories[0]->name) : '';
    ?>

      <div class="news-card">

        <?php if ($thumbnail): ?>
          <div class="news-thumb">
            <a href="<?php the_permalink(); ?>">
              <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php the_title(); ?>">
            </a>
          </div>
        <?php endif; ?>

        <!-- CONTENT COLUMN -->
        <div class="news-content">

          <h3 class="news-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
          </h3>

          <div class="news-meta">
            <?php
              $author = get_field('select_author');
              if ($author):
            ?>
              <span class="author">
                <a href="<?php echo get_permalink($author->ID); ?>">
                  <?php echo get_the_title($author->ID); ?>
                </a>
              </span>
            <?php else: ?>
              <span class="author">By <?php the_author(); ?></span>
            <?php endif; ?>

            | <span class="meta-date"><?php echo $date; ?></span>

            <?php if ($category_name): ?>
              | <span class="meta-category"><?php echo $category_name; ?></span>
            <?php endif; ?>
          </div>
          
          <div class="news-text">
              <p> <?php echo wp_trim_words(get_the_excerpt(), 25, '...'); ?></p>
          </div>

        </div>

        <!-- CTA COLUMN -->
        <div class="news-cta">
          <a href="<?php the_permalink(); ?>" class="btn-report">View Report</a>
        </div>

      </div>

    <?php
      endwhile;
      echo '</div>';
    else:
      echo '<p>No reports found.</p>';
    endif;

    wp_reset_postdata();
    ?>

  </div>
</div>


</section>

<?php get_footer(); ?>

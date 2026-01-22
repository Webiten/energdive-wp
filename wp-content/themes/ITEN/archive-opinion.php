<?php
/**
 * Template Name: Opinion Filter Page
 */

get_header(); 
?>

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



/* GRID LAYOUT */
.filtered-list {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 25px;
}

/* NEWS CARD */
.news-card {
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

/* FORCE PORTRAIT IMAGES */
.news-thumb {
    width: 100%;
    height: 305px; /* adjust portrait height */
    overflow: hidden;
    
}

.news-thumb img {
    width: 100%;
    height: 305px;
    object-fit: cover;  
    object-position: center;
    aspect-ratio: 3 / 4; /* PERFECT portrait ratio */
}

/* TITLE */
.news-title{
    font-size: 16px;
    font-weight: 700;
    display: block;
    margin: 5px 0 10px;
    line-height: 1.2;
    letter-spacing: 1px;
    padding: 5px 10px 0;
    
}
.news-title a {
    color:#000;
}

/* RESPONSIVE GRID */
@media (max-width: 1024px) {
    .filtered-list {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .filtered-list {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .filtered-list {
        grid-template-columns: 1fr;
    }
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
  background: rgba(0, 0, 0, 0.35);
}

/* Content */
.breadcrumb-content {
  position: relative;
  color: #fff;
  padding: 0 20px;
}

.breadcrumb-content h1 {
  font-size: 42px;
  margin: 0;
  font-weight: 700;
}

.breadcrumb-content p {
  margin-top: 8px;
  font-size: 16px;
  opacity: 0.9;
}

/* ----------------------------- */
/* RESPONSIVE STYLES */
/* ----------------------------- */

/* Tablets */
@media (max-width: 992px) {
  .breadcrumb-section {
    height: 240px;
  }
  .breadcrumb-content h1 {
    font-size: 32px;
  }
  .breadcrumb-content p {
    font-size: 15px;
  }
}

/* Mobile Screens */
@media (max-width: 576px) {
  .breadcrumb-section {
    height: 140px;
  }
  .breadcrumb-content h1 {
    font-size: 26px;
    line-height: 1.3;
  }
  .breadcrumb-content p {
    font-size: 14px;
  }
}


.opinion-section {
  padding: 20px 20px;
  
  display: flex;
  justify-content: center;
}

.opinion-content {
  max-width: 950px;
  text-align: center;
}

/* Main paragraph */
.main-text {
  font-size: 18px;
  line-height: 1.8;
  color: #333;
  margin-bottom: 20px;
}

/* Italic one-liner */
.italic-text {
  font-size: 17px;
  font-style: italic;
  color: #555;
  margin: 0;
  opacity: 0.9;
}

/* Responsive */
@media (max-width: 600px) {
  .main-text {
    font-size: 16px;
    line-height: 1.7;
  }
  .italic-text {
    font-size: 15px;
  }
}


</style>


<section class="breadcrumb-section" 
    style="background-image: url('https://energ.energdive.com/wp-content/uploads/2025/12/contact-bg.jpg'); 
           background-size: cover; 
           background-position: center;">
 
</section>

<section class="container-widdthhh">
    
<div class="opinion-section">
  <div class="opinion-content">
      <h1>Contact Us</h1>
    <p class="main-text">We believe great ideas grow through collaboration. If you wish to partner, advertise, contribute editorially, or require subscription assistance, connect with us—we’re always ready to engage. Together, let’s build an intelligent, sustainable energy future for India.</p>

    
  </div>
</div>

    <!-- DEFAULT POSTS SECTION ONLY -->
    <div class="default-results">

        <?php
        $args = [
            'post_type'      => 'opinion',
            'posts_per_page' => -1,
            'post_status'    => 'publish'
        ];

        $default = new WP_Query($args);

        if ($default->have_posts()):
            echo '<div class="filtered-list">';
            while ($default->have_posts()): $default->the_post();

                $thumbnail      = get_the_post_thumbnail_url(get_the_ID(), 'medium_large');
                $author         = get_the_author();
                $date           = get_the_date('F j, Y');
                $categories     = get_the_terms(get_the_ID(), 'sector');
                $category_name  = !empty($categories) ? esc_html($categories[0]->name) : '';
        ?>

                <article class="news-card">

                    <?php if ($thumbnail): ?>
                        <div class="news-thumb">
                            <a href="<?php the_permalink(); ?>">
                                <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php the_title(); ?>">
                            </a>
                        </div>
                    <?php endif; ?>

                    <h2 class="news-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h2>

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

                </article>

        <?php
            endwhile;
            echo '</div>';
        else:
            echo '<p>No opinions found.</p>';
        endif;

        wp_reset_postdata();
        ?>

    </div>

</div>

</section>

<?php get_footer(); ?>

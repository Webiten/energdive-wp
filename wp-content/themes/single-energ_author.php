<?php get_header(); ?>
<style>
/* Page container margins (Desktop: 100px L/R, 50px top) */
.container{
  width: min(1200px, calc(100% - 200px)); /* 100px left + 100px right */
  margin: 50px auto 40px;                /* top 50px */
}

/* Scale margins down on smaller screens */
@media (max-width: 1024px){
  .container{
    width: min(1100px, calc(100% - 80px)); /* 40px L/R */
    margin-top: 40px;
  }
}
@media (max-width: 600px){
  .container{
    width: calc(100% - 30px);  /* 15px L/R */
    margin-top: 24px;
  }
}

/* AUTHOR HEADER */
.author-header{
  font-family: "Playfair Display", serif;
  margin: 0 0 24px 0;                 /* container controls page margins now */
  border-top: 1px solid #dfdfdf;
  border-bottom: 1px solid #dfdfdf;
  width: 100%;
  font-size: 20px;
  line-height: 1.4;
  letter-spacing: 1.1px;
  font-weight: 400;
  padding: 22px 0;
  display: flex;
  flex-direction: column;
  gap: 18px;
  align-items: center;
  flex-wrap: wrap;
}

.author-header h1{
  margin: 0;
  font-size: clamp(22px, 2.2vw, 34px);
  letter-spacing: 0.6px;
}

.author-header p{
  margin: 8px 0 0 0;
  max-width: 700px;
  color: #444;
  text-align:center;
}

/* Author photo */
.author-photo img{
  width: 150px;
  height: 150px;
  object-fit: cover;
  border-radius: 50%;
}

/* Stack author header nicely on mobile */
@media (max-width: 600px){
  .author-header{
    align-items: flex-start;
  }
  .author-photo img{
    width: 110px;
    height: 110px;
  }
}

/* GRID WRAPPER */
.filtered-list{
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 18px;
}

/* Responsive grid */
@media (max-width: 1024px){
  .filtered-list{ grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 600px){
  .filtered-list{ grid-template-columns: 1fr; }
}

/* CARD */
.content-card{
  background: #fff;
  border: 1px solid #e5e5e5;
  border-radius: 10px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  height: 100%;
}

.content-card:hover{
  transform: translateY(-3px);
  box-shadow: 0 10px 24px rgba(0,0,0,0.10);
}

/* Thumbnail */
.content-thumb{
  width: 100%;
  aspect-ratio: 16 / 9;
  overflow: hidden;
  background: #f3f3f3;
}

.content-thumb img{
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.35s ease;
  display: block;
}

.content-card:hover .content-thumb img{
  transform: scale(1.05);
}

/* Title */
.content-title{
  font-size: 18px;
  font-weight: 500;
  line-height: 1.35;
  letter-spacing: 0.7px;
  padding: 12px 15px 0;
  font-family: "Playfair Display", serif;
  margin: 0;
}

.content-title a{
  color: #000;
  text-decoration: none;
}
.content-title a:hover{
  color: #0073aa;
}

/* Meta */
.content-meta{
  font-size: 13px;
  color: #555;
  padding: 10px 15px 15px;
  margin-top: auto;
}
.content-meta span{ color: #666; }
</style>

<?php 
while ( have_posts() ) : the_post(); 
    $author_id = get_the_ID();

    // Author ACF fields
    $bio   = get_field('author_bio', $author_id);
$short_bio = get_field('author_short_bio', $author_id);
    $photo = get_field('author_photo', $author_id);
?>
<div class="container">

    <!-- AUTHOR DETAILS -->
    <div class="author-header">

        <?php if ($photo): ?>
        <div class="author-photo">
            <img src="<?php echo esc_url($photo['url']); ?>" 
                 alt="<?php the_title(); ?>"
                 style="width:150px; height:150px; object-fit:cover; border-radius:50%;">
        </div>
        <?php endif; ?>

        <h1><?php the_title(); ?></h1>

        <?php if ($short_bio): ?>
            <p style="max-width:700px;"><?php echo wp_kses_post($short_bio); ?></p>
        <?php endif; ?>

    </div>

    <!-- POSTS BY AUTHOR -->
    <div class="default-results">
        <?php
        $args = [
            'post_type'      => ['news', 'articles', 'cover_story', 'reports', 'opinion'],
            'posts_per_page' => 40,
            'post_status'    => 'publish',
            'meta_query'     => [
                [
                    'key'     => 'select_author',
                    'value'   => $author_id,
                    'compare' => '='
                ]
            ]
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

                <article class="content-card">

                    <?php if ($thumbnail): ?>
                        <div class="content-thumb">
                            <a href="<?php the_permalink(); ?>">
                                <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php the_title(); ?>">
                            </a>
                        </div>
                    <?php endif; ?>

                    <h2 class="content-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h2>

                    <div class="content-meta">
                        <span class="meta-date"><?php echo $date; ?></span>

                        <?php if ($category_name): ?>
                            | <span class="meta-category"><?php echo $category_name; ?></span>
                        <?php endif; ?>
                    </div>

                </article>

        <?php
            endwhile;
            echo '</div>';
        else:
            echo '<p>No articles found.</p>';
        endif;

        wp_reset_postdata();
        ?>

    </div>

</div>

<?php endwhile; ?>

<?php get_footer(); ?>

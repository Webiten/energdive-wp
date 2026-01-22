<?php get_header(); ?>

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
	--main-container: 1350px;
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
    height: 350px;
    overflow: hidden;
}

.news-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;  
    object-position: center;
    aspect-ratio: 3 / 4;
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

/* META */
.news-meta {
    padding: 0 10px 10px;
    font-size: 14px;
    color: #555;
}

.news-meta a {
    color:#000;
    text-decoration:none;
}

.news-meta span {
    margin-right: 5px;
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
</style>




<section class="container-widdthhh">



<div class="container">

    <h1 class="head-news">
        <?php single_term_title(); ?>
    </h1>

    <?php if (term_description()): ?>
        <p class="tag-description"><?php echo term_description(); ?></p>
    <?php endif; ?>

    <div class="default-results">

        <?php if (have_posts()): ?>
            <div class="filtered-list">

                <?php while (have_posts()): the_post();

                    $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'medium_large');
                    $date      = get_the_date('F j, Y');

                    // Custom Author
                    $author = get_field('select_author');
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
                            <!-- AUTHOR -->
                            <?php if ($author): ?>
                                <span class="author">
                                    <a href="<?php echo get_permalink($author->ID); ?>">
                                        <?php echo get_the_title($author->ID); ?>
                                    </a>
                                </span>
                            <?php else: ?>
                                <span class="author">By <?php the_author(); ?></span>
                            <?php endif; ?>
                            
                            <!-- DATE -->
                            | <span class="date"><?php echo $date; ?></span>
                        </div>

                    </article>

                <?php endwhile; ?>

            </div>

        <?php else: ?>
            <p>No posts found under this tag.</p>
        <?php endif; ?>

    </div>

</div>



</section>

<?php get_footer(); ?>

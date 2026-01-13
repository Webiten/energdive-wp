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
.cover-title{
    font-family: "Playfair Display", serif;
    font-size: 40px;
    line-height: 1.2;
    margin-top: 90px;
    margin-bottom: 8px;
    font-weight: 500;
    text-align: center;
    color:var(--title-color);
}
/* Body text */
.article-left p,
.article-left li {
    font-size: 22px;
    line-height: var(--line);
    color: var(--body-color);
    margin-bottom: var(--space);
    text-align:left;
    font-family: "Abhaya Libre", serif;
}

/* Headings */
.article-left h2,
.article-heading {
    font-size: 28px;
    margin: 40px 0 20px;
    line-height: 1.3;
    color: var(--title-color);
    font-weight: 700;
    font-family: "Playfair Display", serif;
}

/* Drop Cap for FIRST paragraph */
.article-paragraph:first-of-type p:first-letter {
    float: left;
    font-size: 90px;
    line-height: 0.9;
    padding-right: 12px;
    padding-top: 6px;
    color: #a8be73;
    font-weight: bold;
}
.cover-byline, .from-to, .article-date{
    margin-top:0;
    margin-bottom: 0px;
}
.article-ex p{
    margin-top: 15px;
}
.article-summary {
    font-family: "Abhaya Libre", serif;
}
.article-full-image{
    margin:15px 0;
}
/* ============================================================
   HERO SECTION — CLEAN, RESPONSIVE, MAGAZINE LOOK
============================================================ */
.cover-hero {
    display: flex;
    align-items: center;   /* <-- CENTER LEFT CONTENT VERTICALLY */
    justify-content: space-between;
    gap: 40px;
    margin-bottom: 55px;
    flex-wrap: nowrap;
}

.cover-hero-left {
    flex: 1.6;
}

.article-hero-right {
    flex: 1;
    max-width: 500px;
}

/* PORTRAIT HERO IMAGE */
.article-hero-right img {
    width: 100%;
    height: 570px;
    object-fit: cover;
    object-position: center;
    border-radius: 12px;
    display: block;
}


/* Byline + Issue Text */
.cover-byline,
.from-to,
.article-date {
    font-size: 16px;
    color: #666;
    margin-bottom: 0px;
    margin-top:0;
}

/* Excerpt */
.article-ex p {
    font-size: 19px;
    color: #555;
    margin-top: 16px;
}

/* ============================================================
   TWO-COLUMN LAYOUT
============================================================ */
.article-layout {
    display: flex;
    gap: 50px;
    margin-bottom: 60px;
}

.article-left {
    flex: 3;
    max-width: var(--article-max-width);
    font-family: "Abhaya Libre", serif;
}

.article-right {
    flex: 1;
    position: relative;
}

/* ============================================================
   STICKY SIDEBAR
============================================================ */
.article-right {
    position: sticky;
    top: 90px;
    align-self: flex-start;
}

/* Sidebar box */
.article-author-box {
    background: #fafafa;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 30px;
}

.article-author-box h3 {
    margin-bottom: 10px;
    font-size: 20px;
    color: var(--title-color);
}

/* Sidebar images */
.article-right img {
    width: 100%;
    height: auto;
    border-radius: 12px;
}

/* ============================================================
   FULL-WIDTH IMAGE BLOCK STYLE
============================================================ */
.article-full-image img {
    width: 100%;
    height: auto;
    border-radius: 12px;
    margin: 0;
}

.image-caption {
    font-size: 14.5px!important;
    color: #777;
    text-align: left!important;
    margin-bottom: 25px;
    margin-top:-8px;
    padding-left:5px;
    padding-right:5px;
}

/* ============================================================
   QUOTE BLOCK
============================================================ */
.article-quote {
    padding: 20px 30px;
    border-left: 4px solid #a8be73;
    background: #faf7f3;
    margin: 25px 0;
    border-radius: 8px;
    font-family: "Abhaya Libre", serif;
}

.article-quote p {
    margin-bottom: 8px;
    font-size: 26px;
    font-style: italic;
    color:#333;
}

.article-quote cite {
    font-size: 16px;
    color: #666;
}

/* ============================================================
   RECOMMENDED SECTION — MODERN GRID
============================================================ */
.recommended-wrapper {
    margin-top: 60px;
}

.recommended-title {
    font-size: 30px;
    margin-bottom: 25px;
    font-weight: 700;
    color: var(--title-color);
}

.recommended-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 30px;
}

.recommended-card {
    border-radius: 12px;
    overflow: hidden;
    background: #fff;
    transition: 0.3s;
}

.recommended-card:hover {
    transform: translateY(-4px);
}

.recommended-thumb img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.recommended-heading {
    font-size: 18px;
    font-weight: 600;
    margin: 15px;
}

.recommended-meta {
    font-size: 14px;
    color: #777;
    margin: 0 15px 15px;
}

/* ============================================================
   RESPONSIVE RULES
============================================================ */
@media (max-width: 992px) {

    /* Stack hero */
    .cover-hero {
        flex-direction: column;
        margin: 10px 15px;
        margin-bottom: 10px;
    }
.cover-title{
    margin-top:30px;
}
    .article-hero-right img {
        height: 420px;
    }

    /* Stack columns */
    .article-layout {
        flex-direction: column;
    }

    .article-right {
        position: static;
        width: 100%;
    }
}

@media (max-width: 600px) {

    .cover-title {
        font-size: 26px;
        line-height: 1.3;
    }

    .article-hero-right {
        width: 100% !important;
        max-width: 100% !important;
    }

    .article-hero-right img {
        width: 100% !important;
        height: auto !important;
        border-radius: 10px;
    }

    .recommended-grid {
        grid-template-columns: 1fr;
    }
}


.subscribe-box {
    margin: 20px 0;
    padding: px0;
}
.subscribe-desc {
    margin: 4px 0 13px;
    font-size: 20px;
    width: 100%;
}
.subscribe-form {
    display: flex;
    gap: 10px;
    padding: 0px;
    width: 100%;
    border: 1px solid #c0bfbf;
    
}
.subscribe-note {
    font-size: 12px !important;
    line-height:1.3;
    width: 100%;
}

.subscribe-form button {
    background: #92ae55;
    color: #fff;
    padding: 6px 20px;
    border-radius: 0px;
    border: none;
    cursor: pointer;
}
.subscribe-form input {
    flex: 1;
    border: none;
    padding: 8px;
    font-size: 14px;
    outline: none;
    border-radius:2px;
}



.post-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin: 25px 0;
}

.single-tag {
  padding: 3px 16px;
  border-radius: 6px;
  font-size: 14px;
  background: #f2f2f3;
  border: none;
  color: #000;
  text-decoration: none;
  transition: 0.25s ease;
}

.single-tag:hover {
  background: #a8be73;
  color: #fff;
}


@media (max-width: 576px) {
  .single-tag {
    font-size: 13px;
    padding: 7px 14px;
  }
}

</style>
<section class="container-widdthhh">

<div class="single-cover-story">

    <!-- TOP ARTICLE HERO SECTION -->
    <!-- HERO SECTION SAME AS COVER-STORY -->
    <div class="cover-hero">

        <!-- LEFT TEXT SIDE -->
        <div class="cover-hero-left">

            <h1 class="cover-title"><?php the_title(); ?></h1>

            <?php
            $author = get_field('select_author');

            if ($author):
            ?>

                <div class="cover-byline">
                    <strong>
                        By <a href="<?php echo get_permalink($author->ID); ?>">
                            <?php echo get_the_title($author->ID); ?>
                        </a>
                    </strong>
                </div>

            <?php endif; ?>



            <!-- ISSUE -->
            <?php if (get_field('date_from_when_to_when')): ?>
                <p class="from-to"><?php echo get_field('date_from_when_to_when'); ?></p>
            <?php endif; ?>

            <!-- PUBLISHED DATE -->
            <div class="article-date">
                <em>Published on <?php echo get_the_date('F j, Y'); ?></em>
            </div>

            <!-- ARTICLE EXCERPT -->
            <div class="article-ex">
                <p class="article-summary">
                    
                    <?php echo wp_trim_words( get_the_excerpt(), 40, '' ); ?>

                </p>
            </div>


        </div>

        <!-- RIGHT IMAGE SIDE -->
        <div class="article-hero-right">
            <?php the_post_thumbnail('full'); ?>
        </div>

    </div><!-- article-hero END -->

    <?php
    // ACF Access type fix
    $access_type = get_field('access_type');
    if (is_array($access_type)) $access_type = $access_type[0];

    $has_access  = energ_user_can_access(get_the_ID());
    $price       = get_field("paid_price");
    ?>
    <!-- ======== ACCESS RESTRICTION CHECK ======== -->
    <?php if (!$has_access): ?>

        <div class="article-layout">

            <!-- LEFT -->
            <div class="article-left">

                <div class="article-preview-content">
                    <?php echo wp_trim_words(get_the_content(), 80, '...'); ?>
                </div>

                <div class="paywall-box">

                    <h2 class="pw-title">Finish reading this article</h2>

                    <p class="pw-subtitle">
                        <?php
                        if ($access_type === 'signup_required') echo "Create a free account to unlock the full story.";
                        if ($access_type === 'subscription') echo "This is premium content. Subscribe to continue reading.";
                        if ($access_type === 'paid') echo "Unlock this article instantly and continue reading.";
                        ?>
                    </p>

                    <?php if ($access_type === 'signup_required'): ?>
                        <form class="pw-form">
                            <input type="email" placeholder="name@provider.com" required>
                            <button type="submit" class="pw-btn">Get It Now →</button>
                        </form>
                        <p class="pw-small">Already have an account? <a href="/login">Log In →</a></p>
                    <?php endif; ?>

                    <?php if ($access_type === 'subscription'): ?>
                        <a href="/subscribe" class="pw-btn full">Subscribe Now →</a>
                        <p class="pw-small">Already a subscriber? <a href="/login">Log In →</a></p>
                    <?php endif; ?>

                    <?php if ($access_type === 'paid'): ?>
                        <a href="/buy-article?id=<?php echo get_the_ID(); ?>" class="pw-btn full">
                            Buy Article — ₹<?php echo $price; ?> →
                        </a>
                        <p class="pw-small">Already purchased? <a href="/login">Log In →</a></p>
                    <?php endif; ?>

                </div>

            </div>

            <!-- RIGHT SIDEBAR ALWAYS VISIBLE -->
            <div class="article-right">

                <?php
                $author = get_field('select_author');

                if ($author):
                ?>

                    <div class="article-author-box">
                        <h3>About the Author</h3>

                        <p><?php echo get_field('author_short_bio', $author->ID); ?></p>

                        <a href="<?php echo get_permalink($author->ID); ?>"
                            class="more-link">→ View Full Profile</a>

                    </div>

                <?php endif; ?>


                <?php if (get_field('third_image')): ?>
                    <div class="article-third-image">
                        <img src="<?php echo get_field('third_image')['url']; ?>" alt="">
                    </div>
                <?php endif; ?>

            </div>

        </div><!-- cover-layout END -->

        <!-- RECOMMENDED SECTION -->
        <div class="recommended-wrapper">
    <h2 class="recommended-title">Recommended</h2>

    <div class="recommended-grid">
        <?php
        $related = new WP_Query([
            'post_type'      => 'articles','news',
            'posts_per_page' => 3,
            'post__not_in'   => [get_the_ID()]
        ]);

        while ($related->have_posts()) : $related->the_post(); ?>
        
            <div class="recommended-card">
                <a href="<?php the_permalink(); ?>">
                    
                    <div class="recommended-thumb">
                        <?php the_post_thumbnail('large'); ?>
                    </div>

                    <div class="recommended-content">
                        <h3 class="recommended-heading"><?php the_title(); ?></h3>
                        <p class="recommended-meta">
    <?php
    $author = get_field('select_author');
    if (is_array($author)) {
        $author = $author[0] ?? null;
    }

    if ($author) {
        echo 'By <a href="' . esc_url(get_permalink($author->ID)) . '">' . esc_html(get_the_title($author->ID)) . '</a>';
    } else {
        echo 'By ' . esc_html(get_the_author());
    }

    echo ' | ' . esc_html(get_the_date('F j, Y'));
    ?>
</p>
                    </div>

                </a>
            </div>

        <?php endwhile; wp_reset_postdata(); ?>
    </div>
</div>

    <?php return;
    endif; ?>


    <!-- ===== FULL CONTENT (when unlocked) ===== -->

    <div class="article-layout">

        <!-- LEFT FULL -->
        <div class="article-left">

            <?php
            $block_count = 0;

            if (have_rows('article_builder')):
                while (have_rows('article_builder')): the_row();
                    $block_count++;

                    /* -------------------------
           1. PARAGRAPH BLOCK
        ------------------------- */
                    if (get_row_layout() == 'text_block'): ?>

                        <div class="article-paragraph" <?php echo ($block_count == 1 && get_row_layout() == 'text_block') ? 'first-para' : ''; ?>>
                            <?php echo wpautop(get_sub_field('text_content')); ?>
                        </div>

                    <?php
                    /* -------------------------
           2. IMAGE BLOCK
        ------------------------- */
                    elseif (get_row_layout() == 'image_block'): ?>

                        <div class="article-full-image">
                            <?php $img = get_sub_field('image_block'); ?>
                            <img src="<?php echo $img['url']; ?>" alt="">

                            <?php if (get_sub_field('image_caption')): ?>
                                <p class="image-caption"><?php echo get_sub_field('image_caption'); ?></p>
                            <?php endif; ?>
                        </div>

                    <?php
                    /* -------------------------
           3. HEADING BLOCK
        ------------------------- */
                    elseif (get_row_layout() == 'heading_block'): ?>

                        <h2 class="article-heading">
                            <?php echo get_sub_field('heading_text'); ?>
                        </h2>

                    <?php
                    /* -------------------------
           4. QUOTE BLOCK
        ------------------------- */
                    elseif (get_row_layout() == 'quote_block'): ?>

                        <blockquote class="article-quote">
                            <p><?php echo get_sub_field('quote_text'); ?></p>
                            <cite><?php echo get_sub_field('quote_author'); ?></cite>
                        </blockquote>

                    <?php
                    endif;

                    /* =============================
             AUTO SUBSCRIBE BOX INSERT
             After 3rd block ONLY ONCE
        ============================== */
                    if ($block_count == 1): ?>

                        <!--<div class="subscribe-box">

                            <div class="subscribe-topline"></div>

                            <h3 class="subscribe-title">
                                <?php echo get_field('subscribe_option_qoute_heading'); ?>
                            </h3>

                            <p class="subscribe-desc">
                                <?php echo get_field('subscribe_option_qoute_description'); ?>
                            </p>

                            <form class="subscribe-form">
                                <input type="email" placeholder="Email" required>
                                <button type="submit">Sign Up →</button>
                            </form>

                            <?php if (get_field('note_after_subscribe_form')): ?>
                                <p class="subscribe-note">
                                    <?php echo get_field('note_after_subscribe_form'); ?>
                                </p>
                            <?php endif; ?>

                        </div>-->

                    <?php endif; ?>

                <?php endwhile; ?>
            <?php endif; ?>
            
            <?php $terms = get_the_terms(get_the_ID(), 'tags'); if ($terms && !is_wp_error($terms)) { echo '<div class="post-tags">'; foreach ($terms as $term) { echo '<a href="' . esc_url(get_term_link($term)) . '" class="single-tag">'; echo esc_html($term->name); echo '</a>'; } echo '</div>'; } ?>

        </div>



        <!-- RIGHT SIDEBAR -->
        <div class="article-right">

            <?php
            $author = get_field('select_author');

            if ($author):
            ?>

                <div class="article-author-box">
                    <h3>About the Author</h3>

                    <p><?php echo get_field('author_short_bio', $author->ID); ?></p>

                    <a href="<?php echo get_permalink($author->ID); ?>"
                        class="more-link">→ View Full Profile</a>

                </div>

            <?php endif; ?>

            <?php if (get_field('sidebar_image')): ?>
                <div class="article-third-image">
                    <img src="<?php echo get_field('sidebar_image')['url']; ?>" alt="">
                </div>
            <?php endif; ?>

        </div>

    </div><!-- cover-layout END -->



    <!-- RECOMMENDED -->
    <div class="recommended-wrapper">
    <h2 class="recommended-title">Recommended</h2>

    <div class="recommended-grid">
        <?php
        $related = new WP_Query([
            'post_type'      => 'articles','news',
            'posts_per_page' => 6,
            'post__not_in'   => [get_the_ID()]
        ]);

        while ($related->have_posts()) : $related->the_post(); ?>
        
            <div class="recommended-card">
                <a href="<?php the_permalink(); ?>">
                    
                    <div class="recommended-thumb">
                        <?php the_post_thumbnail('large'); ?>
                    </div>

                    <div class="recommended-content">
                        <h3 class="recommended-heading"><?php the_title(); ?></h3>
                        <p class="recommended-meta">
    <?php
    $author = get_field('select_author');
    if (is_array($author)) {
        $author = $author[0] ?? null;
    }

    if ($author) {
        echo 'By <a href="' . esc_url(get_permalink($author->ID)) . '">' . esc_html(get_the_title($author->ID)) . '</a>';
    } else {
        echo 'By ' . esc_html(get_the_author());
    }

    echo ' | ' . esc_html(get_the_date('F j, Y'));
    ?>
</p>

                    </div>

                </a>
            </div>

        <?php endwhile; wp_reset_postdata(); ?>
    </div>
</div>

</section>

<?php get_footer(); ?>
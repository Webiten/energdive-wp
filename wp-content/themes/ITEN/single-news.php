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
	--main-container: 1180px;
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

</style>

<section class="container-widdthhh">

<div class="single-news-page">
    <!-- TOP ARTICLE HERO SECTION -->
    <!-- HERO SECTION LIKE FOREIGN AFFAIRS -->
<div class="news-hero-split">

   
        
        <!-- TITLE -->
        <h1 class="news-title1"><?php the_title(); ?></h1>

        <!-- SUBTITLE -->
        <p class="news-subtitle">
            <?php echo wp_trim_words( get_the_excerpt(), 40, '' ); ?>

        </p>

        <!-- META -->
        <div class="news-metas">
            <?php
            $author = get_field('select_author');
            if ($author):
            ?>
                <strong class="meta-author">
                    BY <a href="<?php echo get_permalink($author->ID); ?>">
                        <?php echo get_the_title($author->ID); ?>
                    </a>
                </strong>
            <?php else: ?>
                <strong class="meta-author">BY <?php the_author(); ?></strong>
            <?php endif; ?>

            <div class="meta-date">
                <em><?php echo get_the_date('F j, Y'); ?></em>
            </div>
        </div>
        <?php the_post_thumbnail('full'); ?>
    

  
</div>




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

           

        </div><!-- cover-layout END -->

        <!-- RECOMMENDED SECTION -->
        <div class="recommended-wrapper">
    <h2 class="recommended-title">Recommended</h2>

    <div class="recommended-grid">
        <?php
        $related = new WP_Query([
            'post_type'      => 'news',
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
                            BY Team ENERGDIVE | <?php echo get_the_date('F j, Y'); ?>
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
                    if ($block_count == 3): ?>

                        <!--<div class="subscribe-box">-->

                        <!--    <div class="subscribe-topline"></div>-->

                        <!--    <h3 class="subscribe-title">-->
                        <!--        <?php echo get_field('subscribe_option_qoute_heading'); ?>-->
                        <!--    </h3>-->

                        <!--    <p class="subscribe-desc">-->
                        <!--        <?php echo get_field('subscribe_option_qoute_description'); ?>-->
                        <!--    </p>-->

                        <!--    <form class="subscribe-form">-->
                        <!--        <input type="email" placeholder="Email" required>-->
                        <!--        <button type="submit">Sign Up →</button>-->
                        <!--    </form>-->

                        <!--    <?php if (get_field('note_after_subscribe_form')): ?>-->
                        <!--        <p class="subscribe-note">-->
                        <!--            <?php echo get_field('note_after_subscribe_form'); ?>-->
                        <!--        </p>-->
                        <!--    <?php endif; ?>-->

                        <!--</div>-->

                    <?php endif; ?>

                <?php endwhile; ?>
            <?php endif; ?>
            
            <?php $terms = get_the_terms(get_the_ID(), 'tags'); if ($terms && !is_wp_error($terms)) { echo '<div class="post-tags">'; foreach ($terms as $term) { echo '<a href="' . esc_url(get_term_link($term)) . '" class="single-tag">'; echo esc_html($term->name); echo '</a>'; } echo '</div>'; } ?>

        </div>



        

    </div><!-- cover-layout END -->



    <!-- RECOMMENDED -->
    <div class="recommended-wrapper">
    <h2 class="recommended-title">Recommended</h2>

    <div class="recommended-grid">
        <?php
        $related = new WP_Query([
            'post_type'      => 'news',
            'posts_per_page' => 10,
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
                            BY Team ENERGDIVE | <?php echo get_the_date('F j, Y'); ?>
                        </p>
                    </div>

                </a>
            </div>

        <?php endwhile; wp_reset_postdata(); ?>
    </div>
</div>

</section>

<?php get_footer(); ?>
 <?php get_header(); ?>
 
<style>
    
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



<div class="single-news-page">

   <div class="hero-banner"> 
    <!-- TITLE -->
        <h1 class="news-title1"><?php the_title(); ?></h1>

        <!-- SUBTITLE -->
        <p class="news-subtitle">
            <?php echo wp_trim_words(get_the_excerpt(), 40, ''); ?>
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
        </div>
    
    
    <!-- TOP ARTICLE HERO SECTION -->
    <!-- HERO SECTION LIKE FOREIGN AFFAIRS -->
<div class="news-hero-split">
    
    
    

    <!-- LEFT: BIG IMAGE -->
    <div class="hero-left">
        <?php the_post_thumbnail('full'); ?>
    </div>

    <!-- RIGHT: TITLE + SUBTITLE + META + AUTHOR BIO -->
    <div class="hero-right">
        <!-- AUTHOR BIO -->
        <?php if ($author): ?>
        <div class="author-bio-box">
            <p class="author-bio-text">
                <?php echo get_field('author_short_bio', $author->ID); ?>
            </p>
            <a class="author-more" href="<?php echo get_permalink($author->ID); ?>">
                → View Full Profile
            </a>
        </div>
        <?php endif; ?>

    </div>

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

            <!-- RIGHT SIDEBAR ALWAYS VISIBLE -->
            <div class="article-right">

                <?php
                $author = get_field('select_author');

                if ($author):
                ?>

                    <!--<div class="article-author-box">-->
                    <!--    <h3>About the Author</h3>-->

                    <!--    <p><?php echo get_field('author_short_bio', $author->ID); ?></p>-->

                    <!--    <a href="<?php echo get_permalink($author->ID); ?>"-->
                    <!--        class="more-link">→ View Full Profile</a>-->

                    <!--</div>-->

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
            'post_type'      => 'case-study',
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
                            ENERG | <?php echo get_the_date('F j, Y'); ?>
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

                <!--<div class="article-author-box">-->
                <!--    <h3>About the Author</h3>-->

                <!--    <p><?php echo get_field('author_short_bio', $author->ID); ?></p>-->

                <!--    <a href="<?php echo get_permalink($author->ID); ?>"-->
                <!--        class="more-link">→ View Full Profile</a>-->

                <!--</div>-->

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
                            ENERG | <?php echo get_the_date('F j, Y'); ?>
                        </p>
                    </div>

                </a>
            </div>

        <?php endwhile; wp_reset_postdata(); ?>
    </div>
</div>

</section>



<?php get_footer(); ?>
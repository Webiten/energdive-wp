<?php get_header(); ?>

<?php
$issue_id  = get_queried_object_id();
$monthyear = get_field('issue_month-year', $issue_id);
$volume    = get_field('volume', $issue_id);
$number    = get_field('number', $issue_id);
?>

<style>
/* MAIN PAGE WRAPPER */
.sip {
    margin: 30px auto;
    max-width: 1200px;
    padding: 0 20px;
}

/* MAIN LAYOUT */
.issue-layout {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 40px;
}

/* LEFT HEADER SECTION */
.issue-left h1 {
    font-family: "Playfair Display", serif;
    font-size: 35px;
    line-height: 1.2;
    font-weight: 500;
    margin-bottom: 10px;
}

.issue-meta {
    font-family: "Roboto Flex", serif;
    font-size: 18px;
    margin-bottom: 15px;
    opacity: 0.8;
}

h2.issue-heading {
    margin: 0px;
    font-size: 20px;
    color: #000000;
    line-height: 1.2em;
    letter-spacing: 1.1px;
}

/* SECTION TITLE */
.issue-section-title {
    font-family: "Playfair Display", serif;
    font-size: 20px;
    color: #fff;
    margin: 40px 0 0px;
    background: #7B9F35;
    max-width: 200px;
    padding: 6px;
    text-align: center;
}

/* ARTICLE LIST */
.issue-article {
    border-bottom: 1px solid #d4d4d4;
    padding: 20px 0px;
    display: flex;
    justify-content: space-between;
    gap: 20px;
}

.ia-left {
    flex: 1;
}

h2.iah {
    font-family: "Playfair Display", serif;
    font-size: 25px;
    margin: 0 0 10px;
    font-weight: 500;
}

h2.iah a {
    color: #000;
    text-decoration: none;
}

p.iap {
    font-family: "Roboto Flex", serif;
    font-size: 18px;
    margin: 0 0 10px;
}

small.iaa {
    font-family: "Roboto Flex", serif;
    font-size: 16px;
}

/* ARTICLE THUMBNAIL */
.issue-article img {
    width: 160px;
    height: 150px;
    object-fit: cover;
    border-radius: 4px;
}

/* RIGHT SIDEBAR */
.issue-right {
    padding-left: 20px;
}

/* ISSUE COVER */
.issue-cover img {
    width: 100%;
    max-width: 335px;
    height: 499px;
    border-radius: 4px;
    border: 10px solid #bbd9f1;
    display: block;
    margin: 25px auto;
}

/* ============================
   RESPONSIVE
============================ */
@media (min-width: 1024px) {
    .issue-right {
        position: sticky;
        top: 120px;
        align-self: flex-start;
    }
}


@media (max-width: 992px) {
    .issue-layout {
        grid-template-columns: 1fr;
    }

    .issue-right {
        padding-left: 0;
        margin-top: 40px;
        text-align: center;
    }
}

@media (max-width: 600px) {
    .issue-article {
        flex-direction: column;
    }

    .issue-article img {
        width: 100%;
        height: auto;
        max-width: 100%;
        margin-top: 10px;
    }

    h2.iah { font-size: 22px; }
    p.iap { font-size: 16px; }
}
</style>

<div class="sip">
    <div class="issue-layout">

        <!-- LEFT SECTION -->
        <div class="issue-left">

            <h1><?php echo esc_html($monthyear); ?></h1>

            <div class="issue-meta">
                Volume <?php echo esc_html($volume); ?>, Issue <?php echo esc_html($number); ?>
            </div>
            <h2 class="issue-heading">ENERGIDVE Insights and Market Intelligence</h2>
            <?php
            $indexing = get_field('issue_indexing', $issue_id);

            if ($indexing):
                foreach ($indexing as $row):

                    $section_title = $row['section_title'];
                    $posts = $row['section_posts'];

                    if (!$posts) continue;
                    ?>

                    <?php if ($section_title): ?>
                        <h2 class="issue-section-title">
                            <?php echo esc_html($section_title); ?>
                        </h2>
                    <?php endif; ?>

                    <?php foreach ($posts as $post):
                        setup_postdata($post); ?>

                        <div class="issue-article">
                            <div class="ia-left">
                                <h2 class="iah">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>

                                <p class="iap">
                                    <?php echo wp_trim_words(get_the_excerpt(), 25, '...'); ?>
                                </p>

                                <?php
                                $acf_author = get_field('select_author');
                                if ($acf_author) {
                                    echo '<small class="iaa"><a href="'.get_permalink($acf_author->ID).'">'.get_the_title($acf_author->ID).'</a></small>';
                                } else {
                                    echo '<small class="iaa">By '.get_the_author().'</small>';
                                }
                                ?>
                            </div>

                            <div class="ia-right">
                                <?php if (has_post_thumbnail()): ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium'); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                    <?php endforeach; wp_reset_postdata(); ?>

                <?php endforeach;
            else:
                echo "<p>No indexed content found for this Issue.</p>";
            endif;
            ?>

        </div><!-- /.issue-left -->

        <!-- RIGHT SECTION -->
        <div class="issue-right">
            <div class="issue-cover">
                <?php echo get_the_post_thumbnail($issue_id, 'large'); ?>
            </div>
        </div>

    </div>
</div>

<?php get_footer(); ?>

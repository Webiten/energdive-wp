<?php
/* Template Name: Issues Archive */
get_header();
?>

<style>
.archive-container {
    max-width: 1200px;
    margin: auto;
    padding: 50px 20px;
}
.archive-title {
    font-size: 42px;
    text-align: center;
    margin-bottom: 40px;
}
.archive-search {
    max-width: 600px;
    margin: 0 auto 40px;
}
.archive-search input {
    width: 100%;
    padding: 15px;
    border-radius: 5px;
    border: 1px solid #333;
    background: #111;
    color: white;
    font-size: 18px;
}
.decade-list {
    text-align: center;
    margin-bottom: 40px;
}
.decade-list a {
    margin: 0 10px;
    color: #aaa;
    font-size: 18px;
}
.year-heading {
    font-size: 28px;
    margin: 40px 0 20px;
    color: #d9534f;
}
.issue-grid {
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
}
.issue-item {
    width: 200px;
    text-align: center;
}
.issue-item img {
    width: 100%;
    border: 8px solid #b4d3ef;
    background: #fff;
}
</style>

<div class="archive-container">

    <h1 class="archive-title">Browse the Archive</h1>

    <!-- SEARCH BAR -->
    <div class="archive-search">
        <input type="text" placeholder="Browse by year, topic, author..." />
    </div>

    <!-- BROWSE BY DECADE -->
    <div class="decade-list">
        Browse by Decade:
        <?php
        $decades = [2020, 2010, 2000, 1990, 1980, 1970, 1960, 1950];
        foreach ($decades as $decade) {
            echo '<a href="#decade-' . $decade . '">' . $decade . 's</a>';
        }
        ?>
    </div>

    <!-- ISSUES GROUPED BY YEAR -->
    <?php
    global $wpdb;

    // get all years from issue_month-year field
    $issues = new WP_Query([
        'post_type' => 'issues',
        'posts_per_page' => -1,
        'orderby' => 'meta_value',
        'meta_key' => 'issue_month-year',
        'order' => 'DESC',
    ]);

    // Group by year
    $years = [];

    while ($issues->have_posts()) {
        $issues->the_post();
        $year_field = get_field('issue_month-year');
        preg_match('/\b(19|20)\d{2}\b/', $year_field, $match);

        if (!empty($match)) {
            $year = $match[0];
            $years[$year][] = get_the_ID();
        }
    }

    krsort($years); // Latest year first

    // Render each year
    foreach ($years as $year => $posts) {
        echo '<h2 id="y-' . $year . '" class="year-heading">' . $year . '</h2>';
        echo '<div class="issue-grid">';

        foreach ($posts as $id) {
            echo '<div class="issue-item">';
            echo '<a href="' . get_permalink($id) . '">';
            echo get_the_post_thumbnail($id, 'medium');
            echo '</a>';
            echo '</div>';
        }

        echo '</div>';
    }

    wp_reset_postdata();
    ?>

</div>

<?php get_footer(); ?>

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
.year-group-title {
    font-size: 32px;
    margin-top: 50px;
    margin-bottom: 20px;
    text-align: left;
}
.month-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
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

.issue-title {
    font-family: "Playfair Display", serif;
    font-size: 18px;
    margin-top: 12px;
    margin-bottom: 4px;
    color: #000;
}

.issue-meta {
    font-family: "Roboto Flex", serif;
    font-size: 14px;
    opacity: 0.75;
    margin: 0;
}

@media(max-width:768px){
    .month-grid { justify-content:center; }
    .issue-item { width: 160px; }
}
</style>

<div class="archive-container">

    <h1 class="archive-title">Browse the Archive</h1>

    <!-- SEARCH BAR -->
    <div class="archive-search">
        <input id="issueSearch" type="text" placeholder="Search by month, year, topic..." />
    </div>

    <?php
    // Fetch all issues ordered by date (based on text field)
    $issues = new WP_Query([
        'post_type' => 'issues',
        'posts_per_page' => -1,
        'orderby' => 'meta_value',
        'meta_key' => 'issue_month-year',
        'order' => 'DESC',
    ]);

    // GROUP BY YEAR + MONTH
    $grouped = [];

    while ($issues->have_posts()) {
        $issues->the_post();
        $raw = get_field('issue_month-year'); // e.g., "January 2025"

        if (!$raw) continue;

        // Extract month + year safely
        $parts = explode(' ', trim($raw));

        if (count($parts) !== 2) continue;

        $month = $parts[0];     // "January"
        $year  = $parts[1];     // "2025"

        $grouped[$year][$month][] = get_the_ID();
    }

    wp_reset_postdata();

    // Sort by year desc
    krsort($grouped);

    // Sort months by real month order (Jan → Dec)
    $monthOrder = [
        "January","February","March","April","May","June",
        "July","August","September","October","November","December"
    ];
    ?>

    <div id="issueArchiveResults">

    <?php foreach ($grouped as $year => $months): ?>

        <h2 class="year-group-title"><?php echo $year; ?></h2>

        <?php
        // Order months
        uksort($months, function($a, $b) use ($monthOrder) {
            return array_search($a, $monthOrder) - array_search($b, $monthOrder);
        });
        ?>

        <?php foreach ($months as $month => $posts): ?>
            <h3 style="font-size:22px; margin:20px 0 10px;"><?php echo $month; ?></h3>

            <div class="month-grid">
                <?php foreach ($posts as $id): ?>
                    <div class="issue-item">
    <a href="<?php echo get_permalink($id); ?>">
        <?php echo get_the_post_thumbnail($id, 'medium'); ?>
    </a>

    <?php 
        $title  = get_field('issue_month-year', $id);
        $volume = get_field('volume', $id);
        $number = get_field('number', $id);
    ?>

    <h4 class="issue-title"><?php echo esc_html($title); ?></h4>

    <p class="issue-meta">
        Volume <?php echo esc_html($volume); ?> 
        • Number <?php echo esc_html($number); ?>
    </p>
</div>
                <?php endforeach; ?>
            </div>

        <?php endforeach; ?>

    <?php endforeach; ?>

    </div>

</div>

<script>
// CLIENT SIDE SEARCH
document.getElementById("issueSearch").addEventListener("keyup", function() {
    let filter = this.value.toLowerCase();
    let container = document.getElementById("issueArchiveResults");
    let items = container.getElementsByClassName("issue-item");

    Array.from(items).forEach(item => {
        let txt = item.innerText.toLowerCase();
        item.style.display = txt.includes(filter) ? "" : "none";
    });
});
</script>

<?php get_footer(); ?>

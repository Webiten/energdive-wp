<?php
// taxonomy-filters.php (stable version)

function taxonomy_filters_markup() {
    ob_start();

    // 🧩 Safe context detection
    global $post;
    $post_type = function_exists('get_post_type') ? get_post_type() : '';

    $is_search     = function_exists('is_search') ? is_search() : false;
    $is_sector_tax = function_exists('is_tax') ? is_tax('sector') : false;

    // ✅ CPT Checks
    $is_news      = (function_exists('is_post_type_archive') && is_post_type_archive('news')) || $post_type === 'news';
    $is_reports   = (function_exists('is_post_type_archive') && is_post_type_archive('reports')) || $post_type === 'reports';
    $is_opinion   = (function_exists('is_post_type_archive') && is_post_type_archive('opinion')) || $post_type === 'opinion';
    $is_magazine  = (function_exists('is_post_type_archive') && is_post_type_archive('magazine')) || $post_type === 'magazine';
    $is_videos    = (function_exists('is_post_type_archive') && is_post_type_archive('videos')) || $post_type === 'videos';
    $is_events    = (function_exists('is_post_type_archive') && is_post_type_archive('events')) || $post_type === 'events';
    ?>

    <div class="taxonomy-filters">
        <div class="filter-box">

            <?php if ($is_search || $is_news || $is_reports || $is_opinion || $is_magazine || $is_events) { ?>
                <div class="filter-group">
                    <label>Sector:</label>
                    <select id="filter-sector">
                        <option value="">All</option>
                        <?php
                        $sectors = get_terms(['taxonomy' => 'sector', 'hide_empty' => false]);
                        if (!empty($sectors) && !is_wp_error($sectors)) {
                            foreach ($sectors as $term) {
                                echo '<option value="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            <?php } ?>

            <?php if ($is_search || $is_sector_tax) { ?>
                <div class="filter-group">
                    <label>Type of Content:</label>
                    <select id="filter-type">
                        <option value="">All</option>
                        <?php
                        $content_types = get_terms(['taxonomy' => 'content_type', 'hide_empty' => false]);
                        if (!empty($content_types) && !is_wp_error($content_types)) {
                            foreach ($content_types as $term) {
                                echo '<option value="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            <?php } ?>

            <?php if ($is_search || $is_news || $is_sector_tax || $is_reports || $is_opinion || $is_magazine || $is_events) { ?>
                <div class="filter-group">
                    <label>Tags:</label>
                    <select id="filter-tags">
                        <option value="">All</option>
                        <?php
                        $tags = get_terms(['taxonomy' => 'tags', 'hide_empty' => false]);
                        if (!empty($tags) && !is_wp_error($tags)) {
                            foreach ($tags as $term) {
                                echo '<option value="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            <?php } ?>

            <?php if ($is_search || $is_news || $is_sector_tax || $is_reports || $is_opinion || $is_magazine || $is_events) { ?>
                <div class="filter-group">
                    <label>Region:</label>
                    <select id="filter-world">
                        <option value="">All</option>
                        <?php
                        $worlds = get_terms(['taxonomy' => 'worlds', 'hide_empty' => false]);
                        if (!empty($worlds) && !is_wp_error($worlds)) {
                            foreach ($worlds as $term) {
                                echo '<option value="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            <?php } ?>

            <!--<?php if ($is_search || $is_news || $is_sector_tax || $is_reports || $is_opinion || $is_magazine || $is_events) { ?>-->
            <!--    <div class="filter-group">-->
            <!--        <label>States:</label>-->
            <!--        <select id="filter-states">-->
            <!--            <option value="">All</option>-->
            <!--            <?php-->
            <!--            $states = get_terms(['taxonomy' => 'states', 'hide_empty' => false]);-->
            <!--            if (!empty($states) && !is_wp_error($states)) {-->
            <!--                foreach ($states as $term) {-->
            <!--                    echo '<option value="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</option>';-->
            <!--                }-->
            <!--            }-->
            <!--            ?>-->
            <!--        </select>-->
            <!--    </div>-->
            <!--<?php } ?>-->

            <?php if ($is_search || $is_news || $is_sector_tax || $is_reports || $is_opinion || $is_magazine || $is_videos || $is_events) { ?>
                <div class="filter-group">
                    <label>Author:</label>
                    <select id="filter-author">
                        <option value="">All</option>
                        <?php
                        $authors = get_users(['who' => 'authors']);
                        foreach ($authors as $author) {
                            echo '<option value="' . esc_attr($author->ID) . '">' . esc_html($author->display_name) . '</option>';
                        }
                        ?>
                    </select>
                </div>
            <?php } ?>

            <!--<?php if ($is_search || $is_news || $is_sector_tax || $is_reports || $is_opinion || $is_magazine || $is_events) { ?>-->
            <!--    <div class="filter-group date-range">-->
            <!--        <label>Date Range:</label>-->
            <!--        <div>-->
            <!--        <input type="date" id="filter-from">-->
            <!--        <input type="date" id="filter-to">-->
            <!--    </div>-->
            <!--</div>-->
            <!--<?php } ?>-->

            <div class="filter-group clear-btn-wrap">
                <button id="clear-filters" type="button">Clear All Filters</button>
            </div>

        </div>
    </div>

    <?php
    return ob_get_clean();
}

add_shortcode('taxonomy_filters', 'taxonomy_filters_markup');

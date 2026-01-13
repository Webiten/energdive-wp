<?php
/*
Template Name: Member Dashboard
*/

get_header();
?>

<main id="site-content">
  <div class="dashboard-container">
    <?php
      // Render your WPEverest login form
      echo do_shortcode('[user_registration_my_account]');
    ?>
  </div>
</main>

<?php
get_footer();
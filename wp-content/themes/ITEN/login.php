<?php
/*
Template Name: Login
*/

get_header();
?>

<main id="site-content">
  <div class="loginregister" style=" display: flex; flex-direction: row; flex-wrap: nowrap; align-content: flex-start; justify-content: center; align-items: flex-start; max-width: 100%; margin: 0px; padding: 20px;">
    <div class="login-container" style="width: 40%;">
      <h2 style="text-align: center; font-size: 20px; font-family: 'DM Sans'sans-serif">Login if You Are A Existing Member</h2>
      <?php
      // Render your WPEverest login form
      echo do_shortcode('[user_registration_login]');
      ?>
    </div>
    <div class="register-container" style="width: 60%;">
      <h2 style="text-align: center; font-size: 20px; font-family: 'DM Sans'sans-serif">Register if You Are A New Member</h2>
      <?php
      // Render your WPEverest login form
      echo do_shortcode('[user_registration_form id="144"]');
      ?>
    </div>
  </div>
</main>

<?php
get_footer();

<?php
/*
Template Name: Membership ThankYou
*/

get_header();
?>

<main id="site-content">
  <div class="thankyou-container">
    <h1>Thank You for Your Registration!</h1>
    <p>Your membership registration was successful.</p>
    <?php
      // Render your WPEverest registration form
      echo do_shortcode('[user_registration_membership_thank_you]');
    ?>
    <div class="loginbutton">
      <button>
        <a href="<?php echo esc_url(wp_login_url()); ?>">Login Your Account Here</a>
      </button>
    </div>
  </div>
</main>

<?php
get_footer();

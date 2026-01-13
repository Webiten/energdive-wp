<?php
/* Template Name: Page Terms & Conditions */
get_header();
?>


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
	--main-container: 1280px;
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




/* Main Banner Section */
.breadcrumb-section {
  width: 100%;
  height: 300px;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;

  display: flex;
  justify-content: center;
  align-items: center;

  text-align: center;
  position: relative;
}

/* Dark overlay for readability */
.breadcrumb-section::before {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.3);
}

/* Content */
.breadcrumb-content {
  position: relative;
  color: #fff;
  padding: 0 20px;
}

.breadcrumb-content h1 {
  font-size: 38px;
  margin: 0;
  font-weight: 700;
  font-family:"Playfair Display", serif;
}

.breadcrumb-content p {
  margin-top: 8px;
  font-size: 16px;
  opacity: 0.9;
}



/* Wrapper */
.terms-wrapper {
  padding: 50px 0px;
}

/* Container */
.terms-container {
  
  margin: 0 auto;
  
}

/* Header */
.terms-header h1 {
  font-family: "Playfair Display", serif;
  font-size: 38px;
  font-weight: 700;
  margin-bottom: 10px;
}

.updated-date {
  font-family: "Abhaya Libre", serif;
  font-size: 16px;
  color: #666;
  margin-bottom: 40px;
}

/* Content */
.terms-content h2 {
  font-family: "Playfair Display", serif;
  font-size: 26px;
  font-weight: 600;
  margin: 50px 0 15px;
}

.terms-content p,
.terms-content li,
.terms-content address {
  font-family: "Abhaya Libre", serif;
  font-size: 17px;
  line-height: 1.4;
  margin: 0px 0 5px;
  color: #2a2a2a;
}

/* Lists */
.terms-content ul {
  padding-left: 25px;
  margin-bottom: 20px;
}

.terms-content li {
  margin-bottom: 10px;
}

/* Address */
.terms-content address {
  font-style: normal;
  margin-top: 10px;
}

/* Responsive */
@media (max-width: 768px) {
  .terms-container {
    padding: 40px 20px;
  }

  .terms-header h1 {
    font-size: 32px;
  }

  .terms-content h2 {
    font-size: 22px;
  }

  .terms-content p,
  .terms-content li {
    font-size: 17px;
  }
}




</style>


<section class="breadcrumb-section" 
    style="background-image: url('https://energ.energdive.com/wp-content/uploads/2025/12/advertise-breadrumb.jpg'); 
           background-size: cover; 
           background-position: center;">
 <!--<div class="breadcrumb-content">
    <h1>Contact Us</h1>
  </div>-->
</section>

<section class="container-widdthhh">
 
  <section class="terms-wrapper">
    <div class="terms-container">

      <header class="terms-header">
        <h1>Terms & Conditions</h1>
        <p class="updated-date">Last Updated: 3rd December 2026</p>
      </header>

      <div class="terms-content">

        <p>
          Welcome to energdive.com (“we”, “us”, “our”). These Terms & Conditions (“Terms”, “Agreement”) govern your access to and use of the website www.energdive.com and any associated digital products, insights, content solutions, or services that reference these Terms (collectively, the “Services”).
        </p>

        <p>
          By accessing or using the Services, you acknowledge that you have read, understood, and agree to be bound by these Terms. If you do not agree, please stop using the Services immediately.
        </p>

        <p>
          These Terms apply to all users, including visitors, registered users, contributors, and any person or entity accessing the Services.
        </p>

        <h2>1. Acceptance of Terms</h2>
        <p>By using the Services, you confirm that:</p>
        <ul>
          <li>You have the legal capacity to enter into this Agreement.</li>
          <li>You are at least the minimum age required under applicable law in your jurisdiction.</li>
          <li>Your use of the Services does not violate any applicable laws or regulations.</li>
          <li>If you are using the Services on behalf of an organisation, you have authority to bind that organisation.</li>
        </ul>

        <h2>2. Changes to Terms</h2>
        <p>
          We may update or modify these Terms from time to time. When changes are made, we will update the effective date.
          Continued use of the Services constitutes acceptance of the revised Terms.
        </p>

        <h2>3. About Our Services</h2>
        <p>
          energdive.com provides digital content, analysis, insights, reports, articles, interviews, market intelligence, and related features focused on the energy and climate ecosystem.
        </p>
        <p>
          We may update, modify, suspend, or discontinue any part of the Services at any time without notice.
        </p>

        <h2>4. User Accounts & Registration</h2>
        <p>By creating an account, you agree to:</p>
        <ul>
          <li>Provide accurate and complete information</li>
          <li>Keep your login credentials secure</li>
          <li>Not share your account with others</li>
          <li>Notify us of any unauthorised access</li>
        </ul>

        <h2>5. Use of the Services</h2>
        <p>You agree to use the Services lawfully. You must not:</p>
        <ul>
          <li>Engage in fraudulent or unlawful activities</li>
          <li>Attempt unauthorised access</li>
          <li>Disrupt website operations</li>
          <li>Upload malware or harmful scripts</li>
          <li>Commercially exploit content without permission</li>
          <li>Use bots or scrapers without consent</li>
          <li>Impersonate others</li>
        </ul>

        <h2>6. Intellectual Property Rights</h2>
        <p>
          All content on energdive.com is protected by copyright, trademark, and intellectual property laws.
          Unauthorised use may result in legal action.
        </p>

        <h2>7. User-Generated Content</h2>
        <p>
          By submitting content, you grant us a worldwide, royalty-free licence to use, reproduce, and distribute it.
        </p>

        <h2>8. Third-Party Links & External Content</h2>
        <p>
          Third-party links are provided for convenience only. We are not responsible for external content, policies, or practices.
        </p>

        <h2>9. Accuracy & Reliability</h2>
        <p>
          While we strive for accuracy, we do not guarantee completeness or reliability. Users must independently verify information.
        </p>

        <h2>10. Advertisements & Marketing</h2>
        <p>
          Advertisements may appear on the Services. We are not responsible for advertiser claims or offerings.
        </p>

        <h2>11. Payments & Transactions</h2>
        <p>
          Payments are processed through third-party gateways. We do not store sensitive payment details.
        </p>

        <h2>12. Termination of Access</h2>
        <p>
          We may suspend or terminate access if these Terms are violated or required by law.
        </p>

        <h2>13. Disclaimer of Warranties</h2>
        <p>
          Services are provided “as is” without warranties of any kind.
        </p>

        <h2>14. Limitation of Liability</h2>
        <p>
          We are not liable for any direct or indirect damages arising from use of the Services.
        </p>

        <h2>15. Indemnification</h2>
        <p>
          You agree to indemnify energdive.com against claims arising from misuse or violations.
        </p>

        <h2>16. Governing Law & Jurisdiction</h2>
        <p>
          These Terms are governed by the laws of India, with jurisdiction in New Delhi.
        </p>

        <h2>17. Severability</h2>
        <p>
          If any provision is invalid, remaining provisions remain effective.
        </p>

        <h2>18. Entire Agreement</h2>
        <p>
          These Terms constitute the complete agreement between you and energdive.com.
        </p>

        <h2>19. Contact Information</h2>
        <address>
          <strong>Clarisector Technologies Pvt. Ltd</strong><br>
          Website: energdive.com<br>
          Email: contact@energdive.com<br><br>
          Plot No-33, Janki House, 3rd Floor,<br>
          Sector-12 A, Dwarka,<br>
          New Delhi – 110075, India
        </address>

      </div>
    </div>
  </section>




</section>







<?php get_footer(); ?>

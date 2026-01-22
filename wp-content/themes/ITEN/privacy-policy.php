<?php
/* Template Name: Page Privacy Policy */
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



/* ==============================
   PRIVACY POLICY STYLES
============================== */

/* Section */
.privacy-policy {
  padding: 50px 0px;
}

/* Content container */
.policy-container {
  
  margin: 0 auto;
}

/* Headings */
.policy-container h1 {
  font-family: "Playfair Display", serif;
  font-size: 38px;
  margin-bottom: 10px;
}

.policy-container h2 {
  font-family: "Playfair Display", serif;
  font-size: 26px;
  margin: 45px 0 15px;
}

.policy-container h3 {
  font-family: "Playfair Display", serif;
  font-size: 20px;
  margin: 30px 0 10px;
}

/* Effective date */
.effective-date {
  font-family: "Abhaya Libre", serif;
  font-size: 15px;
  color: #333;
  margin-bottom: 30px;
}

/* Paragraphs */
.policy-container p {
  font-family: "Abhaya Libre", serif;
  font-size: 17px;
  line-height: 1.3;
  margin-bottom: 18px;
}

/* Lists */
.policy-container ul {
  padding-left: 22px;
  margin-bottom: 22px;
}

.policy-container li {
  font-family: "Abhaya Libre", serif;
  font-size: 17px;
  line-height: 1.6;
  margin-bottom: 10px;
}

/* Address */
.policy-container address {
  font-family: "Abhaya Libre", serif;
  font-size: 16px;
  line-height: 1.4;
  font-style: normal;
  margin-top: 10px;
}

/* Responsive */
@media (max-width: 768px) {
  .policy-container h1 {
    font-size: 34px;
  }

  .policy-container h2 {
    font-size: 22px;
  }

  .policy-container p,
  .policy-container li {
    font-size: 16px;
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
    
<section class="privacy-policy">
  <div class="policy-container">

    <h1>Privacy Policy</h1>
    <p class="effective-date"><strong>Effective Date:</strong> December 03, 2025</p>

    <p>
      We at energdive.com (“we”, “our”, “us”) respect your privacy and are committed to protecting your personal data.
      This Privacy Policy explains how we collect, use, share, and protect your personal information when you access
      or use our website <strong>www.energdive.com</strong> and related services.
    </p>

    <p>
      By using our Services, you agree to the practices described in this Privacy Policy. If you do not agree,
      please do not use the Services.
    </p>

    <p>
      Links from our Services may lead to external websites not covered by this Privacy Policy.
      We are not responsible for their privacy practices.
    </p>

    <h2>1. What information do we collect about you and how do we collect it?</h2>

    <h3>1.1 Information you provide directly</h3>
    <p>We may collect personal information when you:</p>

    <ul>
      <li>Create or manage an account on energdive.com</li>
      <li>Fill out forms or submit information</li>
      <li>Request access to insights, reports, or content</li>
      <li>Subscribe to newsletters or alerts</li>
      <li>Participate in surveys, feedback, or promotions</li>
      <li>Communicate with us via email or contact forms</li>
    </ul>

    <p>This may include:</p>

    <ul>
      <li><strong>Identity details:</strong> name, title, organisation, industry, country</li>
      <li><strong>Contact details:</strong> email, phone number, postal address</li>
      <li><strong>Account information:</strong> username, preferences, settings</li>
      <li><strong>Communication data:</strong> queries, feedback, correspondence</li>
      <li><strong>Marketing preferences:</strong> newsletter and alert choices</li>
    </ul>

    <h3>1.2 Payment and transaction information</h3>
    <p>
      Payment details are processed by authorised payment gateways.
      We receive limited transaction data for billing, access, and record-keeping.
      Full card details are not stored by energdive.com.
    </p>

    <h3>1.3 Information collected automatically</h3>
    <ul>
      <li>Pages visited, content viewed, navigation patterns</li>
      <li>IP address, approximate location, time zone</li>
      <li>Device and browser information</li>
      <li>Session duration and technical logs</li>
    </ul>

    <h2>2. How and why do we use your personal information?</h2>

    <h3>2.1 To provide and manage services</h3>
    <ul>
      <li>Account creation and management</li>
      <li>Providing access to content and insights</li>
      <li>Responding to queries and support requests</li>
      <li>Website performance and maintenance</li>
    </ul>

    <h3>2.2 Communications and marketing</h3>
    <p>
      We may send newsletters, alerts, and updates you opt into.
      You may unsubscribe at any time.
    </p>

    <h2>3. Sharing your personal information</h2>
    <p>
      We do not sell your personal data.
      Information may be shared with trusted service providers, analytics partners,
      and legal authorities where required.
    </p>

    <h2>4. Cookies and tracking technologies</h2>
    <ul>
      <li>Essential cookies for functionality</li>
      <li>Analytics cookies for performance measurement</li>
      <li>Advertising cookies for campaign optimisation</li>
    </ul>

    <h2>5. Data retention</h2>
    <p>
      Personal information is retained only as long as necessary
      to fulfil legal, operational, and security requirements.
    </p>

    <h2>6. Data security</h2>
    <p>
      We use technical and organisational safeguards to protect your data.
      However, no online transmission is fully secure.
    </p>

    <h2>7. Your rights and choices</h2>
    <ul>
      <li>Access, correction, and deletion</li>
      <li>Restriction and objection to processing</li>
      <li>Withdrawal of consent</li>
      <li>Data portability where applicable</li>
    </ul>

    <h2>8. International data transfers</h2>
    <p>
      Your data may be processed outside your jurisdiction with appropriate safeguards.
    </p>

    <h2>9. Children’s privacy</h2>
    <p>
      Our services are not intended for children below 13 years.
      We do not knowingly collect their data.
    </p>

    <h2>10. Changes to this policy</h2>
    <p>
      We may update this policy periodically.
      Changes will be reflected with an updated effective date.
    </p>

    <h2>11. Contact us</h2>
    <address>
        <strong>Clarisector Technologies Pvt. Ltd.<br>
      <strong>Website:</strong> energdive.com<br>
      <strong>Email:</strong> info@energdive.com<br><br>
      Plot No-33, Janki House, 3rd Floor,<br>
      Sector-12A, Dwarka,<br>
      New Delhi-110075, India
    </address>

  </div>
</section>




</section>







<?php get_footer(); ?>

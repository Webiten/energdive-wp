<?php
/* Template Name: Page Subscription */
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



/* Section */
.subscribe-section {
  padding: 40px 0px;
  
}

/* Container */
.subscribe-container {
  
  margin: 0 auto;
  display: grid;
  grid-template-columns: 6fr 6fr;
  gap: 40px;
  align-items: flex-start;
}

/* Content Column */
.subscribe-content h1 {
  font-family: "Playfair Display", serif;
  font-size: 30px;
  font-weight: 700;
  margin: 6px 0 5px 0;
  color:#000;
}

.subscribe-content h3 {
  font-family: "Playfair Display", serif;
  font-size: 20px;
  font-weight: 600;
  margin: 6px 0 18px 0;
  color:#789142;
  font-style:italic;
}

.subscribe-content h4 {
  font-family: "Playfair Display", serif;
  font-size: 22px;
  margin: 30px 0 5px 0;
  color:#000;
}

.subscribe-content p,
.subscribe-content li {
  font-family: "Abhaya Libre", serif;
  font-size: 18px;
  line-height: 1.4;
  color: #333;
  margin: 6px 0 5px 0;
}

/* Lists */
.subscribe-content ul {
  padding-left: 22px;
  margin-bottom: 10px;
}

.subscribe-content li {
  margin-bottom: 10px;
}

/* Highlight */
.highlight-text {
  font-size: 19px;
  font-weight: 600;
  margin-top: 25px;
  color:#789142;
  font-style:italic;
}

/* Form Column */
.subscribe-form {
  background: #ffffff;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
  border-radius: 8px;
  padding:0 20px;
}

.subscribe-form h2 {
  font-family: "Playfair Display", serif;
  font-size: 30px;
  margin: 6px 0 5px 0;
  color:#000;
}

.form-intro {
  font-family: "Abhaya Libre", serif;
  font-size: 17px;
  line-height: 1.4;
  margin-bottom: 25px;
  color: #333;
}

/* iframe */
.form-embed iframe {
  width: 100%;
  height: 450px;
  border: none;
}

/* Responsive */
@media (max-width: 991px) {
  .subscribe-container {
    grid-template-columns: 1fr;
    gap: 40px;
  }

  .subscribe-content h1 {
    font-size: 34px;
  }

  .subscribe-form {
    padding: 35px 25px;
  }
}

@media (max-width: 576px) {
  .subscribe-section {
    padding: 40px 15px;
  }

  .subscribe-content h3 {
    font-size: 20px;
  }

  .subscribe-content p,
  .subscribe-content li {
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
 

<section class="subscribe-section">
  <div class="subscribe-container">

    <!-- Content Column -->
    <div class="subscribe-content">
      <h1>Subscription</h1>
      <h3>Access Insight that shapes India’s energy future</h3>

      <p>
        <strong>ENERGDIVE</strong> is more than a magazine—it is India’s most trusted voice on energy transition,
        policy, technology, and sustainability. Every issue brings sharp analysis, real industry perspectives,
        and actionable intelligence for leaders shaping tomorrow.
      </p>

      <h4>Why Subscribe?</h4>
      <ul>
        <li><strong>Credible Knowledge:</strong> Expert opinions, policy insights & sector intelligence.</li>
        <li><strong>Future-Focused Content:</strong> Coverage decoding India’s clean energy transformation.</li>
        <li><strong>Leadership & Innovation:</strong> Stories of change-makers and pioneers.</li>
        <li><strong>Monthly Engagement:</strong> Delivered to your doorstep and available digitally.</li>
        <li><strong>Community Access:</strong> Connect with decision-makers across government and industry.</li>
      </ul>

      <h4>The ENERGDIVE Advantage</h4>
      <p>
        A platform built on credibility, relevance, and influence—trusted by governments,
        corporates, investors, and innovators committed to a sustainable energy future.
      </p>

      <p class="highlight-text">
        Join the movement shaping India’s energy narrative.
      </p>
    </div>

    <!-- Form Column -->
    <div class="subscribe-form">
      <h2>Subscription Enquiry</h2>
      <p class="form-intro">
        Stay informed with trusted energy insights delivered directly to you.
        Complete the form to begin your ENERGDIVE subscription journey.
      </p>

      <div class="form-embed">
        <iframe
          aria-label="Energdive - Website Form"
          frameborder="0"
          src="https://forms.zohopublic.in/itenmedia1/form/ENERGDIVEEnquiriesForm/formperma/vGdZ0noLDdoGdPS8QIhuH69flKMawpU27Ws-TttbC1A?QueryType=Magazine%20Subscription%20Enquiry">
        </iframe>
      </div>
    </div>

  </div>
</section>

 

</section>



<script>window.$zoho=window.$zoho || {};$zoho.salesiq=$zoho.salesiq||{ready:function(){}}</script><script id="zsiqscript" src="https://salesiq.zohopublic.in/widget?wc=siqe7427becac05b796f13e957c1acd50ed0f72f5df2fa22a28bf6688f5aef8ead2" defer></script>



<?php get_footer(); ?>

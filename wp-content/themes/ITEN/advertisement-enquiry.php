<?php
/* Template Name: Advertisment Enquiry */
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

/* ----------------------------- */
/* RESPONSIVE STYLES */
/* ----------------------------- */

/* Tablets */
@media (max-width: 992px) {
  .breadcrumb-section {
    height: 240px;
  }
  .breadcrumb-content h1 {
    font-size: 32px;
  }
  .breadcrumb-content p {
    font-size: 15px;
  }
}

/* Mobile Screens */
@media (max-width: 576px) {
  .breadcrumb-section {
    height: 140px;
  }
  .breadcrumb-content h1 {
    font-size: 26px;
    line-height: 1.3;
  }
  .breadcrumb-content p {
    font-size: 14px;
  }
}

.media-kit-section {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 60px 20px;
  background: #f7f9fb;
}

.media-container {
  max-width: 800px;
  text-align: center;
  margin: 0 auto;
}

.media-container h1 {
  font-size: 36px;
  font-weight: 700;
  margin-bottom: 15px;
  color: #000;
  font-family:"Playfair Display", serif;
}

.media-container .media-text {
  font-size: 18px;
  line-height: 1.6;
  margin-bottom: 30px;
  color: #333;
  font-family:"Abhaya Libre", serif;
}

.iframe-wrapper {
  display: flex;
  justify-content: center;
}

.iframe-wrapper iframe {
  width: 100%;
  max-width: 650px;
  height: 1250px;
  border: none;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .media-container h1 {
    font-size: 30px;
  }

  .media-container .media-text {
    font-size: 16px;
    padding: 0 10px;
  }

  .iframe-wrapper iframe {
    height: 850px;
  }
}

@media (max-width: 480px) {
  .media-container h1 {
    font-size: 26px;
  }

  .iframe-wrapper iframe {
    height: 900px;
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

<section class="media-kit-section">
  <div class="media-container">

    <h1>Advertisement Enquiry</h1>

    <p class="media-text">Share your advertising requirements with us. Our team will connect with you to craft the right visibility solution for your brand.</p>

    <div class="iframe-wrapper">
      <iframe aria-label='Energdive - Website Form'
        frameborder="0"
        src='https://forms.zohopublic.in/itenmedia1/form/ENERGDIVEEnquiriesForm/formperma/vGdZ0noLDdoGdPS8QIhuH69flKMawpU27Ws-TttbC1A?QueryType=Advertisement%20Enquiry'>
      </iframe>
    </div>

  </div>
</section>




</section>




<script>window.$zoho=window.$zoho || {};$zoho.salesiq=$zoho.salesiq||{ready:function(){}}</script><script id="zsiqscript" src="https://salesiq.zohopublic.in/widget?wc=siqe7427becac05b796f13e957c1acd50ed0f72f5df2fa22a28bf6688f5aef8ead2" defer></script>


<?php get_footer(); ?>

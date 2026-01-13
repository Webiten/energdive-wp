<?php
/* Template Name: Advertise With Us */
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

/* ===============================
   ADVERTISE SECTION
================================ */
.advertise-section {
  padding: 40px 0px;
  
}

.advertise-container {
  
  margin: auto;
  display: flex;
  gap: 50px;
  align-items: center;
}

/* CONTENT COLUMN — ~60% */
.advertise-content {
  flex: 7;
}

.advertise-content h1 {
  font-size: 35px;
  line-height: 1.2;
  margin:6px 0 5px 0;
  color: #000;
  font-family:"Playfair Display", serif;
}

.advertise-content h3 {
  font-size: 19px;
  font-weight: 700;
  color: #789142;
  margin: 6px 0 5px 0;
  font-style:italic;
  font-family:"Playfair Display", serif;
}

.advertise-content p {
  font-size: 17px;
  line-height: 1.3;
  color: #333;
  max-width: 680px;
  margin-bottom: 0;
  font-family:"Abhaya Libre", serif;
}

/* BUTTONS */
.advertise-buttons {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
  margin-top:20px;
}

.btn {
  padding: 4px 26px;
  font-size: 15px;
  text-decoration: none;
  border-radius: 6px;
  transition: all 0.3s ease;
  font-family:"Abhaya Libre", serif;
}

.btn-primary {
  background: #000;
  color: #fff;
}

.btn-primary:hover {
  background: #789142;
}

.btn-outline {
  border: 1px solid #000;
  color: #000;
  background: transparent;
}

.btn-outline:hover {
  background: #789142;
  border:none;
  color: #fff;
}

/* IMAGE COLUMN — ~40% */
.advertise-image {
  flex: 5;
}

.advertise-image img {
  width: 100%;
  height: auto;
  border-radius: 12px;
  object-fit: cover;
}

/* ===============================
   RESPONSIVE
================================ */

/* Tablets */
@media (max-width: 992px) {
  .advertise-container {
    flex-direction: column;
    gap: 40px;
  }

  .advertise-content h1 {
    font-size: 34px;
  }

  .advertise-content p {
    max-width: 100%;
  }

  .advertise-image {
    width: 100%;
  }
}

/* Mobile */
@media (max-width: 600px) {
  .advertise-section {
    padding: 25px 10px;
  }

  .advertise-content h1 {
    font-size: 25px;
    text-align:center;
  }

  .advertise-content h3 {
    font-size: 18px;
    text-align:center;
  }

  .btn {
    width: 100%;
    text-align: center;
  }
}



/* ===============================
   ADVERTISE LEAD SECTION
================================ */
.advertise-lead-section {
  padding: 25px 0px;
  margin-bottom:50px;
}

.advertise-lead-container {
  
  margin: auto;
  display: flex;
  gap: 60px;
  align-items: flex-start;
}

/* FORM COLUMN — ~40% */
.advertise-form {
  flex: 6;
  background: #fafafa;
  
  border-radius: 12px;
  box-shadow: 0 10px 40px rgba(0,0,0,.06);
}

.advertise-form h3 {
      font-size: 32px;
    line-height: 1.2;
    margin: 6px 0 5px 0;
    color: #000;
    font-family: "Playfair Display", serif;
}

.advertise-form p {
  font-size: 17px;
  line-height: 1.3;
  color: #333;
  margin: 6px 0 5px 0;
  max-width: 650px;
  font-family:"Abhaya Libre", serif;
}
/* CONTENT COLUMN — ~60% */
.advertise-info {
  flex: 6;
  padding-top: 10px;
}

.advertise-info h2 {
      font-size: 35px;
    line-height: 1.2;
    margin: 6px 0 5px 0;
    color: #000;
    font-family: "Playfair Display", serif;
}

.advertise-info h4 {
  font-size: 19px;
    font-weight: 700;
    color: #789142;
    margin: 6px 0 5px 0;
    font-style: italic;
    font-family: "Playfair Display", serif
}

.advertise-info p {
  font-size: 17px;
  line-height: 1.3;
  color: #333;
  margin-bottom: 0px;
  max-width: 650px;
  font-family:"Abhaya Libre", serif;
}

/* LIST STYLING */
.advertise-info ul {
  padding-left: 18px;
  max-width: 650px;
}

.advertise-info li {
  font-size: 16px;
  line-height: 1.4;
  color: #333;
  margin-bottom: 12px;
  position: relative;
  font-family:"Abhaya Libre", serif;
}

.advertise-info li::marker {
  color: #000;
}

/* ===============================
   RESPONSIVE
================================ */

/* Tablets */
@media (max-width: 992px) {
  .advertise-lead-container {
    flex-direction: column;
    gap: 45px;
  }

  .advertise-info h2 {
    font-size: 32px;
  }

  .advertise-info p,
  .advertise-info ul {
    max-width: 100%;
  }
}

/* Mobile */
@media (max-width: 600px) {
  .advertise-lead-section {
    padding: 60px 15px;
  }

  .advertise-info h2 {
    font-size: 26px;
  }

  .advertise-info h4 {
    font-size: 18px;
  }

  .advertise-form {
    padding: 28px;
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
    
<section class="advertise-section">
  <div class="advertise-container">

    <!-- CONTENT COLUMN (≈ col-md-7) -->
    <div class="advertise-content">
      <h1>Why Partner with ENERGDIVE?</h1>
      <h3>Influence That Shapes the Future</h3>

      <p>Advertising in ENERGDIVE is not a transaction—it’s an alignment with India’s most credible energy narrative. Every placement, partnership, and feature represents a statement of leadership, signalling that your brand stands at the heart of India’s clean energy transformation.</p>
      <p><strong>For governments,</strong> it is a channel of policy communication.<br>
<strong>For corporates,</strong> a platform of thought leadership.<br>
<strong>For investors and innovators,</strong> a bridge to India’s most strategic markets</p>

<p>In an era defined by credibility, ENERGDIVE offers more than reach—it delivers reputation, relevance, and resonance.</p>

<p>In a world overflowing with information, influence belongs to those who curate knowledge with clarity and purpose. ENERGDIVE distinguishes itself as a Strategic Intelligence Platform—where insight meets action, and content creates credibility.</p>

      <div class="advertise-buttons">
        <a href="https://energdive.com/download-media-kit/" class="btn btn-primary">Download Media Kit</a>
        <a href="https://www.energdive.com/advertisement-enquiry/" class="btn btn-outline">Advertisement Enquiry</a>
      </div>
    </div>

    <!-- IMAGE COLUMN (≈ col-md-5) -->
    <div class="advertise-image">
      <img src="https://energ.energdive.com/wp-content/uploads/2025/12/advertising-digital-marketing-commercial-promotion-concept-scaled.jpg" alt="Advertise with Energdive">
    </div>

  </div>
</section>


<section class="advertise-lead-section">
  <div class="advertise-lead-container">

    
    <!-- CONTENT COLUMN (≈ col-md-7 | RIGHT) -->
    <div class="advertise-info">
      <h2>Platform </h2>
      <h4>The ENERGDIVE Advantage</h4>

      <p>For advertisers, it offers:</p>

      <ul>
        <li><strong>Contextual Credibility:</strong> Placement within India’s most respected editorial ecosystem.</li>
<li><strong>Policy Adjacency:</strong> Direct visibility among government, PSU, and regulatory decision-makers.</li>
<li><strong>Thought Leadership:</strong> Association with authoritative analysis and future-shaping dialogue.</li>
<li><strong>Sustained Recall:</strong> Monthly engagement through both print and digital circulation.</li>
<li><strong>Integrated Value:</strong> The combined reach of ITEN Media, ENCIS, and ClariSector’s technology-driven outreach network.</li>
      </ul>
    </div>

  </div>
</section>



</section>




<script>window.$zoho=window.$zoho || {};$zoho.salesiq=$zoho.salesiq||{ready:function(){}}</script><script id="zsiqscript" src="https://salesiq.zohopublic.in/widget?wc=siqe7427becac05b796f13e957c1acd50ed0f72f5df2fa22a28bf6688f5aef8ead2" defer></script>


<?php get_footer(); ?>

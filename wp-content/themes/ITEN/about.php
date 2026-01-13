<?php
/*
Template Name: About Page
*/
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
  margin-top: 15px;
  font-size: 16px;
  opacity: 0.9;
}



</style>


<section class="breadcrumb-section" 
    style="background-image: url('https://energ.energdive.com/wp-content/uploads/2025/12/advertise-breadrumb.jpg'); 
           background-size: cover; 
           background-position: center;">
 <div class="breadcrumb-content">
    <h1>Events</h1>
    <p>Stay updated with important energy events highlighted by ENERGDIVE, from summits and conferences to exhibitions, shows, and workshops across the sector.</p>
  </div>
</section>


<section class="container-widdthhh">
    <div class="abtbanner">
        <div class="abtbannercontent">
            <h1>About Us - Energ Portal</h1>
        </div>
    </div>
    <div class="a1">
        <section class="a2">
            <div class="a3">
                <div class="a4-col-50">
                    <h2>Our Mission</h2>
                    <p>At ITEN MEDIA, our mission is to provide comprehensive and up-to-date information on energy
                        solutions, technologies, and industry trends. We aim to be a trusted resource for professionals,
                        businesses, and enthusiasts in the energy sector.</p>
                    <h2>What We Do</h2>
                    <p>We curate and publish articles, news, and insights on various aspects of the energy industry,
                        including renewable energy, fossil fuels, energy efficiency, and emerging technologies. Our team
                        of experts ensures that our content is accurate, relevant, and engaging.</p>
                    <h2>Our Team</h2>
                    <p>Our team consists of experienced journalists, industry experts, and researchers who are passionate
                        about energy and committed to delivering high-quality content. We work collaboratively to bring
                        diverse perspectives and expertise to our readers.</p>
                    <div class="abt1">
                        <button id="abtbtn">
                            <a href="https://localhost/wordpress/contact">Contact Us</a>
                        </button>
                    </div>
                </div>
                <div class="a5-col-50">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/nasa.jpg" class="img1" alt="About Us Image">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/nasa.jpg" class="img2" alt="About Us Image">
                </div>
            </div>
        </section>
    </div>
</section>
<script>window.$zoho=window.$zoho || {};$zoho.salesiq=$zoho.salesiq||{ready:function(){}}</script><script id="zsiqscript" src="https://salesiq.zohopublic.in/widget?wc=siqe7427becac05b796f13e957c1acd50ed0f72f5df2fa22a28bf6688f5aef8ead2" defer></script>
<?php
get_footer();
?>
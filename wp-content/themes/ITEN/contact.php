<?php
/**
 * Template Name: Opinion Filter Page
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


.opinion-section {
  padding: 30px 20px;
  
  display: flex;
  justify-content: center;
}

.opinion-content {
  max-width: 980px;
  text-align: center;
}
.opinion-content h1{
font-family:"Playfair Display", serif;
color:#000;
}
/* Main paragraph */
.main-text {
  font-size: 18px;
  line-height: 1.4;
  color: #333;
  margin-bottom: 20px;
  font-family:"Abhaya Libre", serif;
  
}

/* Italic one-liner */
.italic-text {
  font-size: 18px;
  padding-top:20px;
  font-style: italic;
  color: #000;
  margin: 0;
  opacity: 0.9;
  font-family:"Abhaya Libre", serif;
}

/* Responsive */
@media (max-width: 600px) {
  .main-text {
    font-size: 16px;
    line-height: 1.7;
  }
  .italic-text {
    font-size: 15px;
  }
}


.contact-tabs-section {
  padding: 0px 0 80px;
  background: #fff;
  
}

.contact-tabs-section .section-title {
  font-size: 20px;
  font-weight: 700;
  margin-bottom: 40px;
  color: #333;
	text-align: center;
	font-family:"Playfair Display", serif;
}

.contact-tabs-section .contact-tabs-nav {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 0px;
  margin-bottom: 40px;
  padding: 0;
  list-style: none;
	background: none;
	
	text-align: left;
}

.contact-tabs-section .contact-tabs-nav .tab {
  color: #333;
	font-size: 15px;
	text-align: left;
  cursor: pointer;
	text-transform: capitalize;
  transition: all 0.3s ease;
  font-weight: 600;
padding: 4px 24px;
	border-bottom: 1px #e3dede solid;
	font-family:"Abhaya Libre", serif;
}

.contact-tabs-section .contact-tabs-nav .tab:hover {
  border-bottom: 1px solid #000;
  color: #fff;
	background: #000;
}

.contact-tabs-section .contact-tabs-nav .tab.active {
  color: #fff;
	background: #000;
  border-color: #000;
}

.contact-tabs-section .contact-tabs-content {
  margin: 0 auto;
  text-align: left;
  background: #f6f6f6;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 6px 15px rgba(255, 255, 255, 0.2);
  font-family:"Abhaya Libre", serif;
  width:900px;
}

.contact-tabs-section .tab-content {
  display: none;
  animation: fadeIn 0.4s ease;
	color: #333;
	font-family:"Abhaya Libre", serif;
}
.contact-tabs-section .tab-content h2{
  font-family:"Playfair Display", serif;
	color: #000;
	font-weight:800;
	    font-size: 20px;
}
.contact-tabs-section .tab-content h3{
  font-family:"Playfair Display", serif;
	color: #333;
	letter-spacing:1px;
}

.contact-tabs-section .tab-content p{
  font-family:"Abhaya Libre", serif;
	color: #333;
	line-height: 22px;
}
.contact-tabs-section .tab-content p a{
  text-decoration:none;
	color: #333;
}

.contact-tabs-section .tab-content.active {
  display: block;
}

/*@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}*/

/* Responsive */
@media (max-width: 767px) {
  .contact-tabs-section .contact-tabs-nav {
    flex-direction: column;
  }
  .contact-tabs-section .contact-tabs-nav .tab {
    width: 100%;
  }
}


</style>


<section class="breadcrumb-section" 
    style="background-image: url('https://energ.energdive.com/wp-content/uploads/2025/12/contact-bg.jpg'); 
           background-size: cover; 
           background-position: center;">
 <!--<div class="breadcrumb-content">
    <h1>Contact Us</h1>
  </div>-->
</section>

<section class="container-widdthhh">
    
<div class="opinion-section">
  <div class="opinion-content">
      <h1>Contact Us</h1>
    <p class="italic-text">We believe great ideas grow through collaboration. If you wish to partner, advertise, contribute editorially, or require subscription assistance, connect with us—we’re always ready to engage. Together, let’s build an intelligent, sustainable energy future for India.</p>

    
  </div>
</div>


<section class="contact-tabs-section">
          <div class="container">
              
			  <div class="row">
				   <ul class="contact-tabs-nav">
                <li class="tab active" data-tab="address">Address</li>
			    <li class="tab" data-tab="general">General Queries</li>
                <li class="tab" data-tab="advertisement">Advertisement Queries</li>
                <li class="tab" data-tab="editorial">Editorial Collaborations</li>
                <li class="tab" data-tab="careers">Career Opportunities</li>
                <li class="tab" data-tab="susbscription">Subscription</li>
              </ul>
				  </div>
              <div class="row">
              <div class="contact-tabs-content">
				  <div id="address" class="tab-content active">
				      <h2>ENERGDIVE Insights and Market Intelligence </h2>
						  <h3><span style="font-size:17px;">A unit of</span> <strong>Clarisector Technologies Pvt. Ltd.</strong></h3>
                            <p>4th Floor, Janki House, Plot No. 33, Sector 12A, Dwarka, New Delhi 110075, India</p>
                </div>
                <div id="general" class="tab-content">
                  <h3>General Queries</h3>
                  <p>Have a question or need support? Reach out to us at <a href="mailto:info@energdive.com"><span>info@energdive.com</span></a> or Call Us <a href="tel:+91-11-45444425">+91-11-45444425</a></p>
					
                </div>

                <div id="advertisement" class="tab-content">
                  <h3>Advertisement Queries</h3>
                  <p>Looking to advertise on Energdive? Connect with the right audience in India’s energy ecosystem. We’d be glad to connect. Reach out to us at <a href="mailto:advertisement@energdive.com"><span>advertisement@energdive.com</span></a></p>
                </div>
                
                <div id="editorial" class="tab-content">
                  <h3>Editorial Collaborations</h3>
                  <p>For editorial collaborations with Energdive, reach out to discuss content and ideas. Connect with our editorial team at <a href="mailto:editorial@energdive.com"><span>editorial@energdive.com</span></a></p>
                </div>

                <div id="careers" class="tab-content">
                  <h3>Career Opportunities</h3>
                  <p>Looking to build your career with Energdive? Be part of a platform shaping India’s energy narrative. Explore opportunities to grow with us at <a href="mailto:career@energdive.com"><span>career@energdive.com</span></a></p>
                </div>
                
                <div id="susbscription" class="tab-content">
                  <h3>Subscription</h3>
                  <p>For Energdive subscriptions,
reach out to learn more about plans and access.
Connect with us to know more at <a href="mailto:subscription@energdive.com"><span>subscription@energdive.com</span></a></p>
                </div>

                
              </div>
					  </div>
          </div>
</section>


<script>
document.addEventListener('DOMContentLoaded', function () {
  const tabs = document.querySelectorAll('.contact-tabs-nav .tab');
  const contents = document.querySelectorAll('.tab-content');
  const section = document.querySelector('.contact-tabs-section');

  function activateTab(tabName, doScroll = false) {
    if (!tabName) return;
    const tab = document.querySelector(`.contact-tabs-nav .tab[data-tab="${tabName}"]`);
    const content = document.getElementById(tabName);
    if (!tab || !content) return;

    // remove active from all
    tabs.forEach(t => t.classList.remove('active'));
    contents.forEach(c => c.classList.remove('active'));

    // add active to selected
    tab.classList.add('active');
    content.classList.add('active');

    // update URL hash without jumping
    if (history.replaceState) {
      history.replaceState(null, '', `#${tabName}`);
    } else {
      // fallback
      location.hash = `#${tabName}`;
    }

    // optional: smooth scroll to the section when activated via hash or external link
    if (doScroll && section) {
      section.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }

  // click handlers for tabs
  tabs.forEach(tab => {
    tab.addEventListener('click', function () {
      activateTab(this.dataset.tab, false);
    });
  });

  // Activate tab from initial hash (if any) — scroll into view
  const initialHash = window.location.hash.replace('#', '');
  if (initialHash) {
    activateTab(initialHash, true);
  } else {
    // ensure a default tab is active (first tab) if none
    const first = tabs[0];
    if (first) activateTab(first.dataset.tab, false);
  }

  // respond to hash changes (back/forward or external links)
  window.addEventListener('hashchange', function () {
    const h = window.location.hash.replace('#', '');
    if (h) activateTab(h, true);
  });
});
</script>
</section>

<script>window.$zoho=window.$zoho || {};$zoho.salesiq=$zoho.salesiq||{ready:function(){}}</script><script id="zsiqscript" src="https://salesiq.zohopublic.in/widget?wc=siqe7427becac05b796f13e957c1acd50ed0f72f5df2fa22a28bf6688f5aef8ead2" defer></script>

<?php get_footer(); ?>

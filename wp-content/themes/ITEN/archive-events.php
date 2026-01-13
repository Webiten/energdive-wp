<?php

/* Template Name: Page Events */

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


.event-section {
  padding: 60px 20px;
  background: #fff;
  font-family: "Abhaya Libre", serif;
}

.event-card {
  max-width: 1200px;
  margin: auto;
  background: #ffffff;
  border-radius: 22px;
  padding: 25px 15px;
  display: grid;
  grid-template-columns: 220px 1fr 150px;
  gap: 30px;
  align-items: center;
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
  margin-bottom: 40px;
}

/* LEFT */
.event-left {
  text-align: center;
}

.event-logo {
  background: #f4f6f9;
  border-radius: 16px;
  padding: 10px;
  margin-bottom: 14px;
}

.event-logo img {
  width: 100%;
  max-width: 200px;
}

.event-date-big {
  font-size: 16px;
  font-weight: 600;
  color: #111;
}

/* CENTER */
.event-title {
  font-family: "Playfair Display", serif;
  font-size: 22px;
  font-weight: 700;
  margin: 0;
  padding:0;
  color: #000;
}

.event-desc {
  font-size: 15px;
  line-height: 1.3;
  color: #333;
  margin-bottom: 22px;
}

.event-meta {
  display: flex;
  gap: 40px;
  font-size: 15px;
  color: #444;
}

/* RIGHT */
.event-actions {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.btn {
  display: inline-block;
  padding: 4px 22px;
  border-radius: 28px;
  font-size: 14px;
  font-weight: 600;
  text-decoration: none;
  text-align: center;
  transition: all 0.25s ease;
}

.btn.primary {
  background: #ffffff;
  border: 1px solid #d9d9d9;
  color: #111;
}

.btn.primary:hover {
  background: #111;
  color: #fff;
}

.btn.secondary {
  background: #ffffff;
  border: 1px solid #d9d9d9;
  color: #111;
}

.btn.secondary:hover {
  background: #111;
  color: #fff;
}

/* RESPONSIVE */
@media (max-width: 992px) {
  .event-card {
    grid-template-columns: 1fr;
    text-align: center;
  }

  .event-meta {
    justify-content: center;
    flex-wrap: wrap;
    gap: 16px;
  }

  .event-actions {
    flex-direction: row;
    justify-content: center;
  }
}

@media (max-width: 576px) {
  .event-actions {
    flex-direction: column;
  }

  .event-title {
    font-size: 22px;
  }
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
 
 

<section class="event-section">
  <div class="event-card">

    <!-- LEFT -->
    <div class="event-left">
      <div class="event-logo">
        <img src="https://energ.energdive.com/wp-content/uploads/2025/12/energniti-logo-1.png" alt="Energniti Dialogue 2025">
      </div>
      <div class="event-date-big">18<sup>th</sup> December 2025</div>
    </div>

    <!-- CENTER -->
    <div class="event-content">
      <h2 class="event-title">EnergNiti Dialogue 2025</h2>

      <p class="event-desc">India’s energy journey is entering a new epoch — one defined not just by scale and self-reliance, but by ambition, innovation, and global stewardship. As the world’s fastest-growing major economy and home to 1.4 billion aspirations, India's energy narrative must evolve from merely meeting demand to leading the future. Through high-level keynotes, strategic dialogues, and focused sessions, this platform will unlock policy imperatives, investment directions, and collaborative pathways critical for India@2047 — where energy is not just a commodity, but a cornerstone of nation-building and diplomacy. This is not merely a conference. It is a national moment to accelerate India’s energy vision. Let this Dialogue spark the ideas, alliances, and resolve needed to power a cleaner, smarter, and more inclusive energy future.</p>

      <div class="event-meta">
        <span>
          📅 <strong>18th December 2025</strong>
        </span>
        <span>
          📍 <strong>Hyatt Regency, New Delhi</strong>
        </span>
      </div>
    </div>

    <!-- RIGHT -->
    <div class="event-actions">
      <a href="https://www.energniti.com/" target="_blank" class="btn primary">View Event</a>
      <!--<a href="#" class="btn secondary">Add to Calendar</a>-->
    </div>

  </div>
  
  <div class="event-card">

    <!-- LEFT -->
    <div class="event-left">
      <div class="event-logo">
        <img src="https://energ.energdive.com/wp-content/uploads/2025/12/iew-logo.png" alt="India Energy Week 2026">
      </div>
      <div class="event-date-big">27th - 30th January 2026</div>
    </div>

    <!-- CENTER -->
    <div class="event-content">
      <h2 class="event-title">India Energy Week 2026</h2>

      <p class="event-desc">Now in its 4th edition, India Energy Week will take place from 27 – 30 January 2026 in Goa, under the patronage of India’s Ministry of Petroleum and Natural Gas. As India strengthens its role at the heart of the global energy transformation, India Energy Week 2026 will unite policymakers, business leaders, innovators and investors to drive pragmatic solutions for a secure, sustainable and affordable energy future.</p>

      <div class="event-meta">
        <span>
          📅 <strong>27th - 30th January 2026</strong>
        </span>
        <span>
          📍 <strong>Goa, India</strong>
        </span>
      </div>
    </div>

    <!-- RIGHT -->
    <div class="event-actions">
      <a href="https://www.indiaenergyweek.com/" target="_blank" class="btn primary">View Event</a>
      <!--<a href="#" class="btn secondary">Add to Calendar</a>-->
    </div>

  </div>
  
  <div class="event-card">

    <!-- LEFT -->
    <div class="event-left">
      <div class="event-logo">
        <img src="https://energ.energdive.com/wp-content/uploads/2025/12/inpsc-logo-1.png" alt="International Process Safety Conference 2026">
      </div>
      <div class="event-date-big">26th February 2026</div>
    </div>

    <!-- CENTER -->
    <div class="event-content">
      <h2 class="event-title">International Process Safety Conference 2026</h2>

      <p class="event-desc">The seventh edition of the International Process Safety Conference (INPSC) convenes at a pivotal moment—when India’s energy and process industries are not just responding to transformation, but are poised to lead it. As our sectors embrace decarbonization, digitization, and decentralization, one imperative stands immutable: Process safety is national capacity. It is not merely a compliance function—it is the bedrock of public trust, industrial competitiveness, and strategic continuity. From the refinery to the boardroom, from regulatory corridors to plant operators, the question before us is not whether we can prevent the next incident—but whether we are building systems that make them unthinkable.</p>

      <div class="event-meta">
        <span>
          📅 <strong>26th February 2026</strong>
        </span>
        <span>
          📍 <strong>Hyatt Regency, New Delhi</strong>
        </span>
      </div>
    </div>

    <!-- RIGHT -->
    <div class="event-actions">
      <a href="https://www.inpsc.com/" target="_blank" class="btn primary">View Event</a>
      <!--<a href="#" class="btn secondary">Add to Calendar</a>-->
    </div>

  </div>
  
  <div class="event-card">

    <!-- LEFT -->
    <div class="event-left">
      <div class="event-logo">
        <img src="https://www.energdive.com/wp-content/uploads/2025/12/bsf-logo-1.png" alt="International Process Safety Conference 2026">
      </div>
      <div class="event-date-big">14th - 15th May 2026</div>
    </div>

    <!-- CENTER -->
    <div class="event-content">
      <h2 class="event-title">Bharat Fire Safety Congress 2026</h2>

      <p class="event-desc">Fire safety is no longer a reactive function—it is a cornerstone of national resilience, urban transformation, and sustainable development. As India moves decisively toward becoming a developed nation by 2047, the modernization and institutional strengthening of Fire Services must keep pace with the dynamic growth of our infrastructure, industries, and population. Over the past few years, the Government of India has made significant strides in streamlining policies, upgrading standards, and introducing the Model Fire Service Bill 2019, which aims to unify and empower fire services across states through comprehensive legislation. Yet, challenges remain. Wide variations in institutional capacity, fragmented compliance enforcement, lack of advanced training infrastructure, and emerging risks—from climate change-induced fire hazards to fires in high-density urban environments—demand a renewed national strategy.</p>

      <div class="event-meta">
        <span>
          📅 <strong>14th - 15th May 2026</strong>
        </span>
        <span>
          📍 <strong>Yashobhoomi IICC, Dwarka, New Delhi, India</strong>
        </span>
      </div>
    </div>

    <!-- RIGHT -->
    <div class="event-actions">
      <a href="https://www.bharatfiresafety.com/" target="_blank" class="btn primary">View Event</a>
      <!--<a href="#" class="btn secondary">Add to Calendar</a>-->
    </div>

  </div>
  
  <div class="event-card">

    <!-- LEFT -->
    <div class="event-left">
      <div class="event-logo">
        <img src="https://energ.energdive.com/wp-content/uploads/2025/12/grpc-logo-1.png" alt="International Process Safety Conference 2026">
      </div>
      <div class="event-date-big">25th - 26th June 2026</div>
    </div>

    <!-- CENTER -->
    <div class="event-content">
      <h2 class="event-title">Global Refining & Petrochemicals Congress (GRPC) 2026</h2>

      <p class="event-desc">The world is entering a decisive decade for energy — one that will determine how nations grow, industries compete, and societies sustain themselves in an era defined by climate responsibility and technological disruption. Amidst this transformation, India’s refining and petrochemicals sector stands as both anchor and architect of the country’s energy future — powering growth while enabling the transition to a resilient, low-carbon economy.</p>

      <div class="event-meta">
        <span>
          📅 <strong>25th - 26th June 2026</strong>
        </span>
        <span>
          📍 <strong>Le Méridien New Delhi</strong>
        </span>
      </div>
    </div>

    <!-- RIGHT -->
    <div class="event-actions">
      <a href="https://www.refpet.com/" target="_blank" class="btn primary">View Event</a>
      <!--<a href="#" class="btn secondary">Add to Calendar</a>-->
    </div>

  </div>
  
  <div class="event-card">

    <!-- LEFT -->
    <div class="event-left">
      <div class="event-logo">
        <img src="https://energ.energdive.com/wp-content/uploads/2025/12/hse-logo-1.png" alt="International Process Safety Conference 2026">
      </div>
      <div class="event-date-big">06th - 07th August 2026</div>
    </div>

    <!-- CENTER -->
    <div class="event-content">
      <h2 class="event-title">Transform HSE 2026</h2>

      <p class="event-desc">Welcome to Transform HSE, where we set our sights on "The Next Frontier: Advancing HSE to Achieve Global SDGs" a bold and forward-thinking theme that reflects the urgency and potential of Health, Safety, and Environment (HSE) in the global sustainability journey. As we convene this year, the stakes have never been higher. The accelerating pace of industrial transformation, the complexities of climate change, and the dynamic interplay of social and economic challenges demand a paradigm shift in how we approach HSE. Beyond safeguarding operations, HSE is emerging as a central enabler for achieving the United Nations's Sustainable Development Goals (SDGs), shaping the future of industries and societies alike.</p>

      <div class="event-meta">
        <span>
          📅 <strong>06th - 07th August 2026</strong>
        </span>
        <span>
          📍 <strong>Hyatt Regency, New Delhi</strong>
        </span>
      </div>
    </div>

    <!-- RIGHT -->
    <div class="event-actions">
      <a href="https://www.transformhse.com/" target="_blank" class="btn primary">View Event</a>
      <!--<a href="#" class="btn secondary">Add to Calendar</a>-->
    </div>

  </div>
  
  <div class="event-card">

    <!-- LEFT -->
    <div class="event-left">
      <div class="event-logo">
        <img src="https://energ.energdive.com/wp-content/uploads/2025/12/bharat-electricity-logo.png" alt="International Process Safety Conference 2026">
      </div>
      <div class="event-date-big">01st - 03rd September 2026</div>
    </div>

    <!-- CENTER -->
    <div class="event-content">
      <h2 class="event-title">Bharat Electricity 2026</h2>

      <p class="event-desc">In September 2026, the energy sector supply chain will come together at Bharat Electricity, POWERGEN India & Indian Utility Week. Co-located at the Yashobhoomi, IICC, Dwarka in New Delhi, they will form one fully integrated event, underpinned by a high-level Strategic Summit and Exhibition over 3 days.</p>

      <div class="event-meta">
        <span>
          📅 <strong>01st - 03rd September 2026</strong>
        </span>
        <span>
          📍 <strong>Yashobhoomi IICC, Dwarka, New Delhi, India</strong>
        </span>
      </div>
    </div>

    <!-- RIGHT -->
    <div class="event-actions">
      <a href="https://www.powergen-india.com/" target="_blank" class="btn primary">View Event</a>
      <!--<a href="#" class="btn secondary">Add to Calendar</a>-->
    </div>

  </div>
  
  <div class="event-card">

    <!-- LEFT -->
    <div class="event-left">
      <div class="event-logo">
        <img src="https://energ.energdive.com/wp-content/uploads/2025/12/osi-logo.png" alt="International Process Safety Conference 2026">
      </div>
      <div class="event-date-big">06th - 07th October 2026</div>
    </div>

    <!-- CENTER -->
    <div class="event-content">
      <h2 class="event-title">Oil Spill India 2026</h2>

      <p class="event-desc">Welcome to the 8th edition of Oil Spill India (OSI), set against the vibrant backdrop of New Delhi during 6th - 7th October 2026. Since its inception in 2011, OSI—with foundational leadership from the Indian Coast Guard and ONGC Limited—has evolved into one of the world’s leading forums dedicated to Spill Prevention, Planning, Preparedness, Response, and Restoration.</p>

      <div class="event-meta">
        <span>
          📅 <strong>06th - 07th October 2026</strong>
        </span>
        <span>
          📍 <strong>Hotel JW Marriott, Aerocity, Delhi</strong>
        </span>
      </div>
    </div>

    <!-- RIGHT -->
    <div class="event-actions">
      <a href="https://oilspillindia.org/" target="_blank" class="btn primary">View Event</a>
      <!--<a href="#" class="btn secondary">Add to Calendar</a>-->
    </div>

  </div>
</section>


</section>







<?php get_footer(); ?>

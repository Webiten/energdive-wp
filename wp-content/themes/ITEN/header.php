<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php wp_title('|', true, 'right'); ?></title>
    <meta name="robots" content="INDEX,FOLLOW">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/assets/img/favicon.png">

    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">

    <!-- Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<!-- TOPBAR -->
<div class="topbar">
  <div class="inner">
    <div class="topbar-left">
      <a href="#"><i class="fab fa-facebook-f"></i></a>
      <a href="#"><i class="fab fa-x-twitter"></i></a>
      <a href="#"><i class="fab fa-youtube"></i></a>
      <a href="#"><i class="fab fa-instagram"></i></a>
      <a href="#"><i class="fab fa-linkedin-in"></i></a>
    </div>

    <div class="topbar-right">
      <a href="#" class="adv-link"><i class="fas fa-bullhorn"></i> Advertise With US</a>
    </div>
  </div>
</div>


<!-- HEADER -->
<header class="main-header" id="siteHeader" role="banner">
  <div class="header-inner">


    <!-- LEFT MENU (Dynamic) -->
    <nav class="menu-left" aria-label="Main navigation (left)">
      <?php
        wp_nav_menu([
            'theme_location' => 'left_menu',
            'menu_class'     => 'header-menu',
            'container'      => false
        ]);
      ?>
    </nav>


    <!-- LOGO (Center) -->
    <div class="logo-center">
      <a href="<?php echo home_url(); ?>">
        <img src="https://energ.energdive.com/wp-content/uploads/2025/10/EnergDive-Logo-in-Black-copy.png"
             alt="<?php bloginfo('name'); ?>">
      </a>
    </div>


    <!-- RIGHT MENU (Dynamic) -->
    <nav class="menu-right" aria-label="Main navigation (right)">
      <?php
        wp_nav_menu([
            'theme_location' => 'right_menu',
            'menu_class'     => 'header-menu',
            'container'      => false
        ]);
      ?>

      <div style="display:flex;align-items:center;gap:8px">
        <div class="search-icon" id="searchBtn" title="Search"><i class="fas fa-search"></i></div>
        <div class="mobile-menu-btn" id="mobileBtn"><i class="fas fa-bars"></i></div>
      </div>
    </nav>

  </div>


  <!-- SEARCH DROPDOWN -->
  <div class="search-dropdown" id="searchDropdown" role="region" aria-label="Search">
    <form action="<?php echo home_url('/'); ?>" method="GET">
      <input type="text" name="s" placeholder="Search..." aria-label="Search input">
    </form>
  </div>


  <!-- MOBILE MENU (Dynamic) -->
  <div class="mobile-menu" id="mobileMenu" aria-hidden="true">
    <?php
      wp_nav_menu([
          'theme_location' => 'mobile_menu',
          'menu_class'     => 'mobile-nav',
          'container'      => false
      ]);
    ?>
  </div>
</header>
<style>
    /* -------------------------
   TOPBAR
------------------------- */
.topbar{
  background:#000;
  color:#fff;
  font-size:13px;
}
.topbar .inner{
  max-width:var(--container-width);
  margin:0 auto;
  display:flex;
  align-items:center;
  justify-content:space-between;
  padding:6px 16px;
}
.topbar .inner a{color:#fff;text-decoration:none;margin-right:12px;opacity:.9}
.topbar .inner .topbar-left a{margin-right:14px}

/* -------------------------
   HEADER LAYOUT (center logo)
------------------------- */
.main-header{
  position:relative;
  top:0;left:0;right:0;
  z-index:9999;
  background:var(--white);
  border-bottom:1px solid #eee;
  /* ensure positioned parent so absolute dropdown positions correctly */
}
.main-header.stuck{
  position:sticky;
  top:0;
  left:0;
  right:0;
  z-index:99999;
  background:#fff;
  box-shadow:0 2px 12px rgba(0,0,0,.08);
}

.header-inner{
  position:relative;              /* important for stacking */
  max-width:var(--container-width);
  margin:0 auto;
  display:flex;
  align-items:center;
  justify-content:space-between;
  padding:12px 18px;
  gap:12px;
}

/* left / center / right layout */
.menu-left, .menu-right { display:flex; align-items:center; gap:8px; flex:1; }
.logo-center { display:flex; align-items:center; justify-content:center; flex:0 0 auto; z-index:5; margin: 0px 30px; padding: 0 10px;}

/* center logo size */
.logo-center img{ height:42px; display:block; }

/* desktop menu lists */
.header-menu{ margin:0;padding:0; list-style:none; display:flex; gap:5px; align-items:center; }
.header-menu > li { position:relative; z-index:10; } /* ensure menu items sit above logo */
.header-menu a{ text-decoration:none; color:var(--black); font-size:16px; text-transform:none; display:inline-block; padding:8px 2px; transition:color var(--transition-fast) ease; }
.header-menu a:hover{ color:var(--accent) }

/* dropdown (desktop) */
.header-menu .sub-menu{
  position:absolute;
  top:calc(100% + 12px);
  left:0;
  min-width:200px;
  background:#fff;
  border:1px solid #eee;
  box-shadow:0 6px 20px rgba(8,10,12,.08);
  padding:8px 0;
  border-radius:6px;
  opacity:0;
  list-style:none;
  visibility:hidden;
  transform:translateY(8px) scale(.99);
  transition:all 220ms ease;
  z-index:99999;           /* ensure dropdown is on top */
}

/* visible states:
   - :hover for desktop
   - .open added by JS for click/touch
*/
.header-menu li:hover > .sub-menu,
.header-menu li.open > .sub-menu,
.header-menu li.focus > .sub-menu{
  opacity:1;
  visibility:visible;
  transform:translateY(0) scale(1);
  pointer-events:auto;
}

.header-menu .sub-menu li{ padding:0; }
.header-menu .sub-menu a{ display:block; padding:8px 16px; color:#111; font-size:15px; }

/* search icon */
.search-icon{ cursor:pointer; padding:8px; margin-left:8px; color:var(--black); display:flex; align-items:center; }

/* mobile menu button */
.mobile-menu-btn{ display:none; cursor:pointer; padding:8px; font-size:20px; }

/* -------------------------
   SEARCH DROPDOWN
------------------------- */
.search-dropdown{
  position:absolute;
  right:18px;
  top:100%;
  margin-top:8px;
  background:#fff;
  border:1px solid #e6e6e6;
  padding:8px;
  border-radius:8px;
  box-shadow:0 8px 30px rgba(0,0,0,.06);
  width:320px;
  max-width:calc(100% - 40px);
  display:none;
  z-index:999999;
}
.search-dropdown.active{ display:block; }
.search-dropdown form{ display:flex; gap:8px; }
.search-dropdown input{ width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:6px; font-size:15px }

/* -------------------------
   MOBILE MENU (slide down)
------------------------- */
.mobile-menu{
  display:none;
  position:absolute;
  left:0; right:0; top:100%;
  background:#fff;
  border-top:1px solid #eee;
  z-index:9998;
  box-shadow: 0 8px 40px rgba(0,0,0,.06);
  max-height:0; overflow:hidden; transition:max-height 300ms ease;
}
.mobile-menu.open{ display:block; max-height:90vh; overflow:auto; }

/* mobile nav styles */
.mobile-menu .mobile-nav{ list-style:none; margin:0; padding:12px; }
.mobile-menu .mobile-nav li{ border-bottom:1px solid #f1f1f1; padding:10px 6px; }
.mobile-menu a{ text-decoration:none; color:#111; display:block; padding:8px 6px; }

/* mobile dropdown toggles */
.mob-sub > a{ display:flex; justify-content:space-between; align-items:center; }
.mob-sub-menu{ display:none; padding-left:12px; margin-top:6px; }
.mob-sub.open > .mob-sub-menu{ display:block; }

/* -------------------------
   RESPONSIVE
------------------------- */
@media (max-width: 992px){
  .header-inner{ padding:10px 12px; }
  /* left & right hide except icon */
  .menu-left { display:none; }
  .menu-right .header-menu > li { display:none; } /* hide right items on small screens */
  .mobile-menu-btn{ display:block; }
  .logo-center{ flex:1; justify-content:center; }
  .menu-right{ justify-content:flex-end; gap:6px; }
  /* place search icon visible on right */
  .search-icon{ margin-left:6px; }
}

/* smaller screens */
@media (max-width:600px){
  .logo-center img{ height:36px; }
  .search-dropdown{ right:8px; left:8px; width:auto; }
}

/* small polish */
.header-menu .sub-menu a:hover{ background:#fafafa; color:var(--accent) }
.header-inner .header-menu > li > a { letter-spacing: .02em; }

/* keyboard visible state styling */
.header-menu li.focus > a{ color:var(--accent); }
</style>
<script>
document.addEventListener('DOMContentLoaded', function(){

  const searchBtn = document.getElementById('searchBtn');
  const searchDropdown = document.getElementById('searchDropdown');
  const searchInput = document.getElementById('searchInput');
  const mobileBtn = document.getElementById('mobileBtn');
  const mobileMenu = document.getElementById('mobileMenu');
  const header = document.getElementById('siteHeader');

  // Toggle search dropdown
  searchBtn.addEventListener('click', function(e){
    e.stopPropagation();
    // close mobile menu if open
    mobileMenu.classList.remove('open');
    searchDropdown.classList.toggle('active');
    if(searchDropdown.classList.contains('active')){
      setTimeout(()=> searchInput.focus(), 60);
    }
  });

  // Prevent clicks INSIDE the dropdown from closing it
  searchDropdown.addEventListener('click', function(e){ e.stopPropagation(); });

  // Close search if click outside or Escape
  document.addEventListener('click', function(){ searchDropdown.classList.remove('active'); });
  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape'){ searchDropdown.classList.remove('active'); mobileMenu.classList.remove('open'); closeAllDropdowns(); }
  });

  // MOBILE MENU toggle
  mobileBtn.addEventListener('click', function(e){
    e.stopPropagation();
    searchDropdown.classList.remove('active');
    mobileMenu.classList.toggle('open');
  });

  // Close mobile menu when clicking outside (only on small screens)
  document.addEventListener('click', function(){
    if(window.innerWidth <= 992){
      mobileMenu.classList.remove('open');
    }
  });

  // Prevent close when clicking inside mobile menu
  mobileMenu.addEventListener('click', function(e){ e.stopPropagation(); });

  // Mobile dropdown expand/collapse
  document.querySelectorAll('.mob-dropdown').forEach(function(li){
    li.addEventListener('click', function(e){
      // allow clicking inner links normally
      if(e.target.tagName.toLowerCase() === 'a' && e.target.getAttribute('href') && e.target.getAttribute('href') !== '#') {
        return; // allow navigation
      }
      this.classList.toggle('open');
    });
  });

  /* ------------------------
     Desktop: make dropdowns usable via click too
     - toggle .open on parent <li> when anchor clicked
     - fallback for touch devices
  ------------------------ */
  const desktopDropdownLinks = document.querySelectorAll('.header-menu > li.dropdown > a');

  function closeAllDropdowns(){
    document.querySelectorAll('.header-menu li.open').forEach(li => {
      li.classList.remove('open');
      const a = li.querySelector('a');
      if(a) a.setAttribute('aria-expanded','false');
    });
  }

  desktopDropdownLinks.forEach(function(anchor){
    const parentLi = anchor.parentElement;

    // Prevent default for anchors that are '#'
    anchor.addEventListener('click', function(e){
      // Only intercept clicks on small/medium screens or if the user wants to toggle explicitly
      const isSmall = window.innerWidth <= 992;
      // If page navigation uses real URL (not '#'), allow it
      const href = anchor.getAttribute('href') || '';
      const isHash = href.trim() === '#';

      // If small screen OR href is '#' (act as toggle), toggle submenu
      if (isSmall || isHash) {
        e.preventDefault();
        e.stopPropagation();
        const isOpen = parentLi.classList.contains('open');
        closeAllDropdowns();
        if(!isOpen){
          parentLi.classList.add('open');
          anchor.setAttribute('aria-expanded','true');
        } else {
          parentLi.classList.remove('open');
          anchor.setAttribute('aria-expanded','false');
        }
      }
      // otherwise let hover handle it on desktop
    });

    // Also close on mouseleave so hover still works
    parentLi.addEventListener('mouseleave', function(){
      parentLi.classList.remove('open');
      anchor.setAttribute('aria-expanded','false');
    });


    // keyboard focus accessibility
    parentLi.addEventListener('focusin', function(){
      parentLi.classList.add('focus');
    });
    parentLi.addEventListener('focusout', function(){
      parentLi.classList.remove('focus');
    });
  });

  // Close dropdowns on outside click (desktop)
  document.addEventListener('click', function(){
    closeAllDropdowns();
  });

  // Sticky header: add class 'stuck' when scroll past top
  window.addEventListener('scroll', function(){
    const st = window.scrollY || window.pageYOffset;
    if(st > 10){
      header.classList.add('stuck');
    } else {
      header.classList.remove('stuck');
    }
  });

  // On resize, ensure menus are closed to avoid stuck states
  window.addEventListener('resize', function(){
    mobileMenu.classList.remove('open');
    searchDropdown.classList.remove('active');
    closeAllDropdowns();
  });

});
</script>
<?php
defined('ABSPATH') || exit;
?>

<header class="energ-topbar">
    <div class="energ-top-left">
        <button id="energ-menu-toggle" class="energ-menu-btn">☰</button>
        <div class="energ-search">
            <input type="search" placeholder="Search..." id="energ-search-input">
        </div>
    </div>

    <div class="energ-top-right">
        <button class="energ-icon-btn" id="energ-notifications">🔔</button>

        <div class="energ-profile">
            <span class="energ-profile-name"><?php echo esc_html( $member->first_name ?: $member->phone ); ?></span>
            <button class="energ-logout" id="energ-logout">Logout</button>
        </div>
    </div>
</header>
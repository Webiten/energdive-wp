<!----------------------  CSS  -------------------------->
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap');

body, .aurora-auth-card * {
    font-family: "Inter", sans-serif !important;
}

/* MAIN LAYOUT */
.aurora-auth-wrapper {
    display: flex;
    min-height: 90vh;
}

.aurora-auth-left {
    width: 55%;
}

.aurora-auth-left img {
    width: 100%;
    height: 100vh;
    object-fit: cover;
}

.aurora-auth-card {
    width: 45%;
    padding: 60px 75px;
    background: #fff;
}

/* HEADINGS */
.auth-title {
    font-size: 30px;
    font-weight: 600;
}

.auth-subtext {
    font-size: 14px;
    margin-bottom: 25px;
    color: #777;
}

.social-btn {
    width: 100%;
    padding: 13px;
    border-radius: 10px;
    background: #fff;
    border: 1px solid #d1d5db;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
    cursor: pointer;
}

.social-btn img {
    width: 20px;
}

/* DIVIDER */
.divider-wrap {
    display: flex;
    align-items: center;
    margin: 25px 0;
}

.divider-line {
    flex: 1;
    height: 1px;
    background: #e5e7eb;
}

.divider-text {
    padding: 0 12px;
    font-size: 14px;
    color: #888;
}

/* INPUTS */
.aurora-auth-card input {
    width: 100%;
    padding: 14px;
    border: 1px solid #cfd3d9;
    background: #f9fafb;
    border-radius: 10px;
    margin-bottom: 15px;
    font-size: 15px;
}

/* MAIN BUTTON */
.main-btn {
    width: 100%;
    padding: 14px;
    background: #0066ff;
    color: white;
    border-radius: 10px;
    font-size: 16px;
    border: none;
    font-weight: 600;
    cursor: pointer;
}

.main-btn:hover {
    background: #0057db;
}

/* LOGIN TABS */
.auth-tabs {
    display: flex;
    gap: 25px;
    margin-bottom: 20px;
}

.auth-tab {
    font-size: 16px;
    padding-bottom: 8px;
    cursor: pointer;
    border-bottom: 2px solid transparent;
}

.auth-tab.active {
    border-bottom: 2px solid #0066ff;
    font-weight: 600;
}

/* OTP vs PASSWORD toggle */
.mode-toggle {
    display: flex;
    justify-content: flex-start;
    gap: 20px;
    margin-bottom: 15px;
}

.mode-option {
    cursor: pointer;
    font-size: 14px;
    padding-bottom: 5px;
    border-bottom: 2px solid transparent;
    color: #555;
}

.mode-option.active {
    color: #0066ff;
    border-bottom: 2px solid #0066ff;
}

.resend-btn {
    background: none;
    border: none;
    color: #0066ff;
    margin-top: 8px;
    cursor: pointer;
}
</style>




<!----------------------  HTML  -------------------------->
<div class="aurora-auth-wrapper">

    <div class="aurora-auth-left">
        <img src="https://energ.energdive.com/wp-content/uploads/2025/11/mt-sample-background.jpg">
    </div>

    <div class="aurora-auth-card">

        <div class="auth-title">Log in</div>

        <div class="divider-wrap">
            <div class="divider-line"></div>
            <div class="divider-text">or use phone / email</div>
            <div class="divider-line"></div>
        </div>

        <!-- MAIN TABS (Phone / Email) -->
        <div class="auth-tabs">
            <div class="auth-tab active" data-tab="phone-section">Phone Login</div>
            <div class="auth-tab" data-tab="email-section">Email Login</div>
        </div>



        <!-----------------------------------------
                PHONE LOGIN SECTION
        ------------------------------------------>
        <div id="phone-section" class="auth-tab-content">

            <!-- MODE TOGGLE -->
            <div class="mode-toggle">
                <div class="mode-option active" data-mode="phone-otp">OTP Login</div>
            </div>

            <!-- PHONE FIELD -->
            <input id="phone_input" placeholder="+91xxxxxxxxxx">

            <!-- OTP MODE -->
            <div id="phone-otp-box">
                <button class="main-btn" id="send-otp-btn">Send OTP</button>

                <div id="phone-otp-step2" style="display:none;">
                    <input id="phone_otp" placeholder="Enter OTP">
                    <button class="main-btn" id="phone-verify-otp">Verify & Continue</button>
                    <button class="resend-btn">Resend OTP</button>
                </div>
            </div>

        </div>




        <!-----------------------------------------
                EMAIL LOGIN SECTION
        ------------------------------------------>
        <div id="email-section" class="auth-tab-content" style="display:none;">

            <div class="mode-toggle">
                <div class="mode-option active" data-mode="email-otp">OTP Login</div>
            </div>

            <input id="email_input" placeholder="name@example.com">

            <!-- EMAIL OTP -->
            <div id="email-otp-box">
                <button class="main-btn" id="send-email-otp">Send OTP</button>

                <div id="email-otp-step2" style="display:none;">
                    <input id="email_otp" placeholder="Enter OTP">
                    <button class="main-btn" id="email-verify-otp">Verify & Continue</button>
                </div>
            </div>

        </div>

    </div>
</div>




<!----------------------  JS  -------------------------->
<script>
jQuery(function($){

    /* MAIN PHONE / EMAIL TAB SWITCH */
    $(".auth-tab").on("click", function(){
        $(".auth-tab").removeClass("active");
        $(this).addClass("active");

        $(".auth-tab-content").hide();
        $("#" + $(this).data("tab")).show();
    });

    /* MODE TOGGLE (OTP / PASSWORD) */
    $(".mode-option").on("click", function(){
        let mode = $(this).data("mode");
        $(this).siblings().removeClass("active");
        $(this).addClass("active");

        if(mode === "phone-otp"){
            $("#phone-pass-box").hide();
            $("#phone-otp-box").show();
        }
        if(mode === "phone-pass"){
            $("#phone-otp-box").hide();
            $("#phone-pass-box").show();
        }
        if(mode === "email-otp"){
            $("#email-pass-box").hide();
            $("#email-otp-box").show();
        }
        if(mode === "email-pass"){
            $("#email-otp-box").hide();
            $("#email-pass-box").show();
        }
    });

});
</script>
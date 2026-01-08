<?php
defined('ABSPATH') || exit;

$phone = sanitize_text_field($_GET['phone'] ?? '');
$nonce = wp_create_nonce('energ_nonce');
?>
<div class="energ-complete-wrap" style="max-width:980px;margin:30px auto;padding:28px;border-radius:10px;background:#fff;box-shadow:0 6px 30px rgba(0,0,0,0.06);font-family:Inter,Arial,sans-serif;">

  <h1 style="margin:0 0 18px;font-size:28px;">Complete your registration</h1>

  <input type="hidden" id="energ_nonce" value="<?php echo esc_attr($nonce); ?>">
  <input type="hidden" id="cr_phone" value="<?php echo esc_attr($phone); ?>">
  <input type="hidden" id="cr_email_otp_verified" value="">

  <!-- STEP 1: EMAIL VERIFICATION -->
  <div id="step_email" style="margin-bottom:18px;">
    <label style="display:block;font-weight:700;margin-bottom:8px;">Email</label>
    <div style="display:flex;gap:12px;align-items:center;">
      <input id="cr_email" type="email" placeholder="you@example.com" style="flex:1;padding:12px;border:1px solid #dcdcdc;border-radius:8px;font-size:15px;">
      <button id="btn_send_email_otp" style="background:#d23a2a;color:#fff;border:0;padding:10px 14px;border-radius:8px;font-weight:600;cursor:pointer;">
        Send OTP
      </button>
    </div>

    <div id="email_otp_box" style="display:none;margin-top:12px;">
      <label style="font-weight:700;margin-bottom:6px;display:block;">Enter OTP</label>
      <div style="display:flex;gap:12px;align-items:center;">
        <input id="cr_email_otp" placeholder="Enter OTP" style="flex:1;padding:11px;border:1px solid #dcdcdc;border-radius:8px;font-size:15px;">
        <button id="btn_verify_email_otp" style="background:#1677ff;color:#fff;border:0;padding:10px 14px;border-radius:8px;font-weight:600;cursor:pointer;">
          Verify Email
        </button>
      </div>
    </div>

    <div id="step1_msg" style="margin-top:10px;font-weight:600;color:#d23a2a;"></div>
  </div>

  <!-- STEP 2: FULL FORM -->
  <div id="step_full_form" style="display:none;">

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
      <!-- Salutation -->
      <label style="display:block;">
        <div style="font-weight:600;margin-bottom:6px">Salutation</div>
        <select id="cr_salutation" style="width:100%;padding:11px;border:1px solid #dcdcdc;border-radius:8px;">
          <option value="">Select title</option>
          <option>Mr.</option>
          <option>Ms.</option>
          <option>Mrs.</option>
          <option>Dr.</option>
          <option>Prof.</option>
        </select>
      </label>

      <!-- First name -->
      <label>
        <div style="font-weight:600;margin-bottom:6px">First name</div>
        <input id="cr_first" style="width:100%;padding:11px;border:1px solid #dcdcdc;border-radius:8px;">
      </label>

      <!-- Last name -->
      <label>
        <div style="font-weight:600;margin-bottom:6px">Last name</div>
        <input id="cr_last" style="width:100%;padding:11px;border:1px solid #dcdcdc;border-radius:8px;">
      </label>

      <!-- Phone (readonly) -->
      <label>
        <div style="font-weight:600;margin-bottom:6px">Mobile number</div>
        <input id="cr_phone_display" readonly value="<?php echo esc_attr($phone); ?>" style="width:100%;padding:11px;border:1px solid #eee;border-radius:8px;background:#f6f6f6;">
      </label>

      <!-- Email (readonly) -->
      <label style="grid-column:1 / 2;">
        <div style="font-weight:600;margin-bottom:6px">Email</div>
        <input id="cr_email_final" readonly style="width:100%;padding:11px;border:1px solid #eee;border-radius:8px;background:#f6f6f6;">
      </label>

      <!-- Company -->
      <label>
        <div style="font-weight:600;margin-bottom:6px">Company name</div>
        <input id="cr_org" style="width:100%;padding:11px;border:1px solid #dcdcdc;border-radius:8px;">
      </label>

      <!-- Job title -->
      <label>
        <div style="font-weight:600;margin-bottom:6px">Designation</div>
        <input id="cr_designation" list="designations_list" style="width:100%;padding:11px;border:1px solid #dcdcdc;border-radius:8px;">
        <datalist id="designations_list">
          <option>Researcher</option><option>Analyst</option><option>Editor</option>
          <option>Manager</option><option>Director</option><option>VP</option>
          <option>CEO</option><option>Engineer</option><option>Consultant</option>
        </datalist>
      </label>

      <!-- Country -->
      <label>
        <div style="font-weight:600;margin-bottom:6px">Country</div>
        <select id="cr_country" style="width:100%;padding:11px;border:1px solid #dcdcdc;border-radius:8px;">
          <option value="">Select Country</option>
          <option>India</option>
          <option>United States</option>
          <option>United Kingdom</option>
          <option>Australia</option>
          <option>Canada</option>
          <option>Germany</option>
          <option>France</option>
          <option>Singapore</option>
        </select>
      </label>

    </div>

    <!-- Separator -->
    <div style="height:20px;"></div>

    <!-- Community / Sub-Community (Zoho-style multi-select) -->
    <div style="margin-bottom:12px;">
      <div style="display:flex;align-items:center;justify-content:space-between;">
        <div style="font-weight:700;margin-bottom:8px">Community / Sub-Community (Sector)</div>
        <div style="font-size:13px;color:#666">Select one or more sectors</div>
      </div>

      <div id="sector_dropdown" class="sector-dropdown">
        <div id="sector_input" class="sector-input" tabindex="0" style="border:1px solid #84c8b6;padding:12px;border-radius:8px;background:#fff;cursor:pointer;">
          <span id="sector_placeholder" style="color:#777">- Select -</span>
          <div id="sector_tags" style="display:inline-block;margin-left:8px;"></div>
        </div>

        <div id="sector_panel" class="sector-panel" style="display:none;">
          <div style="padding:10px;">
            <input id="sector_search" placeholder="Search sectors..." style="width:100%;padding:8px;border:1px solid #eee;border-radius:6px;margin-bottom:10px;">
            <div id="sector_list" style="max-height:360px;overflow:auto;"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Industry & Sub Industry -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:6px;">
      <label>
        <div style="font-weight:600;margin-bottom:6px">Industry</div>
        <select id="cr_industry" style="width:100%;padding:11px;border:1px solid #dcdcdc;border-radius:8px;">
          <option value="">Loading…</option>
        </select>
      </label>

      <label>
        <div style="font-weight:600;margin-bottom:6px">Sub Industry</div>
        <select id="cr_sub_industry" style="width:100%;padding:11px;border:1px solid #dcdcdc;border-radius:8px;">
          <option value="">Select industry first</option>
        </select>
      </label>
    </div>

    <!-- Final button -->
    <div style="margin-top:20px;">
      <button id="btn_submit_final" style="background:#1f9d4f;color:#fff;border:0;padding:12px 18px;border-radius:8px;font-weight:700;cursor:pointer;">
        Complete Registration
      </button>
      <div id="final_msg" style="margin-top:10px;font-weight:600;color:#d23a2a;"></div>
    </div>

  </div>
</div>

<style>
/* Panel styling (simple) */
.sector-dropdown { position:relative; max-width:100%; }
.sector-panel { position:absolute; left:0; right:0; top:56px; z-index:60; background:#fff; border:1px solid #e6e6e6; border-radius:8px; box-shadow:0 10px 30px rgba(0,0,0,0.08); }
.sector-parent { padding:10px;border-bottom:1px solid #f3f3f3; }
.sector-parent strong{ display:block;margin-bottom:8px; }
.sector-child { display:inline-block;margin-right:14px;margin-bottom:8px; }
.sector-tag { display:inline-block;background:#e7f5ef;color:#0b6b3f;padding:4px 8px;border-radius:14px;margin-right:6px;font-size:13px; }
</style>

<script>
jQuery(function($){

  const ajaxUrl = "<?php echo admin_url('admin-ajax.php'); ?>";
  const nonce = $("#energ_nonce").val();



  /* -------------------------
     SEND OTP
  --------------------------*/
  $("#btn_send_email_otp").click(function(){
    let email = $("#cr_email").val().trim();
    let phone = $("#cr_phone").val().trim(); // hidden input

    if(!email){ $("#step1_msg").text("Enter email"); return; }

    $.post(ajaxUrl,{
      action:"energ_send_email_otp",
      nonce, email, phone
    },function(res){
      if(res.success){
        $("#step1_msg").css("color","green").text("OTP sent!");
        $("#email_otp_box").show();
      } else {
        $("#step1_msg").css("color","#d23a2a").text(res.data);
      }
    });
  });



  /* -------------------------
     VERIFY OTP
  --------------------------*/
  $("#btn_verify_email_otp").click(function(){
    let email = $("#cr_email").val().trim();
    let otp = $("#cr_email_otp").val().trim();
    let phone = $("#cr_phone").val().trim();

    if(!otp){
      $("#step1_msg").text("Enter OTP");
      return;
    }

    $.post(ajaxUrl,{
      action:"energ_verify_email_otp",
      nonce, email, otp, phone
    },function(res){
      if(res.success){

        // STORE VERIFIED OTP
        $("#cr_email_otp_verified").val(otp);

        $("#step1_msg").css("color","green").text("Email verified!");
        $("#step_email").hide();
        $("#step_full_form").show();
        $("#cr_email_final").val(email);

        loadSectors();
        loadIndustries();
      } else {
        $("#step1_msg").css("color","#d23a2a").text(res.data || "Invalid OTP");
      }
    });
  });



  /* -------------------------
     LOAD INDUSTRY
  --------------------------*/
  function loadIndustries(){
    $.post(ajaxUrl,{action:"energ_get_industry_terms",nonce},function(res){
      if(res.success){
        let html = '<option value="">Select industry</option>';
        res.data.forEach(i => html += `<option value="${i.term_id}">${i.name}</option>`);
        $("#cr_industry").html(html);
      }
    });
  }

  $("#cr_industry").change(function(){
    let parent = $(this).val();
    if(!parent){
      $("#cr_sub_industry").html('<option>Select industry first</option>');
      return;
    }
    $.post(ajaxUrl,{
      action:"energ_get_sub_terms",
      nonce,
      taxonomy:"industry",
      parent
    },function(res){
      if(res.success){
        let h = '<option value="">Select sub industry</option>';
        res.data.forEach(s => h += `<option value="${s.term_id}">${s.name}</option>`);
        $("#cr_sub_industry").html(h);
      }
    });
  });



  /* -------------------------
     SECTOR MULTISELECT
  --------------------------*/
  let selectedParents = new Set();
  let selectedChildren = new Set();

  function loadSectors(){
    $.post(ajaxUrl,{
      action:"energ_get_sector_terms",
      nonce
    },function(res){
      if(res.success){
        let html = "";
        res.data.forEach(group => {
          html += `<div class="sector-parent"><strong>${group.parent.name}</strong>`;
          group.children.forEach(c => {
            html += `<label class="sector-child">
                      <input type="checkbox"
                             class="sector-check"
                             data-parent="${group.parent.name}"
                             data-child="${c.name}">
                      ${c.name}
                     </label>`;
          });
          html += `</div>`;
        });
        $("#sector_list").html(html);
      }
    });
  }

  $("#sector_input").click(function(){
    $("#sector_panel").toggle();
  });

  $(document).on("change",".sector-check",function(){
    let parent = $(this).data("parent");
    let child = $(this).data("child");

    if(this.checked){
      selectedParents.add(parent);
      selectedChildren.add(child);
    } else {
      selectedChildren.delete(child);
      if($(`.sector-check[data-parent='${parent}']:checked`).length===0){
        selectedParents.delete(parent);
      }
    }
    renderSectorTags();
  });

  function renderSectorTags(){
    const wrap = $("#sector_tags").empty();
    if(selectedParents.size === 0 && selectedChildren.size === 0){
      $("#sector_placeholder").show();
      return;
    }
    $("#sector_placeholder").hide();
    selectedParents.forEach(p => wrap.append(`<span class="sector-tag">${p}</span>`));
    selectedChildren.forEach(c => wrap.append(`<span class="sector-tag">${c}</span>`));
  }



  /* -------------------------
     SUBMIT FINAL
  --------------------------*/
  $("#btn_submit_final").click(function(){

    const first = $("#cr_first").val().trim();
    const last = $("#cr_last").val().trim();
    const email = $("#cr_email_final").val().trim();
    const phone = $("#cr_phone").val().trim() || $("#cr_phone_display").val().trim();
    const org = $("#cr_org").val().trim();
    const designation = $("#cr_designation").val().trim();
    const country = $("#cr_country").val().trim();
    const salutation = $("#cr_salutation").val().trim();
    const industry = $("#cr_industry").val().trim();
    const sub_industry = $("#cr_sub_industry").val().trim();
    const otp = $("#cr_email_otp_verified").val().trim();

    const parents = Array.from(selectedParents);
    const children = Array.from(selectedChildren);

    /* VALIDATIONS */
    if(!otp){
      $("#final_msg").text("Email OTP missing — verify again.");
      return;
    }

    if(!first || !last || !email || !org || !designation || !country){
      $("#final_msg").text("All fields are required.");
      return;
    }

    if(parents.length === 0 || children.length === 0){
      $("#final_msg").text("Select at least 1 community & sub community.");
      return;
    }

    if(!industry || !sub_industry){
      $("#final_msg").text("Select industry & sub industry.");
      return;
    }

    /* SUBMIT */
    $.post(ajaxUrl,{
      action:"energ_complete_registration",
      nonce,
      phone,
      email,
      email_otp: otp,
      first_name:first,
      last_name:last,
      salutation,
      country,
      organization:org,
      designation,
      community: parents.join(","),
      sub_community: children.join(","),
      industry,
      sub_industry
    },function(res){
      if(res.success){
        window.location.href = res.data.redirect;
      } else {
        $("#final_msg").text(res.data || "Failed");
      }
    });

  });


});
</script>

<?php
// parts/account.php
// expects $member, $member_comm_id, $member_subcomm_id, $member_ind_id, $member_subind_id in scope
?>
<div class="account-wrapper">
    <div class="account-menu">
        <h3>Account Settings</h3>
        <div class="account-item" onclick="showAccTab('personal', this)"><i class="bx bx-user"></i> Personal Information</div>
        <div class="account-item" onclick="showAccTab('work', this)"><i class="bx bxs-graduation"></i> Work & Education</div>
        <div class="account-item" onclick="showAccTab('privacy', this)"><i class="bx bx-lock-alt"></i> Privacy & Protection</div>
        <div class="account-item" onclick="showAccTab('notifications', this)"><i class="bx bx-bell"></i> Notifications & Alerts</div>
    </div>

    <div class="account-content">
        <!-- PERSONAL -->
        <div id="acc_tab_personal" class="acc-tab">
            <h2 class="account-section-title">Personal Information</h2>

            <div class="account-box">
                <div class="account-grid">
                    <div>
                        <label>Salutation</label>
                        <select id="acc_salutation" class="form-select">
                            <option value="">Select</option>
                            <option value="Mr." <?php selected($member->salutation ?? '', 'Mr.'); ?>>Mr.</option>
                            <option value="Ms." <?php selected($member->salutation ?? '', 'Ms.'); ?>>Ms.</option>
                            <option value="Mrs." <?php selected($member->salutation ?? '', 'Mrs.'); ?>>Mrs.</option>
                            <option value="Dr." <?php selected($member->salutation ?? '', 'Dr.'); ?>>Dr.</option>
                        </select>
                    </div>

                    <div>
                        <label>Country</label>
                        <input id="acc_country" class="form-control" type="text" value="<?php echo esc_attr($member->country); ?>">
                    </div>
                </div>
            </div>

            <div class="account-box">
                <div class="account-grid">
                    <div>
                        <label>First Name</label>
                        <input id="acc_firstname" class="form-control" type="text" value="<?php echo esc_attr($member->first_name); ?>">
                    </div>
                    <div>
                        <label>Last Name</label>
                        <input id="acc_lastname" class="form-control" type="text" value="<?php echo esc_attr($member->last_name); ?>">
                    </div>
                </div>
            </div>

            <div class="account-box">
                <div class="account-grid">
                    <div>
                        <label>Phone Number</label>
                        <input id="acc_phone" class="form-control" type="text" value="<?php echo esc_attr($member->phone); ?>">
                    </div>
                    <div>
                        <label>Email</label>
                        <input id="acc_email" class="form-control" type="email" value="<?php echo esc_attr($member->email); ?>">
                    </div>
                </div>
            </div>

            <div class="account-box">
                <div class="account-grid">
                    <div>
                        <label>Community (Sector)</label>
                        <select id="acc_community_personal" class="form-select">
                            <option value="">Select Community</option>
                            <?php
                            $parents = get_terms(['taxonomy'=>'sector','hide_empty'=>false,'parent'=>0]);
                            foreach ($parents as $p): ?>
                                <option value="<?php echo esc_attr($p->term_id); ?>" <?php selected($member_comm_id, $p->term_id); ?>><?php echo esc_html($p->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label>Sub Community</label>
                        <select id="acc_sub_community_personal" class="form-select">
                            <option value="">Select Sub Community</option>
                            <?php
                            if ($member_comm_id) {
                                $children = get_terms(['taxonomy'=>'sector','hide_empty'=>false,'parent'=>$member_comm_id]);
                                foreach ($children as $c): ?>
                                    <option value="<?php echo esc_attr($c->term_id); ?>" <?php selected($member_subcomm_id, $c->term_id); ?>><?php echo esc_html($c->name); ?></option>
                                <?php endforeach;
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <button id="acc_save_personal" class="btn btn-primary mt-2">Save Personal</button>
        </div>

        <!-- WORK -->
        <div id="acc_tab_work" class="acc-tab" style="display:none;">
            <h2 class="account-section-title">Work & Education</h2>

            <div class="account-box">
                <div class="account-grid">
                    <div>
                        <label>Organization</label>
                        <input id="acc_organization" class="form-control" type="text" value="<?php echo esc_attr($member->organization); ?>">
                    </div>

                    <div>
                        <label>Designation</label>
                        <input id="acc_designation" class="form-control" type="text" value="<?php echo esc_attr($member->designation); ?>">
                    </div>
                </div>
            </div>

            <div class="account-box">
                <div class="account-grid">
                    <div>
                        <label>Industry</label>
                        <select id="acc_industry_work" class="form-select">
                            <option value="">Select Industry</option>
                            <?php
                            $ind_parents = get_terms(['taxonomy'=>'industry','hide_empty'=>false,'parent'=>0]);
                            foreach ($ind_parents as $ip): ?>
                                <option value="<?php echo esc_attr($ip->term_id); ?>" <?php selected($member_ind_id, $ip->term_id); ?>><?php echo esc_html($ip->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label>Sub Industry</label>
                        <select id="acc_sub_industry_work" class="form-select">
                            <option value="">Select Sub Industry</option>
                            <?php
                            if ($member_ind_id) {
                                $ind_children = get_terms(['taxonomy'=>'industry','hide_empty'=>false,'parent'=>$member_ind_id]);
                                foreach ($ind_children as $ic): ?>
                                    <option value="<?php echo esc_attr($ic->term_id); ?>" <?php selected($member_subind_id, $ic->term_id); ?>><?php echo esc_html($ic->name); ?></option>
                                <?php endforeach;
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="account-box">
                <div class="account-grid">
                    <div>
                        <label>Community (Sector) — (Work)</label>
                        <select id="acc_community_work" class="form-select">
                            <option value="">Select Community</option>
                            <?php foreach ($parents as $p): ?>
                                <option value="<?php echo esc_attr($p->term_id); ?>" <?php selected($member_comm_id, $p->term_id); ?>><?php echo esc_html($p->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label>Sub Community (Work)</label>
                        <select id="acc_sub_community_work" class="form-select">
                            <option value="">Select Sub Community</option>
                            <?php
                            if ($member_comm_id) {
                                $children = get_terms(['taxonomy'=>'sector','hide_empty'=>false,'parent'=>$member_comm_id]);
                                foreach ($children as $c): ?>
                                    <option value="<?php echo esc_attr($c->term_id); ?>" <?php selected($member_subcomm_id, $c->term_id); ?>><?php echo esc_html($c->name); ?></option>
                                <?php endforeach;
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <button id="acc_save_work" class="btn btn-primary mt-2">Save Work</button>
        </div>
    </div>
</div>

<script>
function showAccTab(tab, el) {
    document.querySelectorAll('.acc-tab').forEach(t => t.style.display = 'none');
    var target = document.getElementById('acc_tab_' + tab);
    if (target) target.style.display = 'block';
    document.querySelectorAll('.account-item').forEach(i => i.classList.remove('active'));
    if (el) el.classList.add('active');
}
document.addEventListener('DOMContentLoaded', function(){
    var first = document.querySelector('.account-item');
    if (first) {
        first.classList.add('active');
        showAccTab('personal', first);
    }
});
</script>

<script>
jQuery(function($){
    // helper: fetch child terms via ajax
    function loadSubTerms(taxonomy, parentId, $target, placeholder){
        placeholder = placeholder || 'Select';
        $target.html('<option value="">Loading…</option>');
        $.post(typeof ENERG_AJAX !== 'undefined' ? ENERG_AJAX.ajax_url : '<?php echo admin_url('admin-ajax.php'); ?>', {
            action: 'energ_get_sub_terms',
            nonce: (typeof ENERG_AJAX !== 'undefined' ? ENERG_AJAX.nonce : ''),
            taxonomy: taxonomy,
            parent: parentId
        }, function(res){
            if (res && res.success && Array.isArray(res.data)) {
                var html = '<option value="">' + placeholder + '</option>';
                res.data.forEach(function(t){ html += '<option value="'+ t.term_id +'">'+ t.name +'</option>'; });
                $target.html(html);
            } else {
                $target.html('<option value="">' + placeholder + '</option>');
            }
        }, 'json').fail(function(){ $target.html('<option value="">' + placeholder + '</option>'); });
    }

    $('#acc_community_personal').on('change', function(){ loadSubTerms('sector', $(this).val(), $('#acc_sub_community_personal'), 'Select Sub Community'); });
    $('#acc_community_work').on('change', function(){ loadSubTerms('sector', $(this).val(), $('#acc_sub_community_work'), 'Select Sub Community'); });
    $('#acc_industry_work').on('change', function(){ loadSubTerms('industry', $(this).val(), $('#acc_sub_industry_work'), 'Select Sub Industry'); });

    // Save personal
    $('#acc_save_personal').on('click', function(e){
        e.preventDefault();
        var payload = {
            action: 'energ_save_account',
            nonce: (typeof ENERG_AJAX !== 'undefined' ? ENERG_AJAX.nonce : ''),
            member_id: "<?php echo (int)$member->id; ?>",
            first_name: $('#acc_firstname').val(),
            last_name: $('#acc_lastname').val(),
            email: $('#acc_email').val(),
            phone: $('#acc_phone').val(),
            salutation: $('#acc_salutation').val(),
            country: $('#acc_country').val(),
            community: $('#acc_community_personal').val() || '',
            sub_community: $('#acc_sub_community_personal').val() || ''
        };
        $.post(typeof ENERG_AJAX !== 'undefined' ? ENERG_AJAX.ajax_url : '<?php echo admin_url('admin-ajax.php'); ?>', payload, function(res){
            if (res && res.success) {
                alert('Personal info saved.');
                location.reload();
            } else {
                alert('Save failed: ' + (res.data || 'unknown'));
            }
        }, 'json').fail(function(xhr){ alert('Request failed: ' + xhr.status); });
    });

    // Save work
    $('#acc_save_work').on('click', function(e){
        e.preventDefault();
        var payload = {
            action: 'energ_save_account',
            nonce: (typeof ENERG_AJAX !== 'undefined' ? ENERG_AJAX.nonce : ''),
            member_id: "<?php echo (int)$member->id; ?>",
            organization: $('#acc_organization').val(),
            designation: $('#acc_designation').val(),
            industry: $('#acc_industry_work').val() || '',
            sub_industry: $('#acc_sub_industry_work').val() || '',
            community: $('#acc_community_work').val() || '',
            sub_community: $('#acc_sub_community_work').val() || ''
        };
        $.post(typeof ENERG_AJAX !== 'undefined' ? ENERG_AJAX.ajax_url : '<?php echo admin_url('admin-ajax.php'); ?>', payload, function(res){
            if (res && res.success) {
                alert('Work info saved.');
                location.reload();
            } else {
                alert('Save failed: ' + (res.data || 'unknown'));
            }
        }, 'json').fail(function(xhr){ alert('Request failed: ' + xhr.status); });
    });

});
</script>

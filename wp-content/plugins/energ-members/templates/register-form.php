<div id="energ-register">
  <form id="energ-register-form">
    <input type="hidden" name="redirect" value="<?php echo esc_attr($_SERVER['REQUEST_URI'] ?? '/dashboard'); ?>" />
    <div id="step1">
      <h3>Create account</h3>
      <input name="username" required placeholder="Username" />
      <input name="email" type="email" required placeholder="Email" />
      <input name="first_name" placeholder="First name" />
      <input name="last_name" placeholder="Last name" />
      <input name="dob" type="date" placeholder="DOB" />
      <input name="phone" placeholder="Phone (with country code)" />
      <button type="button" id="to-choices">Next</button>
    </div>

    <div id="choices" style="display:none">
      <h4>Choose</h4>
      <button type="button" data-mode="quick" class="choiceBtn">Quick Signup</button>
      <button type="button" data-mode="full" class="choiceBtn">Full Signup</button>
    </div>

    <div id="step2" style="display:none">
      <h4>Community</h4>
      <select name="community">
        <option value="">--Select--</option>
        <option value="oil_and_gas">Power & Oil & Gas</option>
        <option value="power_and_utilities">Power & Utilities</option>
        <option value="new_energies">New Energies</option>
        <option value="sustainability">Sustainability</option>
      </select>
      <select name="sub_community">
        <option value="">--Select--</option>
      </select>
      <button type="button" id="final-signup">Sign up</button>
    </div>
  </form>
  <div id="energ-msg"></div>
</div>
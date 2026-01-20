<div id="energ-register">
  <form id="energ-register-form">

    <!-- REQUIRED FOR AJAX -->
    <input type="hidden" name="action" value="energ_register">
    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('energ_nonce'); ?>">
    <input type="hidden" name="mode" id="signup_mode" value="quick">

    <input type="hidden" name="redirect" value="<?php echo esc_attr($_SERVER['REQUEST_URI'] ?? '/dashboard'); ?>" />

    <!-- STEP 1 -->
    <div id="step1">
      <h3>Create account</h3>

      <input name="username" placeholder="Username" />
      <input name="email" type="email" required placeholder="Email" />
      <input name="first_name" placeholder="First name" />
      <input name="last_name" placeholder="Last name" />
      <input name="dob" type="date" placeholder="DOB" />
      <input name="phone" placeholder="Phone (with country code)" />

      <button type="button" id="to-choices">Next</button>
    </div>

    <!-- STEP 1.5 -->
    <div id="choices" style="display:none">
      <h4>Choose Signup Type</h4>

      <button type="button" data-mode="quick" class="choiceBtn">
        Quick Signup
      </button>

      <button type="button" data-mode="full" class="choiceBtn">
        Full Signup
      </button>
    </div>

    <!-- STEP 2 -->
    <div id="step2" style="display:none">
      <h4>Select Community</h4>

      <!-- MULTI-SELECT READY -->
      <select name="community[]" multiple required>

  <!-- OIL & GAS -->
  <option value="Oil & Gas-Upstream">Oil & Gas-Upstream</option>
  <option value="Oil & Gas-Pipelines">Oil & Gas-Pipelines</option>
  <option value="Oil & Gas-Refining">Oil & Gas-Refining</option>
  <option value="Oil & Gas-Petrochemicals">Oil & Gas-Petrochemicals</option>
  <option value="Oil & Gas-CGD">Oil & Gas-CGD</option>
  <option value="Oil & Gas-LPG">Oil & Gas-LPG</option>
  <option value="Oil & Gas-Retail">Oil & Gas-Retail</option>
  <option value="Oil & Gas-Oil Markets">Oil & Gas-Oil Markets</option>

  <!-- POWER -->
  <option value="Power Generation-Thermal">Power Generation-Thermal</option>
  <option value="Power Generation-Nuclear">Power Generation-Nuclear</option>

  <!-- RENEWABLES -->
  <option value="Renewables-Solar">Renewables-Solar</option>
  <option value="Renewables-Wind">Renewables-Wind</option>
  <option value="Renewables-Hydro">Renewables-Hydro</option>
  <option value="Renewables-Biopower">Renewables-Biopower</option>
  <option value="Renewables-Cogeneration">Renewables-Cogeneration</option>
  <option value="Renewables-Waste-to-Energy">Renewables-Waste-to-Energy</option>

  <!-- TRANSMISSION / DISTRIBUTION -->
  <option value="Transmission-Smart Grid">Transmission-Smart Grid</option>
  <option value="Distribution-Smart Meters & AMI">Distribution-Smart Meters & AMI</option>
  <option value="Distribution-EV Charging">Distribution-EV Charging</option>
  <option value="Distribution-Data Centres">Distribution-Data Centres</option>
  <option value="Distribution-Smart Cities">Distribution-Smart Cities</option>
  <option value="Distribution-Railways & Metros">Distribution-Railways & Metros</option>

  <!-- MARKETS -->
  <option value="Electricity Markets-Power Markets">Electricity Markets-Power Markets</option>
  <option value="Electricity Markets-Carbon Markets">Electricity Markets-Carbon Markets</option>
  <option value="Electricity Markets-RCO">Electricity Markets-RCO</option>

  <!-- NEW ENERGIES -->
  <option value="New Energies-Green Hydrogen">New Energies-Green Hydrogen</option>
  <option value="New Energies-E-Fuels">New Energies-E-Fuels</option>

  <!-- ENERGY STORAGE -->
  <option value="Energy Storage Systems-BESS">Energy Storage Systems-BESS</option>
  <option value="Energy Storage Systems-Pumped Hydro">Energy Storage Systems-Pumped Hydro</option>
  <option value="Energy Storage Systems-CAES">Energy Storage Systems-CAES</option>
  <option value="Energy Storage Systems-Thermal">Energy Storage Systems-Thermal</option>
  <option value="Energy Storage Systems-Flywheel">Energy Storage Systems-Flywheel</option>

  <!-- SUSTAINABILITY -->
  <option value="Sustainability-Energy Efficiency">Sustainability-Energy Efficiency</option>
  <option value="Sustainability-Occupational Health">Sustainability-Occupational Health</option>
  <option value="Sustainability-Industrial & Process Safety">Sustainability-Industrial & Process Safety</option>
  <option value="Sustainability-Environment">Sustainability-Environment</option>

</select>
      <button type="submit" id="final-signup">Sign up</button>
    </div>

  </form>

  <div id="energ-msg"></div>
</div>

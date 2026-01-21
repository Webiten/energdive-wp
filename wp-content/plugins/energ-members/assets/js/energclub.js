(function () {
  if (!window.ENERGCLUB_DATA) return;

  const comm = document.getElementById("energclub_community");
  const subComm = document.getElementById("energclub_sub_community");
  const ind = document.getElementById("energclub_industry");
  const subInd = document.getElementById("energclub_sub_industry");
  const areaInput = document.getElementById("energclub_area_of_industry");

  const communities = window.ENERGCLUB_DATA.communities || {};
  const industries = window.ENERGCLUB_DATA.industries || {};
  const areaMapEndpoint = window.ENERGCLUB_DATA.areaMapEndpoint;

  function fillSelect(select, optionsObj, placeholder) {
    select.innerHTML = "";
    const opt0 = document.createElement("option");
    opt0.value = "";
    opt0.textContent = placeholder;
    select.appendChild(opt0);

    Object.keys(optionsObj).forEach((k) => {
      const opt = document.createElement("option");
      opt.value = k;
      opt.textContent = optionsObj[k];
      select.appendChild(opt);
    });
  }

  function setDisabled(select, disabled, placeholder) {
    select.disabled = disabled;
    if (disabled) {
      select.innerHTML = `<option value="">${placeholder}</option>`;
    }
  }

  async function updateArea(community, subCommunity) {
    if (!areaInput) return;

    // local fallback (no ajax)
    areaInput.value = "Mapping...";
    try {
      const form = new FormData();
      form.append("community", community);
      form.append("sub_community", subCommunity);

      const res = await fetch(areaMapEndpoint, { method: "POST", body: form });
      const json = await res.json();
      if (json && json.success) areaInput.value = json.data.area;
      else areaInput.value = "Intelligence: General";
    } catch (e) {
      areaInput.value = "Intelligence: General";
    }
  }

  if (comm && subComm) {
    setDisabled(subComm, true, "Select community first");

    comm.addEventListener("change", () => {
      const c = comm.value;
      if (!c || !communities[c]) {
        setDisabled(subComm, true, "Select community first");
        if (areaInput) areaInput.value = "";
        return;
      }
      const subs = communities[c].subs || {};
      setDisabled(subComm, false, "Select");
      fillSelect(subComm, subs, "Select");
      if (areaInput) areaInput.value = "";
    });

    subComm.addEventListener("change", () => {
      if (!comm.value || !subComm.value) return;
      updateArea(comm.value, subComm.value);
    });
  }

  if (ind && subInd) {
    setDisabled(subInd, true, "Select industry first");

    ind.addEventListener("change", () => {
      const i = ind.value;
      if (!i || !industries[i]) {
        setDisabled(subInd, true, "Select industry first");
        return;
      }
      const subs = industries[i].subs || {};
      setDisabled(subInd, false, "Select");
      fillSelect(subInd, subs, "Select");
    });
  }
})();

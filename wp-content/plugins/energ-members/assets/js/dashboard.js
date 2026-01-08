(function(){
  document.addEventListener('DOMContentLoaded', function(){
    var toggle = document.getElementById('energ-menu-toggle');
    var sidebar = document.querySelector('.energ-sidebar');
    if (toggle && sidebar) {
      toggle.addEventListener('click', function(){
        sidebar.classList.toggle('energ-collapsed');
      });
    }

    // Logout handler uses your existing ajax action "energ_logout"
    var logout = document.getElementById('energ-logout');
    if (logout) {
      logout.addEventListener('click', function(){
        var data = new FormData();
        data.append('action','energ_logout');
        data.append('nonce', ENERG_DASH.nonce);

        fetch(ENERG_DASH.ajax_url, { method: 'POST', body: data })
          .then(r=>r.json())
          .then(function(res){
            if (res && res.success) window.location.href = '/login';
          }).catch(function(){ window.location.href = '/login'; });
      });
    }

    // quick search: jump to saved tab if query provided (optional)
    var q = new URLSearchParams(window.location.search).get('q');
    if (q) {
      var input = document.getElementById('energ-search-input');
      if (input) input.value = q;
    }
  });
})();

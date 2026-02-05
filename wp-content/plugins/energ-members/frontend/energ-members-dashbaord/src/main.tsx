import React from "react";
import ReactDOM from "react-dom/client";
import App from "./app/App";
import "./styles/index.css";

const dashboardRoot = document.getElementById("energ-members-root");
const authRoot = document.getElementById("energ-auth-root");

/* DASHBOARD */
if (dashboardRoot) {
  ReactDOM.createRoot(dashboardRoot).render(
    <React.StrictMode>
      {/* Added 'relative' and 'isolate' 
          'isolate' creates a new CSS stacking context so elements don't overlap 
      */}
      <div className="min-h-screen w-full bg-white text-gray-900 relative isolate">
        <App />
      </div>
    </React.StrictMode>
  );
}

/* AUTH / LOGIN */
if (authRoot) {
  ReactDOM.createRoot(authRoot).render(
    <React.StrictMode>
      <App />
    </React.StrictMode>
  );
}


/* ===========================================
   🔥 ELEMENTOR POPUP SUPPORT (NEW ADDITION)
   =========================================== */

function remountDashboardForPopup() {
  const dashboardRoot =
    document.getElementById("energ-members-root") ||
    document.querySelector('[data-energ-popup="true"]');

  if (!dashboardRoot) {
    console.warn("Energ dashboard root not found in popup yet.");
    return;
  }

  // Prevent double mounting
  if ((dashboardRoot as any)._reactMounted) return;
  (dashboardRoot as any)._reactMounted = true;

  ReactDOM.createRoot(dashboardRoot).render(
    <React.StrictMode>
      <div className="min-h-screen w-full bg-white text-gray-900 relative isolate">
        <App />
      </div>
    </React.StrictMode>
  );
}

// 👉 Run again when Elementor popup opens
document.addEventListener("elementor/popup/show", remountDashboardForPopup);

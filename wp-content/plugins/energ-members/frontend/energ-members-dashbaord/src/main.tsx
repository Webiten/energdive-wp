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

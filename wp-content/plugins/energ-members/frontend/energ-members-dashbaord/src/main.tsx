import { createRoot } from "react-dom/client";
import App from "./app/App";
import "./styles/index.css";

const container = document.getElementById("energ-dashboard-root");

if (!container) {
  console.warn("[ENERG] Root container not found");
} else {
  // 🛑 Prevent double-mount (WP reload / duplicate script issue)
  if ((window as any).__ENERG_REACT_MOUNTED__) {
    console.warn("[ENERG] React already mounted");
  } else {
    (window as any).__ENERG_REACT_MOUNTED__ = true;

    console.log("[ENERG] Mounting React Dashboard");

    createRoot(container).render(<App />);
  }
}

import { createRoot } from "react-dom/client";
import App from "./app/App";
import "./styles/index.css";

const mountEl =
  document.getElementById("energ-dashboard-root") ||
  document.getElementById("root"); // fallback for local dev

if (mountEl) {
  createRoot(mountEl).render(<App />);

  // debug flag
  (window as any).__ENERG_REACT_MOUNTED__ = true;
  console.log("✅ ENERG React mounted");
} else {
  console.error("❌ ENERG root element not found");
}

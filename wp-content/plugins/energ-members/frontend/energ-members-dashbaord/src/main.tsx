import { createRoot } from "react-dom/client";
import App from "./app/App";
import "./styles/index.css";

declare global {
  interface Window {
    __ENERG_REACT_MOUNTED__?: boolean;
  }
}

console.log("[ENERG] main.tsx loaded");

const rootEl = document.getElementById("energ-dashboard-root");

if (!rootEl) {
  console.warn("[ENERG] root element not found");
} else if (window.__ENERG_REACT_MOUNTED__) {
  console.warn("[ENERG] React already mounted");
} else {
  window.__ENERG_REACT_MOUNTED__ = true;
  console.log("[ENERG] Mounting React");

  const root = createRoot(rootEl);
  root.render(<App />);
}

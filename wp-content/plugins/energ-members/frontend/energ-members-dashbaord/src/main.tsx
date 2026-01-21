import { createRoot } from "react-dom/client";
import App from "./App";
import "./styles/index.css";

const el = document.getElementById("energ-dashboard-root");

if (el) {
  createRoot(el).render(<App />);
  (window as any).__ENERG_REACT_MOUNTED__ = true;
}

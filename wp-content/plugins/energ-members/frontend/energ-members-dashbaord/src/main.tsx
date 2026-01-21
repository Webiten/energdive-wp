import { createRoot } from "react-dom/client";
import App from "./app/App";
import "./styles/index.css";

const root = document.getElementById("energ-dashboard-root");

if (root) {
  createRoot(root).render(<App />);
  (window as any).__ENERG_REACT_MOUNTED__ = true;
} else {
  console.error("ENERG: Root div not found");
}

import { createRoot } from "react-dom/client";
import App from "./app/App";
import "./styles/index.css";

const el = document.getElementById("energ-dashboard-root");

if (el) {
  createRoot(el).render(<App />);
}

import { createRoot } from "react-dom/client";
import App from "./app/App";
import "./styles/index.css";

const container = document.getElementById("energ-dashboard-root");

if (container) {
  createRoot(container).render(<App />);
}

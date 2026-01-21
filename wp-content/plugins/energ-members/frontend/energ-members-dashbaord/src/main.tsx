import { createRoot } from "react-dom/client";
import App from "./app/App";
import "./styles/index.css";

declare global {
  interface Window {
    __ENERG_REACT_MOUNTED__?: boolean;
  }
}

const rootEl = document.getElementById("energ-dashboard-root");

if (rootEl && !window.__ENERG_REACT_MOUNTED__) {
  window.__ENERG_REACT_MOUNTED__ = true;

  const root = createRoot(rootEl);
  root.render(<App />);
}

import React from "react";
import ReactDOM from "react-dom/client";
import App from "./App.tsx";
import "default-passive-events";

ReactDOM.createRoot(document.getElementById("mabox_census_count")!).render(
  <React.StrictMode>
    <App />
  </React.StrictMode>
);
import { useState } from "react";
import Register from "./components/Register";
import Update from "./components/Update";
import ViewAll from "./components/ViewAll";
import "./App.css";

function App() {
  const [tab, setTab] = useState("register");

  return (
    <div className="app">
      <header>
        <h1>🎓 Student Registration System</h1>
        <span className="stack-badge">MERN Stack</span>
      </header>

      <div className="tabs">
        <button className={`tab-btn ${tab === "register" ? "active" : ""}`} onClick={() => setTab("register")}>📝 Register</button>
        <button className={`tab-btn ${tab === "update"   ? "active" : ""}`} onClick={() => setTab("update")}>✏️ Update</button>
        <button className={`tab-btn ${tab === "view"     ? "active" : ""}`} onClick={() => setTab("view")}>📋 View All</button>
      </div>

      <div className="content">
        {tab === "register" && <Register />}
        {tab === "update"   && <Update />}
        {tab === "view"     && <ViewAll />}
      </div>
    </div>
  );
}

export default App;
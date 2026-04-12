const express  = require("express");
const mongoose = require("mongoose");
const cors     = require("cors");

const app  = express();
const PORT = 5000;
const MONGO_URI = "mongodb://127.0.0.1:27017/studentdb";

// ── Middleware ────────────────────────────────────────
app.use(cors());
app.use(express.json());

// ── Routes ────────────────────────────────────────────
app.use("/api/students", require("./routes/students"));

// ── Connect to MongoDB & Start Server ─────────────────
mongoose.connect(MONGO_URI)
  .then(() => {
    console.log("✅ MongoDB connected!");
    app.listen(PORT, () => {
      console.log(`✅ Server running at http://localhost:${PORT}`);
      console.log(`\n  Endpoints:`);
      console.log(`    GET    http://localhost:${PORT}/api/students`);
      console.log(`    GET    http://localhost:${PORT}/api/students/:rollNo`);
      console.log(`    POST   http://localhost:${PORT}/api/students`);
      console.log(`    PUT    http://localhost:${PORT}/api/students/:rollNo`);
      console.log(`    DELETE http://localhost:${PORT}/api/students/:rollNo`);
    });
  })
  .catch((err) => console.log("❌ MongoDB connection error:", err));
const mongoose = require("mongoose");

const studentSchema = new mongoose.Schema({
  firstName:  { type: String, required: true, trim: true },
  lastName:   { type: String, required: true, trim: true },
  rollNo:     { type: String, required: true, unique: true, trim: true },
  password:   { type: String, required: true },
  contact:    { type: String, required: true },
}, { timestamps: true });

module.exports = mongoose.model("Student", studentSchema);
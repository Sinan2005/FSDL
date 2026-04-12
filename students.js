const express = require("express");
const router  = express.Router();
const bcrypt  = require("bcryptjs");
const Student = require("../models/Student");

// ── GET all students ──────────────────────────────────
router.get("/", async (req, res) => {
  try {
    const students = await Student.find().select("-password");
    res.json({ success: true, count: students.length, data: students });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── GET one student by rollNo ─────────────────────────
router.get("/:rollNo", async (req, res) => {
  try {
    const student = await Student.findOne({ rollNo: req.params.rollNo }).select("-password");
    if (!student) return res.status(404).json({ success: false, message: "Student not found" });
    res.json({ success: true, data: student });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── POST create new student ───────────────────────────
router.post("/", async (req, res) => {
  try {
    const { firstName, lastName, rollNo, password, confirmPassword, contact } = req.body;

    // Validation
    if (!firstName || !lastName || !rollNo || !password || !confirmPassword || !contact)
      return res.status(400).json({ success: false, message: "All fields are required" });

    if (!/^[A-Za-z ]+$/.test(firstName) || !/^[A-Za-z ]+$/.test(lastName))
      return res.status(400).json({ success: false, message: "Names must contain only letters" });

    if (password.length < 6)
      return res.status(400).json({ success: false, message: "Password must be at least 6 characters" });

    if (password !== confirmPassword)
      return res.status(400).json({ success: false, message: "Passwords do not match" });

    if (!/^[0-9]{10}$/.test(contact))
      return res.status(400).json({ success: false, message: "Contact must be exactly 10 digits" });

    // Check duplicate rollNo
    const existing = await Student.findOne({ rollNo });
    if (existing)
      return res.status(400).json({ success: false, message: `Roll No '${rollNo}' already exists` });

    // Hash password
    const hashed = await bcrypt.hash(password, 10);

    const student = await Student.create({ firstName, lastName, rollNo, password: hashed, contact });
    res.status(201).json({ success: true, message: "Student registered successfully", data: { ...student._doc, password: undefined } });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── PUT update student by rollNo ──────────────────────
router.put("/:rollNo", async (req, res) => {
  try {
    const { firstName, lastName, contact, newPassword, confirmPassword } = req.body;

    if (!firstName || !lastName || !contact)
      return res.status(400).json({ success: false, message: "First name, last name and contact are required" });

    if (!/^[A-Za-z ]+$/.test(firstName) || !/^[A-Za-z ]+$/.test(lastName))
      return res.status(400).json({ success: false, message: "Names must contain only letters" });

    if (!/^[0-9]{10}$/.test(contact))
      return res.status(400).json({ success: false, message: "Contact must be exactly 10 digits" });

    const updateData = { firstName, lastName, contact };

    if (newPassword) {
      if (newPassword.length < 6)
        return res.status(400).json({ success: false, message: "Password must be at least 6 characters" });
      if (newPassword !== confirmPassword)
        return res.status(400).json({ success: false, message: "Passwords do not match" });
      updateData.password = await bcrypt.hash(newPassword, 10);
    }

    const student = await Student.findOneAndUpdate(
      { rollNo: req.params.rollNo },
      updateData,
      { new: true }
    ).select("-password");

    if (!student)
      return res.status(404).json({ success: false, message: "Student not found" });

    res.json({ success: true, message: "Student updated successfully", data: student });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// ── DELETE student by rollNo ──────────────────────────
router.delete("/:rollNo", async (req, res) => {
  try {
    const student = await Student.findOneAndDelete({ rollNo: req.params.rollNo });
    if (!student)
      return res.status(404).json({ success: false, message: "Student not found" });
    res.json({ success: true, message: `Student '${req.params.rollNo}' deleted successfully` });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

module.exports = router;
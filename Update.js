import { useState } from "react";
import axios from "axios";

const API = "http://localhost:5000/api/students";

export default function Update() {
  const [searchRoll, setSearchRoll] = useState("");
  const [student,    setStudent]    = useState(null);
  const [form,       setForm]       = useState({});
  const [errors,     setErrors]     = useState({});
  const [alert,      setAlert]      = useState(null);

  const search = async () => {
    if (!searchRoll.trim()) return;
    try {
      const res = await axios.get(`${API}/${searchRoll}`);
      const s   = res.data.data;
      setStudent(s);
      setForm({ firstName: s.firstName, lastName: s.lastName, contact: s.contact, newPassword: "", confirmPassword: "" });
      setAlert(null); setErrors({});
    } catch {
      setStudent(null);
      setAlert({ type:"error", msg:`❌ No student found with Roll No: ${searchRoll}` });
    }
  };

  const handle = (e) => setForm({ ...form, [e.target.name]: e.target.value });

  const validate = () => {
    const e = {};
    if (!form.firstName.trim())                     e.firstName = "Required";
    else if (!/^[A-Za-z ]+$/.test(form.firstName)) e.firstName = "Letters only";
    if (!form.lastName.trim())                      e.lastName  = "Required";
    else if (!/^[A-Za-z ]+$/.test(form.lastName))  e.lastName  = "Letters only";
    if (!/^[0-9]{10}$/.test(form.contact))         e.contact   = "Must be 10 digits";
    if (form.newPassword && form.newPassword.length < 6)             e.newPassword     = "Min 6 characters";
    if (form.newPassword && form.newPassword !== form.confirmPassword) e.confirmPassword = "Passwords do not match";
    return e;
  };

  const update = async () => {
    const e = validate();
    if (Object.keys(e).length) { setErrors(e); return; }
    setErrors({});
    try {
      await axios.put(`${API}/${student.rollNo}`, form);
      setAlert({ type:"success", msg:"✅ Student updated successfully!" });
    } catch (err) {
      setAlert({ type:"error", msg:"❌ " + (err.response?.data?.message || "Server error") });
    }
  };

  const field = (name, label, type="text", placeholder="", readOnly=false) => (
    <div className="form-group">
      <label>{label}</label>
      <input
        type={type} name={name} value={form[name] ?? ""}
        placeholder={placeholder} onChange={handle}
        readOnly={readOnly}
        className={errors[name] ? "err" : ""}
      />
      {errors[name] && <span className="field-err">{errors[name]}</span>}
    </div>
  );

  return (
    <>
      <div className="card">
        <div className="card-title">🔍 Search Student by Roll No</div>
        <div className="del-row">
          <div className="form-group">
            <label>Roll No / ID</label>
            <input type="text" value={searchRoll} onChange={e => setSearchRoll(e.target.value)}
                   placeholder="e.g. CS2024001" style={{ width: 260 }} />
          </div>
          <button className="btn btn-warning" onClick={search}>🔍 Search</button>
        </div>
      </div>

      {alert && !student && <div className={`alert alert-${alert.type}`}>{alert.msg}</div>}

      {student && (
        <div className="card">
          <div className="card-title">✏️ Update Details — {student.firstName} {student.lastName}</div>
          {alert && <div className={`alert alert-${alert.type}`}>{alert.msg}</div>}
          <div className="form-grid">
            {field("firstName", "First Name *")}
            {field("lastName",  "Last Name *")}
            <div className="form-group">
              <label>Roll No (Read-only)</label>
              <input type="text" value={student.rollNo} readOnly />
            </div>
            {field("contact",         "Contact Number *",   "tel",      "10-digit mobile")}
            {field("newPassword",     "New Password",       "password", "Leave blank to keep current")}
            {field("confirmPassword", "Confirm Password",   "password", "Repeat new password")}
            <div className="form-group full" style={{ marginTop: 8, display:"flex", gap:12 }}>
              <button className="btn btn-primary" onClick={update}>💾 Save Changes</button>
              <button className="btn btn-outline" onClick={() => { setStudent(null); setSearchRoll(""); setAlert(null); }}>Cancel</button>
            </div>
          </div>
        </div>
      )}
    </>
  );
}
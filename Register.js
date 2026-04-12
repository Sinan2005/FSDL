import { useState } from "react";
import axios from "axios";

const API = "http://localhost:5000/api/students";

const init = { firstName:"", lastName:"", rollNo:"", password:"", confirmPassword:"", contact:"" };

export default function Register() {
  const [form,   setForm]   = useState(init);
  const [errors, setErrors] = useState({});
  const [alert,  setAlert]  = useState(null);

  const handle = (e) => setForm({ ...form, [e.target.name]: e.target.value });

  const validate = () => {
    const e = {};
    if (!form.firstName.trim())                        e.firstName = "Required";
    else if (!/^[A-Za-z ]+$/.test(form.firstName))    e.firstName = "Letters only";
    if (!form.lastName.trim())                         e.lastName  = "Required";
    else if (!/^[A-Za-z ]+$/.test(form.lastName))     e.lastName  = "Letters only";
    if (!form.rollNo.trim())                           e.rollNo    = "Required";
    if (form.password.length < 6)                      e.password  = "Min 6 characters";
    if (form.password !== form.confirmPassword)        e.confirmPassword = "Passwords do not match";
    if (!/^[0-9]{10}$/.test(form.contact))            e.contact   = "Must be 10 digits";
    return e;
  };

  const submit = async () => {
    const e = validate();
    if (Object.keys(e).length) { setErrors(e); return; }
    setErrors({});
    try {
      await axios.post(API, form);
      setAlert({ type:"success", msg:"✅ Student registered successfully!" });
      setForm(init);
    } catch (err) {
      setAlert({ type:"error", msg:"❌ " + (err.response?.data?.message || "Server error") });
    }
  };

  const field = (name, label, type="text", placeholder="") => (
    <div className="form-group">
      <label>{label} *</label>
      <input
        type={type} name={name} value={form[name]}
        placeholder={placeholder} onChange={handle}
        className={errors[name] ? "err" : ""}
      />
      {errors[name] && <span className="field-err">{errors[name]}</span>}
    </div>
  );

  return (
    <div className="card">
      <div className="card-title">New Student Registration</div>
      {alert && <div className={`alert alert-${alert.type}`}>{alert.msg}</div>}
      <div className="form-grid">
        {field("firstName",       "First Name",       "text",     "e.g. Ravi")}
        {field("lastName",        "Last Name",        "text",     "e.g. Sharma")}
        {field("rollNo",          "Roll No / ID",     "text",     "e.g. CS2024001")}
        {field("contact",         "Contact Number",   "tel",      "10-digit mobile")}
        {field("password",        "Password",         "password", "Min 6 characters")}
        {field("confirmPassword", "Confirm Password", "password", "Repeat password")}
        <div className="form-group full" style={{ marginTop: 8 }}>
          <button className="btn btn-primary" onClick={submit}>➕ Register Student</button>
        </div>
      </div>
    </div>
  );
}
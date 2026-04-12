import { useState, useEffect } from "react";
import axios from "axios";

const API = "http://localhost:5000/api/students";

export default function ViewAll() {
  const [students, setStudents] = useState([]);
  const [alert,    setAlert]    = useState(null);
  const [delRoll,  setDelRoll]  = useState("");

  const fetchAll = async () => {
    try {
      const res = await axios.get(API);
      setStudents(res.data.data);
    } catch {
      setAlert({ type:"error", msg:"❌ Could not fetch students. Is the server running?" });
    }
  };

  useEffect(() => { fetchAll(); }, []);

  const deleteStudent = async () => {
    if (!delRoll.trim()) { setAlert({ type:"error", msg:"❌ Please enter a Roll No." }); return; }
    if (!window.confirm(`Delete student with Roll No: ${delRoll}?`)) return;
    try {
      await axios.delete(`${API}/${delRoll}`);
      setAlert({ type:"success", msg:`✅ Student '${delRoll}' deleted successfully.` });
      setDelRoll("");
      fetchAll();
    } catch (err) {
      setAlert({ type:"error", msg:"❌ " + (err.response?.data?.message || "Student not found") });
    }
  };

  return (
    <>
      {/* Delete section */}
      <div className="card">
        <div className="card-title">🗑️ Delete Student by Roll No</div>
        {alert && <div className={`alert alert-${alert.type}`}>{alert.msg}</div>}
        <div className="del-row">
          <div className="form-group">
            <label>Roll No / ID</label>
            <input type="text" value={delRoll} onChange={e => setDelRoll(e.target.value)}
                   placeholder="Enter Roll No to delete" style={{ width: 260 }} />
          </div>
          <button className="btn btn-danger" onClick={deleteStudent}>🗑️ Delete Student</button>
        </div>
      </div>

      {/* Table */}
      <div className="card">
        <div className="card-title" style={{ display:"flex", justifyContent:"space-between", alignItems:"center" }}>
          <span>📋 All Students</span>
          <button className="btn btn-outline" style={{ padding:"6px 14px", fontSize:".82rem" }} onClick={fetchAll}>🔄 Refresh</button>
        </div>

        {students.length === 0 ? (
          <div className="empty-state"><span>📭</span>No students registered yet.</div>
        ) : (
          <div className="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>#</th>
                  <th>Roll No</th>
                  <th>First Name</th>
                  <th>Last Name</th>
                  <th>Contact</th>
                </tr>
              </thead>
              <tbody>
                {students.map((s, i) => (
                  <tr key={s._id}>
                    <td>{i + 1}</td>
                    <td><span className="roll-badge">{s.rollNo}</span></td>
                    <td>{s.firstName}</td>
                    <td>{s.lastName}</td>
                    <td>{s.contact}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </>
  );
}
import { useEffect, useState } from "react";
import axios from "axios";
import Cookies from "universal-cookie";
import SideBar from "./Components/SideBar";
import "./pharmacistHome.css";

export default function DoctorHome() {
  const cookie = new Cookies();
  const token = cookie.get("token");

  const [prescriptions, setPrescriptions] = useState([]);
  const [notifications, setNotifications] = useState(0);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (!token) return;

    const fetchData = async () => {
      try {
        const [prescriptionsRes, unreadRes] = await Promise.all([
          axios.get(
            "http://127.0.0.1:8000/api/doctor/prescriptions",
            {
              headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
              },
            }
          ),
          axios.get(
            "http://127.0.0.1:8000/api/notifications/unread",
            {
              headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
              },
            }
          ),
        ]);

        setPrescriptions(prescriptionsRes.data?.data || []);
        setNotifications(unreadRes.data?.data?.length || 0);
      } catch (err) {
        console.log(err);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, [token]);

  return (
    <div className="pharmacist-page">
      <SideBar />

      <main className="pharmacist-main">
        <section className="hero-card">
          <div>
            <p className="eyebrow">Doctor Dashboard</p>

            <h1>Welcome Doctor</h1>

            <p className="hero-text">
              Create prescriptions, communicate with patients,
              and monitor medical activities from one dashboard.
            </p>
          </div>

          <div className="hero-badge">
            <span>Unread Alerts</span>
            <strong>{notifications}</strong>
          </div>
        </section>

        {loading ? (
          <div className="loading-box">Loading dashboard...</div>
        ) : (
          <>
            <section className="stats-grid">
              <div className="stat-card">
                <span className="stat-label">Prescriptions</span>
                <strong className="stat-value">
                  {prescriptions.length}
                </strong>
                <p className="stat-note">Created prescriptions</p>
              </div>

              <div className="stat-card accent-blue">
                <span className="stat-label">Patients</span>
                <strong className="stat-value">--</strong>
                <p className="stat-note">Registered patients</p>
              </div>

              <div className="stat-card accent-green">
                <span className="stat-label">Consultations</span>
                <strong className="stat-value">--</strong>
                <p className="stat-note">Patient interactions</p>
              </div>

              <div className="stat-card accent-purple">
                <span className="stat-label">Notifications</span>
                <strong className="stat-value">
                  {notifications}
                </strong>
                <p className="stat-note">Unread alerts</p>
              </div>
            </section>

            <section className="dashboard-grid">
              <div className="panel">
                <div className="panel-header">
                  <h2>Recent Prescriptions</h2>
                  <span className="panel-subtitle">
                    Latest activity
                  </span>
                </div>

                <div className="list">
                  {prescriptions.length === 0 ? (
                    <div className="empty-state">
                      No prescriptions found.
                    </div>
                  ) : (
                    prescriptions.slice(0, 6).map((item) => (
                      <div className="list-item" key={item.id}>
                        <div>
                          <h3>
                            Prescription #{item.id}
                          </h3>
                          <p>
                            {item.patient_name ||
                              "Patient"}
                          </p>
                        </div>
                      </div>
                    ))
                  )}
                </div>
              </div>

              <div className="panel">
                <div className="panel-header">
                  <h2>Quick Actions</h2>
                  <span className="panel-subtitle">
                    Doctor tools
                  </span>
                </div>

                <div className="list">
                  <div className="list-item">
                    <div>
                      <h3>Create Prescription</h3>
                      <p>Issue new prescriptions</p>
                    </div>
                  </div>

                  <div className="list-item">
                    <div>
                      <h3>Patient Chat</h3>
                      <p>Respond to patient questions</p>
                    </div>
                  </div>

                  <div className="list-item">
                    <div>
                      <h3>Medical Records</h3>
                      <p>Review patient history</p>
                    </div>
                  </div>
                </div>
              </div>
            </section>
          </>
        )}
      </main>
    </div>
  );
}
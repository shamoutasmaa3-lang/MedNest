import { useEffect, useState, useContext } from "react";
import axios from "axios";
import Cookies from "universal-cookie";
import { User } from "./context/UserContext";
import SideBar from "./Components/SideBar";
import "./pharmacistNotification.css";

export default function PharmacistNotifications() {
  const cookie = new Cookies();
  const token = cookie.get("token");

  const { setNotificationsCount } = useContext(User); 

  const [notifications, setNotifications] = useState([]);
  const [loading, setLoading] = useState(true);

  // ===================== GET NOTIFICATIONS =====================
  const fetchNotifications = async () => {
    try {
      const res = await axios.get(
        "http://127.0.0.1:8000/api/notifications",
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );

      setNotifications(res.data?.data || []);
    } catch (err) {
      console.log("Error:",err);
      
    } finally {
      setLoading(false);
    }
  };

  // ===================== MARK ALL AS READ =====================
  const markAllAsRead = async () => {
    try {
      await axios.post(
        "http://127.0.0.1:8000/api/notifications/read-all",
        {},
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );

      // تحديث UI فوراً بدون انتظار
      setNotifications((prev) =>
        prev.map((n) => ({ ...n, read_at: new Date() }))
      );

      // تصفير الرقم بالـ sidebar
      setNotificationsCount?.(0);
    } catch (err) {
      console.log(err);
    }
  };

  // ===================== INIT =====================
  useEffect(() => {
    if (!token) return;
    fetchNotifications();
  }, [token]);

  // ===================== COUNT UNREAD =====================
  const unreadCount = notifications.filter((n) => !n.read_at).length;

  return (
    <div className="notif-page">
      <SideBar/>
      <div className="notif-header">
        <div>
          <h1>Notifications</h1>
          <p>All pharmacist system alerts</p>
        </div>

        <button className="mark-btn" onClick={markAllAsRead}>
          Mark all as read
        </button>
      </div>

      {/* badge */}
      {unreadCount > 0 && (
        <div className="badge-box">
          🔴 {unreadCount} unread notifications
        </div>
      )}

      {/* list */}
      <div className="notif-list">
        {loading ? (
          <p>Loading...</p>
        ) : notifications.length === 0 ? (
          <p>No notifications</p>
        ) : (
          notifications.map((n) => (
            <div
              key={n.id}
              className={`notif-card ${!n.read_at ? "unread" : ""}`}
            >
              <h3>{n.data?.title || "System Alert"}</h3>
              <p>{n.data?.message}</p>

              <small>{n.created_at}</small>
            </div>
          ))
        )}
      </div>
    </div>
  );
}
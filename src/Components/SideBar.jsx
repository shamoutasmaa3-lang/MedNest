import { Link } from "react-router-dom";
import Cookies from "universal-cookie";
import { useEffect, useState } from "react";
import axios from "axios";
import "./style.css";

export default function SideBar() {
  const cookie = new Cookies();
  const role = cookie.get("role");

  const token = cookie.get("token");

  const [unread, setUnread] = useState(0);

  // ==========================
  // GET UNREAD NOTIFICATIONS
  // ==========================
  useEffect(() => {
    if (!token) return;

    const fetchUnread = async () => {
      try {
        const res = await axios.get(
          "http://127.0.0.1:8000/api/notifications/unread",
          {
            headers: {
              Authorization: `Bearer ${token}`,
              Accept: "application/json",
            },
          },
        );

        setUnread(res.data?.data?.length || 0);
      } catch (err) {
        console.log(err);
      }
    };

    fetchUnread();

    const interval = setInterval(fetchUnread, 15000);
    return () => clearInterval(interval);
  }, [token]);

  // ==========================
  // MARK ALL AS READ
  // ==========================
  const markAllAsRead = async () => {
    try {
      await axios.post(
        "http://127.0.0.1:8000/api/notifications/read-all",
        {},
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        },
      );

      // 🔥 اختفاء الرقم فوراً
      setUnread(0);
    } catch (err) {
      console.log(err);
    }
  };

  return (
    <div className="sidebar">
      {/* ================= PATIENT ================= */}
      {role === "patient" && (
        <>
          <Link to="/patient" className="item">
            <i className="fas fa-home icon"></i>
            <span>Home</span>
          </Link>

          <Link to="/categories" className="item">
            <i className="fas fa-capsules icon"></i>
            <span>Categories</span>
          </Link>

          <Link to="/cart" className="item">
            <i className="fas fa-shopping-cart icon"></i>
            <span>Cart</span>
          </Link>

          <Link to="/upload" className="item">
            <i className="fas fa-file-medical icon"></i>
            <span>Prescription</span>
          </Link>

          <Link
            to="/notifaction"
            className="item noti-item"
            onClick={markAllAsRead}
          >
            <i className="fas fa-bell icon"></i>
            <span>Alerts</span>

            {unread > 0 && <span className="noti-badge">{unread}</span>}
          </Link>

          <Link to="/my-orders" className="item">
            <i className="fas fa-clipboard-list icon"></i>
            <span>Orders</span>
          </Link>

          <Link to="/recommendations" className="item">
            <i className="fa-solid fa-notes-medical icon"></i>
            <span>Med Tips</span>
          </Link>

         < Link to="/patientChat" className="item">
            <i className="fas fa-comments icon"></i>
            <span>Chat</span>
          </Link>

          <Link to="/profile" className="item">
            <i className="fa-solid fa-circle-user icon"></i>
            <span>Profile</span>
          </Link>
        </>
      )}

      {/* ================= DOCTOR ================= */}
      {role === "doctor" && (
        <>
          <Link to="/doctor" className="item">
            <i className="fas fa-user-md icon"></i>
            <span>Home</span>
          </Link>

          <Link to="/doctor/prescriptions" className="item">
            <i className="fas fa-file-prescription icon"></i>
            <span>Prescriptions</span>
          </Link>

          <Link to="/doctor/chat" className="item">
            <i className="fas fa-comments icon"></i>
            <span>Chat</span>
          </Link>

          <Link
            to="/notifaction"
            className="item noti-item"
            onClick={markAllAsRead}
          >
            <i className="fas fa-bell icon"></i>
            <span>Alerts</span>

            {unread > 0 && <span className="noti-badge">{unread}</span>}
          </Link>
          <Link to="/profile" className="item">
            <i className="fa-solid fa-circle-user icon"></i>
            <span>Profile</span>
          </Link>
        </>
      )}

      {/* ================= PHARMACIST ================= */}
      {role === "pharmacist" && (
        <>
          <Link to="/pharmacist" className="item">
            <i className="fas fa-clinic-medical icon"></i>
            <span>Home</span>
          </Link>

          <Link to="/inventory" className="item">
            <i className="fas fa-boxes icon"></i>
            <span>Inventory</span>
          </Link>

          <Link to="/pharmacistorders" className="item">
            <i className="fas fa-clipboard-list icon"></i>
            <span>Orders</span>
          </Link>

          <Link to="/pharmacistChat" className="item">
            <i className="fas fa-comments icon"></i>
            <span>Chat</span>
          </Link>

          <Link
            to="/pharmacistnotifaction"
            className="item noti-item"
            onClick={markAllAsRead}
          >
            <i className="fas fa-bell icon"></i>
            <span>Alerts</span>

            {unread > 0 && <span className="noti-badge">{unread}</span>}
          </Link>

          <Link to="/profile" className="item">
            <i className="fa-solid fa-circle-user icon"></i>
            <span>Profile</span>
          </Link>
          <Link to="/profile" className="item">
            <i className="fa-solid fa-circle-user icon"></i>
            <span>Profile</span>
          </Link>
        </>
      )}
    </div>
  );
}

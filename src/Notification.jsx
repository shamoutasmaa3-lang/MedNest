
import { useState, useEffect, useContext } from "react";
import axios from "axios";
import { User } from "./context/UserContext";

import "./notification.css";
import SideBar from "./Components/SideBar";

export default function NotificationsPage() {
  const [notifications, setNotifications] = useState([]);
  const [unreadCount, setUnreadCount] = useState(0);
  const [loading, setLoading] = useState(true);

  const { auth } = useContext(User);
  const token = auth.token;

  const getNotifications = async () => {
    if (!token) return;

    try {
      const res = await axios.get("http://127.0.0.1:8000/api/notifications", {
        headers: {
          Authorization:` Bearer ${token}`,
          Accept: "application/json",
        },
      });

      setNotifications(res.data.data || []);
    } catch (err) {
      console.log(err);
    }
  };

  const getUnread = async () => {
    if (!token) return;

    try {
      const res = await axios.get(
        "http://127.0.0.1:8000/api/notifications/unread",
        {
          headers: {
            Authorization: `Bearer ${token}`,
            Accept: "application/json",
          },
        }
      );

      setUnreadCount(res.data.data.length || 0);
    } catch (err) {
      console.log(err);
    }
  };

  const markAllAsRead = async () => {
    if (!token) return;

    try {
      await axios.post(
        "http://127.0.0.1:8000/api/notifications/read-all",
        {},
        {
          headers: {
            Authorization:` Bearer ${token}`,
            Accept: "application/json",
          },
        }
      );

      await getNotifications();
      await getUnread();
    } catch (err) {
      console.log(err);
    }
  };

  useEffect(() => {
    const run = async () => {
      if (!token) {
        setLoading(false);
        return;
      }

      setLoading(true);
      await getNotifications();
      await getUnread();
      setLoading(false);
    };

    run();

    const interval = setInterval(() => {
      if (token) {
        getUnread();
      }
    }, 15000);

    return () => clearInterval(interval);
  }, [token]);

  return (
    <div className="notifications-app">
      <SideBar />
      <div className="notifications-main">
        <div className="notifications-header-row">
          <div className="header-left">
            <h1>
              Notifications{" "}
              {unreadCount > 0 && (
                <span className="badge">{unreadCount}</span>
              )}
            </h1>
            <h4>Stay updated with your medical alerts</h4>
          </div>

          <button className="mark-all-btn" onClick={markAllAsRead}>
            Mark all as read
          </button>
        </div>

        <div className="notifications-list">
          {loading ? (
            <div className="loading">Loading...</div>
          ) : notifications.length === 0 ? (
            <div className="empty">No notifications</div>
          ) : (
            notifications.map((n) => (
              <div
                key={n.id}
                className={"notification-item " + (n.read_at ? "" : "unread")}
              >
                <h2>{n.data.title}</h2>
                <p>{n.data.message}</p>
                <div className="date">
                  {new Date(n.created_at).toLocaleString()}
                </div>
              </div>
            ))
          )}
        </div>
      </div>
    </div>
  );
}
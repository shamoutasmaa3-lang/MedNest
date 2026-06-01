import { useEffect, useMemo, useState } from "react";
import axios from "axios";
import Cookies from "universal-cookie";
import "./pharmacistHome.css";
import SideBar from "./Components/SideBar";

export default function PharmacistHome() {
  const cookie = new Cookies();
  const token = cookie.get("token");

  const [orders, setOrders] = useState([]);
  const [inventory, setInventory] = useState([]);
  const [unreadNotifications, setUnreadNotifications] = useState(0);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (!token) return;

    const fetchDashboardData = async () => {
      try {
        const [ordersRes, inventoryRes, unreadRes] = await Promise.all([
          axios.get("http://127.0.0.1:8000/api/pharmacist/orders", {
            headers: { Authorization: `Bearer ${token}`, Accept: "application/json" },
          }),
          axios.get("http://127.0.0.1:8000/api/pharmacist/inventory", {
            headers: { Authorization: `Bearer ${token}`, Accept: "application/json" },
          }),
          axios.get("http://127.0.0.1:8000/api/notifications/unread", {
            headers: { Authorization: `Bearer ${token}`, Accept: "application/json" },
          }),
        ]);

        setOrders(ordersRes.data?.data || []);
        setInventory(inventoryRes.data?.data || []);
        setUnreadNotifications(unreadRes.data?.data?.length || 0);
      } catch (err) {
        console.log("Dashboard error:", err.response?.data || err);
      } finally {
        setLoading(false);
      }
    };

    fetchDashboardData();
  }, [token]);

  const stats = useMemo(() => {
    const pendingOrders = orders.filter((o) => o.status === "pending");
    const verifiedOrders = orders.filter((o) => o.status === "verified");
    const lowStock = inventory.filter((item) => item.quantity <= 10);
    const outOfStock = inventory.filter((item) => item.quantity === 0);

    return {
      totalOrders: orders.length,
      pendingOrders: pendingOrders.length,
      verifiedOrders: verifiedOrders.length,
      totalMedicines: inventory.length,
      lowStock: lowStock.length,
      outOfStock: outOfStock.length,
    };
  }, [orders, inventory]);

  return (
    <div className="pharmacist-page">
      <SideBar />

      <main className="pharmacist-main">
        <section className="hero-card">
          <div>
            <p className="eyebrow">Pharmacist Dashboard</p>
            <h1>Welcome back</h1>
            <p className="hero-text">
              Review orders, monitor stock, and manage pharmacy operations from one place.
            </p>
          </div>

          <div className="hero-badge">
            <span>Unread Alerts</span>
            <strong>{unreadNotifications}</strong>
          </div>
        </section>

        {loading ? (
          <div className="loading-box">Loading dashboard...</div>
        ) : (
          <>
            <section className="stats-grid">
              <div className="stat-card">
                <span className="stat-label">Total Orders</span>
                <strong className="stat-value">{stats.totalOrders}</strong>
                <p className="stat-note">All received orders</p>
              </div>

              <div className="stat-card accent-blue">
                <span className="stat-label">Pending</span>
                <strong className="stat-value">{stats.pendingOrders}</strong>
                <p className="stat-note">Needs pharmacist review</p>
              </div>

              <div className="stat-card accent-green">
                <span className="stat-label">Verified</span>
                <strong className="stat-value">{stats.verifiedOrders}</strong>
                <p className="stat-note">Ready for dispensing</p>
              </div>

              <div className="stat-card accent-purple">
                <span className="stat-label">Medicines</span>
                <strong className="stat-value">{stats.totalMedicines}</strong>
                <p className="stat-note">Items in inventory</p>
              </div>

              <div className="stat-card accent-orange">
                <span className="stat-label">Low Stock</span>
                <strong className="stat-value">{stats.lowStock}</strong>
                <p className="stat-note">Need refill soon</p>
              </div>

              <div className="stat-card accent-red">
                <span className="stat-label">Out of Stock</span>
                <strong className="stat-value">{stats.outOfStock}</strong>
                <p className="stat-note">Unavailable medicines</p>
              </div>
            </section>

            <section className="dashboard-grid">
              <div className="panel">
                <div className="panel-header">
                  <h2>Latest Orders</h2>
                  <span className="panel-subtitle">Most recent activity</span>
                </div>

                <div className="list">
                  {orders.slice(0, 6).length === 0 ? (
                    <div className="empty-state">No orders found.</div>
                  ) : (
                    orders.slice(0, 6).map((order) => (
                      <div key={order.id} className="list-item">
                        <div>
                          <h3>Order #{order.id}</h3>
                          <p>{order.delivery_address}</p>
                        </div>
                        <span className={`status-pill ${order.status}`}>
                          {order.status}
                        </span>
                      </div>
                    ))
                  )}
                </div>
              </div>

              <div className="panel">
                <div className="panel-header">
                  <h2>Stock Watch</h2>
                  <span className="panel-subtitle">Medicines needing attention</span>
                </div>

                <div className="list">
                  {inventory.filter((item) => item.quantity <= 10).slice(0, 6).length === 0 ? (
                    <div className="empty-state">No low stock medicines.</div>
                  ) : (
                    inventory
                      .filter((item) => item.quantity <= 10)
                      .slice(0, 6)
                      .map((item) => (
                        <div key={item.id} className="list-item">
                          <div>
                            <h3>{item.medicine_name}</h3>
                            <p>
                              Qty: {item.quantity} • Exp: {item.expiration_date || "N/A"}
                            </p>
                          </div>
                          <span className={`stock-pill ${item.quantity === 0 ? "out" : "low"}`}>
                            {item.quantity === 0 ? "Out" : "Low"}
                          </span>
                        </div>
                      ))
                  )}
                </div>
              </div>
            </section>
          </>
        )}
      </main>
    </div>
  );
}
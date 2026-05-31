import { useEffect, useState } from "react";
import Cookies from "universal-cookie";
import "./inventory.css";
import SideBar from "./components/SideBar";

export default function Inventory() {
  const [total, setTotal] = useState(0);
  const [lowStock, setLowStock] = useState(0);
  const [expiring, setExpiring] = useState(0);
  const [medicines, setMedicines] = useState([]);
  const [error, setError] = useState("");

  const cookie = new Cookies();

  useEffect(() => {
    const token = cookie.get("token");

    const fetchInventory = async () => {
      try {
        const res = await fetch(
          "http://127.0.0.1:8000/api/pharmacist/inventory",
          {
            method: "GET",
            headers: {
              Accept: "application/json",
              "Content-Type": "application/json",
              Authorization: `Bearer ${token}`,
            },
          }
        );

        if (!res.ok) {
          const text = await res.text();
          console.error("Server error:", text);
          setError("Unauthorized or server error");
          return;
        }

        const data = await res.json();

        console.log("INVENTORY DATA:", data);

        setTotal(data.total_items ?? 0);
        setLowStock(data.low_stock_count ?? 0);
        setExpiring(data.expiring_soon_count ?? 0);
        setMedicines(data.data ?? []);
      } catch (err) {
        console.error("Fetch error:", err);
        setError("Failed to load inventory");
      }
    };

    fetchInventory();
  }, []);

  return (
    <>
      <SideBar />

      <div className="inventory-container">
        <div className="header">
          <h1>Inventory Management</h1>
          <h4>Track stock levels and expiry dates</h4>
        </div>

        {error && <p className="error">{error}</p>}

        <div className="stats">
          <div className="stat-box">
            <div className="stat-left">
              <div className="stat-icon">
                <i className="fa-solid fa-chart-column"></i>
              </div>
              <div className="stat-info">
                <div className="stat-label">Total Medicines</div>
                <div className="stat-desc">All medicines in stock</div>
              </div>
            </div>
            <div className="stat-number">
              <span className="num-badge">{total}</span>
            </div>
          </div>

          <div className="stat-box low">
            <div className="stat-left">
              <div className="stat-icon">
                <i className="fa-solid fa-triangle-exclamation"></i>
              </div>
              <div className="stat-info">
                <div className="stat-label">Low Stock</div>
                <div className="stat-desc">Below Minimum level</div>
              </div>
            </div>
            <div className="stat-number">
              <span className="num-badge">{lowStock}</span>
            </div>
          </div>

          <div className="stat-box expiring">
            <div className="stat-left">
              <div className="stat-icon">
                <i className="fa-solid fa-hourglass-half"></i>
              </div>
              <div className="stat-info">
                <div className="stat-label">Expiring Soon</div>
                <div className="stat-desc">within 30 days</div>
              </div>
            </div>
            <div className="stat-number">
              <span className="num-badge">{expiring}</span>
            </div>
          </div>
        </div>

        <table className="med-table">
          <thead>
            <tr>
              <th>Medicine</th>
              <th>Category</th>
              <th>Stock</th>
              <th>Expiry</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>

          <tbody>
            {medicines.length === 0 ? (
              <tr>
                <td colSpan="6" style={{ textAlign: "center" }}>
                  No data available
                </td>
              </tr>
            ) : (
              medicines.map((med) => (
                <tr key={med.id}>
                  <td>{med.medicine_name}</td>
                  <td>{med.category}</td>
                  <td>{med.quantity}</td>
                  <td>{med.expiration_date}</td>
                  <td>
                    {med.quantity === 0 ? (
                      <span className="status out">Out of Stock</span>
                    ) : med.is_low_stock ? (
                      <span className="status low">Low Stock</span>
                    ) : med.is_expiring_soon ? (
                      <span className="status exp">Expiring</span>
                    ) : (
                      <span className="status ok">In Stock</span>
                    )}
                  </td>
                  <td className="action-button">
                    <button className="btnEdit">
                      <i className="fa-solid fa-pencil"></i>
                    </button>
                    <button className="btnDelete">
                      <i className="fa-solid fa-trash"></i>
                    </button>
                  </td>
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>
    </>
  );
}
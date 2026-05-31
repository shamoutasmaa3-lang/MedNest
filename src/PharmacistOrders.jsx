import { useEffect, useState, useContext } from "react";
import axios from "axios";
import { User } from "./Context/UserContext";
import "./PharmacistOrders.css";
import SideBar from "./Components/SideBar";
export default function PharmacistOrders() {
  const { auth } = useContext(User);
  const token = auth?.token;

  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(true);

  // =========================
  // GET ORDERS
  // =========================
  useEffect(() => {
    if (!token) return;

    axios
      .get("http://127.0.0.1:8000/api/pharmacist/orders", {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      })
      .then((res) => {
        setOrders(res.data?.data || []);
      })
      .catch((err) => {
        console.log(err.response?.data || err);
      })
      .finally(() => setLoading(false));
  }, [token]);

  // =========================
  // ACTIONS
  // =========================
  const updateStatus = async (id, action) => {
    const url = `http://127.0.0.1:8000/api/pharmacist/orders/${id}/${action}`;

    try {
      await axios.put(
        url,
        {},
        {
          headers: {
            Authorization: `Bearer ${token}`,
            Accept: "application/json",
          },
        }
      );

      // refresh
      const res = await axios.get(
        "http://127.0.0.1:8000/api/pharmacist/orders",
        {
          headers: {
            Authorization: `Bearer ${token}`,
            Accept: "application/json",
          },
        }
      );

      setOrders(res.data?.data || []);
    } catch (err) {
      console.log(err.response?.data || err);
    }
  };

  if (loading) return <p className="loading">Loading orders...</p>;

  return (
    <div className="orders-container">
      <SideBar/>
      <h1 className="title"> Pharmacist Orders</h1>

      {orders.length === 0 && (
        <p className="empty">No orders available</p>
      )}

      {orders.map((order) => (
        <div key={order.id} className="order-card">
          <div className="order-header">
            <h3>Order #{order.id}</h3>
            <span className={`status ${order.status}`}>
              {order.status}
            </span>
          </div>

          <p><b>Total:</b> {order.total_price}</p>
          <p><b>Address:</b> {order.delivery_address}</p>

          <div className="items">
            <h4>Medicines:</h4>
            <ul>
              {order.items?.map((i) => (
                <li key={i.id}>
                  {i.medicine?.name} — {i.quantity}
                </li>
              ))}
            </ul>
          </div>

          <div className="actions">
            {order.status === "pending" && (
              <>
                <button
                  className="accept"
                  onClick={() => updateStatus(order.id, "verify")}
                >
                  Accept
                </button>

                <button
                  className="reject"
                  onClick={() => updateStatus(order.id, "reject")}
                >
                  Reject
                </button>
              </>
            )}

            {order.status === "verified" && (
              <button
                className="dispense"
                onClick={() => updateStatus(order.id, "dispense")}
              >
                Dispense
              </button>
            )}
          </div>
        </div>
      ))}
    </div>
  );
}
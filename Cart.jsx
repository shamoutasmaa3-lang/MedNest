
import { useContext, useState } from "react";
import { User } from "./Context/UserContext";
import axios from "axios";
import { useNavigate } from "react-router-dom";
import Headers from "./Components/Headers";
import Footer from "./Components/Footer";
import SideBar from "./Components/SideBar";
import "./Cart.css";
export default function Cart() {
  const { cart, setCart } = useContext(User);
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);

  function removeFromCart(id) {
    setCart(cart.filter((item) => item.id !== id));
  }

  const totalPrice = cart.reduce((sum, item) => sum + item.price, 0);

  async function checkInteractions() {
    try {
      setLoading(true);

      
      const ids = cart.map((item) => item.id);

      console.log("IDS SENT:", ids); 

      const res = await axios.post(
        "http://127.0.0.1:8000/api/safety-check",
        {
          medicine_ids: ids
        }
      );

      setLoading(false);

      if (res.data.status === "safe") {
        navigate("/safe");
      } else {
        navigate("/nosafe");
      }

    } catch (err) {
      setLoading(false);
      console.log("API ERROR:", err.response?.data);
      alert("Error checking interactions");
    }
  }

  return (
    <div>
      <SideBar />
    

      <div className="cart-container">
        <h1>Your Cart</h1>

        {cart.length === 0 ? (
          <p>Cart is empty</p>
        ) : (
          <>
            {cart.map((item) => (
              <div className="cart-item" key={item.id}>
                <div>
                  <h3>{item.name}</h3>
                  <p>{item.price} S.P</p>
                </div>

                <button onClick={() => removeFromCart(item.id)}>
                  Remove
                </button>
              </div>
            ))}

            <h2>Total: {totalPrice} S.P</h2>

            <button onClick={checkInteractions} disabled={loading}>
              {loading ? "Checking..." : "Check Interactions"}
            </button>
          </>
        )}
      </div>

      <Footer />
    </div>
  );
}
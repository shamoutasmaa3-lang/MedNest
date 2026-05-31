

// import SideBar from "./Components/SideBar";
// import { useContext, useState } from "react";
// import { User } from "./Context/UserContext";
// import axios from "axios";
// import { useNavigate } from "react-router-dom";
// import Footer from "./Components/Footer";
// import "./Cart.css";
// import plus from "./assets/Plus.png";
// import minus from "./assets/Minus.png";
// import remove from "./assets/Remove.png";
// import cartIcon from "./assets/FastCart.png";
// import Cookies from "universal-cookie";

// export default function Cart() {
//   const { cart, setCart } = useContext(User);
//   const navigate = useNavigate();
//   const [loading, setLoading] = useState(false);

//   function increase(id) {
//     setCart(
//       cart.map((item) =>
//         item.id === id ? { ...item, qty: (item.qty || 1) + 1 } : item,
//       ),
//     );
//   }

//   function decrease(id) {
//     setCart(
//       cart.map((item) =>
//         item.id === id && (item.qty || 1) > 1
//           ? { ...item, qty: item.qty - 1 }
//           : item,
//       ),
//     );
//   }

//   function removeFromCart(id) {
//     setCart(cart.filter((item) => item.id !== id));
//   }

//   const totalPrice = cart.reduce(
//     (sum, item) => sum + item.price * (item.qty || 1),
//     0,
//   );

//   async function checkInteractions() {
//     try {
//       setLoading(true);
//       const cookie = new Cookies();
//       const token = cookie.get("token");

//       const ids = cart.map((item) => item.id);

//       const res = await axios.post(
//         "http://127.0.0.1:8000/api/safety-check",
//         { medicine_ids: ids },
//         {
//           headers: {
//             Authorization: ` Bearer ${token}`,
//             Accept: "application/json",
//           },
//         },
//       );

//       setLoading(false);

//       if (res.data.status === "safe") {
//         navigate("/safe");
//       } else {
//         navigate("/nosafe");
//       }
//     } catch (error) {
//       console.log(error);
//       setLoading(false);
//       alert("Error checking interactions");
//     }
//   }

//   return (
//     <div className="cart-page">
//      <SideBar/>
//       <h1 className="title">Shopping Cart</h1>
//       <h4 className="subtitle">Review your selected medicines</h4>

//       <div className="cart-box">
//         <div className="cart-header">
//           <img src={cartIcon} alt="" className="title-icon" />
//           <span>{cart.length} Items in your cart</span>
//         </div>

//         {cart.length === 0 ? (
//           <p className="empty">Cart is empty</p>
//         ) : (
//           <>
//             {cart.map((item) => (
//               <div className="item" key={item.id}>
//                 <div className="item-info">
//                   <div className="img">
//                     <img src={item.img} alt={item.name} />
//                   </div>

//                   <div>
//                     <h3>{item.name}</h3>
//                     <p>10 Tablets</p>
//                     <div className="stock">in Stock</div>
//                   </div>
//                 </div>

//                 <div className="price">{item.price} S.P</div>

//                 {/*  quantity */}
//                 <div className="qty">
//                   <button onClick={() => decrease(item.id)}>
//                     <img src={minus} alt="" />
//                   </button>

//                   <span style={{ fontSize: "23px" }}>{item.qty || 1}</span>

//                   <button onClick={() => increase(item.id)}>
//                     <img src={plus} alt="" />
//                   </button>
//                 </div>

//                 <button
//                   className="remove"
//                   onClick={() => removeFromCart(item.id)}
//                 >
//                   <img src={remove} alt="" />
//                 </button>
//               </div>
//             ))}

//             <div className="footer">
//               <h2>Total: {totalPrice} S.P</h2>

//               <button className="check" onClick={checkInteractions}>
//                 {loading ? "Checking..." : "Check Interactions"}
//               </button>
//             </div>
//           </>
//         )}
//       </div>

//       <Footer />
//     </div>
//   );
// }

import SideBar from "./Components/SideBar";
import Footer from "./Components/Footer";
import { useContext, useEffect, useState } from "react";
import { User } from "./Context/UserContext";
import axios from "axios";
import "./Cart.css";
import Cookies from "universal-cookie";

 import plus from "./assets/Plus.png";
 import minus from "./assets/Minus.png";
 import remove from "./assets/Remove.png";
import { useNavigate } from "react-router-dom";
export default function Cart() {
  const { cart, setCart } = useContext(User);

  const [medicines, setMedicines] = useState([]);
const [loading, setLoading] = useState(false);
  const token = new Cookies().get("token");
const navigate = useNavigate();
  const headers = {
    Authorization:` Bearer ${token}`,
    Accept: "application/json",
  };

  // ======================
  // LOAD CART + MEDICINES
  // ======================
  useEffect(() => {
    if (!token) return;

    const fetchData = async () => {
      try {
        const [cartRes, medRes] = await Promise.all([
          axios.get("http://127.0.0.1:8000/api/cart", { headers }),
          axios.get("http://127.0.0.1:8000/api/medicines", { headers }),
        ]);

        setCart(cartRes.data.data || []);
        setMedicines(medRes.data.data || []);
      } catch (err) {
        console.log(err);
      }
    };

    fetchData();
  }, [token]);

  // ======================
  // GET IMAGE FROM MEDICINES
  // ======================
  const findImage = (medicine_id) => {
    const med = medicines.find((m) => m.id === medicine_id);

    if (!med || !med.image) return "/no-image.png";

    return `http://127.0.0.1:8000/storage/${med.image}`;
  };

  // ======================
  // INCREASE
  // ======================
  const increase = async (medicine_id) => {
    try {
      await axios.post(
        "http://127.0.0.1:8000/api/cart/items",
        { medicine_id, quantity: 1 },
        { headers }
      );

      const res = await axios.get("http://127.0.0.1:8000/api/cart", {
        headers,
      });

      setCart(res.data.data || []);
    } catch (err) {
      console.log(err);
    }
  };

  // ======================
  // DECREASE (frontend only)
  // ======================
  const decrease = (item_id) => {
    setCart((prev) =>
      prev.map((item) =>
        item.id === item_id && item.quantity > 1
          ? { ...item, quantity: item.quantity - 1 }
          : item
      )
    );
  };

  // ======================
  // REMOVE ITEM
  // ======================
  const removeItem = async (item_id) => {
    try {
      await axios.delete(
        `http://127.0.0.1:8000/api/cart/items/${item_id}`,
        { headers }
      );

      setCart((prev) => prev.filter((item) => item.id !== item_id));
    } catch (err) {
      console.log(err);
    }
  };

  // ======================
  // TOTAL
  // ======================
  const total = cart.reduce(
    (sum, item) => sum + item.price * item.quantity,
    0
  );
   async function checkInteractions() {
     try {
       setLoading(true);
       const cookie = new Cookies();
       const token = cookie.get("token");

       const ids = cart.map((item) => item.id);

       const res = await axios.post(
         "http://127.0.0.1:8000/api/safety-check",
         { medicine_ids: ids },
         {
           headers: {
             Authorization: ` Bearer ${token}`,
             Accept: "application/json",
           },
         },
       );

       setLoading(false);

       if (res.data.status === "safe") {
         navigate("/safe");
       } else {
         navigate("/nosafe");
       }
     } catch (error) {
       console.log(error);
       setLoading(false);
       alert("Error checking interactions");
     }
   }

  return (
    <div className="cart-page">
      <SideBar />

      <h1 className="title">Shopping Cart</h1>

      <div className="cart-box">
        {cart.length === 0 ? (
          <p className="empty">Cart is empty</p>
        ) : (
          cart.map((item) => (
            <div className="item" key={item.id}>
              <div className="item-info">
                <div className="img">
                  <img
                    src={findImage(item.medicine_id)}
                    alt={item.name}
                  />
                </div>

                <div>
                  <h3>{item.name}</h3>
                  <p>Medicine</p>
                </div>
              </div>

              <div className="price">{item.price} S.P</div>

              <div className="qty">
                <button onClick={() => decrease(item.id)}>
                    <img src={minus} alt="" />
                </button>

                <span>{item.quantity}</span>

                <button onClick={() => increase(item.medicine_id)}>
                   <img src={plus} alt="" />
                </button>
              </div>

              <button
                className="remove"
                onClick={() => removeItem(item.id)}
              >
                <img src={remove} alt="" />
              </button>
            </div>
          ))
        )}
      </div>

      <h2>Total: {total}</h2>
              <button className="check" onClick={checkInteractions}>
                 {loading ? "Checking..." : "Check Interactions"}
               </button>
      <Footer />
    </div>
  );
}

// import { useEffect, useState, useContext } from "react";
// import axios from "axios";
// import Cookies from "universal-cookie";
// import { User } from "./Context/UserContext";
// import "./myOrder.css";
// import SideBar from "./Components/SideBar";

// export default function MyOrders() {
//   const { auth } = useContext(User);
//   const [orders, setOrders] = useState([]);
//   const [medicines, setMedicines] = useState([]);

//   useEffect(() => {
//     const cookie = new Cookies();
//     const token = auth?.token || cookie.get("token");

  
//     async function fetchOrders() {
//       try {
//         const res = await axios.get(
//           "http://127.0.0.1:8000/api/orders",
//           {
//             headers: {
//               Authorization: `Bearer ${token}`,
//               Accept: "application/json",
//             },
//           }
//         );

//         setOrders(res.data.data || []);
//       } catch (err) {
//         console.log(err.response?.data || err);
//         setOrders([]);
//       }
//     }


//     async function fetchMedicines() {
//       try {
//         const res = await axios.get(
//           "http://127.0.0.1:8000/api/medicines",
//           {
//             headers: {
//               Authorization: `Bearer ${token}`,
//               Accept: "application/json",
//             },
//           }
//         );

//         setMedicines(res.data.data || []);
//       } catch (err) {
//         console.log(err);
//         setMedicines([]);
//       }
//     }

//     fetchOrders();
//     fetchMedicines();
//   }, [auth?.token]);


//   function getMedicineName(id) {
//     const med = medicines.find((m) => m.id === id);
//     return med ? med.name : "Unknown Medicine";
//   }

//   return (
//     <div className="orders-container">
//       <SideBar/>
//       <h2 className="orders-title">My Orders</h2>

//       {orders.length === 0 ? (
//         <p className="empty-box">No orders yet</p>
//       ) : (
//         orders.map((order) => (
//           <div className="order-card" key={order.id}>
            
//             <div className="order-header">
//               <div className="order-id">Order #{order.id}</div>
//               <div className="order-status">{order.status}</div>
//             </div>

//             <div className="order-date">
//               Date: {order.created_at}
//             </div>

//             <div className="order-items">
//               {order.items?.map((item, index) => (
//                 <div className="item-row" key={index}>
                  
                 
//                   <span className="item-name">
//                     {getMedicineName(item.medicine_id)}
//                   </span>

//                   <span>Qty: {item.quantity}</span>

//                   <span>
                  
//                     {item.price ? item.price + " S.P" : ""}
//                   </span>
//                 </div>
//               ))}
//             </div>

//             <div className="order-total">
//               Total: {order.total_price} S.P
//             </div>

//           </div>
//         ))
//       )}
//     </div>
//   );
// }

import { useEffect, useState, useContext } from "react";
import axios from "axios";
import Cookies from "universal-cookie";
import { User } from "./Context/UserContext";
import "./myOrder.css";
import SideBar from "./Components/SideBar";

export default function MyOrders() {
  const { auth } = useContext(User);
  const [orders, setOrders] = useState([]);
  const [medicines, setMedicines] = useState([]);

  useEffect(() => {
    const cookie = new Cookies();
    const token = auth?.token || cookie.get("token");

    async function fetchOrders() {
      try {
        const res = await axios.get("http://127.0.0.1:8000/api/orders", {
          headers: {
            Authorization: `Bearer ${token}`,
            Accept: "application/json",
          },
        });
console.log("orders response:",res.data.data);
        // ترتيب الأحدث أولاً
        const sortedOrders = (res.data.data || []).sort(
          (a, b) => new Date(b.created_at) - new Date(a.created_at)
        );

        setOrders(sortedOrders);
      } catch (err) {
        console.log(err.response?.data || err);
        setOrders([]);
      }
    }

    async function fetchMedicines() {
      try {
        const res = await axios.get("http://127.0.0.1:8000/api/medicines", {
          headers: {
            Authorization:` Bearer ${token}`,
            Accept: "application/json",
          },
        });

        setMedicines(res.data.data || []);
      } catch (err) {
        console.log(err);
        setMedicines([]);
      }
    }

    fetchOrders();
    fetchMedicines();
  }, [auth?.token]);

  // جلب اسم الدواء بدل الـ ID
  function getMedicineName(id) {
    const med = medicines.find((m) => m.id === id);
    return med ? med.name : "Unknown Medicine";
  }

  return (
    <div className="orders-container">
      <SideBar />
      <h2 className="orders-title">My Orders</h2>

      {orders.length === 0 ? (
        <p className="empty-box">No orders yet</p>
      ) : (
        orders.map((order) => (
          <div className="order-card" key={order.id}>
            
            {/* Header */}
            <div className="order-header">
              <div className="order-id">Order #{order.id}</div>
              <div className="order-status">{order.status}</div>
            </div>

            {/* Date */}
            <div className="order-date">Date: {order.created_at}</div>

            {/* Payment */}
            <div className="order-payment">
              Payment Method: {order.payment_method}
            </div>

            {/* Items */}
            <div className="order-items">
              {order.items?.map((item, index) => (
                <div className="item-row" key={index}>
                  <span className="item-name">
                    {getMedicineName(item.medicine_id)}
                  </span>

                  <span>Qty: {item.quantity}</span>

                  <span>{item.price ? `${item.price} S.P `: ""}</span>
                </div>
              ))}
            </div>

            {/* Total */}
            <div className="order-total">
              Total: {order.total_price} S.P
            </div>
          </div>
        ))
      )}
    </div>
  );
}


// import { useContext, useEffect, useState } from "react";
// import { User } from "./Context/UserContext";
// import "./categoris.css";
// import SideBar from "./Components/SideBar";
// import Header from "./Components/Header";
// import Footer from "./Components/Footer";
// import axios from "axios";
// import Cookies from "universal-cookie";

// export default function Categories() {
//   const { cart, setCart } = useContext(User);

//   const [addedItem, setAdded] = useState([]);
//   const [medicines, setMedicines] = useState([]);

//   useEffect(() => {
//     const fetchMedicines = async () => {
//       const cookie = new Cookies();
//       const token = cookie.get("token");

//       try {
//         const res = await axios.get("http://127.0.0.1:8000/api/medicines", {
//           headers: {
//             Authorization: `Bearer ${token}`,
//             Accept: "application/json",
//           },
//         });

//         setMedicines(res.data.data || []);
//       } catch (err) {
//         console.log(err.response?.data || err);
//         setMedicines([]);
//       }
//     };

//     fetchMedicines();
//   }, []);

//   async function handleAddToCart(item) {
//     const cookie = new Cookies();
//     const token = cookie.get("token");

//     if (!token) {
//       alert("You must login first");
//       return;
//     }

//     try {
//       await axios.post(
//         "http://127.0.0.1:8000/api/cart/items",
//         {
//           medicine_id: item.id,
//           quantity: 1,
//         },
//         {
//           headers: {
//             Authorization: `Bearer ${token}`,
//             Accept: "application/json",
//           },
//         },
//       );

//       const exists = cart.find((p) => p.id === item.id);

//       if (exists) {
//         setCart(
//           cart.map((p) =>
//             p.id === item.id ? { ...p, qty: (p.qty || 1) + 1 } : p,
//           ),
//         );
//       } else {
//         setCart([...cart, { ...item, qty: 1 }]);
//       }

//       setAdded((prev) => [...prev, item.id]);
//     } catch (err) {
//       console.log(err.response?.data || err);
//       alert("Failed to add to cart");
//     }
//   }

//   return (
//     <>
//       <Header />
//       <SideBar />

//       <div className="Contener">
//         {medicines.length === 0 ? (
//           <p>Loading medicines...</p>
//         ) : (
//           medicines.map((item) => (
//             <div className="product" key={item.id}>
//               <div className="info">
//                 <h2>{item.name}</h2>
//                 <img src={item.image_url} />

//                 <p>
//                   <strong>Uses:</strong>
//                   <br />
//                   {item.description?.split(",").map((use, i) => (
//                     <span key={i}>
//                       {use}
//                       <br />
//                     </span>
//                   ))}
//                 </p>

//                 <p>
//                   <strong>Price:</strong> {item.price} S.P
//                 </p>

//                 <button
//                   onClick={() => handleAddToCart(item)}
//                   style={{
//                     backgroundColor: addedItem.includes(item.id) ? "green" : "",
//                     color: "white",
//                   }}
//                 >
//                   {addedItem.includes(item.id) ? "Added" : "Add to cart"}
//                 </button>
//               </div>
//             </div>
//           ))
//         )}
//       </div>

//       <Footer />
//     </>
//   );
// }
import { useContext, useEffect, useState } from "react";
import { User } from "./Context/UserContext";
import "./categoris.css";
import SideBar from "./Components/SideBar";
import Header from "./Components/Header";
import Footer from "./Components/Footer";
import axios from "axios";
import Cookies from "universal-cookie";

export default function Categories() {
  const { cart, setCart } = useContext(User);

  const [addedItem, setAddedItem] = useState([]);
  const [medicines, setMedicines] = useState([]);

  const cookie = new Cookies();
  const token = cookie.get("token");
console.log(cart);
  // ======================
  // FETCH MEDICINES
  // ======================
  useEffect(() => {
    const fetchMedicines = async () => {
      if (!token) return;

      try {
        const res = await axios.get(
          "http://127.0.0.1:8000/api/medicines",
          {
            headers: {
              Authorization: `Bearer ${token}`,
              Accept: "application/json",
            },
          }
        );

        setMedicines(res.data.data || []);
      } catch (err) {
        console.log(err);
        setMedicines([]);
      }
    };

    fetchMedicines();
  }, [token]);

  // ======================
  // ADD TO CART
  // ======================
 async function handleAddToCart(item) {
  if (!token) return;

 
  setCart((prev) => {
    const exists = prev.find((p) => p.medicine_id === item.id);

    if (exists) {
      return prev.map((p) =>
        p.medicine_id === item.id
          ? { ...p, quantity: p.quantity + 1 }
          : p
      );
    }

    return [
      ...prev,
      {
        id: item.id,
        medicine_id: item.id,
        name: item.name,
        price: item.price,
        quantity: 1,
        image_url: item.image_url,
      },
    ];
  });

  setAddedItem((prev) => [...prev, item.id]);

  // 2️⃣ API request بالخلفية (بدون تعطيل UI)
  axios.post(
    "http://127.0.0.1:8000/api/cart/items",
    {
      medicine_id: item.id,
      quantity: 1,
    },
    {
      headers: {
        Authorization:` Bearer ${token}`,
        Accept: "application/json",
      },
    }
  ).catch((err) => {
    console.log(err);

    // rollback بسيط إذا فشل
    setCart((prev) =>
      prev.map((p) =>
        p.medicine_id === item.id && p.quantity > 1
          ? { ...p, quantity: p.quantity - 1 }
          : p
      )
    );
  });
}

  return (
    <>
      <Header />
      <SideBar />

      <div className="Contener">
        {medicines.length === 0 ? (
          <p>Loading medicines...</p>
        ) : (
          medicines.map((item) => (
            <div className="product" key={item.id}>
              <div className="info">
                <h2>{item.name}</h2>

                <img
                  src={item.image_url }
                  alt={item.name}
                  
                  width="150"
                  height="150"
                />

                <p>
                  <strong>Uses:</strong>
                  <br />
                  {item.description?.split(",").map((use, i) => (
                    <span key={i}>
                      {use}
                      <br />
                    </span>
                  ))}
                </p>

                <p>
                  <strong>Price:</strong> {item.price} S.P
                </p>

                <button
                  onClick={() => handleAddToCart(item)}
                  style={{
                    backgroundColor: addedItem.includes(item.id)
                      ? "green"
                      : "",
                    color: "white",
                  }}
                >
                  {addedItem.includes(item.id) ? "Added" : "Add to cart"}
                </button>
              </div>
            </div>
          ))
        )}
      </div>

      <Footer />
    </>
  );
}
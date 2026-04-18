import { Routes, Route } from "react-router-dom";
import Login from "./Login";
import Home from "./Home";
import Categories from "./Categories";
import Cart from "./Cart";
import Safe from "./Safe";
import NoSafe from "./NoSafe";
import Prescription from "./Prescription";
import SignUp from "./Singup";
export default function App() {
  return (
    <>
      <Routes>
        
        <Route path="/home" element={<Home />} />
        <Route path="/" element={<Login />} />
        <Route path="/signup" element={<SignUp />} />

        <Route path="/categories" element={<Categories />} />
        <Route path="/cart" element={<Cart />} />
        <Route path="/safe" element={<Safe />} />
        <Route path="/upload" element={<Prescription />} />
        <Route path="/nosafe" element={<NoSafe />} />
      </Routes>
    </>
  );
}

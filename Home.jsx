

import "./home.css";
import { Link } from "react-router-dom";

import Headers from "./components/Headers";
import SideBar from "./components/SideBar";
import Footer from "./components/Footer";

import panadol from "./assets/panadol.jpeg";
import vitamin from "./assets/vitamin.png";
import cough from "./assets/cough.png";
import cream from "./assets/cream.png";

import medicineCat from "./assets/medicine.jpg";
import healthCat from "./assets/health.jpg";
import careCat from "./assets/care.jpg";
import babyCat from "./assets/baby.jpg";

export default function Home() {
  return (
    <div className="app">
      <SideBar />

      <div className="main">
        <Headers />

        <h2 className="section-title">Categories</h2>

        <div className="categories">

          <Link to="/categories" className="cat-card">
            <img src={medicineCat} alt="medicine" />
            <p>Medicine</p>
          </Link>

          <Link to="/categories" className="cat-card">
            <img src={healthCat} alt="health" />
            <p>Health & Wellness</p>
          </Link>

          <Link to="/categories" className="cat-card">
            <img src={careCat} alt="care" />
            <p>Personal Care</p>
          </Link>

          <Link to="/categories" className="cat-card">
            <img src={babyCat} alt="baby" />
            <p>Baby Care</p>
          </Link>

        </div>

        <h2 className="section-title">Popular Products</h2>

        <div className="products">

          <Link to="/categories" className="product-card">
            <img src={panadol} alt="panadol" />
            <h3>Panadol Tablets</h3>
            <p>See Details</p>
            <button>Add to cart</button>
          </Link>

          <Link to="/categories" className="product-card">
            <img src={vitamin} alt="vitamin" />
            <h3>Vitamin C</h3>
            <p>See Details</p>
            <button>Add to cart</button>
          </Link>

          <Link to="/categories" className="product-card">
            <img src={cough} alt="cough" />
            <h3>Cough Syrup</h3>
            <p>See Details</p>
            <button>Add to cart</button>
          </Link>

          <Link to="/categories" className="product-card">
            <img src={cream} alt="cream" />
            <h3>Moisturizing Cream</h3>
            <p>See Details</p>
            <button>Add to cart</button>
          </Link>

        </div>

        <Footer />
      </div>
    </div>
  );
}
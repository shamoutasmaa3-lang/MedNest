import { Link } from "react-router-dom";
import "./Safe.css";
import waitblue from "./assets/waitblue.png";
import checkpoint from "./assets/checkpoint.png";
import Footer from "./components/Footer";

export default function Safe() {
  return (
    <div className="safe-container">

      <div className="success-message">

        <h2>Analyzing your Order....</h2>

        <div className="safe">

          <div className="waitblue">
            <img src={waitblue} alt="waitblue" />
          </div>

          <h4>Your Order is safe</h4>

          <div className="safety-tips">

            <div className="tip">
              <img src={checkpoint} alt="check" />
              <span>No allergies detected</span>
            </div>

            <div className="tip">
              <img src={checkpoint} alt="check" />
              <span>No drug interaction found</span>
            </div>

            <div className="tip">
              <img src={checkpoint} alt="check" />
              <span>Safe dosage confirmed</span>
            </div>

          </div>

          <Link to="/cart" className="back-button">
            <button>Order Now</button>
          </Link>

        </div>
        <Footer />

      </div>

    </div>
  );
}
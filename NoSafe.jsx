import { Link } from "react-router-dom";
import "./NoSafe.css";
import wait from "./assets/wait.png";
import error from "./assets/error.png";
import Footer from "./components/Footer";
export default function NoSafe() {
  return (
    <div className="body">

      <div className="analize">
        Analyzing your order....
      </div>

      <div className="nosafe">

        <div className="wait">
          <img src={wait} alt="wait" />
        </div>

        <div className="warning-message">

          <h2>Drug interaction detected!</h2>
          <div className="error">
          <img src={error} alt="error" />
        </div>

          <h4>
            This prescription may cause harmful interaction.
          </h4>

          <div className="container">

            <h4>Do not use these two medications together</h4>
            <h4>Avoid taking both drugs at the same time</h4>
            <h4>Consult a pharmacist or healthcare provider</h4>

          </div>

        
          <Link to="/" className="button">
            <button>Back to home</button>
          </Link>

        </div>
        <Footer />
      </div>
    </div>
  );
}
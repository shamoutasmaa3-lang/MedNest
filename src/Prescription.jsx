
import "./Prescription.css";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {
  faCloudUploadAlt,
  faCheckCircle,
  faShieldAlt,
  faBrain,
} from "@fortawesome/free-solid-svg-icons";

import SideBar from "./Components/SideBar";
import Footer from "./Components/Footer";

import { useState, useRef } from "react";
import axios from "axios";
import Cookies from "universal-cookie";

export default function Prescription() {
  const [file, setFile] = useState(null);


  const [available, setAvailable] = useState([]);
  const [missing, setMissing] = useState([]);

 
  const [success, setSuccess] = useState(false);
  const [error, setError] = useState("");

  const fileInputRef = useRef(null);
  const cookie = new Cookies();

  const handleFile = (e) => {
    const selectedFile = e.target.files[0];
    if (!selectedFile) return;

    setFile(selectedFile);
    setSuccess(false);
    setError("");
  };

  const handleUploadClick = () => {
    fileInputRef.current.click();
  };

  async function handleUpload(e) {
    e.preventDefault();

    if (!file) {
      setError("choose a file first.");
      return;
    }

    const token = cookie.get("Bearer");

    if (!token) {
      setError("Register a file first.");
      return;
    }

    const formData = new FormData();
    formData.append("file", file);

    try {
      const res = await axios.post(
        "http://127.0.0.1:8000/api/prescriptions/upload",
        formData,
        {
          headers: {
            "Content-Type": "multipart/form-data",
            Authorization: "Bearer " + token,
          },
        }
      );

  
      setAvailable(res.data.available_medicines || []);
      setMissing(res.data.missing_medicines || []);
     
      setSuccess(true);
      setError("");

    } catch (err) {
      console.log(err.response?.data);
      setError("Lift fail");
      setSuccess(false);
    }
  }

  return (
    <div className="prescription-container">
      <SideBar />
      <div className="upload-section">
        <h2>Upload Prescription</h2>

        <div className="upload-box" >
          <FontAwesomeIcon icon={faCloudUploadAlt} size="3x" />
          <p>Darg & Drop your Prescription here <br/>or<span style={{color:"blue" ,curson:"pointer"
            
          }}
          
          onClick={handleUploadClick}> click to Upload </span></p>

          <input
            type="file"
            hidden
            ref={fileInputRef}
            onChange={handleFile}
            accept="image/png,image/jpeg,application/pdf"
          />
        </div>

        {file && <p>Selected: {file.name}</p>}

        {error && <p style={{ color: "red" }}>{error}</p>}

        <button className="upload-btn" onClick={handleUpload}>
          Upload Prescription
        </button>
        <div style={{ marginTop: "15px",
    marginLeft:" 100px",
  color: "#666",
  fontSize: "17px"}}>Support formays:JPG,PNG,PDF</div>

        {success && (
          <p style={{ color: "green" }}>
            <FontAwesomeIcon icon={faCheckCircle} /> Prescription upload successfully
          </p>
        )}
      </div>

    
      <div className="result-section">
        <div className="result-card">
          

          {available.length === 0 ? (
            <p></p>
          ) : (
            available.map((m, i) => <p key={i}> {m.name}</p>)
          )}

        

          {missing.length === 0 ? (
            <p></p>
          ) : (
            missing.map((m, i) => <p key={i} style={{ color: "red" }}> {m}</p>)
          )}
        </div>
        <button className="order-btn"> Order Now</button>
      </div>

    
      <div className="features">
        <div className="feature">
          <FontAwesomeIcon icon={faShieldAlt} />
          Secure Upload
        </div>

        <div className="feature">
          <FontAwesomeIcon icon={faBrain} />
          AI Analysis
        </div>

        <div className="feature">
          <FontAwesomeIcon icon={faCheckCircle} />
         Medication Safe
        </div>
      </div>

      <Footer />
    </div>
  );
}



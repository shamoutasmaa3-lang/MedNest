import React, { useEffect, useState } from "react";
import axios from "axios";

import "./digitalprescreption.css";
import SideBar from "./Components/SideBar";

import profileIcon from "./assets/profile.png";
import deleteIcon from "./assets/Remove.png";
import shieldIcon from "./assets/Shield.png";

const PrescriptionPage = () => {
  const [prescriptions, setPrescriptions] = useState([]);
  const [notes, setNotes] = useState("");

  const [medications, setMedications] = useState([
    { name: "", dosage: "", frequency: "", duration: "" }
  ]);

  
  useEffect(() => {
    fetchPrescriptions();
  }, []);

  const fetchPrescriptions = async () => {
    try {
      const res = await axios.get("/api/doctor/prescriptions");
      setPrescriptions(res.data);
    } catch (err) {
      console.log(err);
    }
  };


  const handleMedChange = (index, field, value) => {
    const updated = [...medications];
    updated[index][field] = value;
    setMedications(updated);
  };

  const addMedication = () => {
    setMedications([
      ...medications,
      { name: "", dosage: "", frequency: "", duration: "" }
    ]);
  };

  const removeMedication = (index) => {
    const updated = medications.filter((_, i) => i !== index);
    setMedications(updated);
  };

  const createPrescription = async () => {
    try {
      await axios.post("/api/doctor/prescriptions", {
        medications,
        notes
      });

      setNotes("");
      setMedications([{ name: "", dosage: "", frequency: "", duration: "" }]);

      fetchPrescriptions();
    } catch (err) {
      console.log(err);
    }
  };

  return (
    <div className="page-layout">
      <SideBar />

      <div className="prescription-container">

        
        <div className="page-header">
          <h1>Create Digital Prescription</h1>
          <p>Securely issue and sign electronic prescription</p>
        </div>

       
        <div className="top-grid">

          
          <div className="card patient-card">
            <h2>Patient Information</h2>

            <div className="patient-info-box">
              <img src={profileIcon} alt="patient" />

              <div className="patient-details">
                <h3>---</h3>
                <div className="patient-meta">
                  <span>Age: --</span>
                  <span>--</span>
                </div>
              </div>
            </div>
          </div>

          
          <div className="card medications-card">
            <h2>Medications</h2>

            <div className="table-header">
              <span>Medicine</span>
              <span>Dosage</span>
              <span>Frequency</span>
              <span>Duration</span>
              <span>Actions</span>
            </div>

            {medications.map((med, index) => (
              <div className="medication-row" key={index}>
                <input
                  placeholder="Medicine"
                  value={med.name}
                  onChange={(e) =>
                    handleMedChange(index, "name", e.target.value)
                  }
                />

                <input
                  placeholder="Dosage"
                  value={med.dosage}
                  onChange={(e) =>
                    handleMedChange(index, "dosage", e.target.value)
                  }
                />

                <input
                  placeholder="Frequency"
                  value={med.frequency}
                  onChange={(e) =>
                    handleMedChange(index, "frequency", e.target.value)
                  }
                />

                <input
                  placeholder="Duration"
                  value={med.duration}
                  onChange={(e) =>
                    handleMedChange(index, "duration", e.target.value)
                  }
                />

                <img
                  src={deleteIcon}
                  alt="delete"
                  onClick={() => removeMedication(index)}
                  style={{ cursor: "pointer" }}
                />
              </div>
            ))}

            <button className="add-btn" onClick={addMedication}>
              + Add Medication
            </button>
          </div>

          
          <div className="card notes-card">
            <h2>Doctor Notes</h2>

            <textarea
              placeholder="Enter notes and instructions for the patient...."
              value={notes}
              onChange={(e) => setNotes(e.target.value)}
            />
          </div>

          
          <div className="card signature-card">
            <h2>Digital Signature</h2>

            <div className="signature-content">
              <img src={shieldIcon} alt="shield" />

              <div>
                <h3>
                  Digital Signature:<span>Ready</span>
                </h3>

                <p>
                  HMAC Signature will be generated when you create the prescription.
                </p>
              </div>
            </div>

            <button className="generate-btn" onClick={createPrescription}>
              Generate & sign prescription
            </button>
          </div>
        </div>

        
        <div className="archive-section">
          <div className="archive-header">
            <h1>Prescription Archive</h1>
            <p>All prescriptions issued by you</p>
          </div>

          <div className="archive-table">
            <div className="archive-table-header">
              <span>Prescription ID</span>
              <span>Patient</span>
              <span>Date & Time</span>
              <span>Medications</span>
              <span>Status</span>
              <span>Signature</span>
            </div>

            {prescriptions.map((p, index) => (
              <div className="archive-row" key={index}>
                <span>{p.id}</span>
                <span>{p.patientName}</span>
                <span>{p.createdAt}</span>
                <span>{p.medications && p.medications.length ? p.medications.length : 0}</span>
                <span className={p.status}>{p.status}</span>
                <span>{p.signature ? "HMAC Verified" : "Pending"}</span>
              </div>
            ))}
          </div>
        </div>

      </div>
    </div>
  );
};

export default PrescriptionPage;
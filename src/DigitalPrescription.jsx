import React, { useEffect, useState } from "react";
import axios from "axios";

import "./DigitalPrescription.css";
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

  const [patientQuery, setPatientQuery] = useState("");
  const [patients, setPatients] = useState([]);
  const [selectedPatient, setSelectedPatient] = useState(null);

  // ================= NORMALIZER (IMPORTANT) =================
  const toArray = (data) => {
    if (Array.isArray(data)) return data;
    if (Array.isArray(data?.data)) return data.data;
    if (Array.isArray(data?.prescriptions)) return data.prescriptions;
    if (Array.isArray(data?.patients)) return data.patients;
    return [];
  };

  // ================= FETCH PRESCRIPTIONS =================
  useEffect(() => {
    fetchPrescriptions();
  }, []);

  const fetchPrescriptions = async () => {
    try {
      const res = await axios.get("/api/doctor/prescriptions");
      setPrescriptions(toArray(res.data));
    } catch (err) {
      console.log(err);
      setPrescriptions([]);
    }
  };

  // ================= MEDICATIONS =================
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
    setMedications(medications.filter((_, i) => i !== index));
  };

  // ================= PATIENT SEARCH =================
  const searchPatients = async (value) => {
    setPatientQuery(value);

    if (!value.trim()) {
      setPatients([]);
      return;
    }

    try {
      const res = await axios.get(
        `/api/v1/patients/search?name=${encodeURIComponent(value)}`
      );

      setPatients(toArray(res.data));
    } catch (err) {
      console.log(err);
      setPatients([]);
    }
  };

  // ================= CREATE PRESCRIPTION =================
  const createPrescription = async () => {
    try {
      await axios.post("/api/doctor/prescriptions", {
        patient_id: selectedPatient?.id,
        medications,
        notes
      });

      setNotes("");
      setMedications([{ name: "", dosage: "", frequency: "", duration: "" }]);
      setSelectedPatient(null);
      setPatientQuery("");

      fetchPrescriptions();
    } catch (err) {
      console.log(err);
    }
  };

  return (
    <div className="page-layout">
      <SideBar />

      <div className="prescription-container">

        {/* HEADER */}
        <div className="page-header">
          <h1>Create Digital Prescription</h1>
          <p>Securely issue and sign electronic prescription</p>
        </div>

        <div className="top-grid">

          {/* ================= PATIENT ================= */}
          <div className="card patient-card">
            <h2>Patient Information</h2>

            {selectedPatient ? (
              <div className="patient-info-box">
                <img src={profileIcon} alt="patient" />

                <div className="patient-details">
                  <h3>{selectedPatient?.name}</h3>
                  <div className="patient-meta">
                    <span>{selectedPatient?.email}</span>
                    <span>{selectedPatient?.phone}</span>
                  </div>

                  <button onClick={() => setSelectedPatient(null)}>
                    Change Patient
                  </button>
                </div>
              </div>
            ) : (
              <>
                <input
                  type="text"
                  placeholder="Search patient by name..."
                  value={patientQuery}
                  onChange={(e) => searchPatients(e.target.value)}
                />

                {toArray(patients).length > 0 && (
                  <div className="search-results">
                    {toArray(patients).map((p) => (
                      <div
                        key={p.id}
                        className="result-item"
                        onClick={() => {
                          setSelectedPatient(p);
                          setPatients([]);
                          setPatientQuery(p.name);
                        }}
                        style={{ cursor: "pointer" }}
                      >
                        <strong>{p.name}</strong> - {p.email}
                      </div>
                    ))}
                  </div>
                )}
              </>
            )}
          </div>

          {/* ================= MEDICATIONS ================= */}
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

          {/* ================= NOTES ================= */}
          <div className="card notes-card">
            <h2>Doctor Notes</h2>

            <textarea
              placeholder="Enter notes and instructions for the patient...."
              value={notes}
              onChange={(e) => setNotes(e.target.value)}
            />
          </div>

          {/* ================= SIGNATURE ================= */}
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

            <button
              className="generate-btn"
              onClick={createPrescription}
              disabled={!selectedPatient}
            >
              Generate & sign prescription
            </button>
          </div>

        </div>

        {/* ================= ARCHIVE ================= */}
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

            {toArray(prescriptions).map((p) => (
              <div className="archive-row" key={p.id}>
                <span>{p.id}</span>
                <span>{p.patientName}</span>
                <span>{p.createdAt}</span>
                <span>{p.medications?.length || 0}</span>
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
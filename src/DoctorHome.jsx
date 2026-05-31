import SideBar from "./Components/SideBar";
import Header from "./Components/Header";
import Footer from "./Components/Footer";

export default function DoctorHome() {
  return (
    <>
      <SideBar />

      <div className="main">
        <Header />

        <h2 style={{ marginTop: "120px" }}>
          
           Doctor Dashboard
        </h2>

        <div className="card">
          <h3> Create Prescription</h3>
          <p>Write prescriptions for patients</p>
        </div>

        <div className="card">
          <h3> Consultations</h3>
          <p>Reply to patients questions</p>
        </div>

        <Footer />
      </div>
    </>
  );
}
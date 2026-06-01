import { Routes, Route } from "react-router-dom";

import Login from "./Login";
import Home from "./Home";
import Categories from "./Categories";
import Profile from "./Profile";
import Cart from "./Cart";
import Safe from "./Safe";
import NoSafe from "./NoSafe";
import Prescription from "./Prescription";
import SignUp from "./Singup";
import Inventory from "./Inventory";
import PharmacistChat from "./PharmacistChat";
import Notification from "./Notification";
import MyOrders from "./MyOrders";
import PatientChat from "./PatientChat";

import RoleRoute from "./RoleRoute";
import DoctorHome from "./DoctorHome";
import PharmacistHome from "./PharmacistHome";
import PharmacistOrders from "./PharmacistOrders";
import PharmacistNotifications from "./PharmacistNotification";
import Recommendations from "./Recommendations";
import PrescriptionPage from "./DigitalPrescription";

export default function App() {
  return (
    <Routes>

      {/* ------ Auth ------ */}
      <Route path="/" element={<Login />} />
      <Route path="/signup" element={<SignUp />} />

      {/* ------ Patient Routes ------ */}
      <Route
        path="/patient"
        element={
          <RoleRoute allowedRoles={["patient"]}>
            <Home />
          </RoleRoute>
        }
      />

      <Route
        path="/categories"
        element={
          <RoleRoute allowedRoles={["patient"]}>
            <Categories />
          </RoleRoute>
        }
      />

      <Route
        path="/cart"
        element={
          <RoleRoute allowedRoles={["patient"]}>
            <Cart />
          </RoleRoute>
        }
      />

      <Route
        path="/upload"
        element={
          <RoleRoute allowedRoles={["patient"]}>
            <Prescription />
          </RoleRoute>
        }
      />

      <Route
        path="/my-orders"
        element={
          <RoleRoute allowedRoles={["patient"]}>
            <MyOrders />
          </RoleRoute>
        }
      />

<Route
  path="/recommendations"
  element={
    <RoleRoute allowedRoles={["patient"]}>
      <Recommendations />
    </RoleRoute>
  }
/>
<Route
  path="/patientChat"
  element={
    <RoleRoute allowedRoles={["patient"]}>
      <PatientChat />
    </RoleRoute>
  }
/>

      <Route
        path="/safe"
        element={
          <RoleRoute allowedRoles={["patient"]}>
            <Safe />
          </RoleRoute>
        }
      />

      <Route
        path="/nosafe"
        element={
          <RoleRoute allowedRoles={["patient"]}>
            <NoSafe />
          </RoleRoute>
        }
      />

      {/* ------ Shared Routes ------ */}
      <Route
        path="/profile"
        element={
          <RoleRoute allowedRoles={["patient", "doctor", "pharmacist"]}>
            <Profile />
          </RoleRoute>
        }
      />

      <Route
        path="/notifaction"
        element={
          <RoleRoute allowedRoles={["patient", "doctor"]}>
            <Notification />
          </RoleRoute>
        }
      />

      {/* ------ Pharmacist ------ */}
      <Route
        path="/pharmacist"
        element={
          <RoleRoute allowedRoles={["pharmacist"]}>
            <PharmacistHome />
          </RoleRoute>
        }
      />

      <Route
        path="/inventory"
        element={
          <RoleRoute allowedRoles={["pharmacist"]}>
            <Inventory />
          </RoleRoute>
        }
      />

      <Route
        path="/pharmacistnotifaction"
        element={
          <RoleRoute allowedRoles={["pharmacist"]}>
            <PharmacistNotifications />
          </RoleRoute>
        }
      />

      <Route
        path="/pharmacistChat"
        element={
          <RoleRoute allowedRoles={["pharmacist"]}>
            <PharmacistChat />
          </RoleRoute>
        }
      />

      <Route
        path="/pharmacistorders"
        element={
          <RoleRoute allowedRoles={["pharmacist"]}>
            <PharmacistOrders />
          </RoleRoute>
        }
      />

      {/* ------ Doctor ------ */}
      <Route
        path="/doctor"
        element={
          <RoleRoute allowedRoles={["doctor"]}>
            <DoctorHome />
          </RoleRoute>
        }
      />

      {/* ✅ NEW: Doctor Prescriptions Page */}
      <Route
        path="/doctor/prescriptions"
        element={
          <RoleRoute allowedRoles={["doctor"]}>
            <PrescriptionPage />
          </RoleRoute>
        }
      />

    </Routes>
  );
}
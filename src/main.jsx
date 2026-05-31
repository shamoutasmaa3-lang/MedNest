import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter as Router } from "react-router-dom";
import './index.css';
import App from './App.jsx';
import UserProvider from "./Context/UserProvider.jsx"

createRoot(document.getElementById('root')).render(
  <Router>
    <StrictMode>
      <UserProvider>
        <App />
      </UserProvider>
    </StrictMode>
  </Router>
);
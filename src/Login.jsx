import { useState, useContext } from "react";
import axios from "axios";
import Cookies from "universal-cookie";
import { User } from "./context/UserContext";
import { Link, useNavigate } from "react-router-dom";
import logo from "./assets/smart.jpeg";

export default function Login() {
  const cookie = new Cookies();
  const nav = useNavigate();

  const { setAuth } = useContext(User);

  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const [accept, setAccept] = useState(false);

  const submit = async (e) => {
    e.preventDefault();
    setAccept(true);

    if (password.length < 8) {
      setError("Password must be at least 8 characters");
      return;
    }

    try {
      const res = await axios.post("http://127.0.0.1:8000/api/login", {
        email,
        password,
      });

      const token = res.data.token;

      if (!token) {
        setError("Token not found in response");
        return;
      }

      const userDetails = res.data.user;
      const userRole = userDetails?.role?.toLowerCase();

      
      cookie.set("token", token, { path: "/" });
      cookie.set("role", userRole, { path: "/" });
      

      setAuth({ token, user: userDetails });

      console.log("ROLE FROM LOGIN:", userRole);

      
      setTimeout(() => {
        if (userRole === "patient") {
          nav("/patient");
        } else if (userRole === "pharmacist") {
          nav("/pharmacist");
        } else if (userRole === "doctor") {
          nav("/doctor");
        } else {
          nav("/");
        }
      }, 0);

    } catch (error) {
      console.log("Login Error:", error.response?.data || error.message);

      if (error.response?.status === 401) {
        setError("Invalid email or password");
      } else {
        setError("Login failed. Try again.");
      }
    }
  };

  return (
    <div className="login-container">
      <div className="image-section">
        <img src={logo} alt="med" />
      </div>

      <div className="form">
        <h1>Welcome Back</h1>
        <h1>Login to your account</h1>

        <form onSubmit={submit}>
          <input
            type="email"
            placeholder="Enter your email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
          />

          <input
            type="password"
            placeholder="Enter your password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
          />

          {password.length < 8 && accept && (
            <p className="error">Password must be at least 8 characters</p>
          )}

          {error && <p className="error">{error}</p>}

          <button type="submit">Login</button>

          <div>
            Don't have an account?
            <Link to="/signup">Sign up</Link>
          </div>
        </form>
      </div>
    </div>
  );
}
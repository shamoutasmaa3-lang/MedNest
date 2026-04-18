import { Link } from "react-router-dom";
import "./index.css";
import logo from "./assets/smart.jpeg";
import { useContext, useState } from "react";
import { User } from "./Context/UserContext"; 
import { useNavigate } from "react-router-dom";
import Cookies from "universal-cookie";
import axios from "axios";

export default function Login() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [accept, setAccept] = useState(false);
  const [error, setError] = useState("");  
  const [loading, setLoading] = useState(false);  

  const nav = useNavigate();
  const cookie = new Cookies();
  const { setAuth } = useContext(User);  

  async function submit(e) {
    e.preventDefault();
    setAccept(true);
    setError(""); 
    setLoading(true);  

    try {
      let res = await axios.post("http://127.0.0.1:8000/api/login", {
        email: email,
        password: password,
      });

     
      if (res.status === 200 && res.data.token) {
        const token = res.data.token;
        cookie.set("Bearer", token,{path:"/"});
        const userDetails = res.data.data?.user || res.data.user;
        setAuth({ token, userDetails });
        
        console.log("✅ Login successful");
        nav("/home");  
      } else {
        setError("Login failed. Please try again.");
      }

    } catch (error) {
      console.log("Login error:", error.response?.data || error.message);
      
    
      if (error.response?.status === 401) {
        setError("Invalid email or password ❌");
      } else if (error.response?.status === 404) {
        setError("Server not found. Please try again later.");
      } else {
        setError("Login failed. Please check your credentials.");
      }
    } finally {
      setLoading(false); 
    }
  }

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
            id="email"
            placeholder="Enter your email"
            required
            value={email}
            onChange={(e) => setEmail(e.target.value)}
          />

          <input
            type="password"
            id="password"
            placeholder="Enter your Password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
          />

          {password.length < 8 && accept && (
            <p className="error">password must be more than 8 char</p>
          )}

          
          {error && <p className="error" style={{color: "red"}}>{error}</p>}

          <div className="checkbox">
            <input type="checkbox" id="checkbox" />
            <label htmlFor="checkbox">Remember Me</label>
          </div>

          <button type="submit" disabled={loading}>
            {loading ? "Logging in..." : "Login"}
          </button>

          <div>
            Don't have an account?
            <Link to="/signup" className="signup-link">
              Sign up
            </Link>
          </div>
        </form>
      </div>
    </div>
  );
}


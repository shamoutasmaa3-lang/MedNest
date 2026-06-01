import { useEffect, useRef, useState } from "react";
import axios from "axios";
import Cookies from "universal-cookie";

import "./PharmacistChat.css";

import profileImg from "./assets/profile.png";
import sendIcon from "./assets/send.png";

import SideBar from "./components/SideBar";

export default function PharmacistChat() {

  const [messages, setMessages] = useState([]);
  const [input, setInput] = useState("");
  const [consultationId, setConsultationId] = useState(null);

  const messagesEndRef = useRef(null);

  const cookie = new Cookies();
  const token = cookie.get("token");

  const config = {
    headers: {
      Authorization: `Bearer ${token}`,
      Accept: "application/json",
    },
  };

  // ================= AUTO SCROLL =================
  useEffect(() => {
    messagesEndRef.current?.scrollIntoView({
      behavior: "smooth",
    });
  }, [messages]);

  // ================= FETCH + POLLING =================
  useEffect(() => {

    const fetchConsultations = async () => {
      try {

        const res = await axios.get(
          "http://127.0.0.1:8000/api/pharmacist/consultations",
          config
        );

        console.log("CONSULTATIONS RAW:", res.data);

        // ✅ يدعم Object أو Array أو null
        const consultation =
          Array.isArray(res.data)
            ? res.data[0]
            : res.data || null;

        if (!consultation) {
          setMessages([]);
          setConsultationId(null);
          return;
        }

        setConsultationId(consultation.id);

        const formattedMessages =
          consultation.messages?.map((msg) => ({
            id: msg.id,
            sender: msg.sender,
            text: msg.text,
            time: new Date(msg.created_at).toLocaleTimeString([], {
              hour: "2-digit",
              minute: "2-digit",
            }),
          })) || [];

        setMessages(formattedMessages);

      } catch (error) {
        console.error(
          "Failed to fetch consultations:",
          error.response?.data || error.message
        );
      }
    };

    // أول تحميل
    fetchConsultations();

    // polling كل 3 ثواني
    const interval = setInterval(() => {
      fetchConsultations();
    }, 3000);

    return () => clearInterval(interval);

  }, []);

  // ================= SEND MESSAGE =================
  const sendMessage = async () => {

    if (!input.trim() || !consultationId) return;

    const messageText = input;

    const localMessage = {
      id: Date.now(),
      sender: "pharmacist",
      text: messageText,
      time: new Date().toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit",
      }),
    };

    setMessages((prev) => [...prev, localMessage]);
    setInput("");

    try {

      await axios.post(
        `http://127.0.0.1:8000/api/consultations/${consultationId}/reply`,
        { message: messageText },
        config
      );

    } catch (error) {
      console.error(
        "Send error:",
        error.response?.data || error.message
      );
    }
  };

  // ================= ENTER SEND =================
  const handleKeyDown = (e) => {
    if (e.key === "Enter") {
      sendMessage();
    }
  };

  return (
    <div className="chat-page">

      <SideBar />

      <div className="chat-header">
        <h1>Patient Messages</h1>
        <p>Respond to patient questions and consultations</p>
      </div>

      <div className="chat-container">

        <div className="pharmacist-info">
          <img src={profileImg} alt="profile" className="avatar" />
          <div>
            <h2>Patient</h2>
            <div className="status">
              <span className="online-dot"></span>
              Online
            </div>
          </div>
        </div>

        <div className="messages-container">

          {messages.map((msg) => (
            <div
              key={msg.id}
              className={`message-row ${
                msg.sender === "user"
                  ? "user-row"
                  : "pharmacist-row"
              }`}
            >
              <div
                className={`message-bubble ${
                  msg.sender === "user"
                    ? "user-message"
                    : "pharmacist-message"
                }`}
              >
                <p>{msg.text}</p>
                <span>{msg.time}</span>
              </div>
            </div>
          ))}

          <div ref={messagesEndRef}></div>

        </div>

        <div className="chat-input-container">

          <input
            type="text"
            placeholder="Type Your Message ..."
            value={input}
            onChange={(e) => setInput(e.target.value)}
            onKeyDown={handleKeyDown}
          />

          <button onClick={sendMessage} className="send-btn">
            <img src={sendIcon} alt="send" />
          </button>

        </div>

      </div>
    </div>
  );
}
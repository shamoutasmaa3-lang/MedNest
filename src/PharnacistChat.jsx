


import { useEffect, useRef, useState } from "react";
import axios from "axios";
import Cookies from "universal-cookie";
import "./PharmacistChat.css";

// import profileImg from "./assets/profile.png";
// import sendIcon from "./assets/send.png";

import SideBar from "./components/SideBar";

export default function PharmacistChat() {
  const [messages, setMessages] = useState([]);
  const [input, setInput] = useState("");
  const [consultationId, setConsultationId] = useState(null);

  const messagesEndRef = useRef(null);

  const cookies = new Cookies();
  const token = cookies.get("Bearer");

  // Auto scroll
  useEffect(() => {
    messagesEndRef.current?.scrollIntoView({ behavior: "smooth" });
  }, [messages]);

  // Fetch consultations
  useEffect(() => {
    const fetchConsultations = async () => {
      try {
        const res = await axios.get(
          "http://127.0.0.1:8000/api/pharmacist/consultations",
          {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          }
        );

        const consultation = res.data?.[0];

        if (consultation) {
          setConsultationId(consultation.id);

          const formattedMessages = consultation.messages.map((msg) => ({
            id: msg.id,
            sender: msg.sender,
            text: msg.text,
            time: new Date(msg.created_at).toLocaleTimeString([], {
              hour: "2-digit",
              minute: "2-digit",
            }),
          }));

          setMessages(formattedMessages);
        }
      } catch (error) {
        console.error("Failed to fetch consultations:", error);
      }
    };

    fetchConsultations();
  }, []);

  // Send message
  const sendMessage = async () => {
    if (!input.trim() || !consultationId) return;

    const userMessage = {
      id: Date.now(),
      sender: "pharmacist",
      text: input,
      time: new Date().toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit",
      }),
    };

    setMessages((prev) => [...prev, userMessage]);

    const msgText = input;
    setInput("");

    try {
      await axios.post(
        `http://127.0.0.1:8000/api/consultations/${consultationId}/reply`,
        { message: msgText },
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );
    } catch (error) {
      console.error("Failed to send reply:", error);
    }
  };

  const handleKeyDown = (e) => {
    if (e.key === "Enter") sendMessage();
  };

  return (
    <div className="chat-page">
      <SideBar />

      <div className="chat-header">
        <h1>Talk to a Pharmacist</h1>
        <p>Talk to a pharmacist anytime for help with your medications</p>
      </div>

      <div className="chat-container">
        <div className="pharmacist-info">
          {/* <img src={profileImg} alt="profile" className="avatar" /> */}
          <div>
            <h2>Pharmacist Omar</h2>
            <div className="status">
              <span className="online-dot"></span>
              Online
            </div>
          </div>
        </div>

        {/* Messages */}
        <div className="messages-container">
          {messages.map((msg) => (
            <div
              key={msg.id}
              className={`message-row ${
                msg.sender === "user" ? "user-row" : "pharmacist-row"
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

        {/* Input */}
        <div className="chat-input-container">
          <input
            type="text"
            placeholder="Type Your Message ..."
            value={input}
            onChange={(e) => setInput(e.target.value)}
            onKeyDown={handleKeyDown}
          />

          <button onClick={sendMessage} className="send-btn">
            {/* <img src={sendIcon} alt="send" /> */}
          </button>
        </div>
      </div>
    </div>
  );
}

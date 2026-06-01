import { useEffect, useRef, useState } from "react";
import axios from "axios";
import "./PatientChat.css";

import profileImg from "./assets/profile.png";
import sendIcon from "./assets/send.png";

import SideBar from "./components/SideBar";

export default function PatientChat() {
  const [messages, setMessages] = useState([]);
  const [input, setInput] = useState("");
  const [consultationId, setConsultationId] = useState(null);

  const messagesEndRef = useRef(null);

  // Auto Scroll
  useEffect(() => {
    if (messagesEndRef.current) {
        messagesEndRef.current.scrollIntoView({
          behavior: "smooth",
        });
      }
  }, [messages]);

  // Fetch consultation + messages
  useEffect(() => {
    const fetchConsultations = async () => {
      try {
        const res = await axios.get("/api/patient/consultations");

        const consultation = res.data && res.data[0];

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

  // Send Message
  const sendMessage = async () => {
    if (!input.trim()) return;
  
    const patientMessage = {
      id: Date.now(),
      sender: "user",
      text: input,
      time: new Date().toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit",
      }),
    };
  
    setMessages((prev) => [...prev, patientMessage]);
  
    const messageText = input;
    setInput("");
  
    try {
  
      // إذا ما في consultation
      let currentConsultationId = consultationId;
  
      if (!currentConsultationId) {
  
        const createRes = await axios.post(
          "http://127.0.0.1:8000/api/consultations",
          {}
        );
  
        currentConsultationId = createRes.data.id;
  
        setConsultationId(currentConsultationId);
      }
  
      // إرسال الرسالة
      await axios.post(
        `http://127.0.0.1:8000/api/consultations/${currentConsultationId}/message`,
        {
          message: messageText,
        }
      );
  
    } catch (error) {
      console.error("Failed to send message:", error.response?.data || error);
    }
  };
  // Send on Enter
  const handleKeyDown = (e) => {
    if (e.key === "Enter") {
      sendMessage();
    }
  };

  return (
    <div className="chat-page">

      <SideBar />

      <div className="chat-header">
        <h1>Talk to Pharmacist</h1>
        <p>Ask your pharmacist about medications anytime</p>
      </div>

      <div className="chat-container">

        {/* Pharmacist Info */}
        <div className="pharmacist-info">
          <img src={profileImg} alt="profile" className="avatar" />

          <div>
            <h2>Pharmacist</h2>

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
            <img src={sendIcon} alt="send" />
          </button>
        </div>

      </div>
    </div>
  );
}
import { useState } from "react";
import "./style.css";

export default function Header() {
  const [query, setQuery] = useState("");
  const [results, setResults] = useState([]);

  const handleSearch = async (value) => {
    setQuery(value);

    if (!value.trim()) {
      setResults([]);
      return;
    }

    try {
      const res = await fetch(
        `/api/v1/medicines/search?query=${encodeURIComponent(value)}`
      );

      const data = await res.json();

      // حسب Laravel عندك غالبًا البيانات داخل data
      setResults(data.data || []);
    } catch (error) {
      console.error("Search error:", error);
    }
  };

  return (
    <div className="Header">
      <div className="header-top">
        <i className="fas fa-staff-snake icon"></i>

        <div className="title-text">
          <h1>Welcome to Mednest Website</h1>
          <h4>Smart & Safe Medication Management</h4>
        </div>
      </div>

      <div className="header-bottom">
        <div className="ask-text">What do you need today?</div>

        <div className="search-box">
          <input
            type="search"
            placeholder="Search for medicines, products..."
            value={query}
            onChange={(e) => handleSearch(e.target.value)}
          />
        </div>

        {/* النتائج */}
        {results.length > 0 && (
          <div className="search-results">
            {results.map((item, index) => (
              <div key={index} className="result-item">
                {item.name}
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
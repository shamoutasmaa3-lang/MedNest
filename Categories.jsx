import { useState, useEffect, useContext } from "react";
import { products as localProducts } from "./Product";
import "./Categories.css";
import SideBar from "./components/SideBar";
import Headers from "./components/Headers";
import axios from "axios";
import { User } from "./Context/UserContext";

export default function Categories() {
  const [searchQuery, setSearchQuery] = useState("");
  const [searchResults, setSearchResults] = useState([]);
  const [searching, setSearching] = useState(false);
  const [searchError, setSearchError] = useState("");
  const [hasSearched, setHasSearched] = useState(false);
  const { setCart, cart } = useContext(User);

  function handleAddToCart(item) {
    const alreadyIn = cart.find((c) => c.id === item.id);
    if (!alreadyIn) {
      setCart([...cart, item]);
    }
  }

  async function handleSearch(e) {
    e.preventDefault();
    const q = searchQuery.trim();
    if (!q) return;

    setSearching(true);
    setSearchError("");
    setHasSearched(true);

    try {
      const res = await axios.get(
        `http://127.0.0.1:8000/api/v1/medicines/search?query=${encodeURIComponent(q)}`
      );
      setSearchResults(res.data.data || []);
    } catch (err) {
      console.error("Search error:", err.response?.data || err.message);
      setSearchError("Search failed. Please try again.");
      setSearchResults([]);
    } finally {
      setSearching(false);
    }
  }

  function handleClear() {
    setSearchQuery("");
    setSearchResults([]);
    setHasSearched(false);
    setSearchError("");
  }

  const displayProducts = hasSearched ? searchResults : localProducts;

  return (
    <>
      <Headers />
      <SideBar />

      <div style={{ padding: "16px 24px" }}>
        {/* Search Bar */}
        <form onSubmit={handleSearch} style={{ display: "flex", gap: "8px", marginBottom: "20px" }}>
          <input
            type="text"
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            placeholder="Search medicines by name, category..."
            style={{
              flex: 1,
              padding: "10px 16px",
              borderRadius: "8px",
              border: "1px solid #ccc",
              fontSize: "15px",
            }}
          />
          <button
            type="submit"
            disabled={searching}
            style={{
              padding: "10px 20px",
              background: "#2563eb",
              color: "#fff",
              border: "none",
              borderRadius: "8px",
              cursor: "pointer",
              fontWeight: "600",
            }}
          >
            {searching ? "Searching..." : "Search"}
          </button>
          {hasSearched && (
            <button
              type="button"
              onClick={handleClear}
              style={{
                padding: "10px 16px",
                background: "#6b7280",
                color: "#fff",
                border: "none",
                borderRadius: "8px",
                cursor: "pointer",
              }}
            >
              Clear
            </button>
          )}
        </form>

        {searchError && (
          <p style={{ color: "red", marginBottom: "12px" }}>{searchError}</p>
        )}

        {hasSearched && (
          <p style={{ marginBottom: "12px", color: "#555" }}>
            {searchResults.length === 0
              ? "No medicines found."
              : `Found ${searchResults.length} result(s) for "${searchQuery}"`}
          </p>
        )}
      </div>

      <div className="Contener">
        {displayProducts.map((item) => (
          <div className="product" key={item.id}>
            <img src={item.img || item.image || ""} alt={item.name} />

            <div className="info">
              <h2>{item.name}</h2>

              {/* Local products show uses; API results show category/description */}
              {item.uses ? (
                <p>
                  <strong>Uses:</strong>
                  <br />
                  {item.uses.map((u) => (
                    <span key={u}>
                      {u}
                      <br />
                    </span>
                  ))}
                </p>
              ) : (
                <>
                  {item.category && <p><strong>Category:</strong> {item.category}</p>}
                  {item.description && <p>{item.description}</p>}
                  {item.requires_prescription && (
                    <p style={{ color: "orange" }}>⚠ Requires Prescription</p>
                  )}
                </>
              )}

              <p>
                <strong>Price:</strong>{" "}
                {item.price ? `${item.price} S.P` : "N/A"}
              </p>

              <button onClick={() => handleAddToCart(item)}>Add to cart</button>
            </div>
          </div>
        ))}
      </div>
    </>
  );
}

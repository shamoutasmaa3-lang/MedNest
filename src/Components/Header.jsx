import "./style.css";

export default function Header() {
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

        <div className="ask-text">
          What do you need today?
        </div>

        <div className="search-box">
          <input
            type="search"
            placeholder="Search for medicines,products..."
          />
        </div>

      </div>

    </div>
  );
}
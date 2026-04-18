import { products } from "./Product";
import "./Categories.css";
import SideBar from "./Components/SideBar";
import Headers from "./Components/Headers";
export default function Categories() {

  function handleAddToCart(name, price) {
    console.log(name, price)
  }

  return (<>
  <Headers/><SideBar/>
    <div className="Contener">

      {products.map((item) => (
        <div className="product" key={item.id}>
          <img src={item.img} alt={item.name} />

          <div className="info">
            <h2>{item.name}</h2>

            <p>
              <strong >Uses:</strong><br />
              {item.uses.map((u) => (
                <span key={u}>
                  {u}<br />
                </span>
              ))}
            </p>

            <p><strong>Price:</strong> {item.price} s.p</p>

            <button onClick={() => handleAddToCart(item.name, item.price)}>
              Add to cart
            </button>
          </div>
        </div>
      ))}
    </div></>
  )
}

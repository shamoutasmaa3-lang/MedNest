import Panadol from "./assets/Panadol.jpeg";
import Ibuprofen from "./assets/Ibuprofen.jpeg";
import Amoxicillin from "./assets/P500.jpeg";
import Omeprazole from "./assets/Omeprazole.jpeg";
import Loratadine from "./assets/Loratadine.jpeg";
import Paracetamol from "./assets/Paracetamol.jpeg";
import VitaminD from "./assets/VitaminD.jpeg";
import Azimycin from "./assets/azim.webp";
import Metfor from "./assets/meftformin.jpeg";
import Aspirine from "./assets/aspirin.jpeg";
import Dic from "./assets/diclofenac.jpeg";
import Amlop from "./assets/amlopine.jpeg";
import Domp from "./assets/dompridone.jpeg";
import Losart from "./assets/losartan.jpeg";
export const products = [
  {
    id: 1,
    name: "Panadol",
    price: 10000,
    img: Panadol,
    uses: ["Headache relief", "Reducing fever"],
  },

  {
    id: 2,
    name: "Omeprazole",
    price: 20000,
    img: Omeprazole,
    uses: ["Treats acid reflux", "Stomach ulcers"],
  },

  {
    id: 3,
    name: "Ibuprofen",
    price: 25000,
    img: Ibuprofen,
    uses: ["Pain relief", "Reduces inflammation"],
  },

  {
    id: 4,
    name: "Loratadine",
    price: 40000,
    img: Loratadine,
    uses: ["Relieves allergy symptoms"],
  },

  {
    id: 5,
    name: "Amoxicillin",
    price: 30000,
    img: Amoxicillin,
    uses: ["Treats bacterial infections"],
  },

  {
    id: 6,
    name: "Paracetamol",
    price: 8000,
    img: Paracetamol,
    uses: ["Relieves pain", "Reduces fever"],
  },
  
  { id: 7, 
    name: "Loratadine",
     uses: ["Relieves allergy symptoms"],
      price: 40000,
       img: "/images/loratadine.jpg" },


  { id: 8, 
    name: "Azithromycin",
     uses: ["Respiratory infections", "Sore throat"], 
     price: 35000,
      img:Azimycin },

  { id: 9,
     name: "Cetirizine",
      uses: ["Allergy relief", "Hay fever"],
       price: 15000,
        img: "",
      },

  { id: 10, 
    name: "Metformin", 
    uses: ["Lowers blood sugar", "Type 2 diabetes"],
     price: 12000, 
     img:Metfor },

  { id: 11, 
    name: "Aspirin",
     uses: ["Pain relief", "Fever reducer", "Blood thinner"],
      price: 7000, 
      img: Aspirine  },
  { id: 12,
     name: "Diclofenac",
      uses: ["Joint pain", "Anti-inflammatory"], 
      price: 18000, 
      img: Dic },
  { id: 13,
     name: "Amlodipine",
      uses: ["High blood pressure", "Chest pain"],
       price: 22000,
        img: Amlop},
  { id: 14,
     name: "Domperidone",
      uses: ["Nausea relief", "Indigestion"],
       price: 9000, 
       img: Domp  },
  { id: 15,
     name: "Losartan",
      uses: ["High blood pressure", "Kidney protection"],
       price: 25000,
       
       img:Losart  },
  { id: 16,
     name: "Vitamin D",
      uses: ["Bone health", "Immune support"], 
      price: 5000, 
      img: VitaminD },


];


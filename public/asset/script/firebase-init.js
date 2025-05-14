import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.0/firebase-app.js";
import { getFirestore } from "https://www.gstatic.com/firebasejs/11.6.0/firebase-firestore.js";

// Replace only this portion  -- Start

// Replace only this portion -- End

const app = initializeApp(firebaseConfig);
const db = getFirestore(app);

export {app, db};
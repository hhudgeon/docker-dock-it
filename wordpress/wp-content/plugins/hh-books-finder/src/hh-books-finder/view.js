import App from "./app/App";
import { createRoot } from "react-dom/client";

const blocks = document.querySelectorAll(".wp-block-create-block-hh-books-finder");

blocks.forEach((block) => {
	createRoot(block).render(<App />);
});

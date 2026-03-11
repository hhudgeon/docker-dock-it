import React, { useState, useEffect } from "react";
import BookList from "./components/BookList";

export default function App(props) {

	let [books, setBooks] = useState([]);

	useEffect(() => {
		fetch("/wp-json/wp/v2/hh-book?_embed&orderby=title&order=asc")
			.then((response) => response.json())
			.then((data) => {
				setBooks(data);
			});
	}, []);

	return (
		<div>
			<h3>Book Finder</h3>
			<BookList items={books} />
		</div>
	);
}

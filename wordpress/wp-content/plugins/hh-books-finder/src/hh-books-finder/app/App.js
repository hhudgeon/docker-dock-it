import React, { useState, useEffect } from "react";
import BookList from "./components/BookList";
import SearchBar from "./components/SearchBar";
import SortControls from "./components/SortControls";


export default function App(props) {
	let [books, setBooks] = useState([]);
	let [searchTerm, setSearchTerm] = useState("");
	let [sortOrder, setSortOrder] = useState("asc");
	let [currentPage, setCurrentPage] = useState(1);
	let [totalPages, setTotalPages] = useState(1);
	let [loading, setLoading] = useState(true);

	useEffect(() => {
		setLoading(true);

		let apiURL = `/wp-json/wp/v2/hh-book?_embed&per_page=5&page=${currentPage}&orderby=title&order=${sortOrder}`;

		if (searchTerm) {
			apiURL += `&search=${encodeURIComponent(searchTerm)}`;
		}

		fetch(apiURL)
			.then((response) => {
				let pages = response.headers.get("X-WP-TotalPages");
				setTotalPages(Number(pages) || 1);
				return response.json();
			})
			.then((data) => {
				setBooks(data);
				setLoading(false);
			});
	}, [currentPage, sortOrder, searchTerm]);

	return (
		<div className="book-finder">
			<h3>Book Finder</h3>
			<p>Type in a keyword from the title you are looking for.</p>

			<div className="book-controls">
				<SearchBar
					searchTerm={searchTerm}
					setSearchTerm={setSearchTerm}
					setCurrentPage={setCurrentPage}
				/>

				<SortControls
					sortOrder={sortOrder}
					setSortOrder={setSortOrder}
					setCurrentPage={setCurrentPage}
				/>
			</div>

			<p>Showing {books.length} books</p>

			{loading ? (
				<p>Loading books...</p>
			) : (
				<BookList items={books} />
			)}

			<div className="book-pagination">
				<button
					type="button"
					onClick={() => setCurrentPage(currentPage - 1)}
					disabled={currentPage === 1}
				>
					Previous
				</button>

				<span>Page {currentPage} of {totalPages}</span>

				<button
					type="button"
					onClick={() => setCurrentPage(currentPage + 1)}
					disabled={currentPage === totalPages}
				>
					Next
				</button>
			</div>
		</div>
	);
}

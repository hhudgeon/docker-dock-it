import React, { useState, useEffect } from "react";
import BookList from "./components/BookList";

export default function App(props) {

	let [books, setBooks] = useState([]);
	let [searchTerm, setSearchTerm] = useState("");
	let [sortOrder, setSortOrder] = useState("asc");
	let [currentPage, setCurrentPage] = useState(1);
	let [loading, setLoading] = useState(true);

	const booksPerPage = 5;

	useEffect(() => {
		fetch("/wp-json/wp/v2/hh-book?_embed&orderby=title&order=asc")
			.then((response) => response.json())
			.then((data) => {
				setBooks(data);
				setLoading(false);
			});
	}, []);

	let filteredBooks = books.filter((book) =>
		book.title.rendered.toLowerCase().includes(searchTerm.toLowerCase())
	);

	let sortedBooks = [...filteredBooks].sort((a, b) => {
		if (sortOrder === "asc") {
			return a.title.rendered.localeCompare(b.title.rendered);
		} else {
			return b.title.rendered.localeCompare(a.title.rendered);
		}
	});

	const startIndex = (currentPage - 1) * booksPerPage;
	const paginatedBooks = sortedBooks.slice(startIndex, startIndex + booksPerPage);
	const totalPages = Math.ceil(sortedBooks.length / booksPerPage);

	return (
		<div>
			<h3>Book Finder</h3>

			<input
				type="text"
				placeholder="Search books..."
				value={searchTerm}
				onChange={(e) => {
					setSearchTerm(e.target.value);
					setCurrentPage(1);
				}}
			/>

			<p>Showing {paginatedBooks.length} books</p>
			<BookList items={paginatedBooks} />
			<div>
				<button
					onClick={() => setCurrentPage(currentPage - 1)}
					disabled={currentPage === 1}
				>
					Previous
				</button>

				<span> Page {currentPage} of {totalPages} </span>

				<button
					onClick={() => setCurrentPage(currentPage + 1)}
					disabled={currentPage === totalPages}
				>
					Next
				</button>
			</div>
		</div>
	);
}

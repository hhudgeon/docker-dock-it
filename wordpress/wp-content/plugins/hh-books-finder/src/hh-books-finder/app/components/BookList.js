import React from "react";
import BookCard from "./BookCard";

export default function BookList(props) {

	let books = props.items;

	return (
		<div className="book-list">
			{books.map((book) => (
				<BookCard key={book.id} item={book} />
			))}
		</div>
	);
}

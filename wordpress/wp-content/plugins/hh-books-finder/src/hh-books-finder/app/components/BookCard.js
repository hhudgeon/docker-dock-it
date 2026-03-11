import React from "react";

export default function BookCard(props) {

	let book = props.item;

	let image = "";

	if (
		book._embedded &&
		book._embedded["wp:featuredmedia"] &&
		book._embedded["wp:featuredmedia"][0]
	) {
		image = book._embedded["wp:featuredmedia"][0].source_url;
	}

	return (
		<div className="book-card">
			{image && (
				<img
					src={image}
					alt={book.title.rendered}
					className="book-card__image"
				/>
			)}

			<h3 className="book-card__title">{book.title.rendered}</h3>
		</div>
	);
}

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
		<div className="book-card hh-panel">
			<div className="book-card__grid">
				{image && (
					<div className="book-card__media">
						<img
							src={image}
							alt={book.title.rendered}
							className="book-card__image book-cover"
						/>
					</div>
				)}

				<div className="book-card__content">
					<h3
						className="book-card__title"
						dangerouslySetInnerHTML={{ __html: book.title.rendered }}
					/>

					{book.excerpt?.rendered && (
						<div
							className="book-card__excerpt"
							dangerouslySetInnerHTML={{ __html: book.excerpt.rendered }}
						/>
					)}

					<p className="book-card__readmore">
						<a href={book.link}>View Book</a>
					</p>
				</div>
			</div>
		</div>
	);
}

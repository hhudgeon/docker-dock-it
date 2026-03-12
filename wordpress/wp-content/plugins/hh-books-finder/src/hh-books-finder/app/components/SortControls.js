import React from "react";

export default function SortControls({ sortOrder, setSortOrder, setCurrentPage }) {

	return (
		<div className="book-sort-buttons">

			<button
				type="button"
				onClick={() => {
					setSortOrder("asc");
					setCurrentPage(1);
				}}
				disabled={sortOrder === "asc"}
			>
				A–Z
			</button>

			<button
				type="button"
				onClick={() => {
					setSortOrder("desc");
					setCurrentPage(1);
				}}
				disabled={sortOrder === "desc"}
			>
				Z–A
			</button>

		</div>
	);
}

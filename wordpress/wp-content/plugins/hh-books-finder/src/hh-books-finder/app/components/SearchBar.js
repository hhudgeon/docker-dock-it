import React from "react";

export default function SearchBar({ searchTerm, setSearchTerm, setCurrentPage }) {

	return (
		<input
			type="text"
			placeholder="Search books..."
			value={searchTerm}
			onChange={(e) => {
				setSearchTerm(e.target.value);
				setCurrentPage(1);
			}}
		/>
	);
}

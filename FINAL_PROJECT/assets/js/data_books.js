document.addEventListener("DOMContentLoaded", function () {
    const tableBody = document.querySelector("#bookTable tbody");

    // Function to fetch and display books
    function fetchBooks() {
        fetch("../actions/get_books.php")
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Failed to fetch books.");
                }
                return response.json();
            })
            .then((data) => {
                tableBody.innerHTML = ""; // Clear existing rows
                data.forEach(book => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${book.book_id}</td>
                        <td>${book.title}</td>
                        <td>${book.author}</td>
                        <td>${book.Year}</td>
                        <td>${book.quantity}</td>
                    `;
                    tableBody.appendChild(row);
                });
            })
            .catch((error) => {
                console.error("Error fetching books:", error);
            });
    }

    // Initial fetch of books on page load
    fetchBooks();
});

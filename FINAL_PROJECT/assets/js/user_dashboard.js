document.addEventListener("DOMContentLoaded", function () {
    const tableBody = document.querySelector("#bookTable tbody");

    // Fetch available books
    fetch("../actions/get_available_books.php")
        .then(response => response.json())
        .then(data => {
            data.forEach(book => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${book.book_id}</td>
                    <td>${book.title}</td>
                    <td>${book.author}</td>
                    <td>${book.Year}</td>
                    <td>
                        <button class="borrow-btn" data-id="${book.book_id}">Borrow</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });

            // Add event listeners to borrow buttons
            document.querySelectorAll(".borrow-btn").forEach(button => {
                button.addEventListener("click", function () {
                    const bookId = this.getAttribute("data-id");
                    const returnDate = prompt("Enter the return date (YYYY-MM-DD):");
                    if (returnDate) {
                        borrowBook(bookId, returnDate);
                    }
                });
            });
        })
        .catch(error => console.error("Error loading books:", error));
});

// Function to send a borrow request
function borrowBook(bookId, returnDate) {
    fetch("../actions/borrow_book.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ book_id: bookId, return_date: returnDate })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Borrow request sent successfully!");
            location.reload(); // Reload to update available books
        } else {
            alert(data.message || "Failed to send borrow request.");
        }
    })
    .catch(error => console.error("Error sending borrow request:", error));
}

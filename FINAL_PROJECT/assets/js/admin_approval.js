document.addEventListener("DOMContentLoaded", function () {
    const tableBody = document.querySelector("#requestsTable tbody");

    // Fetch pending requests from the server
    fetch("../actions/get_pending_requests.php")
        .then(response => response.json())
        .then(data => {
            data.forEach(request => {
                const row = document.createElement("tr");

                row.innerHTML = `
                    <td>${request.request_id}</td>
                    <td>${request.user_id}</td>
                    <td>${request.book_id}</td>
                    <td>${request.title}</td>
                    <td>${request.return_date}</td>
                    <td>
                        <button class="approve-btn" data-id="${request.request_id}">Approve</button>
                        <button class="reject-btn" data-id="${request.request_id}">Reject</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });

            // Add event listeners for buttons
            tableBody.addEventListener("click", function (e) {
                if (e.target.classList.contains("approve-btn") || e.target.classList.contains("reject-btn")) {
                    const requestId = e.target.dataset.id;
                    const action = e.target.classList.contains("approve-btn") ? "approve" : "reject";

                    fetch("../actions/manage_requests.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify({ requestId, action }),
                    })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                e.target.closest("tr").remove();
                                alert(`Request ${action}d successfully.`);
                            } else {
                                alert(`Failed to ${action} request.`);
                            }
                        });
                }
            });
        })
        .catch(error => console.error("Error fetching requests:", error));
});

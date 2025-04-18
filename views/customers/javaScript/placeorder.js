document.getElementById('place_order_btn').addEventListener('click', function (e) {
    e.preventDefault();

    // Get form values
    const transactionid_container = document.getElementById('transaction_id_input');
    const res_id = document.getElementById('res_id').value;
    const grand_total = document.getElementById('order_total').value;
    const order_note = document.getElementById('order_note').value;
    const payment_method = document.getElementById('order_payment_method').value;
    const transaction_id = transactionid_container.value;
    const paymentProof = document.querySelector('#screenshot_img').files[0];

    // Confirm before placing the order
    Swal.fire({
        title: 'Confirm Order',
        text: "Are you sure you want to place this order?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#38a169',
        cancelButtonColor: '#e53e3e',
        confirmButtonText: 'Yes, place it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {

            // Validate inputs
            if (!res_id || !grand_total || !order_note || !payment_method || !paymentProof) {
                Swal.fire({
                    title: 'Missing Data!',
                    icon: 'error',
                    text: "Please complete all order fields and upload a payment screenshot.",
                });
                return;
            }
            if (!transaction_id.trim()) {
                Swal.fire({
                    title: 'Missing Transaction ID!',
                    icon: 'error',
                    text: "Please enter a valid transaction ID.",
                });
            
                // Fixing the styling line
                transactionid_container.style.borderColor = "red";
                // Correct way to focus an input
                transactionid_container.focus();
                return;
            }

            // Prepare FormData
            const formData = new FormData();
            // formData.append('cust_id', cust_id); // Uncomment if needed
            formData.append('res_id', res_id);
            formData.append('grand_total', grand_total);
            formData.append('order_note', order_note);
            formData.append('payment_method', payment_method);
            formData.append('transaction_id', transaction_id)
            formData.append('payment_proof', paymentProof);
            formData.append('order_status', 'pending');

            // Send data using fetch without manually setting Content-Type
            fetch('../../controllers/place_customer_order_controller.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                Swal.close(); // Close loading state if any

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Order Placed!',
                        text: result.message || 'Your order has been placed successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Redirect to success page or clear form
                        window.location.href = "order_success_page.php"; // replace with actual page
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: result.message || 'Something went wrong. Please try again.',
                    });
                }
            })
            .catch(error => {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'Failed to submit your order. Please try again later.',
                });
            });
        }
    });
});

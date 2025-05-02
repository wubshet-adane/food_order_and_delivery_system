
function cancelOrder(orderId) {
    if (confirm('Are you sure you want to cancel this order?')) {
        fetch('ajax_cancel_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `order_id=${orderId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Order cancelled successfully');
                location.reload();
            } else {
                alert(data.message || 'Failed to cancel order');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while cancelling the order');
        });
    }
}

// function reorderItems(orderId) {
//     if (confirm('Are you sure you want to reorder these items?')) {
//         // Make an AJAX request to reorder the items
//         // You can use fetch or XMLHttpRequest here
//         // For example, using fetch:
//         // Make an AJAX request to reorder the items
//     fetch('ajax_reorder.php', {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/x-www-form-urlencoded',
//         },
//         body: `order_id=${orderId}`
//     })
//     .then(response => response.json())
//     .then(data => {
//         if (data.success) {
//             window.location.href = 'cart.php';
//         } else {
//             alert(data.message || 'Failed to reorder items');
//         }
//     })
//     .catch(error => {
//         console.error('Error:', error);
//         alert('An error occurred while processing your reorder');
//     });
// }


function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    const temp = document.createElement("input");
    temp.value = element.textContent.trim();
    document.body.appendChild(temp);
    temp.select();
    document.execCommand("copy");
    document.body.removeChild(temp);
    alert("Copied: " + temp.value);
}

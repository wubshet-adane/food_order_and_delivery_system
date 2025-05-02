// Toggle order details
function toggleOrderDetails(header) {
    const allDetails = document.querySelectorAll('.order-details');
    const orderCard = header.parentElement;
    const detailsDiv = orderCard.querySelector('.order-details');
    const orderId = orderCard.getAttribute('data-order-id');

    console.log('Clicked Order ID:', orderId);
    console.log('Clicked Header:', header);
    console.log('Clicked Order Card:', orderCard);
    console.log('Details Div:', detailsDiv);
    // Collapse all other cards
    allDetails.forEach(div => {
        if (div !== detailsDiv) div.classList.remove('active');
    });

    // Toggle current one
    if (detailsDiv.classList.contains('active')) {
        detailsDiv.classList.remove('active');
    } else {
        loadOrderDetails(orderId, detailsDiv);
        detailsDiv.classList.add('active');
    }
}

// Load order details using Fetch API
function loadOrderDetails(orderId, targetElement) {
    targetElement.innerHTML = `
        <div class="loading">
            <div class="loading-spinner"></div>
            <p>Loading order details...</p>
        </div>
    `;

    fetch(`ajax_get_order_details.php?order_id=${orderId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Server error');
            }
            return response.text();
        })
        .then(data => {
            targetElement.innerHTML = data;
            console.log('Order details loaded:', data);
        })
        .catch(error => {
            targetElement.innerHTML = `
                <div class="empty-state" style="padding: 20px;">
                    <i class="fas fa-exclamation-circle"></i>
                    <p>${error.message || 'Failed to load order details. Please try again.'}</p>
                </div>
            `;
        });
}

// Load details for first order automatically
document.addEventListener('DOMContentLoaded', function() {
    const firstOrder = document.querySelector('.order-card');
    if (firstOrder) {
        const firstOrderId = firstOrder.getAttribute('data-order-id');
        const firstDetailsDiv = document.getElementById(`details-${firstOrderId}`);
        loadOrderDetails(firstOrderId, firstDetailsDiv);
        firstDetailsDiv.classList.add('active');
    }
});

//function to download QR code
function downloadQRCode() {
    const link = document.createElement('a');
    link.href = `https://api.qrserver.com/v1/create-qr-code/?data=<?= htmlspecialchars($order['secret_code']) ?>&size=200x200`;
    link.download = `order-<?= $order['order_id'] ?>-qrcode.png`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }

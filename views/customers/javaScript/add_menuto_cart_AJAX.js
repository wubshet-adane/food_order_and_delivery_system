document.addEventListener("DOMContentLoaded", function() {
    var buttons = document.querySelectorAll('.add_to_cart');

    buttons.forEach(function(button) {
        button.addEventListener('click', function() {
            var menuId = this.getAttribute('data-menu-id');
            var quantityInput = document.getElementById('quantity_' + menuId);
            var discountElement = document.getElementById('discount_' + menuId);

            if (!quantityInput) {
                alert("Quantity input not found!");
                return;
            }

            if (!discountElement) {
                alert("Discount element not found!");
                return;
            }

            var quantity = parseInt(quantityInput.value);
            var discountValue = parseInt(discountElement.value.replace(/[^\d.]/g, ''));

            if (quantity < 1 || isNaN(quantity)) {
                alert("Please enter a valid quantity.");
                return;
            }

            if (isNaN(discountValue)) {
                alert("Invalid discount value.");
                return;
            }

            fetch("add_to_cart.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "menu_id=" + encodeURIComponent(menuId) +
                      "&quantity=" + encodeURIComponent(quantity) +
                      "&discount=" + encodeURIComponent(discountValue)
            })
            .then(response => response.text())
            .then(data => {
                try {
                    const jsonData = JSON.parse(data);
                    showToast('Item added to cart!', 'success');
                } catch (error) {
                    console.error("JSON Parse Error:", error);
                    alert("Error parsing response. Please try again later.");
                }
            })
            .catch(error => {
                console.error("Fetch Error:", error);
                alert("Error occurred. Please try again later.");
            });

             // Toast notification function
                function showToast(message, type) {
                    const toast = document.createElement('div');
                    toast.className = `toast toast-${type}`;
                    toast.textContent = message;
                    document.body.appendChild(toast);
                    
                    setTimeout(() => {
                        toast.classList.add('show');
                    }, 10);
                    
                    setTimeout(() => {
                        toast.classList.remove('show');
                        setTimeout(() => {
                            toast.remove();
                        }, 300);
                    }, 3000);
                }

            // Add this CSS for toast notifications
            const toastCSS = `
            .toast {
                position: fixed;
                top: 20px;
                left: 50%;
                transform: translateX(-50%);
                padding: 12px 24px;
                border-radius: 4px;
                color: white;
                font-weight: bold;
                z-index: 1000;
                opacity: 0;
                transition: opacity 0.3s ease;
                box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            }

            .toast.show {
                opacity: 1;
            }

            .toast-success {
                background-color: #28a745;
            }

            .toast-error {
                background-color: #dc3545;
            }

            /* Pulse animation */
            .pulse {
                animation: pulse 0.5s ease;
            }

            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.2); }
                100% { transform: scale(1); }
            }
            `;

            // Inject the toast CSS
            const style = document.createElement('style');
            style.textContent = toastCSS;
            document.head.appendChild(style);
        });
    });
});

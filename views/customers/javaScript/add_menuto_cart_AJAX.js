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
                    alert(jsonData.message);
                } catch (error) {
                    console.error("JSON Parse Error:", error);
                    alert("Error parsing response. Please try again later.");
                }
            })
            .catch(error => {
                console.error("Fetch Error:", error);
                alert("Error occurred. Please try again later.");
            });
        });
    });
});

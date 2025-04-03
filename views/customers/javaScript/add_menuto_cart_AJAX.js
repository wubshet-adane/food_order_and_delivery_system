// Wait until the page is fully loaded
document.addEventListener("DOMContentLoaded", function() {
    var buttons = document.querySelectorAll('.add_to_cart');
    
    buttons.forEach(function(button) {
        button.addEventListener('click', function() {
            var menuId = this.getAttribute('data-menu-id');
            var quantityInput = document.getElementById('quantity_' + menuId);
            
            console.log("Menu ID:", menuId);
            console.log("Quantity Input:", quantityInput);

            if (!quantityInput) {
                alert("Quantity input not found!");
                return;
            }

            var quantity = parseInt(quantityInput.value);

            if (quantity < 1 || isNaN(quantity)) {
                alert("Please enter a valid quantity.");
                return;
            }

            //console.log("Quantity:", quantity);

            fetch("add_to_cart.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "menu_id=" + menuId + "&quantity=" + quantity
            })
            .then(response => response.text())  // Fetch as raw text
            .then(data => {
                //console.log("Raw Response Text:", data);  // Log raw response
                try {
                    const jsonData = JSON.parse(data);  // Manually parse JSON if valid
                    console.log("Parsed Response:", jsonData);
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
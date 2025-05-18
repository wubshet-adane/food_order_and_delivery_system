
    let scannerInstance = null;

    document.querySelectorAll('.scanBtn').forEach(button => {
        button.addEventListener('click', function () {
            const orderId = this.getAttribute('data-id');
            const reader = document.getElementById('reader');

            reader.style.display = 'block';

            if (scannerInstance) {
                scannerInstance.stop().then(() => {
                    scannerInstance.clear();
                    startScanner(orderId);
                });
            } else {
                startScanner(orderId);
            }
        });
    });

    function startScanner(orderId) {
        scannerInstance = new Html5Qrcode("reader");

        scannerInstance.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: 250 },
            function onScanSuccess(decodedText) {
                document.getElementById('result').innerText = `Scanned: ${decodedText}`;

                scannerInstance.stop().then(() => {
                    document.getElementById('reader').style.display = 'none';
                });

                // Send order_id + secret_code to backend
                fetch('update_by_scanning.php?request_name=Ajax', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        order_id: orderId,
                        secret_code: decodedText
                    })
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.success ? "✅ Order updated successfully" : "❌ Order update failed");
                    //window.location.href = 'index.php?page=order_control';
                });
            }
        ).catch(err => {
            console.error("Failed to start camera:", err);
        });
    }

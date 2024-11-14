<?php
session_start();

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'volan_app';

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Kapcsolódási hiba: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Számla Generátor</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
    <link rel="stylesheet" href="styles.css">
    <style>
        :root {
            --primary-color: #001F3F;
            --accent-color: #FFC107;
            --text-light: #FFFFFF;
            --shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

/*-----------------------------------------------------------------------------------------------------HEADER------------------------------------------------------------------------------------------------------------*/

        .header {
            position: relative;
            background-color: var(--primary-color);
            color: var(--text-light);
            padding: 16px;
            box-shadow: var(--shadow);
            text-align: center;
            margin-bottom: 20px;
        }

        .navh1{
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        nav {
            position: relative;
            background-color: var(--primary-color);
            padding: 8px;
            width: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 3px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
            transition: width 0.6s linear;
            margin-right: 10px;
            margin-top: 30px;
            margin-bottom: 30px;
            max-height: 50px;
        }

        nav.active {
            width: 99%;
        }

        nav ul {
            display: flex;
            list-style-type: none;
            padding: 0;
            margin: 0;
            width: 0;
            transition: width 0.6s linear;
        }

        nav.active ul {
            width: 100%;
        }

        nav ul li {
            transform: rotateY(0deg);
            opacity: 0;
            transition: transform 0.6s linear, opacity 0.6s linear;
            padding: 15px;
        }

        nav.active ul li {
            opacity: 1;
            transform: rotateY(360deg);
        }

        nav ul a {
            position: relative;
            color: #000;
            text-decoration: none;
            margin: 0 5px;
        }

        .icon {
            background-color: var(--primary-color);
            border: 0;
            cursor: pointer;
            padding: 0;
            position: relative;
            height: 30px;
            width: 30px;
        }

        .icon:hover{
            background-color: var(--primary-color);
        }

        .icon:focus {
            outline: 0;
        }

        .icon .line {
            background-color: var(--text-light);
            height: 2px;
            width: 20px;
            position: absolute;
            top: 10px;
            left: 5px;
            transition: transform 0.6s linear;
        }

        .icon .line2 {
            top: auto;
            bottom: 10px;
        }

        nav.active .icon .line1 {
            transform: rotate(-765deg) translateY(5.5px);
        }

        nav.active .icon .line2 {
            transform: rotate(765deg) translateY(-5.5px);
        }

/*-----------------------------------------------------------------------------------------------------HEADER END--------------------------------------------------------------------------------------------------------*/

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: var(--shadow);
        }

        .input-group {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .input-wrapper {
            flex: 1;
            min-width: 200px;
            padding-right: 30px;
        }

        input, select {
            width: 100%;
            padding: 12px;
            border: 1.5px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background-color: #fff;
        }

        input:focus, select:focus {
            border-color: var(--accent-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.2);
        }

        .button-group {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 25px;
        }

        button {
            padding: 12px 25px;
            background-color: var(--accent-color);
            color: var(--primary-color);
            border: none;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            background-color: #FFD700;
            transform: translateY(-2px);
        }

        #invoice {
            margin-top: 30px;
            padding: 25px;
            border: 2px solid var(--accent-color);
            border-radius: 12px;
            background-color: #fff;
        }

        #invoiceDetails {
            white-space: pre-wrap; /* Ensures that whitespace is preserved */
            font-size: 14px;
        }

        canvas {
            margin-top: 20px;
            border: 1px solid var(--accent-color); /* QR kód keret */
        }

        .section-title {
            font-size: 1.2em;
            font-weight: 600;
            margin: 25px 0 15px;
            color: var(--primary-color);
            border-bottom: 2px solid var(--accent-color);
            padding-bottom: 8px;
        }

        .input-label {
            display: block;
            margin-bottom: 5px;
            font-size: 0.9em;
            color: #666;
        }

        input:invalid, select:invalid {
            border-color: var(--error-color);
        }

        .error-message {
            color: var(--error-color);
            font-size: 0.8em;
            margin-top: 5px;
            display: none;
        }

        .price-display {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            text-align: right;
            font-size: 1.3em;
            font-weight: 600;
            color: var(--primary-color);
            margin: 20px 0;
        }     

        #qrcode {
            display: block;
            margin: 20px auto;
            padding: 10px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

/*-----------------------------------------------------------------------------------------------------@MEDIA------------------------------------------------------------------------------------------------------------*/
        @media (max-width: 768px) {
            .input-group {
                flex-direction: column;
            }
            
            .input-wrapper {
                width: 95%;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            button {
                width: 99%;
            }

            nav.active{
                width: 95%;
            }
        }
/*-----------------------------------------------------------------------------------------------------@MEDIA END--------------------------------------------------------------------------------------------------------*/
   
    </style>
</head>
<body>


    <div class="header">
        <nav class="active" id="nav">
            <ul>
              <li><a href="index.php" style="color: #FFFFFF; font-weight: bold;"><img src="placeholder.png" style="height: 30px; width: 30px;"> Főoldal</a></li>
              <li><a href="buy.html.php" style="color: #FFFFFF; font-weight: bold;"><img src="tickets.png" style="height: 30px; width: 30px;"> Jegyvásárlás</a></li>
              <li><a href="menetrend.php" style="color: #FFFFFF; font-weight: bold;"><img src="calendar.png" style="height: 30px; width: 30px;"> Menetrend</a></li>
              <li><a href="info.php" style="color: #FFFFFF; font-weight: bold;"><img src="information-button.png" style="height: 30px; width: 30px;"> Információ</a></li>

            </ul>
            <button class="icon" id="toggle">
              <div class="line line1"></div>
              <div class="line line2"></div>
            </button>
          </nav>
    
<div class="navh1">
        <h1>Jegy és bérlet vásárlás</h1>
    </div>
    </div>
    <div style="margin-top: 5%;" class="container">
        <form id="invoiceForm" novalidate>
            <div style="font-weight: bold;" class="section-title">Vásárló adatai</div>
            <div class="input-group">
                <div class="input-wrapper">
                    <label class="input-label">Teljes név*</label>
                    <input type="text" id="name" pattern="[A-Za-zÀ-ž\s]{2,50}" 
                           placeholder="pl. Nagy János" required
                           oninput="validateField(this)">
                    <div class="error-message">Kérjük, adjon meg egy érvényes nevet (2-50 karakter)</div>
                </div>
                <div class="input-wrapper">
                    <label class="input-label">E-mail cím*</label>
                    <input type="email" id="email" 
                           placeholder="pelda@email.hu" required
                           oninput="validateField(this)">
                    <div class="error-message">Kérjük, adjon meg egy érvényes email címet</div>
                </div>
            </div>
            <div class="input-group">
                <div class="input-wrapper">
                    <label class="input-label">Telefonszám*</label>
                    <input type="tel" id="phone" 
                           pattern="[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4}" 
                           placeholder="+36 30 123 4567" required
                           oninput="validateField(this)">
                    <div class="error-message">Kérjük, adjon meg egy érvényes telefonszámot</div>
                </div>
                <div class="input-wrapper">
                    <label class="input-label">Adószám</label>
                    <input type="text" id="vatNumber" 
                           pattern="[0-9]{8}-[0-9]{1}-[0-9]{2}"
                           placeholder="12345678-1-12"
                           oninput="validateField(this)">
                    <div class="error-message">Az adószám formátuma: 12345678-1-12</div>
                </div>
            </div>
            <div class="input-wrapper">
                <label class="input-label">Számlázási cím*</label>
                <input type="text" id="address" 
                       placeholder="1234 Város, Példa utca 123." required
                       oninput="validateField(this)">
                <div class="error-message">Kérjük, adja meg a számlázási címet</div>
            </div>

            <div style="font-weight: bold;" class="section-title">Jegy adatai</div>
            <div class="input-group">
                <div class="input-wrapper">
                    <label class="input-label">Jegytípus*</label>
                    <select id="ticketType" required onchange="updatePrice(); validateField(this)">
                        <option value="" disabled selected>Válasszon jegytípust</option>
                        <option value="adult-single" data-price="450">Vonaljegy - Teljes árú (450 Ft)</option>
                        <option value="adult-daily" data-price="1800">Napijegy - Teljes árú (1800 Ft)</option>
                        <option value="adult-monthly" data-price="9500">Havi bérlet - Teljes árú (9500 Ft)</option>
                        <option value="student-monthly" data-price="3450">Havi bérlet - Tanulói (3450 Ft)</option>
                        <option value="senior-monthly" data-price="3450">Havi bérlet - Nyugdíjas (3450 Ft)</option>
                    </select>
                    <div class="error-message">Kérjük, válasszon jegytípust</div>
                </div>
                <div class="input-wrapper">
                    <label class="input-label">Mennyiség*</label>
                    <input type="number" id="quantity" min="1" max="10" value="1" required
                           class="quantity-input" onchange="updatePrice(); validateField(this)">
                    <div class="error-message">A mennyiség 1 és 10 között lehet</div>
                </div>
            </div>
            <div class="input-group">
                <div class="input-wrapper">
                    <label class="input-label">Érvényesség kezdete*</label>
                    <input type="date" id="validFrom" required
                           onchange="updateValidUntil(); validateField(this)">
                    <div class="error-message">Kérjük, válasszon kezdő dátumot</div>
                </div>
                <div class="input-wrapper">
                    <label class="input-label">Érvényesség vége</label>
                    <input type="date" id="validUntil" readonly>
                </div>
            </div>
           

            <div style="font-weight: bold;" class="section-title">Fizetési információk</div>
            <div class="input-group">
                <div class="input-wrapper">
                    <label class="input-label">Fizetési mód*</label>
                    <select id="paymentMethod" required onchange="validateField(this)">
                        <option value="" disabled selected>Válasszon fizetési módot</option>
                        <option value="card">Bankkártya</option>
                        <option value="simplepay">SimplePay</option>
                        <option value="paypal">PayPal</option>
                    </select>
                    <div class="error-message">Kérjük, válasszon fizetési módot</div>
                </div>
            </div>

            <div class="input-wrapper">
                <label class="input-label">Számlaszám*</label>
                <input type="text" id="szamlaszam" placeholder="#### #### #### ####">
            </div>

            <div class="price-display">
                Végösszeg: <span id="totalPrice">0</span> Ft
            </div>

            <div class="button-group">
                <button type="button" onclick="generateInvoice()">Számla generálása</button>
            </div>
        </form>

        <div id="invoice" style="display: none;">
            <h2>Számla előnézet</h2>
            <pre id="invoiceDetails"></pre>
            <canvas id="qrcode"></canvas>
            <div class="button-group">
                <button onclick="downloadPDF()">PDF letöltése</button>
            </div>
        </div>
    </div>


    <script>
      
        const today = new Date();
        document.getElementById("validFrom").value = today.toISOString().split("T")[0];
        document.getElementById("validFrom").min = today.toISOString().split("T")[0];

        // Set validUntil min date and default (next month for monthly tickets)
        document.getElementById("validUntil").min = today.toISOString().split("T")[0];

        // Calculate price when ticket type or quantity changes
        function updatePrice() {
            const ticketSelect = document.getElementById('ticketType');
            const quantity = document.getElementById('quantity').value;
            const selectedOption = ticketSelect.options[ticketSelect.selectedIndex];
            
            if (selectedOption.value) {
                const basePrice = parseInt(selectedOption.dataset.price);
                const total = basePrice * quantity;
                document.getElementById('totalPrice').textContent = total.toLocaleString();

                // Update validUntil date based on ticket type
                const validFrom = new Date(document.getElementById('validFrom').value);
                if (selectedOption.value.includes('monthly')) {
                    const validUntil = new Date(validFrom);
                    validUntil.setMonth(validUntil.getMonth() + 1);
                    document.getElementById('validUntil').value = validUntil.toISOString().split('T')[0];
                } else {
                    const validUntil = new Date(validFrom);
                    validUntil.setDate(validUntil.getDate() + 1);
                    document.getElementById('validUntil').value = validUntil.toISOString().split('T')[0];
                }
            }
        }

        document.getElementById('ticketType').addEventListener('change', updatePrice);
        document.getElementById('quantity').addEventListener('change', updatePrice);
        document.getElementById('validFrom').addEventListener('change', updatePrice);

        function generateRandomId() {
            return 'TKT-' + Date.now().toString(36) + Math.random().toString(36).substr(2, 5);
        }

        function generateInvoice() {
            const formData = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                vatNumber: document.getElementById('vatNumber').value,
                address: document.getElementById('address').value,
                ticketType: document.getElementById('ticketType').options[document.getElementById('ticketType').selectedIndex].text,
                quantity: document.getElementById('quantity').value,
                validFrom: document.getElementById('validFrom').value,
                validUntil: document.getElementById('validUntil').value,
                paymentMethod: document.getElementById('paymentMethod').value,
                totalPrice: document.getElementById('totalPrice').textContent,
                szamlaszam: document.getElementById('szamlaszam').value,
                invoiceId: generateRandomId(),
                invoiceDate: new Date().toLocaleDateString('hu-HU')
            };

            
            if (!formData.name || !formData.email || !formData.phone || !formData.address || 
                !formData.ticketType || !formData.paymentMethod || !formData.szamlaszam) {
                alert("Kérjük, töltse ki az összes kötelező mezőt!");
                return;
            }

            
            const invoiceDetails = `Számla
            
----------------------------------------------------------------------------------------            
Számlaszám: ${formData.szamlaszam}
Kiállítás dátuma: ${formData.invoiceDate}


Név: ${formData.name}
${formData.vatNumber ? 'Adószám: ' + formData.vatNumber : ''}
Cím: ${formData.address}
E-mail: ${formData.email}
Telefon: ${formData.phone}

Termék részletei:

Megnevezés: ${formData.ticketType}
Mennyiség: ${formData.quantity} db
Érvényesség kezdete: ${formData.validFrom}
Érvényesség vége: ${formData.validUntil}

Fizetési indormációk:

Fizetési mód: ${formData.paymentMethod}
Végösszeg: ${formData.totalPrice} Ft

Egyedi azonosító: ${formData.invoiceId}
----------------------------------------------------------------------------------------`;

            document.getElementById('invoiceDetails').innerText = invoiceDetails;
            document.getElementById('invoice').style.display = 'block';

            // Generate QR code
            const qrCode = new QRious({
                element: document.getElementById('qrcode'),
                value: formData.invoiceId,
                size: 150
            });
            
        }

        function downloadPDF() {
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF();
            
            // Add company logo/header
            pdf.setFontSize(20);
            pdf.setTextColor(0, 31, 63); // Primary color
            pdf.text("Volán Számla", 105, 20, { align: "center" });
            
            // Add invoice details
            pdf.setFontSize(12);
            pdf.setTextColor(0, 0, 0);
            const invoiceText = document.getElementById('invoiceDetails').innerText;
            const splitText = pdf.splitTextToSize(invoiceText, 180);
            pdf.text(splitText, 15, 40);

            // Add QR code
            const qrCanvas = document.getElementById('qrcode');
            const imgData = qrCanvas.toDataURL('image/png');
            pdf.addImage(imgData, 'PNG', 150, 200, 40, 40);

            // Add footer
            pdf.setFontSize(8);
            pdf.text("Ez egy elektronikusan generált számla.", 105, 280, { align: "center" });
            
            pdf.save(`számla-${new Date().toISOString().slice(0,10)}.pdf`);
        }

        const toggle = document.getElementById('toggle')
        const nav = document.getElementById('nav')

        toggle.addEventListener('click', () => nav.classList.toggle('active'));
    </script>
</body>
</html>
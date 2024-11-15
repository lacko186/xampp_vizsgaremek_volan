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
   
footer {
            text-align: center;
            padding: 10px;
            background-color: var(--primary-color);
            color: var(--text-light);
            border-radius: 10px;
            margin-top: 20px;
            box-shadow: var(--shadow);
            background: var(--primary-color);
            color: var(--text-light);
            padding: 3rem 2rem;
            margin-top: 4rem;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .footer-section h2 {
            margin-bottom: 1rem;
            color: var(--accent-color);
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.5rem;
        }

        .footer-links a {
            color: var(--text-light);
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-links a:hover {
            color: var(--accent-color);
        }
/*-----------------------------------------------------------------------------------------------------CSS -FOOTER--------------------------------------------------------------------------------------------------------*/

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
    <div style="margin-top: 5%; margin-bottom: 5%" class="container">
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

    
    <!--------------------------------------------------------------------------------------Késés----------------------------------------------------------->

    <div style="max-width: 20%" class="container">
    <form id="delayForm" novalidate>
        <div style="font-weight: bold;" class="section-title">Igazolás</div>
        <div class="input-group">
            <div class="input-wrapper">
                <p style="text-align:center">Késett a busz?</p>
                <label class="input-label">Teljes név*</label>
                <input type="text" id="delayName" pattern="[A-Za-zÀ-ž\s]{2,50}" 
                       placeholder="pl. Nagy János" required
                       oninput="validateField(this)"><br>
                       
                <label class="input-label">Kiindulási Állomás*</label><br>
                <input type="text" id="startStation" pattern="[A-Za-zÀ-ž\s]{2,50}" 
                       placeholder="pl. Kaposvár" required
                       oninput="validateField(this)"><br>
                       
                <label class="input-label">Célállomás*</label>
                <input type="text" id="endStation" pattern="[A-Za-zÀ-ž\s]{2,50}" 
                       placeholder="pl. Nagykanizsa" required
                       oninput="validateField(this)"><br>

                <label class="input-label">Indulás</label>
                <input type="time" id="departureTime" value="00:00"
                        required
                       oninput="validateField(this)">
                <label class="input-label">Érkezés</label>
                <input type="time" id="arrivalTime" value="00:00"
                        required
                       oninput="validateField(this)">
                <label class="input-label">Késés (perc)</label>
                <input type="number" id="delayDuration"
                placeholder="pl. 34"
                        required
                       oninput="validateField(this)">        
            </div>
        </div>
        <p style="color: red; font-weight: bold;text-align:center">Figyelem!!<br> Igazolást csak 20 perc késés esetén állíthat ki!</p>
           
        <button type="button" style="margin-top:10%;margin-left: 30%" onclick="downloadPDF2()">Igazolás letöltése</button>
    </form>
</div>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h2>Kaposvár közlekedés</h2>
                <p style="font-style: italic">Megbízható közlekedési szolgáltatások<br> az Ön kényelméért már több mint 50 éve.</p><br>
                <div class="social-links">
                    <a href="https://www.facebook.com/VOLANBUSZ/"><svg xmlns="http://www.w3.org/2000/svg" style="max-width: 10px" viewBox="0 0 320 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#00008b" d="M279.1 288l14.2-92.7h-88.9v-60.1c0-25.4 12.4-50.1 52.2-50.1h40.4V6.3S260.4 0 225.4 0c-73.2 0-121.1 44.4-121.1 124.7v70.6H22.9V288h81.4v224h100.2V288z"/></svg></a>
                    <a href="https://x.com/volanbusz_hu?mx=2"><svg xmlns="http://www.w3.org/2000/svg" style="max-width: 15px" viewBox="0 0 512 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#add8e6" d="M459.4 151.7c.3 4.5 .3 9.1 .3 13.6 0 138.7-105.6 298.6-298.6 298.6-59.5 0-114.7-17.2-161.1-47.1 8.4 1 16.6 1.3 25.3 1.3 49.1 0 94.2-16.6 130.3-44.8-46.1-1-84.8-31.2-98.1-72.8 6.5 1 13 1.6 19.8 1.6 9.4 0 18.8-1.3 27.6-3.6-48.1-9.7-84.1-52-84.1-103v-1.3c14 7.8 30.2 12.7 47.4 13.3-28.3-18.8-46.8-51-46.8-87.4 0-19.5 5.2-37.4 14.3-53 51.7 63.7 129.3 105.3 216.4 109.8-1.6-7.8-2.6-15.9-2.6-24 0-57.8 46.8-104.9 104.9-104.9 30.2 0 57.5 12.7 76.7 33.1 23.7-4.5 46.5-13.3 66.6-25.3-7.8 24.4-24.4 44.8-46.1 57.8 21.1-2.3 41.6-8.1 60.4-16.2-14.3 20.8-32.2 39.3-52.6 54.3z"/></svg></a>
                    <a href="https://www.instagram.com/volanbusz/"><svg xmlns="htt://www.w3.org/2000/svg" style="max-width: 15px" viewBox="0 0 448 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#ff0000" d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 2p9.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/></svg></a>
                </div>
            </div>
           
            <div  class="footer-section">
                <h3>Elérhetőség</h3>
                <ul class="footer-links">
                    <li><svg style="max-width: 17px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#ffffff" d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z"/></svg> +36-82/411-850</li>
                    <li><svg style="max-width: 17px"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#ffffff" d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48L48 64zM0 176L0 384c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-208L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z"/></svg> titkarsag@kkzrt.hu</li>
                    <li><svg style="max-width: 16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#ffffff" d="M172.3 501.7C27 291 0 269.4 0 192 0 86 86 0 192 0s192 86 192 192c0 77.4-27 99-172.3 309.7-9.5 13.8-29.9 13.8-39.5 0zM192 272c44.2 0 80-35.8 80-80s-35.8-80-80-80-80 35.8-80 80 35.8 80 80 80z"/></svg> 7400 Kaposvár, Cseri út 16.</li>
                    <li><svg style="max-width: 16px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#ffffff" d="M172.3 501.7C27 291 0 269.4 0 192 0 86 86 0 192 0s192 86 192 192c0 77.4-27 99-172.3 309.7-9.5 13.8-29.9 13.8-39.5 0zM192 272c44.2 0 80-35.8 80-80s-35.8-80-80-80-80 35.8-80 80 35.8 80 80 80z"/></svg> Áchim András utca 1.</li>
                </ul>
            </div>
        </div>
        <div style="text-align: center; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1);">
            <p>© 2024 Kaposvár közlekedési Zrt. Minden jog fenntartva.</p>
        </div>
    </footer>
    <script>
      
      // Validate delay form fields
function validateDelayForm() {
    const name = document.getElementById('delayName').value;
    const startStation = document.getElementById('startStation').value;
    const endStation = document.getElementById('endStation').value;
    const departureTime = document.getElementById('departureTime').value;
    const arrivalTime = document.getElementById('arrivalTime').value;
    const delayDuration = document.getElementById('delayDuration').value;

    if (!name || !startStation || !endStation || !departureTime || !arrivalTime || !delayDuration) {
        alert("Kérjük, töltse ki az összes kötelező mezőt!");
        return false;
    }

    // Check if delay is at least 20 minutes
    if (delayDuration < 20) {
        alert("Igazolás csak 20 perc vagy annál hosszabb késés esetén állítható ki!");
        return false;
    }

    return true;
}

// Calculate delay duration automatically
function calculateDelay() {
    const departureTime = document.getElementById('departureTime').value;
    const arrivalTime = document.getElementById('arrivalTime').value;

    if (departureTime && arrivalTime) {
        const departure = new Date(`2000-01-01T${departureTime}`);
        const arrival = new Date(`2000-01-01T${arrivalTime}`);
        
        // If arrival is before departure, add 24 hours to arrival
        if (arrival < departure) {
            arrival.setDate(arrival.getDate() + 1);
        }

        const diffMinutes = Math.round((arrival - departure) / (1000 * 60));
        document.getElementById('delayDuration').value = diffMinutes;
    }
}

// Add event listeners for automatic delay calculation
document.getElementById('departureTime').addEventListener('change', calculateDelay);
document.getElementById('arrivalTime').addEventListener('change', calculateDelay);

// Generate unique ID for delay verification
function generateDelayVerificationId() {
    return 'DELAY-' + Date.now().toString(36) + Math.random().toString(36).substr(2, 5);
}

// Modified downloadPDF2 function to only include image
function downloadPDF2() {
    if (!validateDelayForm()) {
        return;
    }

    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF();
    
    // Set custom font
    pdf.setFont("helvetica", "bold");
    
    // Add title
    pdf.setFontSize(15);
    pdf.setTextColor(0, 31, 63);
    try {
        pdf.addImage('kapos_logo.png', 'PNG', 50, 70, 60, 50);
    } catch (error) {
        console.error('Hiba a kép betöltésekor:', error);
        alert('Hiba történt a kép betöltésekor. Kérjük, ellenőrizze, hogy a kép létezik és megfelelő formátumú.');
        return;
    }
    pdf.text("Késési Igazolás", 105, 35, { align: "center" });

    // Add verification ID
    const verificationId = generateDelayVerificationId();
    pdf.setFontSize(12);
    pdf.setTextColor(0, 0, 0);
    pdf.text(`Igazolás azonosító: ${verificationId}`, 20, 50);

    pdf.text(`Jegykiadó :` `Kaposvári Közlekedési Zrt.` 30,20,100,50);
    pdf.text(`Kiindulási Állomés`);
    pdf.text(`Célállomás:`);
    pdf.text(`Késés-perc:`);
    

    pdf.text(`A menetjegye(ke)t és az igazolást meg kell őrizni az esetleges igényérvényesítés céljáből`);

    pdf.text(`Aláírás::`);


    try {
        pdf.addImage('alairas.png', 'PNG', 50, 70, 60, 50);
    } catch (error) {
        console.error('Hiba a kép betöltésekor:', error);
        alert('Hiba történt a kép betöltésekor. Kérjük, ellenőrizze, hogy a kép létezik és megfelelő formátumú.');
        return;
    }
    
    // Save the PDF
    pdf.save(`kesesi-igazolas-${verificationId}.pdf`);
}

// Initialize date fields
const today = new Date();
document.getElementById("validFrom").value = today.toISOString().split("T")[0];
document.getElementById("validFrom").min = today.toISOString().split("T")[0];
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
            pdf.text("Kaposvári Közlekedési Zrt. Számla", 105, 20, { align: "center" });
            
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

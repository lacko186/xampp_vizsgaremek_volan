<?php
session_start();

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'volan_app';

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);} 
catch(PDOException $e) {
    die("Kapcsolódási hiba: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Volán Szervezeti Információs Lap</title>
    <style>
        :root {
            --primary-color: #001F3F;
            --accent-color: #FFC107;
            --text-light: #FFFFFF;
            --shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: sans-serif, Roboto;
            background-color: #f5f5f5;
            color: #333;
            min-height: 100vh;
            
        }

        .header {
            position: relative;
            background-color: var(--primary-color);
            color: var(--text-light);
            padding: 16px;
            text-align: center;
            box-shadow: var(--shadow);
        }

        #navbar {
            background-color: var(--primary-color);
            color: var(--text-light);
            padding: 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        #navbar h1 {
            font-size: 1.5em;
            color: var(--text-light);
            margin: 0;
            flex-grow: 1;
        }

        .navbar-nav {
            display: flex;
            gap: 20px;
            margin-left: auto;
        }

        .navbar-nav a {
            color: var(--text-light);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .navbar-nav a:hover {
            color: var(--accent-color);
        }

        main {
            padding: 20px;
            margin-top: 100px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .card {
            background-color: white;
            border-radius: 15px;
            box-shadow: var(--shadow);
            padding: 20px;
            margin: 15px 0;
            width: 80%;
            max-width: 600px;
            transition: transform 0.3s;
        }

        .card:hover {
            transform: scale(1.02);
        }

        .card h2 {
            color: var(--primary-color);
        }

        .doc-link {
            text-decoration: none;
            color: #333;
            transition: color 0.3s;
        }

        .doc-link:hover {
            color: var(--accent-color);
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: var(--primary-color);
            color: var(--text-light);
            border-radius: 10px;
            margin-top: 20px;
            box-shadow: var(--shadow);
        }

        ul {
            list-style: none;
        }

        ul li {
            margin-bottom: 8px;
            padding: 5px;
        }
        
        @media (max-width: 768px) {
            #navbar {
                flex-direction: column;
                align-items: flex-start;
            }
            .navbar-nav {
                flex-direction: column;
                gap: 10px;
            }
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
            box-shadow: 0 2px 5px rgba(255, 255, 255, 0.3);
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
        .navh1{
max-width: 1200px;
margin: 0 auto;
padding: 20px;
        }
       .lang{
        height: 40px; background-color: white; color: black; border: 2px solid black; width: 20%; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;border-radius: 15px;
    font-weight: bold;

    }
       .lang:hover{
        background-color: black;
        color: white;
        border: 2px solid black;
        
       }
      p{
        font-weight: bold;
      }
    </style>
</head>
<body>
<div class="header">
    <nav class="active" id="nav">
        <ul>
         <li><a href="index.php" style="color: #FFFFFF; font-weight: bold;"><img src="placeholder.png" style="height: 30px; width: 30px;"> Főoldal</a></li>
         <li><a href="buy.php" style="color: #FFFFFF; font-weight: bold;"><img src="tickets.png" style="height: 30px; width: 30px;"> Jegyvásárlás</a></li>
         <li><a href="menetrend.php" style="color: #FFFFFF; font-weight: bold;"><img src="calendar.png" style="height: 30px; width: 30px;"> Menetrend</a></li>
         <li><a href="info.php" style="color: #FFFFFF; font-weight: bold;"><img src="information-button.png" style="height: 30px; width: 30px;"> Információ</a></li>
        </ul>
        <button class="icon" id="toggle">
          <div class="line line1"></div>
          <div class="line line2"></div>
        </button>
      </nav>
<div class="navh1">
        <h1>Volán Szervezeti Információ</h1>
    </div>
</div>
    <main>
        <section id="about" class="card">
            <button style="margin-top: 5%; margin-right: 5%;margin-left: 15%;" class="lang" id="lang_eng">Eng</button>
            <button style="margin-top: 5%;margin-right: 5%;" class="lang" id="lang_de">De</button>
            <button style="margin-top: 5%; margin-right: 5%;" class="lang" id="lang_hu">Hu</button></span>
            <br>
            <h2 id="rolunk" style="margin-top: 10%;">Rólunk</h2><br>
            <p id="szoveg"><span style="font-weight: bold">Tisztelt utazóközönségünk!</span><br><br>Üdvözlöm  társaságunk, a Kaposvári Közlekedési Zrt. oldalán.<br> Honlapunkon nemcsak cégünk működésével ismerkedhet meg, de megtalálja az utazást érintő legfrissebb információkat és az aktuális menetrendet is, melyet úgy alakítottunk ki és úgy módosítunk, hogy minél kényelmesebbé tegyük a közlekedést utasaink számára.<br>Elkötelezettek vagyunk környezet-tudatosság mellett, ezért néhány éve teljes buszflottánkat alternatív meghajtású sűrített földgázzal (CNG) üzemelő buszokra cseréltük, melyek  szennyezőanyag- és zajkibocsátása is elenyésző.<br> Járatainkat - amelyeken évente mintegy 8,2 millió utast szállítunk - látás-, hallás- és mozgássérültek is könnyen tudják használni.<br>Kollégáimmal együtt arra törekszünk, hogy olyan színvonalas szolgáltatást nyújtsunk, amely által egyre többen veszik igénybe a közösségi közlekedést.<br>Köszönjük, hogy utazása során minket választ!<br>Veizer JánosnévezérigazgatóKaposvári Közlekedési Zrt.</p>
      
        </section>

        <section id="operations" class="card">
            <h2 id="Op">Működés</h2><br>
            <ul>
                
                <li><i id="atlat" class="fas fa-balance-scale"></i> Átláthatóság</li>
                <li><i id="kozszo"class="fas fa-users"></i> Közszolgáltatás</li>
                <li><i id="mino"class="fas fa-check-circle"></i> Minőség</li>
            </ul>
        </section>

        <section id="documents" class="card">
            <h2 id="dok">Dokumentációk</h2>
            <ul>
                <li><a id="dok1"  href="#" class="doc-link"><i class="fas fa-file-alt"></i> Alapító Okirat</a></li>
                <li><a id="Dok2" class="doc-link"><i class="fas fa-file-alt"></i> Működési Szabályzat</a></li>
                <li><a id="Dok3" href="#" class="doc-link"><i class="fas fa-file-alt"></i> Pénzügyi Jelentések</a></li>
                <li><a id="Dok4" href="#" class="doc-link"><i class="fas fa-file-alt"></i> Éves Jelentés</a></li>
            </ul>
        </section>

        <section id="contacts" class="card">
            <h2 id="eler">Elérhetőségek</h2>
            <ul>
                <li><i class="fas fa-phone"></i> +36-82/411-850</li>
                <li><i class="fas fa-envelope"></i> titkarsag@kkzrt.hu</li>
                <li><i class="fas fa-map-marker-alt"></i> 7400 Kaposvár, Cseri út 16.</li>
                <li><i class="fas fa-map-marker-alt"></i> Áchim András utca 1.</li>

            </ul>
        </section>
    </main>

    <footer>
        <p id="copy">© 2024 Kaposvár Volán. Minden jog fenntartva.</p>
    </footer>
    <script>

var btn1 = document.getElementById('lang_eng');
var btn2 = document.getElementById('lang_de');
var btn3 = document.getElementById('lang_hu');
var sz = document.getElementById('szoveg');
var Operation = document.getElementById('Op');
var rolunk = document.getElementById('rolunk');
var atlathatosag = document.getElementById('atlat');
var kozszolgalatisag = document.getElementById('kozszo');
var minoseg = document.getElementById('mino')
var dok = document.getElementById('dok');
var dok1 = document.getElementById('dok1');
var dok2 = document.getElementById('dok2');
var dok3 = document.getElementById('dok3');
var dok4 = document.getElementById('dok4');
var elerhetoseg = document.getElementById('eler');





btn1.addEventListener('click', function() {
    sz.textContent = "Dear Travelers! Welcome to our company, Kaposvári Közlekedési Zrt. Not only will you be able to meet our site not only with the operation of our company, but also find the latest information on the journey and the current timetable that we have designed and modified to make it as comfortable as possible for our passengers. We are committed to environmental consciousness, so for a few years our entire fleet has been replaced by alternative powered compressed natural gas (CNG) buses, with no pollutant or noise emission. Our flights, which deliver around 8.2 million passengers per year, are also easy to use for people with visual, hearing and disability. With my colleagues, we strive to provide a high-quality service that more and more people are using for public transport. Thank you for choosing us during your trip! Veizer Jánosné Chief Executive Officer Kaposvári Közlekedési Zrt.";
    Operation.textContent="Operation";
    rolunk.textContent="about us";
    dok.textContent="Documents";
    elerhetoseg.textContent="Contact Information"
});

btn2.addEventListener('click', function() {
    sz.textContent = "Liebe Reisen Öffentlichkeit! Willkommen zurück unser Unternehmen, Kaposvár Transport Company. Auf unserer Website können Sie nicht nur über den Betrieb unseres Unternehmens erfahren, aber Sie werden die neuesten Informationen und aktuellen Zeitplan finden Sie die Fahrt beeinflussen, was und modifizierte entworfen wurde, um es bequemer für die Passagiere zu transportieren. Wir sind auf das Umweltbewusstsein verpflichtet, so ein paar Jahren ersetzt wurde buszflottánkat insgesamt alternativ mit komprimiertem Erdgas (CNG) betriebene Busse zu arbeiten, was die Emissionen Schadstoff- und Lärm sind unbedeutend. Unsere Flüge, die jährlich rund 8,2 Millionen Passagiere befördern, sind auch für Menschen mit Seh-, Hör- und Behinderungen leicht zu benutzen. Meine Kollegen und ich bemühen sich, einen qualitativ hochwertigen Service zu bieten, von denen immer mehr Menschen mit den öffentlichen Verkehrsmitteln zu machen. Vielen Dank, dass Sie sich für uns während Ihrer Reise! Veizer Jánosné Chief Executive Officer Kaposvári Közlekedési Zrt.";
    Operation.textContent="Betrieb";
    rolunk.textContent="Über uns";
    dok.textContent="Dokumente";
    elerhetoseg.textContent="Kontaktinformationen"
});

btn3.addEventListener('click', function() {
    sz.textContent = "Tisztelt Utazóközönségünk!Üdvözlöm  társaságunk, a Kaposvári Közlekedési Zrt. oldalán. Honlapunkon nemcsak cégünk működésével ismerkedhet meg, de megtalálja az utazást érintő legfrissebb információkat és az aktuális menetrendet is, melyet úgy alakítottunk ki és úgy módosítunk, hogy minél kényelmesebbé tegyük a közlekedést utasaink számára.Elkötelezettek vagyunk környezet-tudatosság mellett, ezért néhány éve teljes buszflottánkat alternatív meghajtású sűrített földgázzal (CNG) üzemelő buszokra cseréltük, melyek  szennyezőanyag- és zajkibocsátása is elenyésző. Járatainkat - amelyeken évente mintegy 8,2 millió utast szállítunk - látás-, hallás- és mozgássérültek is könnyen tudják használni.Kollégáimmal együtt arra törekszünk, hogy olyan színvonalas szolgáltatást nyújtsunk, amely által egyre többen veszik igénybe a közösségi közlekedést.Köszönjük, hogy utazása során minket választ!Veizer JánosnévezérigazgatóKaposvári Közlekedési Zrt.";
    Operation.textContent="Működés";
    rolunk.textContent="Rólunk";
    dok.textContent="Dokumentumok";
    elerhetoseg.textContent="Elérhetőség"
});     
const toggle = document.getElementById('toggle')
        const nav = document.getElementById('nav')

        toggle.addEventListener('click', () => nav.classList.toggle('active'))
    </script>
</body>
</html>

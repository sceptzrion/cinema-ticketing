<?php 
// variable init
$error = "";
$data_valid = false;

function englishDaytoIndonesian($day) { //Konversi ke Bahasa Indonesia
    $hari = "";
    switch ($day) {
        case "Sunday":
            $hari = "Minggu";
            break;
        case "Monday":
            $hari = "Senin";
            break;
        case "Tuesday":
            $hari = "Selasa";
            break;
        case "Wednesday":
            $hari = "Rabu";
            break;
        case "Thursday":
            $hari = "Kamis";
            break;
        case "Friday":
            $hari = "Jumat";
            break;
        case "Saturday":
            $hari = "Sabtu";
            break;
        default:
            $hari = "";
            break;
    }
    return $hari;
}

function ticketType($day) { //menentukan tipe tiket
    if ($day == "Sabtu" || $day == "Minggu") {
        return "Weekend";
    }
    return "Weekday";
}

function ticketPrice($qty, $cust_type) { //menghitung harga tiket
    if ($qty == 0) {
        return 0;
    }
    $ticket_price = 0;
    switch ($cust_type) {
        case "adult":
            $ticket_price = 50000;
            break;
        case "children":
            $ticket_price = 30000;
            break;
    }
    $ticket_price *= $qty;
    return $ticket_price;
}

function ticketInfo($qty, $cust_type) { //membuat output info tiket
    if ($qty > 0) {
        return "Tiket " . $cust_type . " (x" . ltrim($qty, '0') . ")";
    }
    return "";
}

function priceInfoStr($price) { //membuat output harga tiket dengan format Rupiah
    if ($price == 0) {
        return "";
    }
    return "Rp" . number_format($price,0,',','.');
}

function weekendPrice($ticket_type, $qty) { //menjumlahkan tarif weekend
    $weekend_price = 0;
    if ($ticket_type == "Weekend") {
        $weekend_price = 10000 * $qty;
    }
    return $weekend_price;
}

function isDiscount($subtotal) {//cek diskon
    if ($subtotal > 150000) {
        return true;
    }
    return false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST['movie-date'])) {
        $error = "Harap masukkan tanggal penayangan film";
    } elseif ($_POST['adult-ticket'] < 0 || $_POST['children-ticket'] < 0) {
        $error = "Jumlah tiket yang dimasukkan tidak valid";
    } elseif ($_POST['adult-ticket'] > 10 || $_POST['children-ticket'] > 10) {
        $error = "Maksimum pembelian tiket masing-masing 10 tiket";
    } elseif ($_POST['adult-ticket'] + $_POST['children-ticket'] == 0) {
        $error = "Masukkan jumlah tiket terlebih dahulu";
    } else {
        $data_valid = true;

        //ambil data inputan
        $day = englishDaytoIndonesian(date("l", strtotime($_POST['movie-date'])));
        $date = date("d/m/Y", strtotime($_POST['movie-date']));
        $adult_ticket = $_POST['adult-ticket'];
        $children_ticket = $_POST['children-ticket'];
        $ticket_type = ticketType($day);

        //tiket dewasa
        $adult = ticketInfo($adult_ticket, "Dewasa");
        $adPrice = ticketPrice($adult_ticket, "adult");
        $adPriceStr = priceInfoStr($adPrice);

        //tiket anak-anak
        $children = ticketInfo($children_ticket, "Anak-anak");
        $chPrice = ticketPrice($children_ticket, "children");
        $chPriceStr = priceInfoStr($chPrice);

        //hitung total tiket
        $totalTicket = $adult_ticket + $children_ticket;
        $weekendPrice = weekendPrice($ticket_type, $totalTicket);
        $weekendPriceStr = "Rp" . number_format($weekendPrice,0,',','.');

        //hitung harga tiket
        $subtotal = $adPrice + $chPrice + $weekendPrice;
        $subtotalStr = "Rp" . number_format($subtotal,0,',','.');
        $total = $subtotal;

        //cek apakah diskon
        if (isDiscount($subtotal)) {
            $discount = $subtotal * 0.1;
            $discountStr = "-Rp" . number_format($discount,0,',','.');
            $total -= $discount;
        }
        $totalStr = "Rp" . number_format($total,0,',','.');
    }
}
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/styles.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    <title>Pesan Tiket Bioskop</title>
</head>
<body
<?php if ($data_valid) {?> 
    onload='window.scroll(0,1000000)'
<?php } ?>> <!-- Autoscroll ke bawah kalau sudah isi data -->
    <!-- Form Input -->
    <section id="hero">
        <div class="header">
            <h1>Pesan Tiket Bioskop Anda</h1>
            <p>Pilih tanggal dan jumlah tiket untuk memesan tiket film favorit Anda!<br>
            Total harga akan ditampilkan setelah semua informasi lengkap.</p> 
        </div>
        <div class="form">
            <form method="POST" action="index.php">
                <div>
                    <label for="movie-date">Tanggal Film</label>
                    <input type="date" id="movie-date" name="movie-date">
                </div>
                <div>
                    <label for="adult-ticket">Tiket Dewasa</label>
                    <input type="number" id="adult-ticket" name="adult-ticket" value="0">
                </div>
                <div>
                    <label for="children-ticket">Tiket Anak-anak <span class="small-text">&#40;< 13 tahun&#41;</span></label>
                    <input type="number" id="children-ticket" name="children-ticket" value="0">
                </div>
                <div class="button">
                    <p><?php echo $error ?></p>
                    <input type="submit" value="Selanjutnya">
                </div>
            </form>
        </div>
    </section>

    <!-- summary -->
    <?php if ($data_valid) { ?>
    <section id="summary">
        <h2>Rincian Pemesanan</h2>
        <div class="confirm">
            <p>Hari, Tanggal Film: <?php echo $day . ', ' . $date?></p>
            <hr>
            <div class="ticket">
                <div>
                    <p><?php echo $adult?></p>
                    <p><?php echo $adPriceStr?></p>
                </div>
                <div>
                    <p><?php echo $children?></p>
                    <p><?php echo $chPriceStr?></p>
                </div>
                <?php if ($ticket_type == "Weekend") {?>
                <div>
                    <p>Tarif Khusus Weekend &#40;x<?php echo $totalTicket?>&#41;</p>
                    <p><?php echo $weekendPriceStr?></p>
                </div><?php } ?>
            </div>
            <?php if (isDiscount($subtotal)) { ?>
            <hr>
            <div class="subtotal">
                <p>Subtotal Harga</p>
                <p><?php echo $subtotalStr?></p>
            </div>
            <div class="promo">
                <div class="text">
                    <p>Promo Diskon 10%</p>
                    <p class="small">&#40;pembelian tiket di atas Rp150.000&#41;</p>
                </div>
                <p><?php echo $discountStr?></p>
            </div> <?php } ?>
            <hr>
            <div class="total">
                <p>Total Harga</p>
                <p><?php echo $totalStr?></p>
            </div>
        </div>
        <div class="buttons">
            <p>Apakah ingin melakukan pembayaran?</p>
            <button class="finish" onclick="location.href='./index.php'; alert('Pemesanan tiket berhasil!')">Lakukan Pembayaran</button>
            <button class="cancel" onclick="location.href='#hero'">Batal</button>
        </div>
    </section>
    <?php } ?>
</body>
</html>
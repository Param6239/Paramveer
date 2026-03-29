<?php
session_start();
include('config.php');

if(!isset($_SESSION['user']) || !isset($_GET['id'])){
    header('location:index.php');
}

$bookid = mysqli_real_escape_string($con, $_GET['id']);

$user_id = $_SESSION['user'];

$q = mysqli_query($con,"
SELECT 
b.*,
m.movie_name, m.image,
t.name AS theatre, t.place,
sc.screen_name,
st.name AS show_name,
st.start_time
FROM tbl_bookings b
JOIN tbl_shows sh ON b.show_id = sh.s_id
JOIN tbl_movie m ON sh.movie_id = m.movie_id
JOIN tbl_theatre t ON sh.theatre_id = t.id
JOIN tbl_show_time st ON sh.st_id = st.st_id
JOIN tbl_screens sc ON b.screen_id = sc.screen_id
WHERE b.ticket_id = '$bookid' AND b.user_id='$user_id'
");

$data = mysqli_fetch_array($q);
?>

<style>
body {
    background:#111;
    font-family: Arial;
    color:#fff;
    text-align:center;
}

.ticket {
    width: 400px;
    margin: 50px auto;
    background:#222;
    border-radius:10px;
    overflow:hidden;
    box-shadow:0 0 20px rgba(0,0,0,0.5);
}

.ticket-header {
    background:#e50914;
    padding:15px;
    font-size:20px;
    font-weight:bold;
}

.ticket-body {
    padding:20px;
    text-align:left;
}

.ticket img {
    width:100%;
    height:200px;
    object-fit:cover;
}

.row {
    margin:10px 0;
}

.label {
    font-weight:bold;
    color:#aaa;
}

.value {
    float:right;
}

.ticket-footer {
    text-align:center;
    padding:15px;
    border-top:1px dashed #555;
    font-size:14px;
}
</style>

<div class="ticket">
    
    <div class="ticket-header">
        🎟 Movie Ticket
    </div>

    <img src="<?php echo $data['image']; ?>">

    <div class="ticket-body">

        <div class="row">
            <span class="label">Movie</span>
            <span class="value"><?php echo $data['movie_name']; ?></span>
        </div>

        <div class="row">
            <span class="label">Theatre</span>
            <span class="value"><?php echo $data['theatre']." (".$data['place'].")"; ?></span>
        </div>

        <div class="row">
            <span class="label">Screen</span>
            <span class="value"><?php echo $data['screen_name']; ?></span>
        </div>

        <div class="row">
            <span class="label">Seats</span>
            <span class="value"><?php echo $data['seat_numbers']; ?></span>
        </div>

        <div class="row">
            <span class="label">Amount</span>
            <span class="value">Rs <?php echo $data['amount']; ?></span>
        </div>

        <div class="row">
            <span class="label">Date</span>
            <span class="value"><?php echo date('d M Y',strtotime($data['ticket_date'])); ?></span>
        </div>

        <div class="row">
            <span class="label">Ticket ID</span>
            <span class="value"><?php echo $data['ticket_id']; ?></span>
        </div>

    </div>

    <div class="ticket-footer">
        Show this ticket at entry 🎬
    </div>

</div>

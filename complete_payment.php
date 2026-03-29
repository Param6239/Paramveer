<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<?php
session_start();
if(!isset($_SESSION['user']))
{
	header('location:login.php');
}
$userId = $_SESSION['user'];

include('config.php');
if (!$userId) {
    die("User not logged in");
}

/* FETCH USER EMAIL */
$sql = "SELECT * FROM tbl_registration WHERE user_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User not found");
}

$row = $result->fetch_assoc();
$email = $row['email'];

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

extract($_POST);
$correct_otp = $_SESSION['otp'];

if($otp == $correct_otp)
{
    $bookid = "BKID".rand(1000000,9999999);

    // Seats
    $seat_numbers = $_SESSION['seats'];
    $seatArray = explode(",", $seat_numbers);
    $no_seats = count($seatArray);

    // INSERT FIRST
    mysqli_query($con,"INSERT INTO tbl_bookings 
    (book_id, ticket_id, t_id, user_id, show_id, screen_id, no_seats, seat_numbers, amount, ticket_date, date, status)
    VALUES
    (NULL,'$bookid','".$_SESSION['theatre']."','".$_SESSION['user']."','".$_SESSION['show']."','".$_SESSION['screen']."','$no_seats','$seat_numbers','".$_SESSION['amount']."','".$_SESSION['date']."',CURDATE(),'1')");

    // FETCH DATA AFTER INSERT
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
    WHERE b.ticket_id = '$bookid'
    ");

    $data = mysqli_fetch_assoc($q);

    // ASSIGN VARIABLES
    $movie_name   = $data['movie_name'];
    $movie_image  = "http://localhost/theatre_project/".$data['image'];
    $theatre_name = $data['theatre']." (".$data['place'].")";
    $screen_name  = $data['screen_name'];
    $show_time    = date('h:i A',strtotime($data['start_time'])) . " " . $data['show_name'];
    $ticket_date  = date('d M Y',strtotime($data['ticket_date']));
    $amount       = $data['amount'];

    $ticket_url = "http://localhost/theatre_project/ticket.php?id=".$bookid;

    try{
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'unknownsender456.private@gmail.com';
    $mail->Password   = 'asoq ygna dslk bvle';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    /* Sender */
    $mail->setFrom('unknownsender456.private@gmail.com', 'OMTBS - TICKET NOTIFICATION');

    /* Receiver */
    $mail->addAddress($email);


        $mail->isHTML(true);
        $mail->Subject = "Your Movie Ticket - ".$bookid;

        // EMAIL TEMPLATE
        $email_template = '
        <table width="100%" cellpadding="0" cellspacing="0" style="background:#0f172a;padding:20px;font-family:Arial;">
        <tr><td align="center">

        <table width="420" cellpadding="0" cellspacing="0" style="background:#1e293b;border-radius:10px;color:#fff;">
        
        <tr><td style="background:#e50914;padding:15px;text-align:center;font-size:20px;font-weight:bold;">
        🎟 Movie Ticket
        </td></tr>

        <tr><td><img src="'.$movie_image.'" width="100%"></td></tr>

        <tr><td style="padding:20px;">
        <table width="100%">
        <tr><td>Movie</td><td align="right">'.$movie_name.'</td></tr>
        <tr><td>Theatre</td><td align="right">'.$theatre_name.'</td></tr>
        <tr><td>Screen</td><td align="right">'.$screen_name.'</td></tr>
        <tr><td>Show</td><td align="right">'.$show_time.'</td></tr>
        <tr><td>Date</td><td align="right">'.$ticket_date.'</td></tr>
        <tr><td>Seats</td><td align="right">'.$seat_numbers.'</td></tr>
        <tr><td>Amount</td><td align="right">Rs '.$amount.'</td></tr>
        <tr><td>Ticket ID</td><td align="right">'.$bookid.'</td></tr>
        </table>
        </td></tr>

        <tr><td align="center" style="padding:20px;">
        <a href="'.$ticket_url.'" style="background:#e50914;color:#fff;padding:10px 20px;text-decoration:none;">
        View Ticket
        </a>
        </td></tr>

        </table>

        </td></tr></table>
        ';

        $mail->Body = $email_template;
        $mail->AltBody = "Your Ticket ID: ".$bookid;

        $mail->send();

    } catch (Exception $e){
        echo "Mailer Error: {$mail->ErrorInfo}";
    }

    $_SESSION['success'] = "Bookings Done!";
}
else
{
    $_SESSION['error'] = "Payment Failed";
}
?>
<body><table align='center'><tr><td><STRONG>Transaction is being processed,</STRONG></td></tr><tr><td><font color='blue'>Please Wait <i class="fa fa-spinner fa-pulse fa-fw"></i>
<span class="sr-only"></font></td></tr><tr><td>(Do not 'RELOAD' this page or 'CLOSE' this page)</td></tr></table><h2>
<script>
    setTimeout(function(){ 
        window.location = "ticket.php?id=<?php echo $bookid; ?>"; 
    }, 2000);
</script>
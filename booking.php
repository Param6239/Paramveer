<?php include('header.php');
if(!isset($_SESSION['user']))
{
	header('location:login.php');
}
	$qry2=mysqli_query($con,"select * from tbl_movie where movie_id='".$_SESSION['movie']."'");
	$movie=mysqli_fetch_array($qry2);
	?>
	<style>
.seat {
    width: 40px;
    height: 40px;
    margin: 5px;
    display: inline-block;
    text-align: center;
    line-height: 40px;
    background: #28a745;
    color: white;
    cursor: pointer;
    border-radius: 5px;
}
.seat.selected {
    background: #ffc107;
}
.seat.booked {
    background: #dc3545;
    cursor: not-allowed;
}
</style>
<div class="content">
	<div class="wrap">
		<div class="content-top">
				<div class="section group">
					<div class="about span_1_of_2">	
						<h3><?php echo $movie['movie_name']; ?></h3>	
							<div class="about-top">	
								<div class="grid images_3_of_2">
									<img src="<?php echo $movie['image']; ?>" alt=""/>
								</div>
								<div class="desc span_3_of_2">
									<p class="p-link" style="font-size:15px"><b>Cast : </b><?php echo $movie['cast']; ?></p>
									<p class="p-link" style="font-size:15px"><b>Release Date : </b><?php echo date('d-M-Y',strtotime($movie['release_date'])); ?></p>
									<p style="font-size:15px"><?php echo $movie['desc']; ?></p>
									<a href="<?php echo $movie['video_url']; ?>" target="_blank" class="watch_but">Watch Trailer</a>
								</div>
								<div class="clear"></div>
							</div>
							<table class="table table-hover table-bordered text-center">
							<?php
								$s=mysqli_query($con,"select * from tbl_shows where s_id='".$_SESSION['show']."'");
								$shw=mysqli_fetch_array($s);
								
									$t=mysqli_query($con,"select * from tbl_theatre where id='".$shw['theatre_id']."'");
									$theatre=mysqli_fetch_array($t);
									?>
									<tr>
										<td class="col-md-6">
											Theatre
										</td>
										<td>
											<?php echo $theatre['name'].", ".$theatre['place'];?>
										</td>
										</tr>
										<tr>
											<td>
												Screen
											</td>
										<td>
											<?php 
												$ttm=mysqli_query($con,"select  * from tbl_show_time where st_id='".$shw['st_id']."'");
												
												$ttme=mysqli_fetch_array($ttm);
												
												$sn=mysqli_query($con,"select  * from tbl_screens where screen_id='".$ttme['screen_id']."'");
												
												$screen=mysqli_fetch_array($sn);
												echo $screen['screen_name'];
							
												?>
										</td>
									</tr>
									<tr>
										<td>
											Date
										</td>
										<td>
											<?php 
											if(isset($_GET['date']))
							{
								$date=$_GET['date'];
							}
							else
							{
								if($shw['start_date']>date('Y-m-d'))
								{
									$date=date('Y-m-d',strtotime($shw['start_date'] . "-1 days"));
								}
								else
								{
									$date=date('Y-m-d');
								}
								$_SESSION['dd']=$date;
							}
							?>
							<div class="col-md-12 text-center" style="padding-bottom:20px">
								<?php if($date>$_SESSION['dd']){?><a href="booking.php?date=<?php echo date('Y-m-d',strtotime($date . "-1 days"));?>"><button class="btn btn-default"><i class="glyphicon glyphicon-chevron-left"></i></button></a> <?php } ?><span style="cursor:default" class="btn btn-default"><?php echo date('d-M-Y',strtotime($date));?></span>
								<?php if($date!=date('Y-m-d',strtotime($_SESSION['dd'] . "+4 days"))){?>
								<a href="booking.php?date=<?php echo date('Y-m-d',strtotime($date . "+1 days"));?>"><button class="btn btn-default"><i class="glyphicon glyphicon-chevron-right"></i></button></a>
								<?php }
								$av=mysqli_query($con,"select sum(no_seats) from tbl_bookings where show_id='".$_SESSION['show']."' and ticket_date='$date'");
								$avl=mysqli_fetch_array($av);
								?>
							</div>
										</td>
									</tr>
									<tr>
										<td>
											Show Time
										</td>
										<td>
											<?php echo date('h:i A',strtotime($ttme['start_time']))." ".$ttme['name'];?> Show
										</td>
									</tr>
<tr>
<td colspan="2">
<form action="process_booking.php" method="post">

<input type="hidden" name="screen" value="<?php echo $screen['screen_id'];?>"/>
<input type="hidden" name="amount" id="hm" value="<?php echo $screen['charge'];?>"/>
<input type="hidden" name="date" value="<?php echo $date;?>"/>
<input type="hidden" name="seats" id="selectedSeats">

<h4>Select Seats</h4>

<div id="seatContainer">
<?php
$rows = ['A','B','C','D','E'];
$cols = 10;

// Fetch already booked seats
$bookedSeats = [];
$res = mysqli_query($con,"SELECT seat_numbers FROM tbl_bookings 
WHERE show_id='".$_SESSION['show']."' AND ticket_date='$date'");

while($row = mysqli_fetch_assoc($res)){
    $bookedSeats = array_merge($bookedSeats, explode(',', $row['seat_numbers']));
}

foreach($rows as $r){
    for($c=1;$c<=$cols;$c++){
        $seat = $r.$c;
        $class = in_array($seat,$bookedSeats) ? "seat booked" : "seat";
        echo "<div class='$class' data-seat='$seat'>$seat</div>";
    }
    echo "<br/>";
}
?>
</div>

<br>

<div id="amount" style="font-weight:bold;font-size:18px">
    Rs 0
</div>

<br>

<?php if($avl[0]==$screen['seats']){ ?>
    <button disabled type="button" class="btn btn-danger" style="width:100%">House Full</button>
<?php } else { ?>
    <button type="submit" id="bookBtn" class="btn btn-info" style="width:100%" disabled>Book Now</button>
<?php } ?>

</form>
</td>
</tr>
						<table>
							<tr>
								<td></td>
							</tr>
						</table>
					</div>			
				<?php include('movie_sidebar.php');?>
			</div>
				<div class="clear"></div>		
			</div>
	</div>
</div>
<?php include('footer.php');?>

<script>
let selected = [];

$('.seat').not('.booked').click(function(){
    let seat = $(this).data('seat');

    if($(this).hasClass('selected')){
        $(this).removeClass('selected');
        selected = selected.filter(s => s !== seat);
    } else {
        $(this).addClass('selected');
        selected.push(seat);
    }

    $('#selectedSeats').val(selected.join(','));

    let charge = <?php echo $screen['charge'];?>;
    let amount = charge * selected.length;

    // Update amount
    $('#amount').html("Rs " + amount);
    $('#hm').val(amount);

    // Enable / Disable button
    if(selected.length > 0){
        $('#bookBtn').prop('disabled', false);
    } else {
        $('#bookBtn').prop('disabled', true);
    }
});

// Extra safety (prevent manual submit)
$('form').submit(function(){
    if(selected.length === 0){
        alert("Please select at least 1 seat");
        return false;
    }
});
</script>
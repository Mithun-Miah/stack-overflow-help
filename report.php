<?php 
// For Store data
session_start();

if(!isset($_SESSION['email'])){
	header("location:index.php");
}

// For Secure URL / do not permission enter by url type
if($_SESSION['email'] == true){
    // after login fetch email address and password display from database into this page
    echo("<h4 class='text-center text-light bg-success py-3'>Email : $_SESSION[email]</h4>");
    //echo("<h1>Password : $_SESSION[password]</h1>");
} else{
    header('Location: index.php');
}

include_once "db_connect.php";
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports Print</title>
	
	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">

    <!-- font awesome cdn -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
	
    <!-- Pagination and  'copy', 'csv', 'excel', 'pdf', 'print' file all cdn -->
	<link rel="stylesheet" href="export_cdn_files/jquery.dataTables.min.css">
	<link rel="stylesheet" href="export_cdn_files/buttons.dataTables.min.css">
	
	<script src="data_tables_cdn_files/jquery-3.6.0.min.js"></script>
	<script src="export_cdn_files/jquery.dataTables.min.js"></script>
	<script src="export_cdn_files/dataTables.buttons.min.js"></script>
	<script src="export_cdn_files/jszip.min.js"></script>
	<script src="export_cdn_files/pdfmake.min.js"></script>
	<script src="export_cdn_files/vfs_fonts.js"></script>
	<script src="export_cdn_files/buttons.html5.min.js"></script>
	<script src="export_cdn_files/buttons.print.min.js"></script>

    <!-- Jquery date picker cdn -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"/>
    <link rel="stylesheet" href="https://jqueryui.com/resources/demos/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>  
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <!--LOAD PRINTJS LIBRARY-->
    <script src="https://printjs-4de6.kxcdn.com/print.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" ></script>
	
	<style>
		th { white-space: nowrap; }
	</style>
</head>
<body>
<div class="container p-3">
        <div class="row">

			<div class="text-end">
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
		
			<div class="text-center mb-4">
				<button onclick="printDiv()" class="btn btn-primary">Click to Print</button>
				<button id="download-button" class="btn btn-warning">Download as PDF</button>
			</div>
			
			<table id="" class="nowrap table table-bordered">  
				<tr>  
					<th width="5%">ID</th>  
					<th width="30%">Working Days</th>  
					<th width="43%">Date</th>  
					<th width="10%">Amount</th>  
					<th width="12%">Remarks</th>  
				</tr>
					<?php  
					//filter.php  
					if(isset($_POST["from_date"], $_POST["to_date"]))  
					{
						// $user = $_SESSION['email'];
                        // //echo $user;
                        // $u_query = "select * from user_reg where email = '$user'";
                        // $u_queryEx = mysqli_query($connect, $u_query);
                        // $uRow = mysqli_fetch_array($u_queryEx);
                        // //print_r ($uRow);
                        // $user_logId = $uRow['id'];

						$sDate = $_POST["from_date"];  								
						$eDate = $_POST["to_date"];

						$output = '';

						//$query = "SELECT * FROM entry_report WHERE date BETWEEN '$sDate' AND '$eDate' GROUP BY user_id ";
						//SELECT * FROM table1 INNER JOIN table2 on table1.columnName = table2.columnName INNER JOIN table3 on table1.columnName = table3.columnName
						//$query = "SELECT * FROM entry_report INNER JOIN user_reg on entry_report.user_id = user_reg.id WHERE date BETWEEN '$sDate' AND '$eDate' ORDER BY user_id";
						$query = "SELECT * FROM entry_report WHERE date BETWEEN '$sDate' AND '$eDate'";
						//$query = "SELECT * FROM entry_report WHERE date BETWEEN '$sDate' AND '$eDate' AND user_id = $user_id";
						//$query = "SELECT * FROM `entry_report` WHERE user_id BETWEEN 15 AND 15";
						$result = mysqli_query($connect, $query);

						if($numRows = mysqli_num_rows($result))  
						{  
						//while($row = mysqli_fetch_array($result) > 0)  
						while($row = mysqli_fetch_array($result))  
						{
						?>
								<!-- $output .= '  
									<tr align="center">  
										<td>'. $row["id"] .'</td>  
										<td>'. $row["days"] .'</td>  
										<td>'. $row["date"] .'</td>  
										<td>'. $row["amount"] .'</td>  
										<td>'. $row["remarks"] .'</td>  
									</tr>  
								'; -->
								<tr class="text-center">
                                    <td><?php echo $row['id'] ?></td>
                                    <td><?php echo $row['days'] ?></td>
                                    <td><?php echo $row['date'] ?></td>
                                    <td><?php echo $row['amount'] ?></td>
                                    <td><?php echo $row['remarks'] ?></td>
                                </tr>
						<?php
						
						}  
					}  
					else  
					{  
						$output .= '  
								<tr>  
									<td colspan="5">No Order Found</td>  
								</tr>  
						';  
					}  
					//$output .= '</table>';  
					echo $output;  
				}  
				?>

				<?php
					if(isset($_POST["from_date"], $_POST["to_date"]))  
					{
						$querySum = "SELECT SUM(amount) FROM entry_report  
							WHERE date BETWEEN '".$_POST["from_date"]."' AND '".$_POST["to_date"]."' GROUP BY user_id";
						$resultSum = mysqli_query($connect, $querySum);
						//display data on web page
						while($rowSum = mysqli_fetch_array($resultSum)){
							$total = $rowSum['SUM(amount)'];
							//echo " Total cost: ". $rowSum['SUM(amount)'];
							//echo "<br>";
						}
					}
					
				?>
				<tfoot>
					<tr>
						<th colspan="3" style="text-align:right">Total : </th>
						<th class="text-center"><?php echo $total; ?></th>
					</tr>
				</tfoot>
			</table>


			
		</div>
    </div>
	
	<script>
		// for pagination and search datatable code
		$(document).ready(function () {
			$('#example').DataTable({
				
				footerCallback: function (row, data, start, end, display_data) {
					var api = this.api();
		 
					// Remove the formatting to get integer data for summation
					var intVal = function (i) {
						return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
					};
		 
					// Total over all pages
					total = api
						.column(3)
						.data()
						.reduce(function (a, b) {
							return intVal(a) + intVal(b);
						}, 0);
		 
					// Total over this page
					pageTotal = api
						.column(3, { page: 'current' })
						.data()
						.reduce(function (a, b) {
							return intVal(a) + intVal(b);
						}, 0);
		 
					// Update footer
					//$(api.column(3).footer()).html('$' + pageTotal + ' ( $' + total + ' total)');
					$(api.column(3).footer()).html(pageTotal);
				}
			});
		});
		//export / download as pdt file
		const button = document.getElementById('download-button');

			function generatePDF() {
				// Choose the element that your content will be rendered to.
				const element = document.getElementById('example');
				// Choose the element and save the PDF for your user.
				html2pdf().from(element).save();
			}

			button.addEventListener('click', generatePDF);
		// print code
		function printDiv() {
            var divContents = document.getElementById("example").innerHTML;
            var a = window.open('', '', 'height=500, width=500');
            a.document.write('<html>');
            a.document.write('<table > <h1>Monthly Reports <br>');
            a.document.write(divContents);
            a.document.write('</table></html>');
            a.document.close();
            a.print();
        }
        //After refresh/reload Data Resubmission Stop with this code
        if (window.history.replaceState) {
            window.history.replaceState(null, null, location.href)
        }

    </script>
</body>
</html>

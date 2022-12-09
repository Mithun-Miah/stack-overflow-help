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
    <title>Print Reports</title>
	
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
	
	<style>
		th { white-space: nowrap; }
        .sDate, .eDate{
            border-radius: 10px;
            padding: 7px;
        }
	</style>
</head>
<body>

    <div class="container">
        <div class="row">
            <h2 class="text-center py-3 text-light bg-success">Monthly Book Entry Reports</h2>
            <hr>
            <div class="text-end">
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>

            <h3 class="text-center">Find Your Targeted Reports</h3>
            <hr>
            <form action="report.php" method="post" target="_blank">
                <div class="text-center">
					<input type="text" name="from_date" id="from_date" class="w-25 sDate" placeholder="From Date" />  
					<input type="text" name="to_date" id="to_date" class="w-25 mx-2 eDate" placeholder="To Date" />
					<input type="submit" name="filter" id="filter" value="Search By Date" class="btn btn-info w-25" />
				</div>
            </form>
        </div>
        <hr>
    </div>
	
	    <!-- show data start -->
        <div class="container">
            <div class="row">
                <h3 class="text-center text-white bg-primary p-2">REPORTS DATA FETCH</h3>

                <div class="col-lg-12 col-md-12 col-sm-12">
                    <?php
                        $user = $_SESSION['email'];
                        //echo $user;
                        $u_query = "select * from user_reg where email = '$user'";
                        $u_queryEx = mysqli_query($connect, $u_query);
                        $uRow = mysqli_fetch_array($u_queryEx);
                        //print_r ($uRow);
                        $user_logId = $uRow['id'];

                        //$read = "SELECT * FROM entry_report where user_id = '$user_logId' ORDER BY id DESC";
                        $read = "SELECT * FROM entry_report where user_id = '$user_logId' ORDER BY id DESC";
                        $query = mysqli_query($connect, $read);
                        $user_rowCount = mysqli_num_rows($query);

                        for($i=1;$user_rowCount >= $i;$i++){
                            $row = mysqli_fetch_array($query);
                            ?>
                        <!-- <p>Total Working Days : <?php //echo $row['user_id']; ?> </p> -->
                    <?php
                        }
                    ?>

                    <h3 class="text-center">Display Reports</h3>
                    <h5 class="total">Total Working Days : 
                    <?php 
                    $total_amount = "SELECT * FROM entry_report WHERE user_id = '$user_logId' ";
                    $query_total_amount = mysqli_query($connect, $total_amount);
                    
                    if($total_amount_add = mysqli_num_rows($query_total_amount)){
                        echo '<span id="total" name="total">'.$total_amount_add.'</span>'; 
                        
                    }else{
                        echo '<span id="total" name="total">No Data</span>';
                    }
                        
                    ?>
                    </h5>

                    <?php
                        $totalSumQuery = "SELECT SUM(amount) AS total_amount FROM entry_report WHERE user_id = $user_logId";
                        $sumResult = mysqli_query($connect, $totalSumQuery);
                        $sumRow = mysqli_fetch_assoc($sumResult); 
                        $sumAll = $sumRow['total_amount'];
                    ?>
                    
                    <h5 class="text-primary"> Total Book Entry : <span><?php echo $sumAll; ?></span> </h5>
                    <hr>

                    <form action="" method="post">

                        <table id="example" class="table table-bordered table-striped table-hover display_data nowrap">
                            <thead class="bg-success">
                                <tr class="text-center">
                                    <th>Id</th>
                                    <th>Working Days</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Remarks</th>
                                    <!--<th>Edit</th>
                                    <th>Delete</th>-->
                                </tr>
                            </thead>
                                <!-- ======= user wide data fetch code start ============ -->
                                <?php

                                $user = $_SESSION['email'];
                                //echo $user;
                                $u_query = "select * from user_reg where email = '$user'";
                                $u_queryEx = mysqli_query($connect, $u_query);
                                $uRow = mysqli_fetch_array($u_queryEx);
                                //print_r ($uRow);
                                $user_logId = $uRow['id'];

                                $read = "SELECT * FROM entry_report where user_id = $user_logId ORDER BY id DESC";
                                $query = mysqli_query($connect, $read);
                                $user_rowCount = mysqli_num_rows($query);

                                for($i=1;$user_rowCount >= $i;$i++){
                                    $row = mysqli_fetch_array($query);
                                    ?>

                                <tr class="text-center">
                                    <td><?php echo $row['id'] ?></td>
                                    <td><?php echo $row['days'] ?></td>
                                    <td><?php echo $row['date'] ?></td>
                                    <td><?php echo $row['amount'] ?></td>
                                    <td><?php echo $row['remarks'] ?></td>
                                </tr>

                            <?php
                            }
                            ?>
							
                            <tbody id="tbody">
                            
                            </tbody>
                            
                            <tfoot>
                                <tr>
                                    <th colspan="3" style="text-align:right">Total:</th>
                                    <th class="text-center"></th>
                                </tr>
                            </tfoot>

                        </table>
                    </form>
                </div>

            </div>
        </div>
        </div>
        <!-- show data end -->
    
	
	    <script>
		
        $( function() {
            $( "#datepicker" ).datepicker();
        });

        // for date picker code
        $(document).ready(function(){  
           $.datepicker.setDefaults({  
                dateFormat: 'yy-mm-dd'   
           });  
           $(function(){  
                $("#from_date").datepicker();  
                $("#to_date").datepicker();  
           });  
           $('#filter').click(function(){  
                var from_date = $('#from_date').val();  
                var to_date = $('#to_date').val();  
                if(from_date != '' && to_date != '')  
                {  
                     $.ajax({  
                          url:"report.php",  
                          method:"POST",  
                          data:{from_date:from_date, to_date:to_date},  
                          success:function(data)  
                          {  
                               $('#order_table').html(data);  
                          }  
                     });  
                }  
                else  
                {  
                     alert("Please Select Date");  
                }  
           });  
      });

        // for pagination and search datatable code
		$(document).ready(function () {
			$('#example').DataTable({
				"pagingType": "full_numbers",
				"pageLength": 10,
				"bInfo": false,
				//dom: 'Bfrtip',
				//buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search Records by Anything",
                },
				
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
				},
			});
		});

        //After refresh/reload Data Resubmission Stop with this code
        if (window.history.replaceState) {
            window.history.replaceState(null, null, location.href)
        }
        </script>

    <!-- <script src="app.js"></script>-->
</body>
</html>

<?php $page_title="Manage Reports";

include("includes/header.php");

  require("includes/function.php");
  require("language/language.php");
  
  function get_user_info($user_id)
   {
    global $mysqli;

    $user_qry="SELECT * FROM tbl_users WHERE `id`='".$user_id."'";
    $user_result=mysqli_query($mysqli,$user_qry);
    $user_row=mysqli_fetch_assoc($user_result);

    return $user_row;
   }
    
  // Get page data
  $tableName="tbl_reports";    
  $targetpage = "manage_reports.php";  
  $limit = 15; 
  
  $query = "SELECT COUNT(*) as num FROM $tableName LEFT JOIN tbl_books ON tbl_reports.`book_id`= tbl_books.`id` ORDER BY tbl_reports.`id`";
  $total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query));
  $total_pages = $total_pages['num'];
  
  $stages = 3;
  $page=0;
  if(isset($_GET['page'])){
  $page = mysqli_real_escape_string($mysqli,$_GET['page']);
  }
  if($page){
    $start = ($page - 1) * $limit; 
  }else{
    $start = 0; 
    }


   $qry="SELECT report.*, book.`book_title`, user.`name`, user.`email` FROM tbl_reports report
          JOIN tbl_books book ON report.`book_id`=book.`id`
          JOIN tbl_users user ON report.`user_id`=user.`id`
          ORDER BY report.`id` DESC LIMIT $start, $limit";   

  $result=mysqli_query($mysqli,$qry);
 
   
?>
<style>
#applied_user .dataTables_wrapper .top{
	position: relative;
	width: 100%;
}	
.dataTables_wrapper .top{
	margin-top: -25px;
	padding-right: 15px;
}
</style>
    <div class="row">
      <div class="col-xs-12">
        <div class="card mrg_bottom">
          <div class="page_title_block">
            <div class="col-md-5 col-xs-12">
              <div class="page_title"><?=$page_title?></div>
            </div>             
          </div>
          <div class="clearfix"></div>        	
          <div class="col-md-12 mrg-top">
          	<button class="btn btn-danger btn_cust btn_delete_all" style="margin-bottom:20px;"><i class="fa fa-trash"></i> Delete All</button>
             <table class="datatable table table-striped table-bordered table-hover">
              <thead>
			   <tr>
            	<th style="width:40px">
                <div class="checkbox" style="margin: 0px">
                  <input type="checkbox" name="checkall" id="checkall_input" value="">
                  <label for="checkall_input"></label>
                </div>
			    </th> 
                  <th>Name</th>
                  <th>Email</th>
                  <th>Book Name</th>
                  <th style="width: 200px;">Report</th> 
                  <th>Action</th>
                </tr>
              	</thead>
              	<tbody>
              	<?php
				      $i=0;
				      while($row=mysqli_fetch_array($result))
				      {?>

                	<tr>
                	<td> 
                    <div class="checkbox">
                      <input type="checkbox" name="post_ids[]" id="checkbox<?php echo $i;?>" value="<?php echo $row['id']; ?>" class="post_ids">
                      <label for="checkbox<?php echo $i;?>"></label>
                    </div>
                  </td>
                  <td><?php echo get_user_info($row['user_id'])['name'];?></td>
                  <td><?php echo get_user_info($row['user_id'])['email'];?></td>
                  <td><?php echo $row['book_title'];?></td>
                  <td style="width: 200px;"><p><?php echo $row['report'];?></p></td>
	              <td> 
					<a href="javascript:void(0)" data-id="<?php echo $row['id'];?>" class="btn btn-danger btn_delete" data-toggle="tooltip" data-tooltip="Delete"><i class="fa fa-trash"></i></a> 
				  </td>
                </tr>
               <?php $i++;
						}
			   ?>
              </tbody>
            </table>
          </div>
          <div class="col-md-12 col-xs-12">
            <div class="pagination_item_block">
              <nav>
              	<?php if(!isset($_POST["search"])){ include("pagination.php");}?>                 
              </nav>
            </div>
          </div>
          <div class="clearfix"></div>
        </div>
      </div>
    </div>            
               
        
<?php include("includes/footer.php");?>       

<script type="text/javascript">

/*
  $(".btn_delete_a").on("click",function(e){

    var _user_id=$(this).data("user");
    var _book_id=$(this).data("book");

    e.preventDefault();

      if(confirm("Are you sure you want to delete this report?")){
          $.ajax({
            type:'post',
            url:'processdata.php',
            dataType:'json',
            data:{id:_user_id,book_id:_book_id,'action':'removeData','tbl_nm':"tbl_reports","tbl_id":"id"},
            success:function(res){
                console.log(res);
                if(res.status=='1'){
                  location.reload();
                }
                else if(res.status=='-2'){
                  alert(res.message);
                }
              }
          });
        }
  });

*/

   // for multiple deletes
  $(".btn_delete_all").click(function(e){
	var _ids = $.map($('.post_ids:checked'), function(c){return c.value; });

	if(_ids!='')
	{
		swal({
          title: "Are you sure to delete this records?",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger btn_edit",
          cancelButtonClass: "btn-warning btn_edit",
          confirmButtonText: "Yes",
          cancelButtonText: "No",
          closeOnConfirm: false,
          closeOnCancel: false,
          showLoaderOnConfirm: true
        },

        function(isConfirm) {
          if (isConfirm) {
            $.ajax({
              type:'post',
              url:'processdata.php',
              dataType:'json',
              data:{ids:_ids,'action':'removeReports'},
              success:function(res){
                  console.log(res);
                  if(res.status=='1'){
                    swal({
					  title: "Successfully", 
					  text: "Data has been deleted...", 
					  type: "success"
					},function() {
					  location.reload();
					});
                  }
                  else{
                  	swal("Something went to wrong !");
                  }
                }
            });
          }
          else{
            swal.close();
          }

        });
	}
	else{
		swal("Sorry no records selected !!")
	}
  });


   $(".btn_delete").click(function(e){
		e.preventDefault();
		var _ids=$(this).data("id");
		swal({
          title: "Are you sure to delete?",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger btn_edit",
          cancelButtonClass: "btn-warning btn_edit",
          confirmButtonText: "Yes",
          cancelButtonText: "No",
          closeOnConfirm: false,
          closeOnCancel: false,
          showLoaderOnConfirm: true
        },

        function(isConfirm) {
          if (isConfirm) {
            $.ajax({
              type:'post',
              url:'processdata.php',
              dataType:'json',
              data:{ids:_ids,'action':'removeReports'},
              success:function(res){
                  console.log(res);
                  if(res.status=='1'){
                    swal({
					  title: "Successfully", 
					  text: "Data has been deleted...", 
					  type: "success"
					},function() {
					  location.reload();
					});
                  }
                  else{
                  	swal("Something went to wrong !");
                  }
                }
            });
          }
          else{
            swal.close();
          }

        });

	});

var totalItems=0;

 $("#checkall_input").click(function () {
	
			totalItems=0;
	
			$("input[name='post_ids[]']").prop('checked', this.checked);
	
			$.each($("input[name='post_ids[]']:checked"), function(){
				totalItems=totalItems+1;
			});
	
	
			if($("input[name='post_ids[]']").prop("checked") == true){
				$('.notifyjs-corner').empty();
				$.notify(
					'Total '+totalItems+' item checked',
					{ position:"top center",className: 'success'}
					);
			}
			else if($("input[name='post_ids[]']").prop("checked") == false){
				totalItems=0;
				$('.notifyjs-corner').empty();
			}
		});
	
		var noteOption = {
			clickToHide : false,
			autoHide : false,
		}
	
		$.notify.defaults(noteOption);
	
		$(".post_ids").click(function(e){
	
			if($(this).prop("checked") == true){
				totalItems=totalItems+1;
			}
			else if($(this). prop("checked") == false){
				totalItems = totalItems-1;
			}
	
			if(totalItems==0){
				$('.notifyjs-corner').empty();
				exit;
			}
	
			$('.notifyjs-corner').empty();
	
			$.notify(
				'Total '+totalItems+' item checked',
				{ position:"top center",className: 'success'}
				);
		});
	 
</script>

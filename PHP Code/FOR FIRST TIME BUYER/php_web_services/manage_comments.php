<?php $page_title="Manage Comments";

include('includes/header.php'); 

    include('includes/function.php');
	include('language/language.php');  


	function book_info($book_id)
	{
		global $mysqli;

		$query="SELECT * FROM tbl_books WHERE tbl_books.`id`='$book_id'";
		$sql = mysqli_query($mysqli,$query)or die(mysqli_error());
		$row=mysqli_fetch_assoc($sql);

		return stripslashes($row['book_title']);
	}
    
	function total_comments($book_id)
	{
		global $mysqli;

		$query="SELECT COUNT(*) AS total_comments FROM tbl_comments WHERE `book_id`='$book_id'";
		$sql = mysqli_query($mysqli,$query) or die(mysqli_error());
		$row=mysqli_fetch_assoc($sql);
		
		return stripslashes($row['total_comments']);
	}
	 
	$tableName="tbl_comments";		
	$targetpage = "manage_comments.php"; 	
	$limit = 15; 
	
	$query = "SELECT COUNT(*) as num FROM $tableName GROUP BY `book_id`";
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
	
	
 	$users_qry="SELECT comment.`id`, comment.`book_id`, max(comment.`comment_on`) as comment_on FROM
				tbl_comments comment
				LEFT JOIN tbl_users user 
				ON comment.`user_id`=user.`id`
				GROUP BY comment.`book_id`
				ORDER BY comment.`id` DESC LIMIT $start, $limit";
	  
							 
	$users_result=mysqli_query($mysqli,$users_qry);

	
?>
<link rel="stylesheet" type="text/css" href="assets/css/stylish-tooltip.css">

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
  				  <th>Book Title</th>
 				  <th>Total Comment</th>	
				  <th>Last Comment</th>	 
                  <th class="cat_action_list">Action</th>
                </tr>
              </thead>
              <tbody>
              	<?php
              		
              		$i=0;
					while($users_row=mysqli_fetch_array($users_result))
					{?>
				  <tr class="<?=$users_row['book_id']?>">
				  	<td> 
					<div class="checkbox">
						<input type="checkbox" name="post_ids[]" id="checkbox<?php echo $i;?>" value="<?php echo $users_row['book_id']; ?>" class="post_ids">
						<label for="checkbox<?php echo $i;?>"></label>
					</div>
					</td>
		           	<td>
		           		<?php echo book_info($users_row['book_id']);?>		
		           	</td>
					<td>
		           		<a href="view_comments.php?book_id=<?=$users_row['book_id']?>"><?php echo total_comments($users_row['book_id']);?> Comments</a>
		           	</td>
		           	<td>
		           		<?=date('d-m-Y',$users_row['comment_on']);?>
		           	</td>
				    <td> 
						<a href="javascript:void(0)" data-id="<?php echo $users_row['book_id'];?>" class="btn btn-danger btn_delete" data-toggle="tooltip" data-tooltip="Delete"><i class="fa fa-trash"></i></a> 
				    </td>
                </tr>
               <?php $i++; }  ?>
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


<?php include('includes/footer.php');?>

<script type="text/javascript">
/*
$(".btn_delete").click(function(e){
		e.preventDefault();
		var _book=$(this).data("book");
		if(confirm('Are you sure you want to delete this comments ?')){
			var _element=$(this).parents("tbody").find("."+_book);	
			$.ajax({
		      type:'post',
		      url:'processdata.php',
		      dataType:'json',
		      data:{book_id:_book,'action':'removeAllComment'},
		      success:function(res){
		          console.log(res);
		          if(res.status=='1'){
		            _element.remove();
		          }
		        }
		    });

		}
	});
*/
	  // for multiple deletes
  $(".btn_delete_all").click(function(e){
  		e.preventDefault();

	var _ids = $.map($('.post_ids:checked'), function(c){return c.value; });

	if(_ids!='')
	{
		swal({
          title: "Are you sure?",
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
              data:{ids:_ids,'action':'removeAllComment'},
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


    // for single comment row delete
  $(".btn_delete").click(function(e){
	  	e.preventDefault();
		var _id = $(this).data('id');
		if(_id!='')
		{
			swal({
	          title: "Are you sure?",
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
	              data:{book_id:_id,'action':'removeComment'},
	              success:function(res){
	                  console.log(res);
	                  if(res.status=='1'){
	                    location.reload();
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

var totalItems=0;

	{$("#checkall_input").click(function () {
	
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
	}

</script>                  
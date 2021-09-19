<?php
include("includes/connection.php");
include("includes/function.php");
include("language/app_language.php"); 	
include("smtp_email.php");

error_reporting(0);

$file_path = getBaseUrl();

define("PACKAGE_NAME",$settings_details['package_name']);
define("API_SUB_CAT_ORDER_BY",$settings_details['api_sub_cat_order_by']);
define("API_SUB_CAT_POST_ORDER_BY",$settings_details['api_sub_cat_post_order_by']);
define("NATIVE_POSITION",$settings_details['native_position']);
define("NATIVE_ADS",$settings_details['native_ad']);
define("NATIVE_POSITION_GRID",$settings_details['native_position_grid']);
define("NATIVE_CAT_POSITION",$settings_details['native_cat_position']);

$mysqli->set_charset('utf8mb4');

date_default_timezone_set("Asia/Kolkata");

	  
      if($settings_details['envato_buyer_name']=='' OR $settings_details['envato_purchase_code']=='' OR $settings_details['envato_purchased_status']==0) {  

		$set = array('message' => 'Purchase code verification failed!','status'=>-1);	
		
		header( 'Content-Type: application/json; charset=utf-8' );
	    echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}
	
	function get_thumb($filename,$thumb_size)
	{
		
		$file_path = getBaseUrl();

		return $thumb_path=$file_path.'thumb.php?src='.$filename.'&size='.$thumb_size;
	}

	function generateRandomPassword($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	function get_user_info($user_id,$field_name) 
	{
		global $mysqli;

		$qry_user="SELECT * FROM tbl_users WHERE id='".$user_id."'";
		$query1=mysqli_query($mysqli,$qry_user);
		$row_user = mysqli_fetch_array($query1);

		$num_rows1 = mysqli_num_rows($query1);
		
		if ($num_rows1 > 0)
		{		 	
			return $row_user[$field_name];
		}
		else
		{
			return "";
		}
	}

  
  function get_total_item($cat_id)
  { 
    global $mysqli;   

    $qry_songs="SELECT COUNT(*) as num FROM tbl_books WHERE  tbl_books.`status`='1' AND `cat_id`='".$cat_id."'";
     
    $total_songs = mysqli_fetch_array(mysqli_query($mysqli,$qry_songs));
    $total_songs = $total_songs['num'];
     
    return $total_songs;
  } 
   function get_total_item1($cat_id)
  { 
    global $mysqli;   

    $qry_songs="SELECT COUNT(*) as num FROM tbl_sub_category WHERE `cat_id`='".$cat_id."'";
     
    $total_songs = mysqli_fetch_array(mysqli_query($mysqli,$qry_songs));
    $total_songs = $total_songs['num'];
     
    return $total_songs;
  } 	 	 

   function get_total_books($cat_id)
	{	
		global $mysqli;

		$qry_books="SELECT COUNT(*) as num FROM tbl_books WHERE tbl_books.`status`='1' AND `cat_id` ='".$cat_id."' AND `status` ='1'";
		$total_books = mysqli_fetch_array(mysqli_query($mysqli,$qry_books));
		$total_books = $total_books['num'];

		return $total_books;
	}

	function get_total_books_sub($sub_cat_id)
	{	
		global $mysqli;

		$qry_books="SELECT COUNT(*) as num FROM tbl_books WHERE tbl_books.`status`='1' AND `sub_cat_id` ='".$sub_cat_id."' AND `status` ='1'";
		$total_books = mysqli_fetch_array(mysqli_query($mysqli,$qry_books));
		$total_books = $total_books['num'];

		return $total_books;
	}

	function get_subject_info($id,$field_name) 
	{
		global $mysqli;

		$qry_sub="SELECT * FROM tbl_contact_sub WHERE id='$id'";
		$query1=mysqli_query($mysqli,$qry_sub);
		$row_sub = mysqli_fetch_array($query1);

		$num_rows1 = mysqli_num_rows($query1);
		
		if ($num_rows1 > 0)
		{		 	
			return $row_sub[$field_name];
		}
		else
		{
			return "";
		}
	}

	function get_auth_name($author_id)
	{	
		global $mysqli;
		
		$u_query="SELECT * FROM tbl_author WHERE tbl_author.`author_id`='".$author_id."'";
		$u_sql = mysqli_query($mysqli,$u_query)or die(mysqli_error($mysqli));
		
		$u_row=mysqli_fetch_assoc($u_sql);
		
		return $u_row['author_name'];
	}
	
	// paramater wise info
	function get_single_info($book_id,$param,$type='Book')
	{
		global $mysqli;
		
		switch ($type) {
			case 'Book':
			$query="SELECT * FROM tbl_books WHERE `id`='$book_id'";
			break;
			
			default:
			$query="SELECT * FROM tbl_books WHERE `id`='$book_id'";
			break;
		}
		
		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));
		$row=mysqli_fetch_assoc($sql);

		return stripslashes($row[$param]);
	}

	function get_status_check($id) 
	{
		global $mysqli;
		
		$qry_ads="SELECT * FROM tbl_books WHERE tbl_books.`id`='$id' AND  tbl_books.`status` ='1'";
		$query=mysqli_query($mysqli,$qry_ads);
		$num_rows = mysqli_num_rows($query);
		
		if ($num_rows > 0)
		{		 	
			return true;
		}
		else
		{
			return false;
		}
		
	}

	function get_status_check_cat($cat_id) 
	{
		global $mysqli;

		$qry_ads="SELECT * FROM tbl_category WHERE tbl_category.`cid`='$cat_id' AND  tbl_category.`status` ='1'";
		$query=mysqli_query($mysqli,$qry_ads);
		$num_rows = mysqli_num_rows($query);
		
		if ($num_rows > 0)
		{		 	
			return true;
		}
		else
		{
			return false;
		}
		
	}  

	function get_status_check_author($id) 
	{
		global $mysqli;

		$qry_ads="SELECT * FROM tbl_author WHERE tbl_author.`author_id`='$id' AND  tbl_author.`status` ='1'";
		$query=mysqli_query($mysqli,$qry_ads);
		$num_rows = mysqli_num_rows($query);
		
		if ($num_rows > 0)
		{		 	
			return true;
		}
		else
		{
			return false;
		}
		
	} 
	
	$get_method = checkSignSalt($_POST['data']);	
	
	if($get_method['method_name']=="get_home"){
		$row['status'] = 1;
		$row['message'] = '';	

		$jsonObj_2= array();

		$query_all="SELECT * FROM tbl_slider WHERE `status`='1' ORDER BY `id` DESC";

		$sql_all = mysqli_query($mysqli,$query_all) or die(mysqli_error($mysqli));

		while($data = mysqli_fetch_assoc($sql_all))
		{
			$total_views=0;

			$book_id=$data['book_id'];

			switch ($data['slider_type']) {
				case 'Book':
				
				$query="SELECT tbl_books.`book_title`, tbl_books.`book_bg_img`, tbl_books.`book_cover_img`,tbl_books.`rate_avg`,tbl_books.`book_views`,tbl_books.`total_rate`,tbl_books.`book_views`, tbl_books.*,tbl_author.`author_name` FROM tbl_books
				LEFT JOIN tbl_author ON tbl_books.`aid`= tbl_author.`author_id` 
				WHERE tbl_books.`status`='1' AND tbl_author.`status`='1' AND tbl_books.`id`='$book_id' ORDER BY tbl_books.`id` DESC";	
				
				$sql_res=mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));

				$row_data=mysqli_fetch_assoc($sql_res);

				$slider_title=$row_data['book_title'];
				$image=$row_data['book_bg_img'];
				$book_views=$row_data['book_views'];
				$rate_avg=$row_data['rate_avg'];
				$total_rate=$row_data['total_rate'];
				$book_views=$row_data['book_views'];
				$author_name=$row_data['author_name'];
				
				break;
				
				default:
				$slider_title=$data['slider_title'];
				$image=$data['external_image'];
				break;
				
			}
			
			if($sql_res->num_rows == 0 AND $data['slider_type']!='external'){
				continue;
			}
			
			$row2['book_id'] = $data['book_id'];
			$row2['book_type'] = $data['slider_type'];
			$row2['book_title'] = $slider_title;
			$row2['book_bg_img'] = $file_path.'images/'.$image;
			$row2['total_rate'] = ($total_rate!='') ? $total_rate : '0';
			$row2['rate_avg'] = ($rate_avg!='') ? $rate_avg : '0';
			$row2['author_name'] = ($author_name!='') ? $author_name : '';
			$row2['external_link'] = ($data['external_url']!='') ? $data['external_url'] : '';
			$row2['total_viewer'] = ($book_views!='') ? $book_views : '0';

			array_push($jsonObj_2,$row2);
			
		}

		$row['slider_books']=$jsonObj_2;

		$jsonObj2= array(); 
		
		$query2="SELECT * FROM tbl_books
		LEFT JOIN tbl_author ON tbl_books.`aid`= tbl_author.`author_id`
		WHERE tbl_books.`status`='1' AND tbl_author.`status`='1' 
		ORDER BY tbl_books.`id` DESC LIMIT 5";

		$sql2 = mysqli_query($mysqli,$query2)or die(mysqli_error($mysqli));

		while($data_2 = mysqli_fetch_assoc($sql2))
		{

			$row_2['id'] = $data_2['id'];
			$row_2['book_title'] = stripslashes($data_2['book_title']);
			$row_2['book_cover_img'] = $file_path.'images/'.$data_2['book_cover_img'];
			
			$row_2['total_rate'] = $data_2['total_rate'];
			$row_2['rate_avg'] = $data_2['rate_avg'];

			$row_2['author_name'] = $data_2['author_name'];
			
			array_push($jsonObj2,$row_2);
			
		} 
		$row['latest_books'] = $jsonObj2;

		$jsonObj3= array(); 
		
		$query3="SELECT * FROM tbl_books
		LEFT JOIN tbl_author ON tbl_books.`aid` = tbl_author.`author_id`   
		WHERE tbl_books.`status`=1 AND tbl_author.`status`='1' 
		ORDER BY tbl_books.`book_views` DESC,tbl_books.`total_rate` DESC LIMIT 5";

		$sql3 = mysqli_query($mysqli,$query3)or die(mysqli_error($mysqli));

		while($data3 = mysqli_fetch_assoc($sql3))
		{
			
			$row3['id'] = $data3['id'];
			$row3['book_title'] = stripslashes($data3['book_title']);
			$row3['book_cover_img'] = $file_path.'images/'.$data3['book_cover_img'];
			
			$row3['total_rate'] = $data3['total_rate'];
			$row3['rate_avg'] = $data3['rate_avg'];

			$row3['author_name'] = $data3['author_name'];
			
			array_push($jsonObj3,$row3);
			
		} 
		$row['popular_books'] = $jsonObj3;
		
		$jsonObj4= array();
		
		$cat_order=API_CAT_ORDER_BY;

		$limit=$settings_details['cat_show_home_limit'];

		$query4="SELECT * FROM tbl_category WHERE tbl_category.`status`='1' 
		AND tbl_category.`show_on_home`='1' ORDER BY tbl_category.$cat_order 
		DESC LIMIT $limit";

		$sql4 = mysqli_query($mysqli,$query4) or die(mysqli_error($mysqli));

		if($sql4->num_rows == 0){

			mysqli_free_result($sql4);
			$query4="SELECT * FROM tbl_category WHERE tbl_category.`status`='1'
			ORDER BY tbl_category.$cat_order DESC LIMIT $limit";

			$sql4 = mysqli_query($mysqli,$query4) or die(mysqli_error($mysqli));
		}

		while($data4 = mysqli_fetch_assoc($sql4))
		{
			
			$row4['cid'] = $data4['cid'];
			$row4['category_name'] = $data4['category_name'];
			$row4['total_books'] = get_total_item($data4['cid']);
			$row4['cat_image'] = $file_path.'images/'.$data4['category_image'];
			$row4['cat_image_thumb'] = get_thumb('images/'.$data4['category_image'],'300x300');

			if(get_total_item1($data4['cid']) > 0) {
				$row4['sub_cat_status'] = 'true';
			} else {
				$row4['sub_cat_status'] = 'false';
			}

			array_push($jsonObj4,$row4);
			
		}
		$row['category_list'] = $jsonObj4;
		
		$jsonObj5= array();
		$author_order=API_AUTHOR_ORDER_BY;

		$query5="SELECT * FROM tbl_author WHERE tbl_author.`status`='1' ORDER BY tbl_author.".$author_order." DESC LIMIT 5";
		$sql5 = mysqli_query($mysqli,$query5)or die(mysqli_error($mysqli));

		while($data5 = mysqli_fetch_assoc($sql5))
		{	
			
			$row5['author_id'] = $data5['author_id'];
			$row5['author_name'] = $data5['author_name'];
			$row5['author_image'] = $file_path.'images/'.$data5['author_image'];
			
			array_push($jsonObj5,$row5);
			
		}
		
		$row['author_list'] = $jsonObj5;

		$jsonObj6= array(); 
		
		$user_id=$get_method['user_id'];

		if($user_id != ''){

			$query6="SELECT * FROM tbl_user_continue
			LEFT JOIN tbl_users ON tbl_users.`id`= tbl_user_continue.`user_id`
			LEFT JOIN tbl_books ON tbl_books.`id`= tbl_user_continue.`book_id`
			LEFT JOIN tbl_author ON tbl_author.`author_id`= tbl_books.`aid`
			WHERE tbl_user_continue.`user_id` = $user_id AND tbl_books.`status`= 1
			ORDER BY tbl_user_continue.`user_con_date` DESC LIMIT 5";

			$sql6 = mysqli_query($mysqli,$query6)or die(mysqli_error($mysqli));

			while($data6 = mysqli_fetch_assoc($sql6))
			{
				
				$row6['id'] = $data6['id'];
				$row6['book_title'] = stripslashes($data6['book_title']);
				$row6['book_cover_img'] = $file_path.'images/'.$data6['book_cover_img'];
				
				$row6['total_rate'] = $data6['total_rate'];
				$row6['rate_avg'] = $data6['rate_avg'];

				$row6['author_name'] = get_auth_name($data6['aid'],'author_name');
				
				array_push($jsonObj6,$row6);
			}
		} 

		$row['continue_books'] = $jsonObj6;
		
		$set= $row;

		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}
	else if($get_method['method_name']=="get_category"){

		$jsonObj= array();

		$cid=API_CAT_ORDER_BY;

		$page_limit=API_PAGE_LIMIT;

		$limit=($get_method['page']-1) * $page_limit;

		$query="SELECT * FROM tbl_category WHERE tbl_category.`status`=1 ORDER BY tbl_category.$cid LIMIT $limit, $page_limit";
		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		$j = $get_method['ads_param'];

		$data_arr=array();
		
		while($data = mysqli_fetch_assoc($sql))
		{	
			$data_arr[]=$data;
		}

		$var_j=0;

		for ($i=0; $i < count($data_arr); $i++){ 

			if(NATIVE_ADS=='true'){
				if($j % NATIVE_POSITION == 0){

					if(NATIVE_ADS=='true'){

						$row['is_ads'] = true;
						$row['total_books'] = "";
						$row['cid'] = "";
						$row['category_name'] = "";
						$row['cat_image'] = "";
						$row['cat_image_thumb'] = "";
						$row['sub_cat_status'] = "";

						$row['native_ad_type'] = $settings_details['native_ad_type'];
						$row['native_ad_id'] = ($settings_details['native_ad_type']=='facebook') ? $settings_details['native_facebook_id'] : $settings_details['native_ad_id'];

						$i--;
					}
				}	
				else{

					$row['is_ads'] = false;
					$row['total_books'] = get_total_item($data_arr[$i]['cid']);
					$row['cid'] = $data_arr[$i]['cid'];
					$row['category_name'] = $data_arr[$i]['category_name'];
					$row['cat_image'] = $file_path.'images/'.$data_arr[$i]['category_image'];
					$row['cat_image_thumb'] = get_thumb('images/'.$data_arr[$i]['category_image'],'300x300');

					if(get_total_item1($data_arr[$i]['cid']) > 0) {
						$row['sub_cat_status'] = 'true';
					} else {
						$row['sub_cat_status'] = 'false';
					}

					$row['native_ad_type'] = "";
					$row['native_ad_id'] = "";
				}

				$j++;
			}

			else{
				$row['is_ads'] = false;
				$row['total_books'] = get_total_item($data_arr[$i]['cid']);
				$row['cid'] = $data_arr[$i]['cid'];
				$row['category_name'] = $data_arr[$i]['category_name'];
				$row['cat_image'] = $file_path.'images/'.$data_arr[$i]['category_image'];
				$row['cat_image_thumb'] = get_thumb('images/'.$data_arr[$i]['category_image'],'300x300');

				if(get_total_item1($data_arr[$i]['cid']) > 0) {
					$row['sub_cat_status'] = 'true';
				} else {
					$row['sub_cat_status'] = 'false';
				}

				$row['native_ad_type'] = "";
				$row['native_ad_id'] = "";
			}
			
			array_push($jsonObj,$row);
			
		}

		$set['status'] = '1';
		$set['message'] = '';
		$set['ads_param'] = strval($j);
		$set['category_list'] = $jsonObj;

		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}	
	else if($get_method['method_name']=="get_category_name"){

		$jsonObj= array();
		
		$cat_order=API_CAT_ORDER_BY;

		$query="SELECT * FROM tbl_category WHERE tbl_category.`status`='1'
		ORDER BY tbl_category.".$cat_order." DESC ";

		$sql = mysqli_query($mysqli,$query)or die(mysql_error($mysqli));
		
		while($data = mysqli_fetch_assoc($sql))
		{	
			$row['id'] = $data['cid'];
			$row['category_name'] = $data['category_name'];

			array_push($jsonObj,$row);
			
		}

		$row2['status']=1;
		$row2['message']='';
		$row2['category_name']=$jsonObj;

		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($row2,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}
	else if($get_method['method_name']=="get_sub_cat_name"){
		
		$cat_id=$get_method['cat_id'];

		$jsonObj= array();
		
		$sub_cat_order_by=API_SUB_CAT_ORDER_BY;

		$query="SELECT * FROM tbl_sub_category WHERE  tbl_sub_category.`cat_id`='$cat_id' AND tbl_sub_category.`status` = 1 ORDER BY tbl_sub_category.".$sub_cat_order_by."  DESC";

		$sql = mysqli_query($mysqli,$query)or die(mysql_error($mysqli));
		
		while($data = mysqli_fetch_assoc($sql))
		{	
			$row['sid'] = $data['sid'];
			$row['sub_cat_name'] = $data['sub_cat_name'];
			
			array_push($jsonObj,$row);
			
		}

		$row2['status']=1;
		$row2['message']='';
		$row2['sub_category_name']=$jsonObj;

	    //$set=$row;

		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($row2,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}
	else if($get_method['method_name']=="get_sub_category"){

		$cat_id=$get_method['cat_id'];

		$page_limit=API_PAGE_LIMIT;
		
		$limit=($get_method['page']-1) * $page_limit;

		$sub_cat_order_by=API_SUB_CAT_ORDER_BY;

		$sub_cat_post_order=API_SUB_CAT_POST_ORDER_BY;			

		$jsonObj= array();

		$query="SELECT * FROM tbl_sub_category WHERE tbl_sub_category.`cat_id`='$cat_id' AND tbl_sub_category.`status` = 1 ORDER BY tbl_sub_category.".$sub_cat_order_by."  $sub_cat_post_order  LIMIT $limit, $page_limit";

		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		while($data = mysqli_fetch_assoc($sql))
		{	
	
			$row['sub_cat_id'] = $data['sid'];
			$row['cat_id'] = $data['cat_id'];
			$row['total_books'] = get_total_books_sub($data['sid']);
			$row['sub_cat_name'] = $data['sub_cat_name'];
			$row['sub_cat_image'] = $file_path.'images/'.$data['sub_cat_image'];
			$row['sub_category_image_thumb'] = get_thumb('images/'.$data['sub_cat_image'],'140x100');
			array_push($jsonObj,$row);
			
		}

		$set['status'] = '1';
		$set['message'] = '';
		$set['ads_param'] = strval($j);
		$set['EBOOK_APP'] = $jsonObj;

		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}
	else if($get_method['method_name']=="get_cat_id"){

		$post_order_by=API_CAT_POST_ORDER_BY;

		$cat_id=$get_method['cat_id'];	
		$sub_cat_id=$get_method['sub_cat_id'];	

		if(get_status_check_cat($cat_id)){

			$query_rec ="SELECT COUNT(*) as num FROM tbl_books
			LEFT JOIN tbl_category ON tbl_books.`cat_id` = tbl_category.`cid`
			LEFT JOIN tbl_author ON tbl_books.`aid` = tbl_author.`author_id` 
			WHERE  tbl_books.`sub_cat_id`='$sub_cat_id' AND tbl_books.`cat_id`='$cat_id' AND tbl_books.`status`='1' AND tbl_category.`status`='1' AND tbl_author.`status`='1' ORDER BY tbl_books.`id`";

			$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));
			
			$page_limit=API_PAGE_LIMIT;
			
			$limit=($get_method['page']-1) * $page_limit;

			$jsonObj= array();	

			$query="SELECT * FROM tbl_books
			LEFT JOIN tbl_category ON tbl_books.`cat_id` = tbl_category.`cid`
			LEFT JOIN tbl_author ON tbl_books.`aid` = tbl_author.`author_id` 
			WHERE tbl_books.`sub_cat_id`='$sub_cat_id' AND tbl_books.`cat_id`='$cat_id' AND tbl_books.`status`='1' AND tbl_category.`status`='1' AND tbl_author.`status`='1' ORDER BY tbl_books.`id` ".$post_order_by." LIMIT $limit, $page_limit";

			$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

			$j = $get_method['ads_param'];
			$is_book = $get_method['is_book'];

			$data_arr=array();

			while($data = mysqli_fetch_assoc($sql))
			{	
				$data_arr[]=$data;
			}

			$var_j=0;

			for ($i=0; $i < count($data_arr); $i++){ 

				if(NATIVE_ADS=='true'){
					if($is_book == 'list_book'){
						if($j % NATIVE_CAT_POSITION == 0){
							
							$row['is_ads'] = true;
							$row['native_ad_type'] = $settings_details['native_ad_type'];
							$row['native_ad_id'] = ($settings_details['native_ad_type']=='facebook') ? $settings_details['native_facebook_id'] : $settings_details['native_ad_id'];

							$row['id'] = "";
							$row['book_title'] = "";
							$row['book_description'] = "";
							$row['book_cover_img'] ="";
							$row['total_rate'] = "";
							$row['rate_avg'] = "";
							$row['book_views'] = "";
							$row['author_name'] = "";
							
							$i--;
						}else{

							$row['is_ads'] = false;
							$row['id'] = $data_arr[$i]['id'];
							$row['book_title'] = stripslashes($data_arr[$i]['book_title']);
							$row['book_description'] = stripslashes($data_arr[$i]['book_description']);
							$row['book_cover_img'] = $file_path.'images/'.$data_arr[$i]['book_cover_img'];
							$row['total_rate'] = $data_arr[$i]['total_rate'];
							$row['rate_avg'] = $data_arr[$i]['rate_avg'];
							$row['book_views'] = $data_arr[$i]['book_views'];
							$row['author_name'] = $data_arr[$i]['author_name'];

							$row['native_ad_type'] = "";
							$row['native_ad_id'] = "";
						}
						$j++;
					}else{
						if($j % NATIVE_POSITION_GRID == 0){

							$row['is_ads'] = true;
							$row['native_ad_type'] = $settings_details['native_ad_type'];
							$row['native_ad_id'] = ($settings_details['native_ad_type']=='facebook') ? $settings_details['native_facebook_id'] : $settings_details['native_ad_id'];

							$row['id'] = "";
							$row['book_title'] = "";
							$row['book_description'] = "";
							$row['book_cover_img'] ="";
							$row['total_rate'] = "";
							$row['rate_avg'] = "";
							$row['book_views'] = "";

							$row['author_name'] = "";
							
							$i--;

						}else{
							$row['is_ads'] = false;
							$row['native_ad_type'] = "";
							$row['native_ad_id'] = "";

							$row['id'] = $data_arr[$i]['id'];
							$row['book_title'] = stripslashes($data_arr[$i]['book_title']);
							$row['book_description'] = stripslashes($data_arr[$i]['book_description']);
							$row['book_cover_img'] = $file_path.'images/'.$data_arr[$i]['book_cover_img'];
							$row['total_rate'] = $data_arr[$i]['total_rate'];
							$row['rate_avg'] = $data_arr[$i]['rate_avg'];
							$row['book_views'] = $data_arr[$i]['book_views'];

							$row['author_name'] = $data_arr[$i]['author_name'];

						}
						$j++;
					}
				}else{
					$row['is_ads'] = false;
					$row['native_ad_type'] = "";
					$row['native_ad_id'] = "";

					$row['id'] = $data_arr[$i]['id'];
					$row['book_title'] = stripslashes($data_arr[$i]['book_title']);
					$row['book_description'] = stripslashes($data_arr[$i]['book_description']);
					$row['book_cover_img'] = $file_path.'images/'.$data_arr[$i]['book_cover_img'];

					$row['total_rate'] = $data_arr[$i]['total_rate'];
					$row['rate_avg'] = $data_arr[$i]['rate_avg'];
					$row['book_views'] = $data_arr[$i]['book_views'];

					$row['author_name'] = $data_arr[$i]['author_name'];
					
					
				}

				array_push($jsonObj,$row);

			}
			$set['status'] = '1';
			$set['message'] = '';
			$set['ads_param'] = strval($j);
			$set['total_books'] = $total_pages['num'];
			$set['EBOOK_APP']=$jsonObj;
		}		
		else{

			$set = array('status' => '-1','message' => 'Category is not found');

		}
		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}		
	else if($get_method['method_name']=="get_author"){

		$jsonObj= array();
		
		$author_order=API_AUTHOR_ORDER_BY;

		$page_limit=API_PAGE_LIMIT;
		
		$limit=($get_method['page']-1) * $page_limit;

		$query="SELECT *FROM tbl_author WHERE tbl_author.`status`='1' 
		ORDER BY tbl_author.".$author_order." LIMIT $limit, $page_limit ";

		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		$j = $get_method['ads_param'];

		$data_arr=array();
		
		while($data = mysqli_fetch_assoc($sql))
		{	
			$data_arr[]=$data;
		}

		$var_j=0;

		for ($i=0; $i < count($data_arr); $i++){ 

			if(NATIVE_ADS=='true'){
				if($j % NATIVE_POSITION == 0){

					if(NATIVE_ADS=='true'){

						$row['is_ads'] = true;
						$row['author_id'] = "";
						$row['author_name'] = "";
						$row['author_city_name'] = "";
						$row['author_description'] = "";
						$row['author_image'] = "";
						
						$row['native_ad_type'] = $settings_details['native_ad_type'];
						$row['native_ad_id'] = ($settings_details['native_ad_type']=='facebook') ? $settings_details['native_facebook_id'] : $settings_details['native_ad_id'];

						$i--;
					}
				}	
				else{

					$row['is_ads'] = false;
					$row['author_id'] = $data_arr[$i]['author_id'];
					$row['author_name'] = $data_arr[$i]['author_name'];
					$row['author_city_name'] = $data_arr[$i]['author_city_name'];
					$row['author_description'] = $data_arr[$i]['author_description'];
					$row['author_image'] = $file_path.'images/'.$data_arr[$i]['author_image'];

					$row['native_ad_type'] = "";
					$row['native_ad_id'] = "";
				}

				$j++;
			}

			else{
				$row['is_ads'] = false;
				$row['author_id'] = $data_arr[$i]['author_id'];
				$row['author_name'] = $data_arr[$i]['author_name'];
				$row['author_city_name'] = $data_arr[$i]['author_city_name'];
				$row['author_description'] = $data_arr[$i]['author_description'];
				$row['author_image'] = $file_path.'images/'.$data_arr[$i]['author_image'];
				
				$row['native_ad_type'] = "";
				$row['native_ad_id'] = "";
			}
			
			array_push($jsonObj,$row);
			
		}

		$set['status'] = '1';
		$set['message'] = '';
		$set['ads_param'] = strval($j);
		$set['author_list'] = $jsonObj;

		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}		
	else if($get_method['method_name']=="get_author_name"){
		$jsonObj= array();
		
		$author_order=API_AUTHOR_ORDER_BY;

		$query="SELECT *FROM tbl_author WHERE tbl_author.`status`='1' 
		ORDER BY tbl_author.".$author_order." DESC";

		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		while($data = mysqli_fetch_assoc($sql))
		{	
			$row['id'] = $data['author_id'];
			$row['author_name'] = $data['author_name'];
			
			array_push($jsonObj,$row);
			
		}

		$row2['status']=1;
		$row2['message']='';
		$row2['author_name']=$jsonObj;

		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($row2,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}
	else if($get_method['method_name']=="get_author_id"){

		$post_order_by=API_AUTHOR_POST_ORDER_BY;
		
		$author_id=$get_method['author_id'];

		if(get_status_check_author($author_id)){

			$query_rec ="SELECT COUNT(*) as num FROM tbl_books
			LEFT JOIN tbl_category ON tbl_books.`cat_id` = tbl_category.`cid`
			LEFT JOIN tbl_author ON tbl_books.`aid`= tbl_author.`author_id` 
			WHERE tbl_books.`aid`='".$author_id."' AND tbl_books.`status` ='1' 
			AND tbl_author.`status`='1' AND tbl_category.`status`='1'
			ORDER BY tbl_books.`id`";

			$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));
			
			$page_limit=API_PAGE_LIMIT;
			
			$limit=($get_method['page']-1) * $page_limit;

			$jsonObj= array();	

			$query="SELECT * FROM tbl_books
			LEFT JOIN tbl_category ON tbl_books.`cat_id` = tbl_category.`cid`
			LEFT JOIN tbl_author ON tbl_books.`aid`= tbl_author.`author_id` 
			WHERE tbl_books.`aid`='".$author_id."' AND tbl_books.`status` ='1' 
			AND tbl_author.`status`='1' AND tbl_category.`status`='1' 
			ORDER BY tbl_books.`id` ".$post_order_by."  LIMIT $limit, $page_limit";
			
			$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));
			
			$j = $get_method['ads_param'];

			$data_arr=array();
			
			while($data = mysqli_fetch_assoc($sql))
			{	
				$data_arr[]=$data;
			}

			$var_j=0;

			for ($i=0; $i < count($data_arr); $i++){ 

				if(NATIVE_ADS=='true'){
					if($j % NATIVE_CAT_POSITION == 0){

						if(NATIVE_ADS=='true'){

							$row['is_ads'] = true;
							$row['native_ad_type'] = $settings_details['native_ad_type'];
							$row['native_ad_id'] = ($settings_details['native_ad_type']=='facebook') ? $settings_details['native_facebook_id'] : $settings_details['native_ad_id'];

							$row['id'] = "";
							$row['book_title'] = "";
							$row['book_description'] = "";
							$row['book_cover_img'] ="";
							
							$row['total_rate'] = "";
							$row['rate_avg'] = "";
							$row['book_views'] = "";

							$set['author_name'] = "";
							
							$i--;
						}
					}	
					else{

						$row['is_ads'] = false;
						$row['native_ad_type'] = "";
						$row['native_ad_id'] = "";

						$row['id'] = $data_arr[$i]['id'];
						$row['book_title'] = stripslashes($data_arr[$i]['book_title']);
						$row['book_description'] = stripslashes($data_arr[$i]['book_description']);
						$row['book_cover_img'] = $file_path.'images/'.$data_arr[$i]['book_cover_img'];
						
						$row['total_rate'] = $data_arr[$i]['total_rate'];
						$row['rate_avg'] = $data_arr[$i]['rate_avg'];
						$row['book_views'] = $data_arr[$i]['book_views'];

						$row['author_name'] = $data_arr[$i]['author_name'];
						
					}

					$j++;
				}

				else{
					$row['is_ads'] = false;
					$row['native_ad_type'] = "";
					$row['native_ad_id'] = "";

					$row['id'] = $data_arr[$i]['id'];
					$row['book_title'] = stripslashes($data_arr[$i]['book_title']);
					$row['book_description'] = stripslashes($data_arr[$i]['book_description']);
					$row['book_cover_img'] = $file_path.'images/'.$data_arr[$i]['book_cover_img'];
					
					$row['total_rate'] = $data_arr[$i]['total_rate'];
					$row['rate_avg'] = $data_arr[$i]['rate_avg'];
					$row['book_views'] = $data_arr[$i]['book_views'];

					$row['author_name'] = $data_arr[$i]['author_name'];

				}
				
				array_push($jsonObj,$row);
				
			}

			$set['status'] = '1';
			$set['message'] = '';
			$set['ads_param'] = strval($j);
			$set['EBOOK_APP'] = $jsonObj;

		}		
		else{

			$row2 = array('status' => '-1','message' => 'Book is not found');

		}

		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}		
	else if($get_method['method_name']=="get_author_details"){

		$post_order_by=API_AUTHOR_POST_ORDER_BY;

		$author_id=$get_method['author_id'];

		$query0="SELECT *FROM tbl_author WHERE tbl_author.`status`='1' AND author_id='".$author_id."'";
		$sql0 = mysqli_query($mysqli,$query0);
		$data0 = mysqli_fetch_assoc($sql0);

		if($sql0->num_rows > 0)
		{

			$set['status']=1;
			$set['message']='';
			$set['author_id'] = $data0['author_id'];
			$set['author_name'] = $data0['author_name'];
			$set['author_city_name'] = $data0['author_city_name'];
			$set['author_description'] = $data0['author_description'];
			$set['author_image'] = $file_path.'images/'.$data0['author_image'];
			$set['author_instagram'] = $data0['author_instagram'];
			$set['author_facebook'] = $data0['author_facebook'];
			$set['author_website'] = $data0['author_website'];
			$set['author_youtube'] = $data0['author_youtube'];
			
		}		
		else{

			$set = array('status' => '-1','message' => 'Book is not found');

		}

		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}	
	else if($get_method['method_name']=="get_latest_books"){

		$query_rec = "SELECT COUNT(*) as num FROM tbl_books
		LEFT JOIN tbl_category ON tbl_books.`cat_id`= tbl_category.`cid`
		LEFT JOIN tbl_author ON tbl_books.`aid` = tbl_author.`author_id`
		WHERE tbl_books.`status` ='1'
		ORDER BY tbl_books.`id`";

		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));

		$page_limit=API_PAGE_LIMIT;	

		$limit=($get_method['page']-1) * $page_limit;

		$jsonObj= array();	

		$query="SELECT * FROM tbl_books
		LEFT JOIN tbl_category ON tbl_books.`cat_id` = tbl_category.`cid`
		LEFT JOIN tbl_author ON tbl_books.`aid` = tbl_author.`author_id`
		WHERE tbl_books.`status` ='1'
		ORDER BY tbl_books.`id` DESC LIMIT $limit,$page_limit";

		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		$j = $get_method['ads_param'];
		$is_book = $get_method['is_book'];

		$data_arr=array();

		while($data = mysqli_fetch_assoc($sql))
		{	
			$data_arr[]=$data;
		}

		$var_j=0;

		for ($i=0; $i < count($data_arr); $i++){ 

			if(NATIVE_ADS=='true'){
				if($is_book == 'list_book'){
					if($j % NATIVE_CAT_POSITION == 0){
						
						$row['is_ads'] = true;
						$row['native_ad_type'] = $settings_details['native_ad_type'];
						$row['native_ad_id'] = ($settings_details['native_ad_type']=='facebook') ? $settings_details['native_facebook_id'] : $settings_details['native_ad_id'];

						$row['id'] = "";
						$row['book_title'] = "";
						$row['book_description'] = "";
						$row['book_cover_img'] ="";
						
						$row['total_rate'] = "";
						$row['rate_avg'] = "";
						$row['book_views'] = "";

						$row['author_name'] = "";

						$i--;
					}else{

						$row['is_ads'] = false;
						$row['native_ad_type'] = "";
						$row['native_ad_id'] = "";

						$row['id'] = $data_arr[$i]['id'];
						$row['book_title'] = stripslashes($data_arr[$i]['book_title']);
						$row['book_description'] = stripslashes($data_arr[$i]['book_description']);
						$row['book_cover_img'] = $file_path.'images/'.$data_arr[$i]['book_cover_img'];
						
						$row['total_rate'] = $data_arr[$i]['total_rate'];
						$row['rate_avg'] = $data_arr[$i]['rate_avg'];
						$row['book_views'] = $data_arr[$i]['book_views'];
						
						$row['author_name'] = $data_arr[$i]['author_name'];
						
					}
					$j++;
				}else{
					if($j % NATIVE_POSITION_GRID == 0){

						$row['is_ads'] = true;
						$row['native_ad_type'] = $settings_details['native_ad_type'];
						$row['native_ad_id'] = ($settings_details['native_ad_type']=='facebook') ? $settings_details['native_facebook_id'] : $settings_details['native_ad_id'];

						$row['id'] = "";
						$row['book_title'] = "";
						$row['book_description'] = "";
						$row['book_cover_img'] ="";
						
						$row['total_rate'] = "";
						$row['rate_avg'] = "";
						$row['book_views'] = "";
						
						$row['author_name'] = "";

						$i--;

					}else{
						$row['is_ads'] = false;
						$row['native_ad_type'] = "";
						$row['native_ad_id'] = "";

						$row['id'] = $data_arr[$i]['id'];
						$row['book_title'] = stripslashes($data_arr[$i]['book_title']);
						$row['book_description'] = stripslashes($data_arr[$i]['book_description']);
						$row['book_cover_img'] = $file_path.'images/'.$data_arr[$i]['book_cover_img'];
						
						$row['total_rate'] = $data_arr[$i]['total_rate'];
						$row['rate_avg'] = $data_arr[$i]['rate_avg'];
						$row['book_views'] = $data_arr[$i]['book_views'];
						
						$row['author_name'] = $data_arr[$i]['author_name'];
						
					}
					$j++;
				}
			}else{
				$row['is_ads'] = false;
				$row['id'] = $data_arr[$i]['id'];
				$row['native_ad_type'] = "";
				$row['native_ad_id'] = "";
				
				$row['book_title'] = stripslashes($data_arr[$i]['book_title']);
				$row['book_description'] = stripslashes($data_arr[$i]['book_description']);
				$row['book_cover_img'] = $file_path.'images/'.$data_arr[$i]['book_cover_img'];

				$row['total_rate'] = $data_arr[$i]['total_rate'];
				$row['rate_avg'] = $data_arr[$i]['rate_avg'];
				$row['book_views'] = $data_arr[$i]['book_views'];

				$row['author_name'] = $data_arr[$i]['author_name'];
				
			}

			array_push($jsonObj,$row);

		}
		$set['status'] = '1';
		$set['message'] = '';
		$set['ads_param'] = strval($j);
		$set['total_books'] = $total_pages['num'];
		$set['EBOOK_APP']=$jsonObj;

		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}			
	else if($get_method['method_name']=="get_contact"){
		$jsonObj= array();	

		$user_id=$get_method['user_id'];

		$query="SELECT * FROM tbl_contact_sub WHERE status='1' ORDER BY id DESC";
		$sql = mysqli_query($mysqli,$query);

		if(mysqli_num_rows($sql) > 0){
			while ($data = mysqli_fetch_assoc($sql)){
				$info['id']=$data['id'];
				$info['subject']=$data['title'];

				array_push($jsonObj, $info);
			}
		}

		$row2['status']=1;
		$row2['message']='';
		$row2['name']=get_user_info($user_id,'name');
		$row2['email']=get_user_info($user_id,'email');
		$row2['contact_list']=$jsonObj;

		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($row2,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();	
	}
	else if($get_method['method_name']=="get_popular_books"){

		$query_rec = "SELECT COUNT(*) as num FROM tbl_books
		LEFT JOIN tbl_category ON tbl_books.`cat_id`= tbl_category.`cid`
		LEFT JOIN tbl_author ON tbl_books.`aid` = tbl_author.`author_id`
		WHERE tbl_books.`status` ='1' ORDER BY tbl_books.`id`";

		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));

		$page_limit=API_PAGE_LIMIT;	

		$limit=($get_method['page']-1) * $page_limit;

		$jsonObj= array();	

		$query="SELECT * FROM tbl_books
		LEFT JOIN tbl_category ON tbl_books.`cat_id`= tbl_category.`cid`
		LEFT JOIN tbl_author ON tbl_books.`aid` = tbl_author.`author_id`  
		WHERE tbl_books.`status` ='1' ORDER BY tbl_books.`book_views` DESC,tbl_books.`total_rate` DESC LIMIT $limit ,$page_limit";

		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		$j = $get_method['ads_param'];
		$is_book = $get_method['is_book'];

		$data_arr=array();

		while($data = mysqli_fetch_assoc($sql))
		{	
			$data_arr[]=$data;
		}

		$var_j=0;

		for ($i=0; $i < count($data_arr); $i++){ 

			if(NATIVE_ADS=='true'){
				if($is_book == 'list_book'){
					if($j % NATIVE_CAT_POSITION == 0){
						
						$row['is_ads'] = true;
						$row['native_ad_type'] = $settings_details['native_ad_type'];
						$row['native_ad_id'] = ($settings_details['native_ad_type']=='facebook') ? $settings_details['native_facebook_id'] : $settings_details['native_ad_id'];

						$row['id'] = "";
						$row['book_title'] = "";
						$row['book_description'] = "";
						$row['book_cover_img'] ="";
						$row['total_rate'] = "";
						$row['rate_avg'] = "";
						$row['book_views'] = "";
						
						$i--;
					}else{

						$row['is_ads'] = false;
						$row['native_ad_type'] = "";
						$row['native_ad_id'] = "";

						$row['id'] = $data_arr[$i]['id'];
						$row['book_title'] = stripslashes($data_arr[$i]['book_title']);
						$row['book_description'] = stripslashes($data_arr[$i]['book_description']);
						$row['book_cover_img'] = $file_path.'images/'.$data_arr[$i]['book_cover_img'];
						$row['total_rate'] = $data_arr[$i]['total_rate'];
						$row['rate_avg'] = $data_arr[$i]['rate_avg'];
						$row['book_views'] = $data_arr[$i]['book_views'];

						$row['author_name'] = $data_arr[$i]['author_name'];

						
					}
					$j++;
				}else{
					if($j % NATIVE_POSITION_GRID == 0){

						$row['is_ads'] = true;
						$row['native_ad_type'] = $settings_details['native_ad_type'];
						$row['native_ad_id'] = ($settings_details['native_ad_type']=='facebook') ? $settings_details['native_facebook_id'] : $settings_details['native_ad_id'];

						$row['id'] = "";
						$row['book_title'] = "";
						$row['book_description'] = "";
						$row['book_cover_img'] ="";
						$row['total_rate'] = "";
						$row['rate_avg'] = "";
						$row['book_views'] = "";

						$row['author_name'] = "";
						
						$i--;

					}else{
						$row['is_ads'] = false;
						$row['native_ad_type'] = "";
						$row['native_ad_id'] = "";

						$row['id'] = $data_arr[$i]['id'];
						$row['book_title'] = stripslashes($data_arr[$i]['book_title']);
						$row['book_description'] = stripslashes($data_arr[$i]['book_description']);
						$row['book_cover_img'] = $file_path.'images/'.$data_arr[$i]['book_cover_img'];
						$row['total_rate'] = $data_arr[$i]['total_rate'];
						$row['rate_avg'] = $data_arr[$i]['rate_avg'];
						$row['book_views'] = $data_arr[$i]['book_views'];

						$row['author_name'] = $data_arr[$i]['author_name'];
						
					}
					$j++;
				}
			}else{
				$row['is_ads'] = false;
				$row['native_ad_type'] = "";
				$row['native_ad_id'] = "";

				$row['id'] = $data_arr[$i]['id'];
				$row['book_title'] = stripslashes($data_arr[$i]['book_title']);
				$row['book_description'] = stripslashes($data_arr[$i]['book_description']);
				$row['book_cover_img'] = $file_path.'images/'.$data_arr[$i]['book_cover_img'];
				$row['total_rate'] = $data_arr[$i]['total_rate'];
				$row['rate_avg'] = $data_arr[$i]['rate_avg'];
				$row['book_views'] = $data_arr[$i]['book_views'];

				$row['author_name'] = $data_arr[$i]['author_name'];
					
			}
			
			array_push($jsonObj,$row);
			
		}
		$set['status'] = '1';
		$set['message'] = '';
		$set['ads_param'] = strval($j);
		$set['total_books'] = $total_pages['num'];
		$set['EBOOK_APP']=$jsonObj;

		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}	
	else if($get_method['method_name']=="get_search_books"){

		$jsonObj= array();	

		$search_keyword=trim($get_method['search_text']);

		$page_limit=API_PAGE_LIMIT;	

		$limit=($get_method['page']-1) * $page_limit;

		if(isset($get_method['category_id']) && $get_method['category_id']!=''){

			$query_rec = "SELECT COUNT(*) as num FROM tbl_books
			LEFT JOIN tbl_category ON tbl_books.`cat_id`= tbl_category.`cid`
			LEFT JOIN tbl_author ON tbl_books.`aid` = tbl_author.`author_id`
			WHERE tbl_books.`status`='1' AND  tbl_books.`cat_id` ='$get_method[category_id]' 
			AND tbl_books.`book_title` LIKE '%".$search_keyword."%' ORDER BY tbl_books.`book_title`";

			$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));

			$query="SELECT * FROM tbl_books
			LEFT JOIN tbl_category ON tbl_books.`cat_id`= tbl_category.`cid`
			LEFT JOIN tbl_author ON tbl_books.`aid` = tbl_author.`author_id` 
			WHERE tbl_books.`cat_id` ='$get_method[category_id]' AND tbl_books.`status`='1' AND tbl_books.`book_title` LIKE '%".$search_keyword."%'
			ORDER BY tbl_books.`book_title` DESC LIMIT $limit ,$page_limit";

		}else if(isset($get_method['sub_cat_id']) && $get_method['sub_cat_id']!=''){

			$query_rec = "SELECT COUNT(*) as num FROM tbl_books
			LEFT JOIN tbl_category ON tbl_books.`cat_id` = tbl_category.`cid`
			LEFT JOIN tbl_author ON tbl_books.`aid` = tbl_author.`author_id` 
			LEFT JOIN tbl_sub_category ON tbl_books.`sub_cat_id` = tbl_sub_category.`sid` 
			WHERE tbl_books.`sub_cat_id` ='$get_method[sub_cat_id]' AND tbl_books.`status`='1' AND tbl_books.`book_title` LIKE '%".$search_keyword."%'
			ORDER BY tbl_books.`book_title`";

			$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));

			$query="SELECT * FROM tbl_books
			LEFT JOIN tbl_category ON tbl_books.`cat_id` = tbl_category.`cid`
			LEFT JOIN tbl_author ON tbl_books.`aid` = tbl_author.`author_id` 
			LEFT JOIN tbl_sub_category ON tbl_books.`sub_cat_id` = tbl_sub_category.`sid` 
			WHERE tbl_books.`sub_cat_id` ='$get_method[sub_cat_id]' AND tbl_books.`status`='1' AND tbl_books.`book_title` LIKE '%".$search_keyword."%'
			ORDER BY tbl_books.`book_title` DESC LIMIT $limit ,$page_limit";
		}

		else if(isset($get_method['author_id']) && $get_method['author_id']!=''){

			$query_rec = "SELECT COUNT(*) as num FROM tbl_books
			LEFT JOIN tbl_category ON tbl_books.`cat_id` = tbl_category.`cid`
			LEFT JOIN tbl_author ON tbl_books.`aid` = tbl_author.`author_id` 
			WHERE tbl_books.`aid` ='$get_method[author_id]' AND tbl_books.`status`='1' AND tbl_books.`book_title` LIKE '%".$search_keyword."%'
			ORDER BY tbl_books.`book_title`";

			$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));

			$query="SELECT * FROM tbl_books
			LEFT JOIN tbl_category ON tbl_books.`cat_id` = tbl_category.`cid`
			LEFT JOIN tbl_author ON tbl_books.`aid` = tbl_author.`author_id` 
			WHERE tbl_books.`aid` ='$get_method[author_id]' AND tbl_books.`status`='1' AND tbl_books.`book_title` LIKE '%".$search_keyword."%'
			ORDER BY tbl_books.`book_title` DESC LIMIT $limit ,$page_limit";
		}else{

			$query_rec = "SELECT COUNT(*) as num FROM tbl_books
			LEFT JOIN tbl_category ON tbl_books.`cat_id` = tbl_category.`cid`
			LEFT JOIN tbl_author ON tbl_books.`aid` = tbl_author.`author_id` 
			WHERE tbl_books.`status`='1' AND tbl_books.`book_title` LIKE '%".$search_keyword."%' ORDER BY tbl_books.`book_title`";

			$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));

			$query="SELECT * FROM tbl_books
			LEFT JOIN tbl_category ON tbl_books.`cat_id` = tbl_category.`cid`
			LEFT JOIN tbl_author ON tbl_books.`aid`= tbl_author.`author_id` 
			WHERE  tbl_books.`book_title` LIKE '%".$search_keyword."%' AND tbl_books.`status`='1'
			ORDER BY tbl_books.`book_title` DESC LIMIT $limit ,$page_limit";
		}	

		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		$j = $get_method['ads_param'];
		$is_book = $get_method['is_book'];

		$data_arr=array();

		while($data = mysqli_fetch_assoc($sql))
		{	
			$data_arr[]=$data;
		}

		$var_j=0;

		for ($i=0; $i < count($data_arr); $i++){ 

			if(NATIVE_ADS=='true'){
				if($is_book == 'list_book'){
					if($j % NATIVE_CAT_POSITION == 0){
						
						$row['is_ads'] = true;
						$row['native_ad_type'] = $settings_details['native_ad_type'];
						$row['native_ad_id'] = ($settings_details['native_ad_type']=='facebook') ? $settings_details['native_facebook_id'] : $settings_details['native_ad_id'];
						
						$row['id'] ="";
						$row['book_title'] = "";
						$row['book_description'] = "";
						$row['book_cover_img'] = "";
						$row['total_rate'] ="";
						$row['rate_avg'] ="";
						$row['book_views'] ="";
						$row['author_name'] ="";
						
						$i--;
					}else{

						$row['is_ads'] = false;
						$row['native_ad_type'] = "";
						$row['native_ad_id'] = "";

						$row['id'] =$data_arr[$i]['id'];
						$row['book_title'] = stripslashes($data_arr[$i]['book_title']);
						$row['book_description'] = stripslashes($data_arr[$i]['book_description']);
						$row['book_cover_img'] = $file_path.'images/'.$data_arr[$i]['book_cover_img'];
						$row['total_rate'] =$data_arr[$i]['total_rate'];
						$row['rate_avg'] =$data_arr[$i]['rate_avg'];
						$row['book_views'] =$data_arr[$i]['book_views'];
						$row['author_name'] =$data_arr[$i]['author_name'];
						
					}
					$j++;
				}else{
					if($j % NATIVE_POSITION_GRID == 0){

						$row['is_ads'] = true;
						$row['native_ad_type'] = $settings_details['native_ad_type'];
						$row['native_ad_id'] = ($settings_details['native_ad_type']=='facebook') ? $settings_details['native_facebook_id'] : $settings_details['native_ad_id'];

						$row['id'] ="";
						$row['book_title'] = "";
						$row['book_description'] = "";
						$row['book_cover_img'] = "";
						$row['total_rate'] ="";
						$row['rate_avg'] ="";
						$row['book_views'] ="";
						$row['author_name'] ="";

						$i--;

					}else{
						$row['is_ads'] = false;
						$row['native_ad_type'] = "";
						$row['native_ad_id'] = "";

						$row['id'] =$data_arr[$i]['id'];
						$row['book_title'] = stripslashes($data_arr[$i]['book_title']);
						$row['book_description'] = stripslashes($data_arr[$i]['book_description']);
						$row['book_cover_img'] = $file_path.'images/'.$data_arr[$i]['book_cover_img'];
						$row['total_rate'] =$data_arr[$i]['total_rate'];
						$row['rate_avg'] =$data_arr[$i]['rate_avg'];
						$row['book_views'] =$data_arr[$i]['book_views'];
						$row['author_name'] =$data_arr[$i]['author_name'];
					}
					$j++;
				}
			}else{
				$row['is_ads'] = false;
				$row['native_ad_type'] = "";
				$row['native_ad_id'] = "";

				$row['id'] =$data_arr[$i]['id'];
				$row['book_title'] = stripslashes($data_arr[$i]['book_title']);
				$row['book_description'] = stripslashes($data_arr[$i]['book_description']);
				$row['book_cover_img'] = $file_path.'images/'.$data_arr[$i]['book_cover_img'];
				
				$row['total_rate'] =$data_arr[$i]['total_rate'];
				$row['rate_avg'] =$data_arr[$i]['rate_avg'];
				$row['book_views'] =$data_arr[$i]['book_views'];

				$row['author_name'] =$data_arr[$i]['author_name'];
				
			}
			
			array_push($jsonObj,$row);
			
		}
		$set['status'] = '1';
		$set['message'] = '';
		$set['ads_param'] = strval($j);
		$set['total_books'] = $total_pages['num'];
		$set['EBOOK_APP']=$jsonObj;

		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}		

	else if($get_method['method_name']=="get_single_book")
	{
		//$user_id=$get_method['user_id'];
		
		$jsonObj= array();
		
		$query="SELECT * FROM tbl_books 
		LEFT JOIN tbl_category ON tbl_books.`cat_id` = tbl_category.`cid`
		LEFT JOIN tbl_author ON tbl_books.`aid`= tbl_author.`author_id`  
		WHERE tbl_books.`id`='".$get_method['book_id']."'  AND tbl_books.`status`='1' ";

		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		if($sql->num_rows > 0)
		{

			while($data = mysqli_fetch_assoc($sql))
			{	
				$row['status']=1;
				$row['message']='';
				$row['total_comment'] = CountRow('tbl_comments',"book_id='".$get_method['book_id']."'");
				$row['id'] = $data['id'];
				$row['cat_id'] = $data['cat_id'];
				$row['sub_cat_id'] = $data['sub_cat_id'];
				$row['aid'] = $data['aid'];
				$row['featured'] = $data['featured'];
				$row['book_title'] = stripslashes($data['book_title']);
				$row['book_description'] = stripslashes($data['book_description']);
				$row['book_cover_img'] = $file_path.'images/'.$data['book_cover_img'];
				$row['book_bg_img'] = $file_path.'images/'.$data['book_bg_img'];

				$row['book_file_type'] = $data['book_file_type'];

				$book_file=$data['book_file_url'];

				if($data['book_file_type']=='local'){

					$book_file=$file_path.'uploads/'.basename($data['book_file_url']);
				}

				$row['book_file_url'] = $book_file;

				$row['total_rate'] = $data['total_rate'];
				$row['rate_avg'] = $data['rate_avg'];
				$row['book_views'] = $data['book_views'];

				$row['share_link'] = $file_path.'view_books.php?book_id='.$get_method['book_id'];

				$row['author_id'] = $data['author_id'];
				$row['author_name'] = $data['author_name'];
				$row['author_city_name'] = $data['author_city_name'];
				$row['author_description'] = $data['author_description'];
				$row['author_image'] = $file_path.'images/'.$data['author_image'];
				
				$row['cid'] = $data['cid'];
				$row['category_name'] = $data['category_name'];
				$row['cat_image'] = $file_path.'images/'.$data['category_image'];
				$row['cat_image_thumb'] = $file_path.'images/thumbs/'.$data['category_image'];
				
				$query1 = mysqli_query($mysqli,"SELECT * FROM tbl_favourite WHERE book_id='".$data['id']."' && user_id='".$get_method['user_id']."' "); 
				$num_rows1 = mysqli_num_rows($query1);
				
				if ($num_rows1 == 1)
				{
					$row['is_fav']="true";
				}
				else
				{
					$row['is_fav']="false";
				}

			//Related book
				$qry2="SELECT * FROM tbl_books 
				LEFT JOIN tbl_author ON tbl_books.`aid` = tbl_author.`author_id`  
				LEFT JOIN tbl_category ON tbl_books.`cat_id` = tbl_category.`cid`
				WHERE id!='".$get_method['book_id']."' AND (`cat_id` ='".$data['cat_id']."' OR `aid` ='".$data['aid']."' OR `sub_cat_id` ='".$data['sub_cat_id']."') AND tbl_books.`status`='1' AND tbl_category.`status`='1' AND tbl_author.`status`='1' ORDER BY tbl_books.`id` DESC LIMIT 5";

				$result2=mysqli_query($mysqli,$qry2); 

				if($result2->num_rows > 0)
				{
					while ($related_books=mysqli_fetch_array($result2)) {
						
						$row2['id'] = $related_books['id'];
						$row['sub_cat_id'] = $related_books['sub_cat_id'];
						$row2['book_title'] = stripslashes($related_books['book_title']);
						$row2['book_cover_img'] = $file_path.'images/'.$related_books['book_cover_img'];

						$row2['total_rate'] = $related_books['total_rate'];
						$row2['rate_avg'] = $related_books['rate_avg'];
						$row2['book_views'] = $related_books['book_views'];

						$row2['author_name'] = $related_books['author_name'];
		
						$row['related_books'][]= $row2;
					}
					
				}
				else
				{	
					
					$row['related_books']= array();
				}

			//Comments
				$qry3="SELECT tbl_comments.*, tbl_users.`user_profile` FROM tbl_comments
				LEFT JOIN tbl_users
				ON tbl_comments.`user_id`=tbl_users.`id` 
				WHERE book_id='".$get_method['book_id']."' ORDER BY id DESC LIMIT 2";
				$result3=mysqli_query($mysqli,$qry3); 

				if($result3->num_rows > 0)
				{
					while ($row_comments=mysqli_fetch_array($result3)) {

						$row3['comment_id'] = $row_comments['id'];
						$row3['user_id'] = $row_comments['user_id'];
						$row3['book_id'] = $row_comments['book_id'];
						$row3['user_name'] = $row_comments['user_name'];
						$row3['comment_text'] = $row_comments['comment_text'];
						$row3['user_profile'] = $file_path.'images/'.$row_comments['user_profile'];
						$row3['comment_date'] = calculate_time_span($row_comments['comment_on']);

						$row['user_comments'][]= $row3;
					}
				}
				else
				{	
					
					$row['user_comments']=array();
				}

			}
		}
		else{
			$row['status']=-1;
			$row['message']='Book is not found!..';
		}
		$view_qry=mysqli_query($mysqli,"UPDATE tbl_books SET book_views = book_views + 1 WHERE id = '".$get_method['book_id']."'");

		$set = $row;
		
		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();	
	}
	else if($get_method['method_name']=="related_books"){

		$book_id= $get_method['book_id'];
		$cat_id= $get_method['cat_id'];
		$sub_cat_id= $get_method['sub_cat_id'];

		$query_rec ="SELECT COUNT(*) as num FROM tbl_books
		LEFT JOIN tbl_category ON tbl_books.`cat_id` = tbl_category.`cid`
		LEFT JOIN tbl_author ON tbl_books.`aid`= tbl_author.`author_id` 
		WHERE id!='$book_id' AND (tbl_books.`cat_id` ='$cat_id' OR tbl_books.`sub_cat_id` ='$sub_cat_id' OR tbl_books.`aid` ='$aid') AND tbl_books.`status`='1' AND tbl_author.`status`='1' AND tbl_category.`status`='1'";

		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));
		
		$page_limit=API_PAGE_LIMIT;	

		$limit=($get_method['page']-1) * $page_limit;

		$post_order_by=API_CAT_POST_ORDER_BY; 

		$jsonObj= array();

		$query="SELECT * FROM tbl_books 
		LEFT JOIN tbl_category ON tbl_books.`cat_id` = tbl_category.`cid`
		LEFT JOIN tbl_author ON tbl_books.`aid` = tbl_author.`author_id`  
		WHERE id!='$book_id' AND (tbl_books.`cat_id` ='$cat_id' OR tbl_books.`sub_cat_id` ='$sub_cat_id' OR tbl_books.`aid` ='$aid') AND tbl_books.`status`='1' AND tbl_author.`status`='1'  AND tbl_category.`status`='1' ORDER BY tbl_books.`id` ".$post_order_by." LIMIT $limit, $page_limit";				

		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		$j = $get_method['ads_param'];
		$is_book = $get_method['is_book'];

		$data_arr=array();

		while($data = mysqli_fetch_assoc($sql))
		{	
			$data_arr[]=$data;
		}

		$var_j=0;

		for ($i=0; $i < count($data_arr); $i++){ 

			if(NATIVE_ADS=='true'){
				if($is_book == 'list_book'){
					if($j % NATIVE_CAT_POSITION == 0){
						
						$row['is_ads'] = true;
						$row['native_ad_type'] = $settings_details['native_ad_type'];
						$row['native_ad_id'] = ($settings_details['native_ad_type']=='facebook') ? $settings_details['native_facebook_id'] : $settings_details['native_ad_id'];

						$row['id'] = "";
						$row['book_title'] = "";
						$row['book_description'] = "";
						$row['book_cover_img'] ="";
						
						$row['total_rate'] = "";
						$row['rate_avg'] = "";
						$row['book_views'] = "";

						$row['author_name'] = "";
						
						$i--;
					}else{

						$row['is_ads'] = false;
						$row['native_ad_type'] = "";
						$row['native_ad_id'] = "";

						$row['id'] = $data_arr[$i]['id'];
						$row['book_title'] = stripslashes($data_arr[$i]['book_title']);
						$row['book_description'] = stripslashes($data_arr[$i]['book_description']);
						$row['book_cover_img'] = $file_path.'images/'.$data_arr[$i]['book_cover_img'];
						$row['total_rate'] = $data_arr[$i]['total_rate'];
						$row['rate_avg'] = $data_arr[$i]['rate_avg'];
						$row['book_views'] = $data_arr[$i]['book_views'];

						$row['author_name'] = $data_arr[$i]['author_name'];
						
					}
					$j++;
				}else{
					if($j % NATIVE_POSITION_GRID == 0){

						$row['is_ads'] = true;
						$row['native_ad_type'] = $settings_details['native_ad_type'];
						$row['native_ad_id'] = ($settings_details['native_ad_type']=='facebook') ? $settings_details['native_facebook_id'] : $settings_details['native_ad_id'];

						$row['id'] = "";
						$row['book_title'] = "";
						$row['book_description'] = "";
						$row['book_cover_img'] ="";
						$row['total_rate'] = "";
						$row['rate_avg'] = "";
						$row['book_views'] = "";

						$row['author_name'] = "";

						$i--;

					}else{
						$row['is_ads'] = false;
						$row['native_ad_type'] = "";
						$row['native_ad_id'] = "";

						$row['id'] = $data_arr[$i]['id'];
						$row['book_title'] = stripslashes($data_arr[$i]['book_title']);
						$row['book_description'] = $data_arr[$i]['book_description'];
						$row['book_cover_img'] = $file_path.'images/'.$data_arr[$i]['book_cover_img'];
						$row['total_rate'] = $data_arr[$i]['total_rate'];
						$row['rate_avg'] = $data_arr[$i]['rate_avg'];
						$row['book_views'] = $data_arr[$i]['book_views'];
						
						$row['author_name'] = $data_arr[$i]['author_name'];
						
						
					}
					$j++;
				}
			}else{
				$row['is_ads'] = false;
				$row['native_ad_type'] = "";
				$row['native_ad_id'] = "";

				$row['id'] = $data_arr[$i]['id'];
				$row['book_title'] = stripslashes($data_arr[$i]['book_title']);
				$row['book_description'] = $data_arr[$i]['book_description'];
				$row['book_cover_img'] = $file_path.'images/'.$data_arr[$i]['book_cover_img'];
				$row['total_rate'] = $data_arr[$i]['total_rate'];
				$row['rate_avg'] = $data_arr[$i]['rate_avg'];
				$row['book_views'] = $data_arr[$i]['book_views'];
				
				$row['author_name'] = $data_arr[$i]['author_name'];
				
			}
			
			array_push($jsonObj,$row);
			
		}
		$set['status'] = '1';
		$set['message'] = '';
		$set['ads_param'] = strval($j);
		$set['total_books'] = $total_pages['num'];
		$set['EBOOK_APP']=$jsonObj;

		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}		
	else if($get_method['method_name']=="user_continue_book")
	{
		$user_id =$get_method['user_id'];
		$book_id =$get_method['book_id'];

		$sql="SELECT * FROM tbl_user_continue WHERE `user_id`='$user_id' AND `book_id`='$book_id'";
		$res=mysqli_query($mysqli, $sql);

		if($res->num_rows == 0){
			// add to favourite list

			$data = array( 
				'book_id'  =>  $book_id,
				'user_id'  =>  $user_id,
				'user_con_date'  =>  strtotime(date('d-m-Y h:i:s A'))
			);      

			$qry = Insert('tbl_user_continue',$data);

			$last_id = mysqli_insert_id($mysqli);

			$set = array('status' => '1','message' => '','msg'=>$app_lang['add_continue'],'success'=>1);

		}
		else{
			$user_con_date=strtotime(date('d-m-Y h:i:s A'));

			$sql="UPDATE tbl_user_continue SET user_con_date  = '$user_con_date' WHERE `user_id`='$user_id' AND `book_id`='$book_id'";

			mysqli_query($mysqli,$sql);
			
			$set = array('status' => '1','message' => '','msg'=>'Book continue update successfully','success'=>1);
		}
		
		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();

	}
	else if($get_method['method_name']=="get_continue_book"){

		$user_id=$get_method['user_id'];

		$page_limit=API_PAGE_LIMIT;			
		$limit=($get_method['page']-1) * $page_limit;

		$jsonObj= array();

		$query_rec = "SELECT COUNT(*) as num FROM tbl_user_continue
		WHERE tbl_user_continue.`user_id`='".$user_id."'";

		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));
		
		$query="SELECT * FROM tbl_books
		LEFT JOIN tbl_user_continue ON tbl_books.`id`= tbl_user_continue.`book_id` 
		LEFT JOIN tbl_category ON tbl_books.`cat_id`= tbl_category.`cid`
		LEFT JOIN tbl_author ON tbl_books.`aid` = tbl_author.`author_id`  
		WHERE tbl_books.`status`='1'  AND tbl_user_continue.`user_id`='$user_id' ORDER BY tbl_user_continue.`con_id` DESC LIMIT $limit, $page_limit";	
		
		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		$j = $get_method['ads_param'];
		$is_book = $get_method['is_book'];

		$data_arr=array();

		while($data = mysqli_fetch_assoc($sql))
		{	
			$data_arr[]=$data;
		}

		$var_j=0;

		for ($i=0; $i < count($data_arr); $i++){ 

			if(NATIVE_ADS=='true'){
				if($is_book == 'list_book'){
					if($j % NATIVE_CAT_POSITION == 0){
						
						$row['is_ads'] = true;
						$row['native_ad_type'] = $settings_details['native_ad_type'];
						$row['native_ad_id'] = ($settings_details['native_ad_type']=='facebook') ? $settings_details['native_facebook_id'] : $settings_details['native_ad_id'];
						
						$row['id'] = "";
						$row['book_title'] = "";
						$row['book_description'] = "";
						$row['book_cover_img'] ="";
						$row['total_rate'] = "";
						$row['rate_avg'] = "";
						$row['book_views'] = "";
						$row['author_name'] = "";
						
						$i--;
					}else{

						$row['is_ads'] = false;
						$row['native_ad_type'] = "";
						$row['native_ad_id'] = "";

						$row['id'] = $data_arr[$i]['id'];
						
						$row['book_title'] = stripslashes($data_arr[$i]['book_title']);
						$row['book_description'] = stripslashes($data_arr[$i]['book_description']);
						$row['book_cover_img'] = $file_path.'images/'.$data_arr[$i]['book_cover_img'];
						$row['total_rate'] = $data_arr[$i]['total_rate'];
						$row['rate_avg'] = $data_arr[$i]['rate_avg'];
						$row['book_views'] = $data_arr[$i]['book_views'];

						$row['author_name'] = $data_arr[$i]['author_name'];
						
					}
					$j++;
				}else{
					if($j % NATIVE_POSITION_GRID == 0){

						$row['is_ads'] = true;
						$row['native_ad_type'] = $settings_details['native_ad_type'];
						$row['native_ad_id'] = ($settings_details['native_ad_type']=='facebook') ? $settings_details['native_facebook_id'] : $settings_details['native_ad_id'];

						$row['id'] = "";
						$row['book_title'] = "";
						$row['book_description'] = "";
						$row['book_cover_img'] ="";
						$row['total_rate'] = "";
						$row['rate_avg'] = "";
						$row['book_views'] = "";
						$row['author_name'] = "";
						
						$row['is_favourite']="";

						$i--;

					}else{
						$row['is_ads'] = false;
						$row['native_ad_type'] = "";
						$row['native_ad_id'] = "";

						$row['id'] = $data_arr[$i]['id'];
						$row['book_title'] = stripslashes($data_arr[$i]['book_title']);
						$row['book_description'] = $data_arr[$i]['book_description'];
						$row['book_cover_img'] = $file_path.'images/'.$data_arr[$i]['book_cover_img'];
						$row['total_rate'] = $data_arr[$i]['total_rate'];
						$row['rate_avg'] = $data_arr[$i]['rate_avg'];
						$row['book_views'] = $data_arr[$i]['book_views'];
						$row['author_name'] = $data_arr[$i]['author_name'];

						
					}
					$j++;
				}
			}else{
				$row['is_ads'] = false;
				$row['native_ad_type'] = "";
				$row['native_ad_id'] = "";

				$row['id'] = $data_arr[$i]['id'];
				$row['book_title'] = stripslashes($data_arr[$i]['book_title']);
				$row['book_description'] = $data_arr[$i]['book_description'];
				$row['book_cover_img'] = $file_path.'images/'.$data_arr[$i]['book_cover_img'];
				
				$row['total_rate'] = $data_arr[$i]['total_rate'];
				$row['rate_avg'] = $data_arr[$i]['rate_avg'];
				$row['book_views'] = $data_arr[$i]['book_views'];

				$row['author_name'] = $data_arr[$i]['author_name'];
				
			}
			
			array_push($jsonObj,$row);
			
		}
		$set['status'] = '1';
		$set['message'] = '';
		$set['ads_param'] = strval($j);
		$set['total_books'] = $total_pages['num'];
		$set['EBOOK_APP']=$jsonObj;

		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}		
	else if($get_method['method_name']=="book_favourite")
	{
		$user_id =$get_method['user_id'];
		$book_id =$get_method['book_id'];
		
		if(get_status_check($book_id))
		{

			$sql="SELECT * FROM tbl_favourite WHERE `user_id`='$user_id' AND `book_id`='$book_id'";
			$res=mysqli_query($mysqli, $sql);

			if($res->num_rows == 0){
			// add to favourite list

				$data = array( 
					'book_id'  =>  $book_id,
					'user_id'  =>  $user_id,
					'created_at'  =>  strtotime(date('d-m-Y h:i:s A'))
				);      

				$qry = Insert('tbl_favourite',$data);

				$set = array('status' => '1','message' => '','msg'=>$app_lang['add_favourite'],'success'=>1,'is_favourite'=>true);

			}

			else{
			// remove to favourite list

				$deleteSql="DELETE FROM tbl_favourite WHERE `user_id`='$user_id' AND `book_id`='$book_id'";

				if(mysqli_query($mysqli, $deleteSql)){
					$set = array('status' => '1','message' => '','msg'=>$app_lang['remove_favourite'],'success'=>1,'is_favourite'=>false);
				}
				else{

					$set = array('status' => '1','message' => '','msg'=>$app_lang['error_msg'],'success'=>0,'is_favourite'=>false);
				}
			}
		}
		else{
			$set = array('status' => '-1','message' => 'Book is not found');
		}	
		
		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}
	else if($get_method['method_name']=="get_favourite_book"){

		$user_id=$get_method['user_id'];

		$page_limit=API_PAGE_LIMIT;			
		$limit=($get_method['page']-1) * $page_limit;

		$jsonObj= array();

		$query_rec = "SELECT COUNT(*) as num FROM tbl_favourite
		WHERE tbl_favourite.`user_id`='".$user_id."'";

		$total_pages = mysqli_fetch_array(mysqli_query($mysqli,$query_rec));
		
		$query="SELECT * FROM tbl_books
		LEFT JOIN tbl_favourite ON tbl_books.`id`= tbl_favourite.`book_id` 
		LEFT JOIN tbl_category ON tbl_books.`cat_id`= tbl_category.`cid`
		LEFT JOIN tbl_author ON tbl_books.`aid` = tbl_author.`author_id`  
		WHERE tbl_books.`status`='1'  AND tbl_favourite.`user_id`='$user_id' ORDER BY tbl_favourite.`fa_id` DESC LIMIT $limit, $page_limit";				

		$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

		$j = $get_method['ads_param'];
		$is_book = $get_method['is_book'];

		$data_arr=array();

		while($data = mysqli_fetch_assoc($sql))
		{	
			$data_arr[]=$data;
		}

		$var_j=0;

		for ($i=0; $i < count($data_arr); $i++){ 

			if(NATIVE_ADS=='true'){
				if($is_book == 'list_book'){
					if($j % NATIVE_CAT_POSITION == 0){
						
						$row['is_ads'] = true;
						$row['id'] = "";
						$row['native_ad_type'] = $settings_details['native_ad_type'];
						$row['native_ad_id'] = ($settings_details['native_ad_type']=='facebook') ? $settings_details['native_facebook_id'] : $settings_details['native_ad_id'];
						
						$row['book_title'] = "";
						$row['book_description'] = "";
						$row['book_cover_img'] ="";
						
						$row['total_rate'] = "";
						$row['rate_avg'] = "";
						$row['book_views'] = "";

						$row['author_name'] = "";

						$row['is_favourite']="";
						
						
						
						$i--;
					}else{

						$row['is_ads'] = false;
						$row['id'] = $data_arr[$i]['id'];
						$row['native_ad_type'] = "";
						$row['native_ad_id'] = "";
						
						$row['book_title'] = stripslashes($data_arr[$i]['book_title']);
						$row['book_description'] = stripslashes($data_arr[$i]['book_description']);
						$row['book_cover_img'] = $file_path.'images/'.$data_arr[$i]['book_cover_img'];
						
						$row['total_rate'] = $data_arr[$i]['total_rate'];
						$row['rate_avg'] = $data_arr[$i]['rate_avg'];
						$row['book_views'] = $data_arr[$i]['book_views'];

						$row['author_name'] = $data_arr[$i]['author_name'];
						
						$row['is_favourite']=get_favourite_info($data_arr[$i]['id'],$get_method['user_id']);
						
					}
					$j++;
				}else{
					if($j % NATIVE_POSITION_GRID == 0){

						$row['is_ads'] = true;
						$row['id'] = "";
						$row['native_ad_type'] = $settings_details['native_ad_type'];
						$row['native_ad_id'] = ($settings_details['native_ad_type']=='facebook') ? $settings_details['native_facebook_id'] : $settings_details['native_ad_id'];

						$row['book_title'] = "";
						$row['book_description'] = "";
						$row['book_cover_img'] ="";
						
						$row['total_rate'] = "";
						$row['rate_avg'] = "";
						$row['book_views'] = "";

						$row['author_name'] = "";
						
						$row['is_favourite']="";
						
						$i--;

					}else{
						$row['is_ads'] = false;
						$row['id'] = $data_arr[$i]['id'];
						$row['native_ad_type'] = "";
						$row['native_ad_id'] = "";
						
						$row['book_title'] = stripslashes($data_arr[$i]['book_title']);
						$row['book_description'] = $data_arr[$i]['book_description'];
						$row['book_cover_img'] = $file_path.'images/'.$data_arr[$i]['book_cover_img'];
						
						$row['total_rate'] = $data_arr[$i]['total_rate'];
						$row['rate_avg'] = $data_arr[$i]['rate_avg'];
						$row['book_views'] = $data_arr[$i]['book_views'];

						$row['author_name'] = $data_arr[$i]['author_name'];
						
						$row['is_favourite']=get_favourite_info($data_arr[$i]['id'],$get_method['user_id']);
						
					}
					$j++;
				}
			}else{
				$row['is_ads'] = false;
				$row['id'] = $data_arr[$i]['id'];
				$row['native_ad_type'] = "";
				$row['native_ad_id'] = "";
				
				$row['book_title'] = stripslashes($data_arr[$i]['book_title']);
				$row['book_description'] = $data_arr[$i]['book_description'];
				$row['book_cover_img'] = $file_path.'images/'.$data_arr[$i]['book_cover_img'];
				
				$row['total_rate'] = $data_arr[$i]['total_rate'];
				$row['rate_avg'] = $data_arr[$i]['rate_avg'];
				$row['book_views'] = $data_arr[$i]['book_views'];

				$row['author_name'] = $data_arr[$i]['author_name'];
				
				$row['is_favourite']=get_favourite_info($data_arr[$i]['id'],$get_method['user_id']);
			}
			
			array_push($jsonObj,$row);
			
		}
		$set['status'] = '1';
		$set['message'] = '';
		$set['ads_param'] = strval($j);
		$set['total_books'] = $total_pages['num'];
		$set['EBOOK_APP']=$jsonObj;

		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}		

	else if($get_method['method_name']=="book_report")
	{		
		$report=$get_method['report'];
		$book_id=$get_method['book_id'];

		if(get_status_check($book_id)){

			if($report!='')
			{

				$sql="SELECT * FROM tbl_reports WHERE `user_id`='".$get_method['user_id']."' AND `book_id`='".$book_id."'";

				$res=mysqli_query($mysqli, $sql);

				if(mysqli_num_rows($res) > 0){

					$updateSql="UPDATE `tbl_reports` SET `report`='".$report."',`report_on`='".strtotime(date('d-m-Y h:i:s A'))."' WHERE `user_id`='".$get_method['user_id']."' AND `book_id`='".$book_id."'";

					mysqli_query($mysqli, $updateSql);

				}
				else{
					
					$data = array(
						'user_id'=> $get_method['user_id'],	
						'book_id'  => $book_id,				    
						'report'  =>  $report,
						'report_on' => strtotime(date('d-m-Y h:i:s A'))
					);		
					
					$qry = Insert('tbl_reports',$data); 	

				}
				
				$set = array('status' => '1','message' => '','msg' => $app_lang['report_success'],'success'=>'1');
			}
			else
			{
				$set = array('status' => '1','message' => '','msg' => $app_lang['report_fail'],'success'=>'0');
			}
		} 
		else{

			$set = array('status' => '-1','message' => 'Book is not found');

		}
		
		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}
	else if($get_method['method_name']=="get_rating")
	{

		$user_id=$get_method['user_id'];	
		$book_id=$get_method['book_id'];	

		$jsonObj= array();	

		if(get_status_check($book_id)){

			$query="SELECT * FROM tbl_rating
			WHERE tbl_rating.`user_id`='$user_id' AND tbl_rating.`book_id`='$book_id' ORDER BY tbl_rating.`id` DESC";		 
			
			$res = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

			if(mysqli_num_rows($res) > 0){

				$usr_rate=mysqli_fetch_assoc($res);
				$jsonObj = array('status' => '1','message' => '','user_rate' => $usr_rate['rate'],'success'=>"1");	
				
			}else{

				$jsonObj = array('status' => '1','message' => '','user_rate' => "0",'success'=>"1");
			}
		}		
		else{

			$jsonObj = array('status' => '-1','message' => 'Book is not found');

		}
		$set= $jsonObj;
		
		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
		
	}	
	else if($get_method['method_name']=="get_report"){

		$user_id=$get_method['user_id'];	
		$book_id=$get_method['book_id'];	

		$jsonObj= array();	

		if(get_status_check($book_id)){

			$query="SELECT * FROM tbl_reports
			WHERE `user_id`='$user_id' AND `book_id`='$book_id' ORDER BY tbl_reports.`id` DESC";
			
			$sql = mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));
			$num_rows = mysqli_num_rows($sql);

			if($num_rows > 0)
			{
				while($data = mysqli_fetch_assoc($sql))
				{	
					$set['status']=1;
					$set['message']='';
					$set['id'] = $data['id'];
					$set['email'] = $data['email'];
					$set['report'] = $data['report'];
					$set['report_on'] = date('d M Y',$data['report_on']);
					
				}
			}
			else{
				
				$set = array('status' => '1','message' => '','id' =>'','email'=>'','report' => '' ,'report_on' => '');
			}
		}		
		else{

			$set = array('status' => '-1','message' => 'Book is not found');

		}

		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
		
	}	
	else if($get_method['method_name']=="user_comment"){		

		$comment_text = addslashes(trim($get_method['comment_text']));
		$book_id=$get_method['book_id'];

		if(get_status_check($book_id)){

			$qry = "SELECT * FROM tbl_users WHERE id = '".$get_method['user_id']."'"; 
			$result = mysqli_query($mysqli,$qry);
			$row = mysqli_fetch_assoc($result);

			$data = array(
				'book_id'  => $book_id,
				'user_id'  => $get_method['user_id'],
				'user_name'  => $row['name'],				    
				'user_email'  =>  $row['email'],
				'comment_text'  =>  $get_method['comment_text'],
				'comment_on'  =>  strtotime(date('d-m-Y h:i:s A'))
			);		
			
			$qry = Insert('tbl_comments',$data);									 
			
			$last_id = mysqli_insert_id($mysqli);

			if($last_id > 0)
			{	
				$set['status']=1;
				$set['message']='';
				$set['success']=1;
				$set['msg']=$app_lang['comment_success'];

				$sql="SELECT * FROM tbl_comments where id='".$last_id."'";
				$res=mysqli_query($mysqli, $sql);
				$row_comments=mysqli_fetch_assoc($res);
				$set['total_comment'] = strval(CountRow("tbl_comments","book_id='".$book_id."'"));			 
				$set['comment_id'] = $row_comments['id'];			 
				$set['user_id'] = $row_comments['user_id'];			 
				$set['user_name'] = get_user_info($row_comments['user_id'],'name') ? get_user_info($row_comments['user_id'],'name'): $row_comments['user_name'];			 

				if(get_user_info($row_comments['user_id'],'user_profile')!='')
				{
					$set['user_profile'] = $file_path.'images/'.get_user_info($row_comments['user_id'],'user_profile');
				}	
				else
				{
					$set['user_profile'] ='';
				}
				$set['book_id'] = $row_comments['book_id'];
				$set['comment_text'] = $row_comments['comment_text'];
				$set['comment_date'] = calculate_time_span($row_comments['comment_on']);
			}
			else{
				$set['status']=1;
				$set['message']='';
				$set['success']=0;
				$set['msg']=$app_lang['comment_fail'];
			}
		}		
		else{

			$set = array('status' => '-1','message' => 'Book is not found');

		}	
		
		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}
	else if($get_method['method_name']=="get_all_comments"){
		$jsonObj= array();

		$book_id=$get_method['book_id'];
		$user_id=$get_method['user_id'];

		$page_limit=API_PAGE_LIMIT;

		$limit=($get_method['page']-1) * $page_limit;

		$sql="SELECT * FROM tbl_comments WHERE tbl_comments.`book_id`='$book_id' 
		AND tbl_comments.`user_id` = '$user_id' ORDER BY tbl_comments.`id`
		DESC LIMIT $limit, $page_limit";

		$res=mysqli_query($mysqli,$sql);

		if(mysqli_num_rows($res) > 0){
			while ($row=mysqli_fetch_assoc($res)) {

				$info['total_comment'] = CountRow('tbl_comments',"book_id='$book_id'");;			 
				$info['comment_id'] = $row['id'];			 
				$info['user_id'] = $row['user_id'];			 
				$info['user_name'] = get_user_info($row['user_id'],'name') ? get_user_info($row['user_id'],'name'): $row['user_name'];			 

				if(get_user_info($row['user_id'],'user_profile')!='')
				{
					$info['user_profile'] = $file_path.'images/'.get_user_info($row['user_id'],'user_profile');
				}	
				else
				{
					$info['user_profile'] ='';
				}

				$info['book_id'] = $row['book_id'];
				$info['comment_text'] = $row['comment_text'];
				$info['comment_date'] = calculate_time_span($row['comment_on']);

				array_push($jsonObj,$info);
			}
		}

		mysqli_free_result($res);

		$sql="SELECT * FROM tbl_comments WHERE tbl_comments.`book_id`='$book_id' 
		AND tbl_comments.`user_id` <> '$user_id' ORDER BY tbl_comments.`id` 
		DESC LIMIT $limit, $page_limit";

		$res=mysqli_query($mysqli,$sql);

		if(mysqli_num_rows($res) > 0){
			while ($row=mysqli_fetch_assoc($res)) {

				$info['total_comment'] = CountRow('tbl_comments',"book_id='$book_id'");;			 
				$info['comment_id'] = $row['id'];			 
				$info['user_id'] = $row['user_id'];			 
				$info['user_name'] = get_user_info($row['user_id'],'name') ? get_user_info($row['user_id'],'name'): $row['user_name'];			 

				if(get_user_info($row['user_id'],'user_profile')!='')
				{
					$info['user_profile'] = $file_path.'images/'.get_user_info($row['user_id'],'user_profile');
				}	
				else
				{
					$info['user_profile'] ='';
				}

				$info['book_id'] = $row['book_id'];
				$info['comment_text'] = $row['comment_text'];
				$info['comment_date'] = calculate_time_span($row['comment_on']);

				array_push($jsonObj,$info);
			}
		}

		$info2['status']=1;
		$info2['message']='';
		$info2['all_comments']=$jsonObj;
		//$set = $jsonObj;
		
		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($info2,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();

	}
	else if($get_method['method_name']=="delete_comment"){
		$jsonObj= array();	

		$comment_id=$get_method['comment_id'];
		$book_id=$get_method['book_id'];

		$set['status']=1;
		$set['message']='';
		
		$sql="SELECT * FROM tbl_comments WHERE tbl_comments.`book_id`='$book_id' ORDER BY tbl_comments.`id` DESC LIMIT 1 OFFSET 2";

		$res=mysqli_query($mysqli,$sql);

		Delete('tbl_comments','id='.$comment_id);

		if(mysqli_num_rows($res) > 0){

			$set['comment_status'] = "1";
			$set['status']=1;
			$set['message']='';
			$set['success']="1";	

			$set['msg']=$app_lang['comment_delete'];

			$row=mysqli_fetch_assoc($res);

			$set['total_comment'] = CountRow('tbl_comments',"book_id='$book_id'");
			$set['comment_id'] = $row['id'];			 
			$set['user_id'] = $row['user_id'];			 
			$set['user_name'] = get_user_info($row['user_id'],'name') ? get_user_info($row['user_id'],'name') : $row['user_name'];			 

			if(get_user_info($row['user_id'],'user_profile')!='')
			{
				$set['user_profile'] = $file_path.'images/'.get_user_info($row['user_id'],'user_profile');
			}	
			else
			{
				$set['user_profile'] ='';
			}

			$set['book_id'] = $row['book_id'];
			$set['comment_text'] = $row['comment_text'];
			$set['comment_date'] = calculate_time_span($row['comment_on']);
		}
		else{
			$set['success']="1";	
			$set['msg']=$app_lang['comment_delete'];
			$set['comment_status'] = "0";
			$set['total_comment'] = CountRow('tbl_comments',"book_id='$book_id'");		 
		}
		
		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE));
		die();

	}
	else if($get_method['method_name']=="book_rating"){
		
      //search if the user(ip) has already gave a note
		$book_id = $get_method['book_id'];
		$user_id = $get_method['user_id'];
		$therate = $get_method['rate'];
		$rate_on=date("Y-m-d h:i:s A");

		if(get_status_check($book_id)){

			$query1 = mysqli_query($mysqli,"SELECT * FROM tbl_rating WHERE book_id  = '$book_id' && user_id = '$user_id' "); 

			while($data1 = mysqli_fetch_assoc($query1)){
				$rate_db1[] = $data1;
			}
			if(@count($rate_db1) == 0){
				
				$data = array(            
					'book_id'  =>$book_id,
					'user_id'  =>$user_id,
					'rate'  =>  $therate,
					'dt_rate'  => $rate_on,
				);  
				$qry = Insert('tbl_rating',$data); 
	          //Total rate result
				
				$query = mysqli_query($mysqli,"SELECT * FROM tbl_rating WHERE book_id  = '$book_id' ");
				
				while($data = mysqli_fetch_assoc($query)){
					$rate_db[] = $data;
					$sum_rates[] = $data['rate'];
					
				}
				
				if(@count($rate_db)){
					$rate_times = count($rate_db);
					$sum_rates = array_sum($sum_rates);
					$rate_value = $sum_rates/$rate_times;
					$rate_bg = (($rate_value)/5)*100;
				}else{
					$rate_times = 0;
					$rate_value = 0;
					$rate_bg = 0;
				}
				
				$rate_avg=round($rate_value); 
				
				$sql="update tbl_books set total_rate=total_rate + 1,rate_avg='$rate_avg' WHERE id='".$book_id."'";
				mysqli_query($mysqli,$sql);
				
				$total_rat_sql="SELECT * FROM tbl_books WHERE id='".$book_id."'";
				$total_rat_res=mysqli_query($mysqli,$total_rat_sql);
				$total_rat_row=mysqli_fetch_assoc($total_rat_res);
				
				$set =array('status' => '1','message' => '','msg' => $app_lang['book_rating'],'success'=>'1','total_rate'=>$total_rat_row['total_rate'],'rate_avg'=>$total_rat_row['rate_avg']);
				
			}else{  

				$sql="update tbl_rating set rate='$therate',dt_rate='$rate_on' WHERE user_id='".$user_id."' AND book_id='$book_id'";

				mysqli_query($mysqli,$sql);

          //Total rate result
				
				$query = mysqli_query($mysqli,"SELECT * FROM tbl_rating WHERE book_id  = '$book_id' ");
				
				while($data = mysqli_fetch_assoc($query)){
					$rate_db[] = $data;
					$sum_rates[] = $data['rate'];
					
				}

				if(@count($rate_db)){
					$rate_times = count($rate_db);
					$sum_rates = array_sum($sum_rates);
					$rate_value = $sum_rates/$rate_times;
					$rate_bg = (($rate_value)/5)*100;
				}else{
					$rate_times = 0;
					$rate_value = 0;
					$rate_bg = 0;
				}
				
				$rate_avg=round($rate_value); 
				
				$sql="update tbl_books set rate_avg='$rate_avg' WHERE id='".$book_id."'";
				mysqli_query($mysqli,$sql);
				
				$total_rat_sql="SELECT * FROM tbl_books WHERE id='".$book_id."'";
				$total_rat_res=mysqli_query($mysqli,$total_rat_sql);
				$total_rat_row=mysqli_fetch_assoc($total_rat_res);
				
				$set=array('status' => '1','message' => '','msg' => $app_lang['book_rating'],'success'=>'1','total_rate'=>$total_rat_row['total_rate'],'rate_avg'=>$total_rat_row['rate_avg']);
			}
		}		
		else{

			$set = array('status' => '-1','message' => 'Book is not found');

		}
		header( 'Content-Type: application/json; charset=utf-8' );
		echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		die();
	}
else if($get_method['method_name']=="user_register"){

	$user_type=trim($get_method['type']); //Google, Normal, Facebook
	$device_id=trim($get_method['device_id']); //Google, Normal, Facebook
	$email=cleanInput($get_method['email']);
	$auth_id=cleanInput($get_method['auth_id']);

	$registration_on=strtotime(date('d-m-Y h:i A'));

	$to = $get_method['email'];
	$recipient_name=$get_method['name'];
	// subject
	$subject = '[IMPORTANT] '.APP_NAME.  'Registration completed';

	if($user_type=='Google' || $user_type=='google'){

		$qry = "SELECT * FROM tbl_users WHERE (`email` = '$email' OR `auth_id`='$auth_id') AND `user_type`='Google'";
		$result = mysqli_query($mysqli,$qry) or die(mysqli_error($mysqli));
		$num_rows = mysqli_num_rows($result);
		$row = mysqli_fetch_assoc($result);
		
		if($num_rows == 0){

			$is_duplicate='';

			$sql_device="SELECT * FROM tbl_users WHERE `device_id` = '".$device_id."'";
			$res_device=mysqli_query($mysqli,$sql_device);
			if(mysqli_num_rows($res_device) > 0){
				$is_duplicate='1';
			}else{
				$is_duplicate='0';
			}

			$data = array(
				'user_type' => 'Google',
				'name'  => cleanInput($get_method['name']),				    
				'email'  =>  cleanInput($get_method['email']),
				'password'  =>  trim($get_method['password']),
				'phone'  =>  cleanInput($get_method['phone']),
				'auth_id' => $auth_id,
				'is_duplicate' => $is_duplicate,
				'device_id' => $device_id,
				'registration_on' => $registration_on,
				'status'  =>  '1'
			);		

			$qry = Insert('tbl_users',$data);	

			$user_id=mysqli_insert_id($mysqli);

			$sql="SELECT * FROM tbl_active_log WHERE `user_id`='$user_id'";
			$res=mysqli_query($mysqli, $sql);

			if(mysqli_num_rows($res) == 0){
                // insert active log

				$data_log = array(
					'user_id'  =>  $user_id,
					'date_time'  =>  strtotime(date('d-m-Y h:i:s A'))
				);

				$qry = Insert('tbl_active_log',$data_log);

			}
			else{

                // update active log
				$data_log = array(
					'date_time'  =>  strtotime(date('d-m-Y h:i:s A'))
				);

				$update=Update('tbl_active_log', $data_log, "WHERE tbl_active_log.`user_id` = '$user_id'");  
			}

			$message='<div style="background-color: #eee;" align="center"><br />
			<table style="font-family: OpenSans,sans-serif; color: #666666;" border="0" width="600" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
			<tbody>
			<tr>
			<td colspan="2" bgcolor="#FFFFFF" align="center" ><img src="'.$file_path.'images/'.APP_LOGO.'" alt="logo" style="width:100px;height:auto"/></td>
			</tr>
			<br>
			<br>
			<tr>
			<td colspan="2" bgcolor="#FFFFFF" align="center" style="padding-top:25px;">
			<img src="'.$file_path.'assets/images/thankyoudribble.gif" alt="header" auto-height="100" width="50%"/>
			</td>
			</tr>
			<tr>
			<td width="600" valign="top" bgcolor="#FFFFFF">
			<table style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; padding: 15px;" border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
			<tbody>
			<tr>
			<td valign="top">
			<table border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; width:100%;">
			<tbody>
			<tr>
			<td>
			<p style="color: #717171; font-size: 24px; margin-top:0px; margin:0 auto; text-align:center;"><strong>'.$app_lang['welcome_lbl'].', '.addslashes(trim($get_method['name'])).'</strong></p>
			<br>
			<p style="color:#15791c; font-size:18px; line-height:32px;font-weight:500;margin-bottom:30px; margin:0 auto; text-align:center;">'.$app_lang['google_register_msg'].'<br /></p>
			<br/>
			<p style="color:#999; font-size:17px; line-height:32px;font-weight:500;">'.$app_lang['thank_you_lbl'].' '.APP_NAME.'</p>
			</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			<tr>
			<td style="color: #262626; padding: 20px 0; font-size: 18px; border-top:5px solid #52bfd3;" colspan="2" align="center" bgcolor="#ffffff">'.$app_lang['email_copyright'].' '.APP_NAME.'.</td>
			</tr>
			</tbody>
			</table>
			</div>';

			send_email($to,$recipient_name,$subject,$message);

			$set=array('status' => '1','message' => '','user_id' => $user_id,'name'=>$get_method['name'],'email'=>$get_method['email'],'msg' => $app_lang['register_success'],'success'=>'1');

		}else{	

			if($row['status']==0)
			{
				$set=array('status' => '1','message' => '','msg' =>$app_lang['account_blocked'],'success'=>'0');
			}	

			else
			{  
				$set=array('status' => '1','message' => '','user_id' => $row['id'],'name'=>$row['name'],'email'=>$row['email'],'msg' => $app_lang['login_success'],'success'=>'1');
			}
		}

	}
	else if($user_type=='Normal' || $user_type=='normal'){			

		$qry = "SELECT * FROM tbl_users WHERE tbl_users.`email` = '".$get_method['email']."' AND tbl_users.`user_type`='Normal'";
		$result = mysqli_query($mysqli,$qry);
		$num_rows = mysqli_num_rows($result);
		$row = mysqli_fetch_assoc($result);
		
		if($num_rows == 0){

			$is_duplicate='';

			$sql_device="SELECT * FROM tbl_users WHERE tbl_users.`device_id` = '".$device_id."'";
			$res_device=mysqli_query($mysqli,$sql_device);
			if(mysqli_num_rows($res_device) > 0){
				$is_duplicate='1';
			}else{
				$is_duplicate='0';
			}

			$data = array(
				'user_type' => 'Normal',
				'name'  => cleanInput($get_method['name']),				    
				'email'  =>  cleanInput($get_method['email']),
				'password'  =>  md5(trim($get_method['password'])),
				'phone'  =>  cleanInput($get_method['phone']),
				'device_id'  =>  $device_id,
				'is_duplicate' => $is_duplicate,
				'auth_id' => $auth_id,
				'registration_on' => $registration_on,
				'status'  =>  '1'
			);	

			$qry = Insert('tbl_users',$data);	

			$user_id=mysqli_insert_id($mysqli);

			$message='<div style="background-color: #eee;" align="center"><br />
			<table style="font-family: OpenSans,sans-serif; color: #666666;" border="0" width="600" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
			<tbody>
			<tr>
			<td colspan="2" bgcolor="#FFFFFF" align="center" ><img src="'.$file_path.'images/'.APP_LOGO.'" alt="logo" style="width:100px;height:auto"/></td>
			</tr>
			<br>
			<br>
			<tr>
			<td colspan="2" bgcolor="#FFFFFF" align="center" style="padding-top:25px;">
			<img src="'.$file_path.'assets/images/thankyoudribble.gif" alt="header" auto-height="100" width="50%"/>
			</td>
			</tr>
			<tr>
			<td width="600" valign="top" bgcolor="#FFFFFF">
			<table style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; padding: 15px;" border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
			<tbody>
			<tr>
			<td valign="top">
			<table border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; width:100%;">
			<tbody>
			<tr>
			<td>
			<p style="color: #717171; font-size: 24px; margin-top:0px; margin:0 auto; text-align:center;"><strong>'.$app_lang['welcome_lbl'].', '.addslashes(trim($get_method['name'])).'</strong></p>
			<br>
			<p style="color:#15791c; font-size:18px; line-height:32px;font-weight:500;margin-bottom:30px; margin:0 auto; text-align:center;">'.$app_lang['google_register_msg'].'<br /></p>
			<br/>
			<p style="color:#999; font-size:17px; line-height:32px;font-weight:500;">'.$app_lang['thank_you_lbl'].' '.APP_NAME.'</p>
			</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			<tr>
			<td style="color: #262626; padding: 20px 0; font-size: 18px; border-top:5px solid #52bfd3;" colspan="2" align="center" bgcolor="#ffffff">'.$app_lang['email_copyright'].' '.APP_NAME.'.</td>
			</tr>
			</tbody>
			</table>
			</div>';

			send_email($to,$recipient_name,$subject,$message);

			$set=array('status' => '1','message' => '','user_id' => $user_id,'name'=>$get_method['name'],'email'=>$get_method['email'],'msg' => $app_lang['register_success'],'success'=>'1');
		}else{

			if($row['status']==0)
			{
				$set=array('status' => '1','message' => '','msg' =>$app_lang['account_blocked'],'success'=>'0');
			}	
			else
			{  

				$set=array('status' => '1','message' => '','user_id' => $row['id'],'name'=>$row['name'],'email'=>$row['email'],'msg' => $app_lang['register_success'],'success'=>'1');
			}
		}

	}
	else if($user_type=='Facebook' || $user_type=='facebook'){			

		$qry = "SELECT * FROM tbl_users WHERE  (`email` = 'email' AND `auth_id`='$auth_id') AND `user_type`='Facebook'";
		$result = mysqli_query($mysqli,$qry);
		$num_rows = mysqli_num_rows($result);
		$row = mysqli_fetch_assoc($result);

		if($num_rows == 0){

			$is_duplicate='';

			$sql_device="SELECT * FROM tbl_users WHERE `device_id` = '".$device_id."'";
			$res_device=mysqli_query($mysqli,$sql_device);
			if(mysqli_num_rows($res_device) > 0){
				$is_duplicate='1';
			}else{
				$is_duplicate='0';
			}

			$data = array(

				'user_type' => 'Facebook',
				'name'  => cleanInput($get_method['name']),				    
				'email'  =>  cleanInput($get_method['email']),
				'password'  =>  trim($get_method['password']),
				'phone'  =>  cleanInput($get_method['phone']),
				'device_id'  =>  $device_id,
				'is_duplicate' => $is_duplicate,
				'auth_id' => $auth_id,
				'registration_on' => $registration_on,
				'status'  =>  '1'
			);		

			$qry = Insert('tbl_users',$data);	

			$user_id=mysqli_insert_id($mysqli);

			$sql="SELECT * FROM tbl_active_log WHERE `user_id`='$user_id'";
			$res=mysqli_query($mysqli, $sql);

			if(mysqli_num_rows($res) == 0){
                        // insert active log

				$data_log = array(
					'user_id'  =>  $user_id,
					'date_time'  =>  strtotime(date('d-m-Y h:i:s A'))
				);

				$qry = Insert('tbl_active_log',$data_log);

			}
			else{
                        // update active log
				$data_log = array(
					'date_time'  =>  strtotime(date('d-m-Y h:i:s A'))
				);

				$update=Update('tbl_active_log', $data_log, "WHERE user_id = '$user_id'");  
			}


			$message='<div style="background-color: #eee;" align="center"><br />
			<table style="font-family: OpenSans,sans-serif; color: #666666;" border="0" width="600" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
			<tbody>
			<tr>
			<td colspan="2" bgcolor="#FFFFFF" align="center" ><img src="'.$file_path.'images/'.APP_LOGO.'" alt="logo" style="width:100px;height:auto"/></td>
			</tr>
			<br>
			<br>
			<tr>
			<td colspan="2" bgcolor="#FFFFFF" align="center" style="padding-top:25px;">
			<img src="'.$file_path.'assets/images/thankyoudribble.gif" alt="header" auto-height="100" width="50%"/>
			</td>
			</tr>
			<tr>
			<td width="600" valign="top" bgcolor="#FFFFFF">
			<table style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; padding: 15px;" border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
			<tbody>
			<tr>
			<td valign="top">
			<table border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; width:100%;">
			<tbody>
			<tr>
			<td>
			<p style="color: #717171; font-size: 24px; margin-top:0px; margin:0 auto; text-align:center;"><strong>'.$app_lang['welcome_lbl'].', '.addslashes(trim($get_method['name'])).'</strong></p>
			<br>
			<p style="color:#15791c; font-size:18px; line-height:32px;font-weight:500;margin-bottom:30px; margin:0 auto; text-align:center;">'.$app_lang['google_register_msg'].'<br /></p>
			<br/>
			<p style="color:#999; font-size:17px; line-height:32px;font-weight:500;">'.$app_lang['thank_you_lbl'].' '.APP_NAME.'</p>
			</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			<tr>
			<td style="color: #262626; padding: 20px 0; font-size: 18px; border-top:5px solid #52bfd3;" colspan="2" align="center" bgcolor="#ffffff">'.$app_lang['email_copyright'].' '.APP_NAME.'.</td>
			</tr>
			</tbody>
			</table>
			</div>';

			send_email($to,$recipient_name,$subject,$message);

			$set=array('status' => '1','message' => '','user_id' => $user_id,'name'=>$get_method['name'],'email'=>$get_method['email'],'msg' => $app_lang['register_success'],'success'=>'1');
		}
		else{

			if($row['status']==0)
			{
				$set=array('status' => '1','message' => '','msg' =>$app_lang['account_blocked'],'success'=>'0');
			}

			else
			{  
				$set=array('status' => '1','message' => '','user_id' => $row['id'],'name'=>$row['name'],'email'=>$row['email'],'msg' => $app_lang['login_success'],'success'=>'1');
			}
		}
	}

	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE));
	die();
}
else if($get_method['method_name']=="user_status"){

	$user_id = $get_method['user_id'];

	$qry = "SELECT * FROM tbl_users WHERE  tbl_users.`id` = '".$user_id."'"; 
	$result = mysqli_query($mysqli,$qry);
	$num_rows = mysqli_num_rows($result);
	$row = mysqli_fetch_assoc($result);

	if($num_rows > 0){ 	 
		if($row['status']==1){
			$set=array('status' => '1','message' => '','msg' => '','success'=>'1');	 
		}else{
			$set=array('status' => '1','message' => '','msg' => '','success'=>'0');
		}
	}else{
		$set=array('status' => '1','message' => '','msg' => '','success'=>'0');
	}

	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE));
	die();

}
else if($get_method['method_name']=="user_login"){

	$email = cleanInput($get_method['email']);
	$password = trim($get_method['password']);

	$sql_user="SELECT * FROM tbl_users WHERE tbl_users.`email`='$email'";
	$res_user=mysqli_query($mysqli, $sql_user) or die('Error in fetch data ->'.mysqli_error($mysqli));

	if(mysqli_num_rows($res_user) > 0){
		$row=mysqli_fetch_assoc($res_user);

		if($row['status']==1){

			if($row['password']==md5($password)){

				$user_id=$row['id'];

				$sql="SELECT * FROM tbl_active_log WHERE tbl_active_log.`user_id`='$user_id'";
				$res=mysqli_query($mysqli, $sql);

				if(mysqli_num_rows($res) == 0){
                    // insert active log

					$data_log = array(
						'user_id'  =>  $user_id,
						'date_time'  =>  strtotime(date('d-m-Y h:i:s A'))
					);

					$qry = Insert('tbl_active_log',$data_log);

				}
				else{
                    // update active log
					$data_log = array(
						'date_time'  =>  strtotime(date('d-m-Y h:i:s A'))
					);

					$update=Update('tbl_active_log', $data_log, "WHERE tbl_active_log.`user_id` = '$user_id'");  
				}

				mysqli_free_result($res);

				$set=array('status' => '1','message' => '','msg' => $app_lang['login_success'],'user_id' => $row['id'],'name'=>$row['name'],'success'=>'1');

			}
			else{
				// invalid password
				$set=array('status' => '1','message' => '','msg' =>$app_lang['invalid_password'],'success'=>'0');
			}
		}
		else{
			// account is deactivated
			$set=array('status' => '1','message' => '','msg' =>$app_lang['account_deactive'],'success'=>'0');
		}

	}
	else{
		// email not found
		$set=array('status' => '1','message' => '','msg' =>$app_lang['email_not_found'],'success'=>'0');
	}

	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	die();

}
else if($get_method['method_name']=="user_profile"){
	$qry = "SELECT * FROM tbl_users WHERE  tbl_users.`status`=1 AND id = '".$get_method['user_id']."'"; 
	$result = mysqli_query($mysqli,$qry);
	$row = mysqli_fetch_assoc($result);
	
	$file_path.'images/'.$row['user_profile'];

	if(!file_exists($file_path.'images/'.$row['user_profile'])){
		$set=array('status' => '1','message' => '','user_id' => $row['id'],'name'=>$row['name'],'email'=>$row['email'],'phone'=>$row['phone'],'user_profile'=>$file_path.'images/'.$row['user_profile'],'success'=>'1');
	}else{
		$set=array('status' => '1','message' => '','user_id' => $row['id'],'name'=>$row['name'],'email'=>$row['email'],'phone'=>$row['phone'],'user_profile'=>'','success'=>'1');
	}
	
	header( 'Content-Type: application/json; charset=utf-8' );
	echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE));
	die();

}
else if($get_method['method_name']=="user_profile_update"){

	$user_id=$get_method['user_id'];
	$is_remove=$get_method['is_remove'];

	$qry = "SELECT * FROM tbl_users WHERE tbl_users.`id`='$user_id'"; 
	$result = mysqli_query($mysqli,$qry);
	$row = mysqli_fetch_assoc($result);

	if(isset($is_remove) && $is_remove){
		if($row['user_profile']!=""){
			if (file_exists('images/' .$row['user_profile'])){
				unlink('images/'.$row['user_profile']);
			} 

			$sql="UPDATE tbl_users SET `user_profile`='' WHERE `id`='".$row['id']."'";
			mysqli_query($mysqli,$sql);
		}
		$user_profile ='';
	}

	if(isset($_FILES['user_profile']))
	{
		if($row['user_profile']!="")
		{
			unlink('images/'.$row['user_profile']);
		}

		$ext = pathinfo($_FILES['user_profile']['name'], PATHINFO_EXTENSION);

				$path = "images/"; //set your folder path
				$user_profile=date('dmYhis').'_'.rand(0,99999).".".$ext;
		        //Main Image
				$tpath1=$path.$user_profile;        
				$pic1=compress_image($_FILES["user_profile"]["tmp_name"], $tpath1, 80);

				$data = array(
					'name'  => cleanInput($get_method['name']),
					'phone'  =>  cleanInput($get_method['phone']),
					'user_profile'  =>  $user_profile 
				);

			}else{

				$data = array(
					'name'  => cleanInput($get_method['name']),
					'phone'  =>  cleanInput($get_method['phone'])
				);
			}
			
			$user_edit=Update('tbl_users', $data, "WHERE `id`= '".$get_method['user_id']."'");

			$set=array('status' => '1','message' => '','msg'=>$app_lang['update_success'],'success'=>'1');		 		    
			header( 'Content-Type: application/json; charset=utf-8' );
			echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE));
			die();

		}
		else if($get_method['method_name']=="user_contact_us"){	
			
			$contact_name = htmlentities(trim($get_method['contact_name']));
			$contact_email = htmlentities(trim($get_method['contact_email']));
			$contact_subject = trim($get_method['contact_subject']);
			$contact_msg = htmlentities(trim($get_method['contact_msg']));

			$data = array(
				'contact_name'  => $contact_name,
				'contact_email'  => $contact_email,
				'contact_subject'  =>  $contact_subject,
				'contact_msg'  =>  $contact_msg,
				'created_at'  =>  strtotime(date('d-m-Y h:i:s A'))
			);	

			$qry = Insert('tbl_contact_list',$data);

			$to = $settings_details['app_email'];
			$recipient_name=APP_NAME;
		// subject
			$subject = '[IMPORTANT] '.APP_NAME.' Contact';
			
			$message='<div style="background-color: #f9f9f9;" align="center"><br />
			<table style="font-family: OpenSans,sans-serif; color: #666666;" border="0" width="600" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
			<tbody>
			<tr>
			<td colspan="2" bgcolor="#FFFFFF" align="center"><img src="'.$file_path.'images/'.APP_LOGO.'" alt="header" width="120" /></td>
			</tr>
			<tr>
			<td width="600" valign="top" bgcolor="#FFFFFF"><br>
			<table style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; padding: 15px;" border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
			<tbody>
			<tr>
			<td valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; width:100%;">
			<tbody>
			<tr>
			<td>
			<p style="color:#262626; font-size:20px; line-height:32px;font-weight:500;">Hello Admin,<br>
			Name: '.$contact_name.'</p>
			<p style="color:#262626; font-size:20px; line-height:30px;font-weight:500;"> 
			Email: '.$contact_email.'</p>
			<p style="color:#262626; font-size:20px; line-height:30px;font-weight:500;"> 
			Subject: '.get_subject_info($contact_subject,'title').'</p>
			<p style="color:#262626; font-size:20px; line-height:30px;font-weight:500;"> 
			Message: '.$contact_msg.'</p>
			<p style="color:#262626; font-size:20px; line-height:32px;font-weight:500;margin-bottom:30px;">Thanks you,<br />
			'.APP_NAME.'.</p></td>
			</tr>
			</tbody>
			</table></td>
			</tr>
			
			</tbody>
			</table></td>
			</tr>
			<tr>
			<td style="color: #262626; padding: 20px 0; font-size: 20px; border-top:5px solid #52bfd3;" colspan="2" align="center" bgcolor="#ffffff">Copyright © '.APP_NAME.'.</td>
			</tr>
			</tbody>
			</table>
			</div>';

			send_email($to,$recipient_name,$subject,$message);
			
			$set=array('status' => '1','message' => '','msg' => $app_lang['msg_sent'],'success'=>'1');

			header( 'Content-Type: application/json; charset=utf-8' );
			echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
			die();
		}	
	// change password
		else if($get_method['method_name']=="change_password"){

			$user_id=$get_method['user_id'];
			$old_password=$get_method['old_password'];
			$new_password=$get_method['new_password'];

			$qry = "SELECT * FROM tbl_users WHERE `id`='$user_id' AND `status` = 1"; 
			$result = mysqli_query($mysqli,$qry);
			$num_rows = mysqli_num_rows($result);
			$row = mysqli_fetch_assoc($result);

			if ($row['password'] == md5($old_password)) {

				$data = array(
					'password' => md5($new_password)
				);

				$edit=Update('tbl_users', $data, "WHERE `id` = '$user_id'");

				$set=array('status' => '1','message' => '','msg' => $app_lang['change_password_msg'],'success' => '1');	 
			}
			else{
				$set=array('status' => '1','message' => '','msg' =>$app_lang['wrong_password_error'],'success' => '0');
			}

			header( 'Content-Type: application/json; charset=utf-8' );
			echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
			die();

		}
		else if($get_method['method_name']=="forgot_pass"){

			$email=trim($get_method['user_email']);

			$qry = "SELECT * FROM tbl_users WHERE tbl_users.`email` = '$email' AND tbl_users.`user_type`='Normal'"; 
			$result = mysqli_query($mysqli,$qry);
			$row = mysqli_fetch_assoc($result);

			if($result->num_rows > 0)
			{
				$password=generateRandomPassword(7);

				$new_password=md5($password);

				$to = $row['email'];
				$recipient_name=$row['name'];
			// subject
				$subject = str_replace('###', APP_NAME, $app_lang['forgot_password_sub_lbl']);

				$message='<div style="background-color: #f9f9f9;" align="center"><br />
					  <table style="font-family: OpenSans,sans-serif; color: #666666;" border="0" width="600" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
					    <tbody>
					      <tr>
					        <td colspan="2" bgcolor="#FFFFFF" align="center"><img src="'.$file_path.'images/'.APP_LOGO.'" alt="header" style="width:100px;height:auto"/></td>
					      </tr>
					      <tr>
					        <td width="600" valign="top" bgcolor="#FFFFFF"><br>
					          <table style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; padding: 15px;" border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
					            <tbody>
					              <tr>
					                <td valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; width:100%;">
					                    <tbody>
					                      <tr>
					                        <td>
					                        	<p style="color: #262626; font-size: 24px; margin-top:0px;"><strong>'.$app_lang['dear_lbl'].' '.$row['name'].'</strong></p>
					                          <p style="color:#262626; font-size:20px; line-height:32px;font-weight:500;margin-top:5px;"><br>'.$app_lang['your_password_lbl'].': <span style="font-weight:400;">'.$password.'</span></p>
					                          <p style="color:#262626; font-size:17px; line-height:32px;font-weight:500;margin-bottom:30px;">'.$app_lang['thank_you_lbl'].' '.APP_NAME.'</p>

					                        </td>
					                      </tr>
					                    </tbody>
					                  </table></td>
					              </tr>
					               
					            </tbody>
					          </table></td>
					      </tr>
					      <tr>
					        <td style="color: #262626; padding: 20px 0; font-size: 18px; border-top:5px solid #52bfd3;" colspan="2" align="center" bgcolor="#ffffff">'.$app_lang['email_copyright'].' '.APP_NAME.'.</td>
					      </tr>
					    </tbody>
					  </table>
					</div>';

			send_email($to,$recipient_name,$subject,$message);

			$sql="UPDATE tbl_users SET `password`='$new_password' WHERE `id`='".$row['id']."'";
	      	mysqli_query($mysqli,$sql);
			 	  
			$set=array('status' => '1','message' => '','msg' => $app_lang['password_sent_mail'],'success'=>'1');
			}
			else
			{  	 	
				$set=array('status' => '1','message' => '','msg' => $app_lang['email_not_found'],'success'=>'0');		
			}

			header( 'Content-Type: application/json; charset=utf-8' );
			echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
			die();	
		}
		else if($get_method['method_name']=="app_faq"){

			$jsonObj= array();	

			$query="SELECT * FROM tbl_settings WHERE tbl_settings.`id`='1'";
			$sql = mysqli_query($mysqli,$query);

			$data = mysqli_fetch_assoc($sql);

			$row['status'] = '1';
			$row['message'] = '';
			$row['success'] = '1';
			$row['app_faq'] = stripslashes($data['app_faq']);

			$set = $row;

			header( 'Content-Type: application/json; charset=utf-8' );
			echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
			die();	
		}
		else if($get_method['method_name']=="get_app_details"){

			$jsonObj= array();	

			$query="SELECT * FROM tbl_settings WHERE `id` ='1'";
			$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

			$data = mysqli_fetch_assoc($sql);

			$row['status'] = 1;
			$row['message'] = '';

			$row['publisher_id'] = $data['publisher_id'];
			$row['interstitial_ad'] = $data['interstital_ad'];
			$row['interstitial_ad_type'] = $data['interstital_ad_type'];
			$row['interstitial_ad_id'] = ($data['interstital_ad_type']=='facebook') ? $data['facebook_interstital_ad_id'] : $data['interstital_ad_id'];
			$row['interstitial_ad_click'] = $data['interstital_ad_click'];

			$row['banner_ad'] = $data['banner_ad'];
			$row['banner_ad_type'] = $data['banner_ad_type'];

			$row['banner_ad_id'] = ($data['banner_ad_type']=='facebook') ? $data['facebook_banner_ad_id'] : $data['banner_ad_id'];
			
			$row['app_update_status'] = $data['app_update_status'];
			$row['app_new_version'] = $data['app_new_version'];
			$row['app_update_desc'] = stripslashes($data['app_update_desc']);
			$row['app_redirect_url'] = $data['app_redirect_url'];
			$row['cancel_update_status'] = $data['cancel_update_status'];

			$row['privacy_policy_link'] = $file_path.'/privacy_policy.php';

			$set= $row;
			
			header( 'Content-Type: application/json; charset=utf-8' );
			echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
			die();	
		}	
		else if($get_method['method_name']=="app_about"){

			$jsonObj= array();	

			$query="SELECT * FROM tbl_settings WHERE `id` ='1'";
			$sql = mysqli_query($mysqli,$query)or die(mysqli_error($mysqli));

			$data = mysqli_fetch_assoc($sql);

			$row['status'] = 1;
			$row['message'] = '';
			$row['app_name'] = $data['app_name'];
			$row['app_logo'] = $file_path.'images/'.$data['app_logo'];
			$row['app_version'] = $data['app_version'];
			$row['app_author'] = $data['app_author'];
			$row['app_contact'] = $data['app_contact'];
			$row['app_email'] = $data['app_email'];
			$row['app_website'] = $data['app_website'];
			$row['app_description'] = stripslashes($data['app_description']);
			

			$set = $row;
			
			header( 'Content-Type: application/json; charset=utf-8' );
			echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
			die();	
		}	
		else if($get_method['method_name']=="app_privacy_policy"){
			
			$jsonObj= array();	

			$query="SELECT * FROM tbl_settings WHERE `id` ='1'";
			$sql = mysqli_query($mysqli,$query);

			$data = mysqli_fetch_assoc($sql);

			$row['status'] = 1;
			$row['message'] = '';
			$row['app_privacy_policy'] = stripslashes($data['app_privacy_policy']);
			
			$set= $row;
			
			header( 'Content-Type: application/json; charset=utf-8' );
			echo $val= str_replace('\\/', '/', json_encode($set,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
			die();	
		}else
		{
			$get_method = checkSignSalt($_POST['data']);
		}
		
		?>
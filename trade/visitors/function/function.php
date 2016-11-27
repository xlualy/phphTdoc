<?php
function myconnection(){
    return mysqli_connect("localhost","root","","oss1");
}
$con=myconnection();
function Run($query){
    global $con;
    return $con->query($query);
}
function removePro($id){
    $sql="Delete from productdetails where pid='$id'";
    return Run($sql);
}
//-!product view----//
function moreProxm(){
    global $con;
    $lable = "select * from productdetails WHERE approved=1 limit 5";
    $res = mysqli_query($con, $lable);

    while ($row = mysqli_fetch_array($res)) {
        $name = $row['p_name'];
        $price = $row['price'];
        $id = $row['43pid'];
        $img = $row['image'];
        $date = $row['date'];
        $type = $row['type'];
        $image = array();
        $image = json_decode($img);
        echo '
              
			<div class="ad-item row">
                            <!-- item-image -->
                            <div class="item-image-box col-sm-4">
                                <div class="item-image">
                                    <a href="details.html"><img src="images/listing/1.jpg" alt="Image" class="img-responsive"></a>
                                </div><!-- item-image -->
                            </div>

                            <!-- rending-text -->
                            <div class="item-info col-sm-8">
                                <!-- ad-info -->
                                <div class="ad-info">
                                    <h3 class="item-price">$800.00</h3>
                                    <h4 class="item-title"><a href="#">Apple TV - Everything you need to know!</a></h4>
                                    <div class="item-cat">
                                        <span><a href="#">Electronics & Gedgets</a></span> /
                                        <span><a href="#">Tv & Video</a></span>
                                    </div>
                                </div><!-- ad-info -->

                                <!-- ad-meta -->
                                <div class="ad-meta">
                                    <div class="meta-content">
                                        <span class="dated"><a href="#">7 Jan, 16  10:10 pm </a></span>
                                        <a href="#" class="tag"><i class="fa fa-tags"></i> New</a>
                                    </div>
                                    <!-- item-info-right -->
                                    <div class="user-option pull-right">
                                        <a href="#" data-toggle="tooltip" data-placement="top" title="Los Angeles, USA"><i class="fa fa-map-marker"></i> </a>
                                        <a class="online" href="#" data-toggle="tooltip" data-placement="top" title="Individual"><i class="fa fa-user"></i> </a>
                                    </div><!-- item-info-right -->
                                </div><!-- ad-meta -->
                            </div><!-- item-info -->
                        </div>				
              ';
    }

}
function isSell(){
    $con=myconnection();
    $sql="Select sell_out from productdetails";
    Run($sql);
}
///....remove items
if(isset($_REQUEST['re_pid'])){
    $id=$_REQUEST['re_pid'];
    if(removePro($id))
    header("location:../items.php");

}
//-----------------product add-----

if (isset($_POST['pro_submit'])) {

    $con=myConnection();
    $p_name = $_POST['p_name'];
    $price = $_POST['price'];
    $location = $_POST['location'];
    $time=date("Y-m-d H:i:s");
    $type = $_POST['type'];
    $description = $_POST['description'];
    $vid = $_POST['vid'];
    $brand = $_POST['brand'];

    $file_name=$_FILES['file']['name'];
    $file_path=$_FILES['file']['tmp_name'];
    $arr_path=array();
    $arr_name=array();
    foreach ($file_path as $item)
        array_push($arr_path,$item);
    foreach ($file_name as $item)
        array_push($arr_name,$item);
    $img_path=json_encode($arr_name);
    $sql="insert into productdetails(p_name,price,type,date,approved,description,vid,sell_out,brand,image,location) 
                                VALUES ('$p_name','$price','$type','$time','0','$description','$vid','0','$brand','$img_path','$location')";


    if($con->query($sql)){
        /*for notication*/
        $latest="SELECT * FROM  productdetails WHERE pid = (SELECT MAX(pid)  FROM productdetails)";
        $last=mysqli_fetch_array(Run($latest));
        $sql1="insert into notification (vid,pid,status,message) VALUES ('$vid','$last[0]','0','Need approval')";
        Run($sql1);
        /*---------*/
        move_uploaded_file($arr_path[0],"../images/".$arr_name[0]);
        move_uploaded_file($arr_path[1],"../images/".$arr_name[1]);
        move_uploaded_file($arr_path[2],"../images/".$arr_name[2]);
        header("location:../items.php");
    }
    else{
        echo "error";
    }

}
if(isset($_POST['data'])){
    if($_POST['data']=='upd_buyer') {
         $pid=$_POST['pname'];
         $bname=$_POST['bname'];
//        echo $status= $_REQUEST['status'];
//        $sql="update productdetails set on_bet='$status' WHERE pid='$pid'";


    }
}
//___________buying record
function buy($vid){

  $query = "SELECT D.pid,date(D.B_date),V.name,P.p_name,P.price FROM deals AS D  join productdetails As P on D.pid=P.pid
                JOIN visitor AS V on V.vid=D.vid WHERE V.vid='$vid'";
    $res=Run($query);

    echo '
    <div class="col-lg-6">
    <h2>Previous Shopping Details</h2>
    <table class="table">
            <th>Product name</th>
                    <th>Price</th>
                    <th>Date</th>
                    <tr>';
    if(mysqli_num_rows($res)>0){
    while ($row=mysqli_fetch_array($res))
    {
            $name=$row['name'];
            $pname=$row['p_name'];
            $price=$row['price'];
            $date=$row['date(D.B_date)'];
        echo '      <td>'.$pname.' </td>
                    <td>'.$price.'</td>
                    <td>'.$date.'</td>
                    </tr>';
    }
    }
    else{
        echo' <hr><h2 class="alert-danger ">No transision record</h2>';
    }
    echo' </table>
    </div>
    ';
}
//............notification
if(isset($_POST['data'])=='udata'){
if($_POST['data']=='udata') {
    $vid = $_POST['vid'];
    $sql = "delete from notification where vid='$vid' and status='1'";
    Run($sql);
}
}//notification

//................end product entery..
function headder(){


    $vid=$_SESSION['vid'];
    $sql="Select name from visitor WHERE vid='$vid'";
    $res=Run($sql);
    $name=mysqli_fetch_assoc($res);

    echo '

    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Document</title>
        <link href="style/visitor.css" rel="stylesheet">
        <link href="style/bootstrap.min.css" rel="stylesheet">
        <link href="style/font-awesome.min.css" rel="stylesheet">
          <script type="text/javascript" src="js/jquery.js"></script>
          <script type="text/javascript" src="js/custom.js"></script>
          <script type="text/javascript" src="../js/bootstrap.min.js"></script>
    </head>
    <body>
    <div class="container-fluid">';
    
    sideBar();
        echo'
        <div class="col-lg-10 header" style="height: 40px;background-color: #a94a42">
        
        <div class="col-lg-5 ">
        <div class="row">
        <ul class="sign-in">
                    <li><i class="fa fa-user"></i></li>
                    <li><a href="">'.ucfirst($name['name']).'</a></li>
                    <li><a href="../logout.php?&pp=1">Logout</a></li>
                </ul>
        </div>
        
        </div>';
        include 'notification.php';


}
function footer(){

    echo '
      </div>
       </div>


    <div class="col-lg-12 footer" style="border-bottom: 1px solid red;">
    </div>
    </div>
    </div>

    </body>
    </html>
    ';
}
function sideBar(){
    echo '<div class="col-lg-2 side-bar" style="height: 520px;>
                <h3 class="list-group-item"></h3>
                <ul class="list-group">
                <a href="items.php"><li class="list-group-item fa fa-dashboard fa-1x"></li>
                    <span>Dashboard</span>
                    </a>
                    <hr>
                   <a href="index.php"> <li class="list-group-item fa fa-tags fa-1x"></li>
                   <span>Add New</span>
                   </a>
                   <hr>
                    <a href=""><li class="list-group-item fa fa-eye fa-1x"></li>
                    <span>testbar</span>
                    </a>
                    
                   <hr>
                </ul>
            </div>';
}

function cusPro($vid){
    echo '<div class="col-lg-6 pull-left">

            <h2>Items for Sell</h2>
                <table class="table table-condensed">
                    <th>Product name</th>
                    <th>Price</th>
                    <th>status</th>
                    <th>Approved</th>';


                        
                        $sql="Select * from productdetails WHERE vid='$vid'";
                        $res=Run($sql);
                        while($row=mysqli_fetch_array($res)){
                            $pid=$row['pid'];
                            $name=$row['p_name'];
                            $price=$row['price'];
                        if($row['approved']=='1'){
                            $approve="Yes";
                        }
                        else
                            $approve="No";
                            if($row['sell_out']=='1'){
                                $status="Sell out";
                            }
                            else
                                $status="Not sell";
                        
                        echo '<tr>
                        <td>
                            <a href="prodetails.php?p_id='.$pid.'">';
                        echo $name.' </a>
                        </td>
                        <td> '.$price.'</td>
                        <td> '.$status.' </td>
                        <td> '.$approve .'</td>
                        <td>
                       
                        </td>';

                             }
                    echo '</tr>
                </table>
                </div>
';
}

function UpdateStatus($vid){
    $_SESSION['LAST_ACTIVITY'] = time();
    $last_activity = $_SESSION['LAST_ACTIVITY'];
    $sql = "UPDATE visitor SET login_activity = '$last_activity' WHERE vid = '$vid'";
    Run($sql);
}
?>


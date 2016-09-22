<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="initial-scale=1">
<title>ECI GIS MAP</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<?php require('db/system.php'); 
$eccgis = 'ecc_gis_data';
?>

<body onload="disableselect();">
<?php
  @$qs = ($_GET['q']) ? $_GET['q'] : '';
  @$cs = ($_GET['c']) ? $_GET['c'] : '';
  @$cm = ($_GET['cc']) ? $_GET['cc'] : '';

?>
<div class="container">
  <div class="content">
    <h1>Find An ECI</h1>
    <p>Click on the map markers or the links below for more information on a selected ECI.</p>
    <div  style="height:400px; background-color:#ccc; margin:30px;">
      <div id="map" style="height:400px;"></div>
      <div id="map-side-bar">
        <div align="" style="margin-bottom:40px;">
          <div class="row">
            <div class="col-md-4">
              <label for="ddlparish">Parish: </label>
             
				<select name='parishes' id='ddlparish' class='form-control' onchange='getParish(this.form)'>
				<option value=''>All</option>
				 <?php
					$sql = "select parish_id, parish_name from parishes order by parish_name";
					$result = $conn->query($sql);

					while($row = $result->fetch_assoc()) {
						$selected = ($row['parish_id'] == @$qs) ? "selected" : "";
						echo "<option ".$selected." value='".$row['parish_id']."'>".$row['parish_name']."</option>";
					}
				?>
				</select>
				<?php
					mysqli_free_result($result);
				?>
            </div>
            <div class="col-md-4">
              <label for="ddlconstituency">Constituency: </label>
              	<select name='Genotssype' id='ddlconstituency' class='form-control' onchange='getConstituency(this.form)'>
				<option value=''>All</option>
				<?php

					$qry = (isset($qs) and strlen($qs) > 0) ? " where con_parish_id = ".$qs." order by constituency" : "";
					$sql = "select con_id, constituency from constituencies".$qry;
					$result = $conn->query($sql);

					while($row = $result->fetch_assoc()) {
						$selected = ($row['con_id'] == @$cs) ? "selected" : "";
						echo "<option ".$selected ." value='".$row['con_id']."'>".$row['constituency']."</option>";
					}
				?>
				</select>
				<?php mysqli_free_result($result); ?>
              <!-- insert filter links --> 
            </div>
            <div class="col-md-4">
              <label for="ddlcommunity">Community: </label>
              <select name='Genotssype' id='ddlcommunity' class='form-control' onchange="getCommunity(this.form)">
				<option value=''>All</option>";
              <?php

				$qry1 = (isset($qs) and strlen($qs) > 0) ? " and com_parish_id = ".$qs : "";
				$qry2 = (isset($cs) and strlen($cs) > 0) ? " and com_con_id = ".$cs : "";
				$sql = "select distinct (community), comm_id from communities where (com_parish_id is not null ". $qry1 .") and (com_con_id is not null ". $qry2. ") order by community";
				
				$result = $conn->query($sql);
				
				while($row = $result->fetch_assoc()) {
					$selected = ($row['comm_id'] == @$cm) ? "selected" : "";
					echo "<option ".$selected ." value='".$row['comm_id']."'>".$row['community']."</option>";
				}
				
			?>
			</select>
			<?php mysqli_free_result($result); ?>
              <!-- insert filter links --> 
            </div>
          </div>
        </div>
        <div class="row">
        <?php
        	//if options from the dropdowns are selected use value along with related sql or pass an empty string
        	$qryParish = (isset($qs) and strlen($qs) > 0) ? " and eci_parish_id = ".$qs : "";
        	$qryCon = (isset($cs) and strlen($cs) > 0) ? " and eci_con_id = ".$cs : "";
        	$qryComm = (isset($cm) and strlen($cm) > 0) ? " and eci_community_id = ".$cm : "";

			$sql = "select gis_id, eccid, eci_name, address, parish, telephone, facility_type, latlng, inspection_report from ecc_gis_data where latlng is not null ".$qryParish ." ". $qryCon ." ". $qryComm." order by eci_name";

			$result = $conn->query($sql);	

			while($row = $result->fetch_assoc()) {
				//separate the latitude and longitude and pass to google maps API
				$lat = (explode(",", $row['latlng']))[0];
				$lng = (explode(",", $row['latlng']))[1];

		?>
          <div class="map-location col-xs-12 col-sm-4 col-md-3" style="height:55px; overflow:hidden;" 
      data-jmapping="{id: <?=$row['gis_id'] ?>, point: {lng: <?=$lng ?>, lat: <?=$lat ?>}, category: '<?=$row["facility_type"]?>'}" > <span style="margin-bottom:15px;">
            <div style="float:left; height:25px;"> <img src="img/blue.png"> </div>
            <a href="#" class="map-link" style="font-size:0.8em" zoom="15" longitude="<?=$lng ?>" latitude="<?=$lat ?>">
            <?=$row['eci_name'] ?>
            <!-- , <?=(isset($parish))? $parish : ''; ?> --></a> </span>
            <div class="info-box" style="display:none">
              <p style="font-size:12px; color:#000;"><b>
                <?=$row['eci_name'] ?>
                </b><br />
                <!-- <i> -->
                <?php //$row['facility_type'] ?>
                <!--</i><br />-->
                <?=$row['address'] ?>
                <br />
                <?=$row['telephone'] ?>
                <br /><br />
                <a href="<?=$row['inspection_report'] ?>"><u>Inspection Report</u></a>
              </p>
            </div>
          </div>
        <?php
			}	
			
			mysqli_free_result($result);
 //mysqli_close($result);
		?>
        </div>
        <p id="pagination"></p>
        <div class="footer"> </div>
      </div>
    </div>
    <!-- end .container --> 
  </div>
</div>

<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>--> 
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> 

<script src="https://maps.google.com/maps/api/js?key=AIzaSyCHJuG56hgBgwB8GEPbCl-l58r7RZzkFxg" type="text/javascript"></script> 
<!-- script src="https://maps.googleapis.com/maps/api/js?v=3.3&sensor=false&key=AIzaSyD7NJO0gAn8e9VTqHjsVBaFP3Q9JBUEpP8" async defer></script>--> 

<script src="js/bootstrap.min.js"></script> 
<script src="js/jquery.jmapping.js"></script> 
<script src="js/jquery.lowpro.js"></script> 
<script src="js/jquery.metadata.js"></script> 
<script src="js/markermanager.js"></script> 
<script src="js/StyledMarker.js"></script> 
<script src="js/jquery.quickpaginate.js"></script> 
<!-- <script src="js/markerclusterer.js"></script> --> 
<script>
	<!--
	function getParish(form){
		//var val=form.cat.options[form.cat.options.selectedIndex].value;
		var ddlpar = $('#ddlparish').val();
		//var ddlcon = $('#ddlconstituency').val();
		self.location='?q=' + ddlpar ;
	}
	
	function getConstituency(form){
		//var val=form.cat.options[form.cat.options.selectedIndex].value;
		var ddlpar = $('#ddlparish').val();
		var ddlcon = $('#ddlconstituency').val();
		self.location='?q=' + ddlpar + '&c=' + ddlcon;
	}

	function getCommunity(form){
		//var val=form.cat.options[form.cat.options.selectedIndex].value;
		var ddlpar = $('#ddlparish').val();
		var ddlcon = $('#ddlconstituency').val();
		var ddlcom = $('#ddlcommunity').val();
		self.location='?q=' + ddlpar + '&c=' + ddlcon + '&cc=' + ddlcom;
	}
	
	function disableselect(){
	<?php
		if(isset($qs) and strlen($qs) > 0){
			//echo "document.f1.subcat.disabled = false;";
			echo "$('#ddlconstituency').removeAttr('disabled');";
			echo "$('#ddlcommunity').removeAttr('disabled');";
			}else{
			//echo "document.f1.subcat.disabled = true;";
			echo "$('#ddlconstituency').attr('disabled', 'disabled');";
			echo "$('#ddlcommunity').attr('disabled', 'disabled');";
		}
	?>
	}
</script> 
<script type="text/javascript"> 
  $(document).ready(function(){
  		$('#map').jMapping({
				map_config: {
					navigationControlOptions: {
						style: google.maps.NavigationControlStyle.DEFAULT
					},
					mapTypeId: google.maps.MapTypeId.ROADMAP,
					zoom: 7
				},
				location_selector: '.map-location:visible',
				category_icon_options: function(category){
					color = (category == "Basic School") ? "red" : "blue";
					return new google.maps.MarkerImage('img/'+ color +'pin.png');
				
				}
		  });
	  console.log('ready');
	  $(document).on('init_finished.quickpaginate', function(e){
		// $('#map').jMapping({location_selector: '.map-location:visible'});
			var color;
			
			/*$('#map').jMapping({
				map_config: {
					navigationControlOptions: {
						style: google.maps.NavigationControlStyle.DEFAULT
					},
					mapTypeId: google.maps.MapTypeId.ROADMAP,
					zoom: 7
				},
				location_selector: '.map-location:visible',
				category_icon_options: function(category){
					color = (category == "Basic School") ? "red" : "blue";
					return new google.maps.MarkerImage('img/'+ color +'pin.png');
				
				}
		  });*/
			console.log('bind');
  	});
  
  	$(document).on('paginate.quickpaginate', function(e, direction, page){
		 	$('#map').jMapping('update');
		 	console.log('update');
		});
  
  	$('#map-side-bar .map-location').quickpaginate({
    	perpage: 16, showcounter: false,
    	pager: $("p#pagination")
  	});
 
});

 
</script>
</body>
</html>

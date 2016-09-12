<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="initial-scale=1">
<title>ECI GIS MAP</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<?php require('db/system.php'); ?>

<body onload="disableselect();">
<?php
  @$qs = ($_GET['q']) ? $_GET['q'] : '';
  @$cs = ($_GET['c']) ? $_GET['c'] : '';
  @$cm = ($_GET['cc']) ? $_GET['cc'] : '';

?>
<div class="container">
  <div class="content">
    <h1>Find An ECI</h1>
    <p>You're ECI may not be listed as this map is currently under construction. Thanks for your patience.</p>
    <div  style="height:400px; background-color:#ccc; margin:30px;">
      <div id="map" style="height:400px;"></div>
      <div id="map-side-bar">
        <div align="" style="margin-bottom:40px;">
          <div class="row">
            <div class="col-md-4">
              <label for="ddlparish">Parish: </label>
              <?php
					$sql = "select distinct parish from ecc_gis_data order by parish";
					$result = $conn->query($sql);
				?>
				<select name='parishes' id='ddlparish' class='form-control' onchange='getParish(this.form)'>
				<option value=''>All</option>
				
				<?php
					while($row = $result->fetch_assoc()) {
						$selected = ($row['parish'] == @$qs) ? "selected" : "";
						echo "<option ".$selected." value='".$row['parish']."'>".$row['parish']."</option>";
					}
				?>
				</select>
				<?php
					mysqli_free_result($result);
				?>
            </div>
            <div class="col-md-4">
              <label for="ddlconstituency">Constituency: </label>
              <?php

				$qry = (isset($qs) and strlen($qs) > 0) ? "and parish like '".$qs."%' order by constituency" : "";
				$sql = "select distinct constituency from ecc_gis_data where longitude is not null and latitude is not null ".$qry;
				$result = $conn->query($sql);
				
				echo "<select name='Genotssype' id='ddlconstituency' class='form-control' onchange=\"getConstituency(this.form)\">";
				echo "<option value=''>All</option>";

				while($row = $result->fetch_assoc()) {
					$selected = ($row['constituency'] == @$cs) ? "selected" : "";
					echo "<option ".$selected ." value='".$row['constituency']."'>".$row['constituency']."</option>";
				}
				echo "</select>";
				
				mysqli_free_result($result);
		
			?>
              <!-- insert filter links --> 
            </div>
            <div class="col-md-4">
              <label for="ddlcommunity">Community: </label>
              <?php

				$qry1 = (isset($qs) and strlen($qs) > 0) ? " and parish like '".$qs."%'" : "";
				$qry2 = (isset($cs) and strlen($cs) > 0) ? " and constituency like '".$cs."%'" : "";
				$sql = "select distinct community from ecc_gis_data where longitude is not null and latitude is not null ". 
				$qry1 . $qry2. " order by community";
				
				$result = $conn->query($sql);
				
				echo "<select name='Genotssype' id='ddlcommunity' class='form-control' onchange=\"getCommunity(this.form)\">";
				echo "<option value=''>All</option>";
				while($row = $result->fetch_assoc()) {
					$selected = ($row['community'] == @$cm) ? "selected" : "";
					echo "<option ".$selected ." value='".$row['community']."'>".$row['community']."</option>";
				}
				echo "</select>";
				
				mysqli_free_result($result);
		
			?>
              <!-- insert filter links --> 
            </div>
          </div>
        </div>
        <div class="row">
        <?php
			$sql = "select gis_id, eccid, eci_name, address, parish, telephone, facility_type, longitude, latitude, latlng from ecc_gis_data where longitude is not null and latitude is not null and parish like '".$qs."%' and constituency like '".$cs."%'  and community like '".$cm."%' order by eci_name";
			$result = $conn->query($sql);	
			while($row = $result->fetch_assoc()) {
				$lat = (explode(",", $row['latlng']))[0];
				$lng = (explode(",", $row['latlng']))[1];

		?>
          <div class="map-location col-xs-12 col-sm-4 col-md-3" style="height:55px; overflow:hidden;" 
      data-jmapping="{id: <?=$row['gis_id'] ?>, point: {lng: <?=$lng ?>, lat: <?=$lat ?>}, category: '<?=$row["facility_type"]?>'}" > <span style="margin-bottom:15px;">
            <div style="float:left; height:25px;"> <img src="img/blue.png"> </div>
            <a href="#" class="map-link" style="font-size:0.8em" zoom="15" longitude="<?=$lng ?>" latitude="<?=$lat ?>">
            <?=$row['eci_name'] ?>
            <!-- , <?=$parish ?> --></a> </span>
            <div class="info-box" style="display:none">
              <p style="font-size:12px; color:#000;"><b>
                <?=$row['eci_name'] ?>
                </b><br />
                <i>
                <?=$row['facility_type'] ?>
                </i><br />
                <?=$row['address'] ?>
                <br />
                <?=$row['telephone'] ?>
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
	  var color;
	  // $('#map').jMapping({location_selector: '.map-location:visible'});
	  $(document).bind('init_finished.quickpaginate', function(e){
		  //$('#map').jMapping({location_selector: '.map-location:visible'});
			
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

  	});
 
  	$(document).bind('paginate.quickpaginate', function(e, direction, page){
		 	$('#map').jMapping('update');
		});
  
  	$('#map-side-bar .map-location').quickpaginate({
    	perpage: 16, showcounter: false,
    	pager: $("p#pagination")
  	});

  });
</script>
</body>
</html>

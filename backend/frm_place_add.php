<?php 
        require 'header_admin.php';
        
        //herb_owner
        $sqlOwner = "SELECT * FROM herb_owner";
        $resOwner = pg_query($db, $sqlOwner);
        
        //herb_place
        $sql_place = "SELECT MAX(place_id) FROM herb_place";
        $res_place = pg_query($db, $sql_place);
        $row_place = pg_fetch_row($res_place);
        $row_place1 = $row_place[0];
        $row_place2 = $row_place1 + 1;
        
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <script src="../bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="script.js" type="text/javascript"></script>
        <style type="text/css">
            /* css กำหนดความกว้าง ความสูงของแผนที่ */
            #map_canvas { 
                width:100%;
                height:400px;
                margin:auto;
            /*  margin-top:100px;*/
            }
        </style>
    </head>
    
    <body>
        <div class="container">
            <h2>กรอกข้อมูลต้นไม้</h2>
            <br>
            <form action="place_insert.php" method="POST" enctype="multipart/form-data" class="form-horizontal">
                
                <!-- place_id -->
                <input name="place_id" type="hidden" value="<?php echo $row_place2; ?>">

                <!-- owner_name -->
                <div class="form-group">
                    <label for="owner_id" class="col-md-2 control-label">ชื่อเจ้าของต้นไม้ :</label>
                    <div class="col-md-10">
                        <select name="owner_id" id="owner_id" class="form-control" required>
                            <option value="">--ชื่อเจ้าของต้นไม้--</option>
                                
                                    <!-- ดึงข้อมูลจากฐานข้อมูล -->
                                    <?php
                                        while($rowOwner = pg_fetch_row($resOwner))
                                        {
                                            echo "<option value='$rowOwner[0]'>$rowOwner[1]</option>"; 
                                        }
                                    ?>
                                
                        </select>
                    </div>
                </div>
                
                <!-- alphabet -->
                <div class="form-group">
                    <label for="alphabet" class="col-md-2 control-label">ชื่อต้นไม้ :</label>
                    <div class="col-md-10">
                            <select name="alphabet" id="alphabet" class="form-control" required>
                                <option value="">--เลือกตัวอักษร--</option>
                                
                                    <!-- ดึงข้อมูลจากฐานข้อมูล -->
                                    <?php
                                        $sql_alphabet = "SELECT * FROM herb_alphabet";
                                        $res_alphabet = pg_query($db, $sql_alphabet);
                                    
                                        while($row_alphabet = pg_fetch_array($res_alphabet))
                                        {
                                            $alphabet_id = $row_alphabet['alphabet_id'];
                                            $alphabet_th = $row_alphabet['alphabet_th'];
                                            echo "<option value='$alphabet_id'>$alphabet_th</option>";
                                        }
                                    ?>
                                
                            </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="col-md-offset-2 col-md-10">
                        <p class="bg-warning">*** ไม่พบชื่อต้นไม่ กรุณาคลิก <kbd>+ เพิ่มต้นไม้ไม่พบชื่อ</kbd> ***</p>
                        <a href="frm_place_herbname.php" class="btn btn-success">
                            <span class="glyphicon glyphicon-plus"> เพิ่มต้นไม้ไม่พบชื่อ</span>
                        </a>
                    </div>
                </div>
                
                <!-- data_image -->
                <div class="form-group">
                    <label for="place_herbimg" class="col-md-2 control-label">รูปภาพ :</label>
                    <div class="col-md-10">
                        <input type="file" name="place_herbimg" accept="image/*" required>
                    </div>
                </div>
                
                <!-- Map -->
                <div class="form-group">
                    <div class="col-md-offset-2 col-md-10">
                        <div id="map_canvas"></div>
                    </div>
                </div>

                <!-- latitude -->
                <div class="form-group">
                    <label for="place_lat" class="col-md-2 control-label">ละติจูด :</label>
                    <div class="col-md-10">
                        <input name="place_lat" type="text" id="place_herb_lat" value="0" class="form-control" required>
                    </div>
                </div>
                
                
                <!-- longitude -->
                <div class="form-group">
                    <label for="place_lng" class="col-md-2 control-label">ลองติจูด :</label>
                    <div class="col-md-10">
                        <input name="place_lng" type="text" id="place_herb_lng" value="0" class="form-control" required>
                    </div>
                </div>
                
                <!-- googleMap -->
                <script>
                    var map; // กำหนดตัวแปร map ไว้ด้านนอกฟังก์ชัน เพื่อให้สามารถเรียกใช้งาน จากส่วนอื่นได้
                    var GGM; // กำหนดตัวแปร GGM ไว้เก็บ google.maps Object จะได้เรียกใช้งานได้ง่ายขึ้น
                    function initialize() { // ฟังก์ชันแสดงแผนที่
                            GGM = new Object(google.maps); // เก็บตัวแปร google.maps Object ไว้ในตัวแปร GGM
                            // กำหนดจุดเริ่มต้นของแผนที่
                            var my_Latlng  = new GGM.LatLng(14.951142, 102.178896);
                            var my_mapTypeId = GGM.MapTypeId.ROADMAP; // กำหนดรูปแบบแผนที่ที่แสดง
                            // กำหนด DOM object ที่จะเอาแผนที่ไปแสดง ที่นี้คือ div id=map_canvas
                            var my_DivObj = $("#map_canvas")[0];
                            // กำหนด Option ของแผนที่
                            var myOptions = {
                                    zoom: 15, // กำหนดขนาดการ zoom
                                    center: my_Latlng, // กำหนดจุดกึ่งกลาง
                                    mapTypeId: my_mapTypeId // กำหนดรูปแบบแผนที่
                            };
                            map = new GGM.Map(my_DivObj, myOptions);// สร้างแผนที่และเก็บตัวแปรไว้ในชื่อ map

                            // เรียกใช้คุณสมบัติ ระบุตำแหน่ง ของ html 5 ถ้ามี
                            if (navigator.geolocation) {
                                        navigator.geolocation.getCurrentPosition(function (position) {
                                                var pos = new GGM.LatLng(position.coords.latitude, position.coords.longitude);
                                                var infowindow = new GGM.InfoWindow({
                                                        map: map,
                                                        position: pos,
                                                        content: 'คุณอยู่ที่นี่.'
                                                });
                                 
                                                var my_Point = infowindow.getPosition();  // หาตำแหน่งของตัว marker เมื่อกดลากแล้วปล่อย
                                                //map.panTo(my_Point);  // ให้แผนที่แสดงไปที่ตัว marker       
                                                $("#place_herb_lat").val(my_Point.lat());  // เอาค่า latitude ตัว marker แสดงใน textbox id=lat_value
                                                $("#place_herb_lng").val(my_Point.lng()); // เอาค่า longitude ตัว marker แสดงใน textbox id=lon_value        
                                                map.setCenter(pos);
                                        }, function () {
                                                // คำสั่งทำงาน ถ้า ระบบระบุตำแหน่ง geolocation ผิดพลาด หรือไม่ทำงาน
                                        });
                            } else {
                                     // คำสั่งทำงาน ถ้า บราวเซอร์ ไม่สนับสนุน ระบุตำแหน่ง
                            }
                   
                            // กำหนด event ให้กับตัวแผนที่ เมื่อมีการเปลี่ยนแปลงการ zoom
                            GGM.event.addListener(map, 'zoom_changed', function () {
                                    $("#zoom_value").val(map.getZoom()); // เอาขนาด zoom ของแผนที่แสดงใน textbox id=zoom_value  
                            });
                    }
                    $(function () {
                            // โหลด สคริป google map api เมื่อเว็บโหลดเรียบร้อยแล้ว
                            // ค่าตัวแปร ที่ส่งไปในไฟล์ google map api
                            // v=3.2&sensor=false&language=th&callback=initialize
                            //  v เวอร์ชัน่ 3.2
                            //  sensor กำหนดให้สามารถแสดงตำแหน่งทำเปิดแผนที่อยู่ได้ เหมาะสำหรับมือถือ ปกติใช้ false
                            //  language ภาษา th ,en เป็นต้น
                            //  callback ให้เรียกใช้ฟังก์ชันแสดง แผนที่ initialize
                            $("<script/>", {
                                  "type": "text/javascript",
                                  src: "https://maps.googleapis.com/maps/api/js?key=AIzaSyBjSSLVC9Mpi8wMLUoJNb-zSrHzlGkXYPs&callback=initialize"
                            }).appendTo("body");    
                    });
                </script>
        
                <!-- button -->
                <div class="form-group">
                    <div class="col-md-offset-2 col-md-10">
                        <button type="submit" class="btn btn-primary">บันทึก</button>
                        <a href="place_manage.php" class="btn btn-danger">
                            กลับหน้าหลัก
                        </a>
                    </div>
                </div>
                <br><br>
                
            </form>
            
        </div>
    </body>
</html>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Current Weather</title>
        <link rel="stylesheet" href="public/weather.css" type="text/css" /> 
    </head>
    <body>
        <form>
            <input type="hidden" class="formVal" id="lat" name="lat"><br><br>
            <input type="hidden" class="formVal" id="lng" name="lng"><br><br>
            <div id="weather"></div> <br/><br/>
            <input type="button" value="Get Current Weather" onclick="getGeoCords();">
            <input type="button" value="Send SMS" id="send_sms" disabled="disabled" onclick="sendSms();">
        </form>
        
    </body>
    
    <script>
        function formatResponse (responseData) {
            var strucData = '';
            strucData = '<table> <tr> <td> City </td> <td>'+ responseData.name +'</td></tr> ' +
                    '<tr> <td>Weather </td> <td>'+ responseData.weather[0].description +'</td></tr>' +
                    '<tr> <td>Wind Speed </td> <td>'+ responseData.wind.speed +' Km/h</td></tr>' +
                    '<tr> <td>Current Temperature </td> <td>'+ responseData.main.temp +' C</td></tr>' +
                    '<tr> <td>Pressure </td> <td>'+ responseData.main.pressure +' </td></tr>' +
                    '<tr> <td>Humidity </td> <td>'+ responseData.main.humidity +' </td></tr>';
            
            return strucData;
        }
        
         function getWeather() {
            var xhttp = new XMLHttpRequest();
            var elements = document.getElementsByClassName("formVal");
            var formData = new FormData(); 
            formData.append('class', 'Weather');
            formData.append('method', 'getWeather');
            for(var i=0; i<elements.length; i++)
            {
                formData.append(elements[i].name, elements[i].value);
            }
            xhttp.onreadystatechange = function() {
              if (this.readyState == 4 && this.status == 200) {
                  var response = JSON.parse(this.responseText);
                  if(response.hasOwnProperty('error')) {
                      document.getElementById('weather').innerHTML = response.error;
                  } else {
                    document.getElementById('weather').innerHTML = formatResponse(response);
                  }
              }
            };
            xhttp.open("POST", "helper/AjaxHelper.php", true);
            xhttp.send(formData);
        }
        
        function sendSms() {
            var xhttp = new XMLHttpRequest();
            var elements = document.getElementsByClassName("formVal");
            var formData = new FormData(); 
            formData.append('class', 'SmsHelper');
            formData.append('method', 'sendSms');
            for(var i=0; i<elements.length; i++)
            {
                formData.append(elements[i].name, elements[i].value);
            }
            xhttp.onreadystatechange = function() {
              if (this.readyState == 4 && this.status == 200) {
                  var response = JSON.parse(this.responseText);
                 if(response.hasOwnProperty('error')) {
                      document.getElementById('weather').innerHTML = response.error;
                  } else {
                    alert('Sms sent');
                  }
              }
            };
            xhttp.open("POST", "helper/AjaxHelper.php", true);
            xhttp.send(formData);
        }
        
        function getGeoCords() {
            document.getElementById('weather').innerHTML = 'getting weather.......';
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(gotLocation);
            } else {
                document.getElementById('weather').innerHTML = 'Error in getting let and lng';
            }
        }

        function gotLocation (position) {
            var latitude = position.coords.latitude;
            var longitude = position.coords.longitude;
            document.getElementById("lat").value = latitude;
            document.getElementById("lng").value = longitude;
            document.getElementById("send_sms").disabled = false;
            getWeather();
        }
    </script>
</html>
#include <SPI.h>
#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>

int c_i = 0;
String gps_info = "";
String utc = "";
String dval = "";//to check whether data is valid/invalid
String lat = "";//to get current latitute in degrees and minutes
String n_s = "";//to know whether we are heading north or south
String lon = "";//to get the longitude
String e_w = "";//to know whether we are heading east or west
String knot_speed = "";//speed over ground in knots
String cog = "";//to get course over ground value
String date = "";//to get current date in ddmmyy form
HTTPClient http;//Declare an object of class HTTPClient for sending http requests to php page for database updation
int httpCode = 0;//initially no http connection so http code intialized to 0
String httppage;//for sending url of http page
String request;//for http request 
char *ssid = "Connectify-dt"; //  your network SSID (name) 
char *password = "chillout";    // your network password (use for WPA, or use as key for WEP)
void setup() {
  Serial.begin(9600);//Standard baud rate for serial communication between nodemcu and server
  //To connect to wifi network
  Serial.println();//print garbage values if any
  Serial.print("Attempting to connect to ");
  Serial.println(ssid);
  WiFi.mode(WIFI_STA); // <<< Define client as Station so that it doesn't act as server itself
  WiFi.begin(ssid, password);//to begin connection with wifi network/hotspot on laptop
  while(WiFi.status()!=WL_CONNECTED)
  {
    delay(500);//wait at intervals of 500ms or 0.5s
    Serial.print(".");
  }
  Serial.println();//to start printing from newline after connection is successful
  Serial.print("Connected to network:");
  Serial.print(WiFi.SSID());
  Serial.print(" with client ipaddress:");
  Serial.println(WiFi.localIP().toString());//to display network configuration of connected network
}
String get_gps()
{
  String gpss = "";
  if(Serial.available())
  {
    char c = Serial.read();
    while(c!='\n' && c!='\r')
    {
      gpss += String(c);//continue reading till end of line
    }
  }
  return gpss;
}
String get_time_utc()
{
  String res = "";
  c_i = 7;//to search after $GPGGA onwards
  char c = '\0';
  while(c!=',')
  {
    c = gps_info[c_i];
    res += String(c);
    c_i++;
  }
  return res; 
}
String get_latitude()
{
  String res = "";
  c_i += 2;//to skip the comma in between utc and latitude 
  char c = '\0';
  while(c!=',')
  {
    c = gps_info[c_i];
    res += String(c);
    c_i++;
  }
  return res;
}
String get_longitude()
{
  String res = "";
  c_i += 2;//to skip comma 
  char c = '\0';
  while(c!=',')
  {
    c = gps_info[c_i];
    res += String(c);
    c_i++;
  }
  return res;
}
String get_knot_speed()
{
  String res = "";
  c_i += 2;//to skip comma 
  char c = '\0';
  while(c!=',')
  {
    c = gps_info[c_i];
    res += String(c);
    c_i++;
  }
  return res;
}
String get_cog()
{
  String res = "";
  c_i += 2;//to skip comma 
  char c = '\0';
  while(c!=',')
  {
    c = gps_info[c_i];
    res += String(c);
    c_i++;
  }
  return res;
}
String get_date()
{
  String res = "";
  c_i += 2;//to skip comma 
  char c = '\0';
  while(c!=',')
  {
    c = gps_info[c_i];
    res += String(c);
    c_i++;
  }
  return res;
}
/*String update_c_i()
{
  String res = "";
  c_i += 2;//to skip comma 
  char c = '';
  while(c!=',')
  {
    c = gps_info[c_i];
    res += String(c);
    c_i++;
  }
  return res;
}*/
void send_gps_location()
{
  if(WiFi.status()==WL_CONNECTED) 
  { 
    //Check WiFi connection status
    httppage = "http://192.168.87.1/skaipal/send_gps_info.php?latitude=";//Specify request destination 
    request = httppage+lat+"&longitude="+lon;
    http.begin(request); 
    httpCode = http.GET();//Send the request
    if (httpCode > 0) 
    { 
      //Check the returning code
      Serial.println("HTTP Request to server successful. Payload recieved...");
      String payload = http.getString();   //Get the request response payload
      Serial.println(payload);                     //Print the response payload
    }
    else
    {
      Serial.println("HTTPClient GET Request failed");
    }
    http.end();   //Close HTTP connection with server
  }
  else
  {
    Serial.println("WiFi disconnected");       
  }
  delay(500);//wait for 500 ms or 1s
}
void loop() {
  int c_i = 0;
  gps_info = get_gps();
  Serial.println(gps_info);
  if(gps_info && gps_info.substring(0, 6) == "$GPGGA")
  {
    Serial.println(gps_info);
    utc = get_time_utc();
    dval = gps_info[++c_i];
    lat = get_latitude();
    n_s = gps_info[++c_i];
    lon = get_longitude();
    e_w = gps_info[++c_i];
    knot_speed = get_knot_speed();
    cog = get_cog();
    date = get_date();
    send_gps_location();
  }
  else
  {
    Serial.println("Error in reveiving gps data...try again!!!");
  }
  delay(200);
}

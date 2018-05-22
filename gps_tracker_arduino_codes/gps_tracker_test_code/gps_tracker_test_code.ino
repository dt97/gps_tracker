#include <SPI.h>
#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>

String gps_info = "";
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
void loop() {
  gps_info = get_gps();
  if(gps_info && gps_info.substring(0, 6) == "$GPGGA")
  {
    Serial.println(gps_info);
  }
  delay(200);
}

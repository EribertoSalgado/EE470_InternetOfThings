/*
 * ----------------------------------------------
 * Project/Program Name : TwoSensors
 * File Name            : main.cpp
 * Author               : Eriberto Salgado
 * Date                 : 10/17/24
 * Version              : 1.94.2
 * 
 * Purpose:
 *  Connect a two sensors (Hall effect and temperature) to our ESP8266.
 *  Include a push button to send data ONLY when it is HIGH.
 *  The MCU must generate the acual time (Use timeapi.io). The dB must automatically timestamp
 *      if URL does not specify time. User selects time zone through USB.
 *  Data sent must be stored in sensor_data table.
 *  Plot newly received datapoints once insterd into the table.
 *  Average temperature and humidity values.
 *  Include functions: check_switch(), read_time(), read_sensor_1(), read_sensor_2(), transmit(), 
 *      check_error(), etc. 
 * 
 * Inputs:
 *    HallEffect, Temperature, Button
 * 
 * Outputs:
 *    data1
 * 
 * Example Application:
 *  During an experiment the Hall effect sensor has contact with a magnet while the temperature 
 *      sensor is at room temperature. Once we press the button, we record our values in the webpage.
 *      The value for Hall effect will be 0 and temp is 1. This will be sent to the webpage
 *      for plotting and recording values.
 * 
 * Dependencies:
 *    <Arduino.h>, <ESP8266HTTPClient.h>, <ESP8266WiFi.h>, <ArduinoJson.h>, <sendRequest.h>
 * 
 * Usage Notes:
 *    Final Draft
 * 
 * --------
*/
#include <Arduino.h> // Ardiuno functons
#include <ESP8266HTTPClient.h> // Connect to HTTP servers (My Web Page)
#include <ESP8266WiFi.h> // Wifi compatible
#include <ArduinoJson.h>  // JSON compatible
#include <sendRequest.h> // connect to webpage

    // variable definitions
    int HallEffect = 16;      // D0
    int Temperature = 5;  // D1
    int Button = 4;  // D2
    int val1 = 0; // variable to store result
    int val2 = 0;           
    int val3 = 0;

    //Wifi log-in
    String theTime;
    const char* ssid     = "Pokemon Center"; // mst be exactly as it apears - case sensitive
    const char* password = "SalgadoE";

    // Url to get time. API time zones available:
    // https://timeapi.io/documentation/iana-timezones
    String urlTime     = "https://timeapi.io/api/Time/current/zone?timeZone=America/New_York";
    DynamicJsonDocument doc(1024);

    // Url of my webpage to later conactenate extensions
    String urlWeb     = "https://lightpink-sheep-430801.hostingersite.com/singleFilePlot(1).php";
    u_int8_t tempValue = 0;

    // Example of URL
      //https://lightpink-sheep-430801.hostingersite.com/singleFilePlot(1).php?nodeId=node_1&nodeTemp=0&timeReceived=2024-10-21T22:03:01.6409247 or 2001-06-21%2012:00:00
      //String data1   = "?nodeId=node_1&nodeTemp=11&humidity=99";
      //String data2   = "&timeReceived=2022-10-25T20:44:11.4055468";

    // Declare Functions in the global scope OR just add them here
    bool check_switch(); // Function to check if the button is pressed
    String read_time(); 
    int read_sensor1(); // Read Hall value
    int read_sensor2(); // Read Temperature value
    void transmit();

void setup()
{
    // Define pins as INPUT/OUTPUT
    pinMode(HallEffect, INPUT);  
    pinMode(Temperature, INPUT);
    pinMode(Button, INPUT);

    Serial.begin(9600); // Starts the serial communication
    delay(10); // just a short delay
    Serial.println("");
  
    // Start connecting to the WiFI
    Serial.println("Connecting to WiFi"); 
    WiFi.begin(ssid, password); // Connection to WiFi Starts until status()=WL_CONNECTED
    
    while (WiFi.status() != WL_CONNECTED) {
        delay(100);
        Serial.print("."); // waiting to get connected
    }
    Serial.println("Connected to Wifi!");
    Serial.println(WiFi.macAddress());
}

void loop()
{
  if (check_switch()){
    val2 = read_sensor1(); //Hall Effect
    val1 = read_sensor2(); //Temperature
    delay(1000); 

    theTime = read_time(); // timeReceived value
    delay(5000); // delay between each REQUEST to the server

    // Transmit data
    transmit();
  }
}

////////////////Functions/////////////////////

bool check_switch() {
  // Return true if the button is pressed (HIGH)
  if (digitalRead(Button) == HIGH) {
    return true;
  }
  // Otherwise, return false
  return false;
}

String read_time() {
    //wifi connection
  if (WiFi.status() == WL_CONNECTED) {
    WiFiClientSecure client;
    client.setInsecure();
    HTTPClient https;

    String fullUrl = urlTime; // preparing the full URL
    Serial.println("Requesting: --> " + fullUrl);
    if (https.begin(client, fullUrl)) { // start the connection 1=started / 0=failed

      int httpCode = https.GET(); // choose GET or POST method
      //int httpCode = https.POST(fullUrl); // need to include URL
      
      Serial.println("Response code <--: " + String(httpCode)); // print response code: e.g.,:200
      if (httpCode > 0) {
        Serial.println(https.getString()); // this is the content of the get request received
        deserializeJson(doc,https.getString()); // deserialize the JSON file
        /*--- Sample Response ----
        {"year":2022,"month":10,"day":25,"hour":20,"minute":44,"seconds":11,"milliSeconds":405,
        "dateTime":"2022-10-25T20:44:11.4055468","date":"10/25/2022","time":"20:44",
        "timeZone":"America/Los_Angeles","dayOfWeek":"Tuesday","dstActive":true}
        ------------------------ */
        deserializeJson(doc,https.getString()); // deserialize the JSON format into keys and values
        String mytime = doc["dateTime"]; // get the value associated with the dataTime key
        Serial.println(mytime); // soomething like 2022-10-25T21:03:44.1790514
        return mytime;
      }
      https.end(); // end of request
    } else {
        Serial.printf("[HTTPS] Unable to connect\n");
        return "";
    }
    return "";
   }
return "";
}

int read_sensor1(){
  int val = digitalRead(HallEffect); // Read value
  Serial.print("Hall Effect: ");
  Serial.println(val);
  return val;
}

int read_sensor2(){
  int val = digitalRead(Temperature); // Read value
  Serial.print("Temperature: ");
  Serial.println(val);
  return val;
}

void transmit(){
  if (WiFi.status() == WL_CONNECTED) {
    WiFiClientSecure client;
    client.setInsecure();
    HTTPClient https;

    //tempValue = random(0, 100); // generate a random number. For testing only.

    String data1   = "?nodeId=node_1&nodeTemp=" + String(val1)+ "&hall="+ String(val2) +"&timeReceived="+ theTime; 
    String fullUrl = urlWeb + data1; // preparing the full URL
    Serial.println("Requesting: --> " + fullUrl);
    if (https.begin(client, fullUrl)) { // start the connection 1=started / 0=failed

      //int httpCode = https.GET(); // choose GET or POST method
      int httpCode = https.POST(fullUrl); // need to include URL
      
      Serial.println("Response code <--: " + String(httpCode)); // print response code: e.g.,:200
      return;
      if (httpCode > 0) {
        //Serial.println(https.getString()); // this is the content of the get request received
        return;
      }
      https.end(); // end of request
    } else {
        Serial.printf("[HTTPS] Unable to connect\n");
        return;
    }
  }
  delay(5000); // delay between each REQUEST to the server
  return;
}

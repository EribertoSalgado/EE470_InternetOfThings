#include <Arduino.h>
#include <ESP8266HTTPClient.h>
#include <ESP8266WiFi.h>
#include <ArduinoJson.h>

// WiFi log-in
const char* ssid = "<>";
const char* password = "<>";

int Button = 4;  // D2
int LEDPin = 13; // D7
int RGBred = 0;  // D3
int RGBblue = 2; // D4
int RGBgreen = 14; // D5

String textURL = "https://lightpink-sheep-430801.hostingersite.com/results.txt";
String urlRGB = "https://lightpink-sheep-430801.hostingersite.com/tp2.txt";

// Instantiate functions
bool check_switch();
String read_Text();
void read_RGB(int &red, int &green, int &blue); // Update to pass RGB values by reference

StaticJsonDocument<200> doc;  // Define the JSON document size

void setup() {
    Serial.begin(9600);
    pinMode(Button, INPUT);
    pinMode(LEDPin, OUTPUT);
    pinMode(RGBred, OUTPUT);
    pinMode(RGBblue, OUTPUT);
    pinMode(RGBgreen, OUTPUT);

    delay(10);
    Serial.println("Connecting to WiFi"); 
    WiFi.begin(ssid, password);
    
    while (WiFi.status() != WL_CONNECTED) {
        delay(100);
        Serial.print("."); 
    }    
    Serial.println("Connected to WiFi!");
}

void loop() {
    if (check_switch()) {
        String userLEDInput = read_Text();
        if (userLEDInput == "on") {
            digitalWrite(LEDPin, HIGH);
        } else if (userLEDInput == "off") {
            digitalWrite(LEDPin, LOW);
        }
        
        int red, green, blue; // Declare variables to hold RGB values
        read_RGB(red, green, blue); // Call the read_RGB function
        
        // Set the RGB values to the respective pins
        analogWrite(RGBred, red);
        analogWrite(RGBgreen, green);
        analogWrite(RGBblue, blue);
    }
    delay(100); // Add a delay for debouncing
}

bool check_switch() {
    return digitalRead(Button) == HIGH;
}

String read_Text() {
    if (WiFi.status() == WL_CONNECTED) {
        WiFiClientSecure client;
        client.setInsecure();  
        HTTPClient https;

        Serial.println("Requesting: --> " + textURL);
        if (https.begin(client, textURL)) {
            int httpCode = https.GET();
            Serial.println("Response code <--: " + String(httpCode));

            if (httpCode > 0) {
                String text = https.getString();
                Serial.println(text);
                https.end();
                return text;
            } else {
                Serial.printf("[HTTPS] GET request failed, error: %d\n", httpCode);
            }
            https.end();
        } else {
            Serial.println("[HTTPS] Unable to connect");
        }
    } else {
        Serial.println("[WiFi] Not connected");
    }
    return "";  
}

void read_RGB(int &red, int &green, int &blue) { // Use reference parameters to return RGB values
    if (WiFi.status() == WL_CONNECTED) {
        WiFiClientSecure client;
        client.setInsecure();
        HTTPClient https;

        Serial.println("Requesting: --> " + urlRGB);
        if (https.begin(client, urlRGB)) {
            int httpCode = https.GET();
            Serial.println("Response code <--: " + String(httpCode));
            if (httpCode > 0) {
                String payload = https.getString();
                Serial.println(payload); // prints the RGB slider values
                // Deserialize the JSON response
                DeserializationError error = deserializeJson(doc, payload);
                if (!error) {
                    red = doc["Red"];     // Set red value
                    green = doc["Green"]; // Set green value
                    blue = doc["Blue"];   // Set blue value
                    
                    // Print RGB values for debugging
                    Serial.println("RGB Red Value: " + String(red));
                    Serial.println("RGB Green Value: " + String(green));
                    Serial.println("RGB Blue Value: " + String(blue));
                } else {
                    Serial.printf("JSON deserialization failed: %s\n", error.c_str());
                }
            }
            https.end();
        } else {
            Serial.printf("[HTTPS] Unable to connect\n");
        }
    }
}

#include <Arduino.h> // Arduino functions
#include <ESP8266HTTPClient.h> // Connect to HTTP servers (My Web Page)
#include <ESP8266WiFi.h> // Wifi compatible

// Wifi log-in
const char* ssid     = "<>"; // must be exactly as it appears - case sensitive
const char* password = "<>";

int Button = 4;  // D2
int LEDPin = 13; // Pin for LED
String textURL = "https://lightpink-sheep-430801.hostingersite.com/results.txt";

// Instantiate functions
bool check_switch(); // Function to check if the button is pressed
String read_Text();

void setup() {
    Serial.begin(9600); // Starts the serial communication
    pinMode(Button, INPUT);
    pinMode(LEDPin, OUTPUT); // Set LED pin as output
    delay(10); // Just a short delay
    Serial.println("");

    // Start connecting to the WiFi
    Serial.println("Connecting to WiFi"); 
    WiFi.begin(ssid, password); // Connection to WiFi starts until status()=WL_CONNECTED
    while (WiFi.status() != WL_CONNECTED) {
        delay(100);
        Serial.print("."); // Waiting to get connected
    }    
    Serial.println("Connected to WiFi!");
}

void loop() {
    if (check_switch()) {
        String userLEDInput = read_Text();
        if (userLEDInput == "on") {
            digitalWrite(LEDPin, HIGH); // Turn LED on
        } else if (userLEDInput == "off") {
            digitalWrite(LEDPin, LOW); // Turn LED off
        }
    }
}

//////////////////Functions////////////////////
bool check_switch() {
    // Return true if the button is pressed (HIGH)
    if (digitalRead(Button) == HIGH) {
        return true;
    }
    // Otherwise, return false
    return false;
}

String read_Text() {
    // Check WiFi connection
    if (WiFi.status() == WL_CONNECTED) {
        WiFiClientSecure client;
        client.setInsecure();  // Use insecure connection for simplicity (not recommended for production)
        HTTPClient https;

        String fullUrl = textURL;
        Serial.println("Requesting: --> " + fullUrl);

        if (https.begin(client, fullUrl)) {  // Start the HTTPS connection
            int httpCode = https.GET();      // Send the GET request
            Serial.println("Response code <--: " + String(httpCode));

            if (httpCode > 0) {  // Check if request was successful
                String text = https.getString();
                Serial.println(text);  // Print the response content
                https.end();  // Close connection
                return text;
            } else {
                Serial.printf("[HTTPS] GET request failed, error: %d\n", httpCode);
            }

            https.end();  // Close connection
        } else {
            Serial.println("[HTTPS] Unable to connect");
        }
    } else {
        Serial.println("[WiFi] Not connected");
    }

    return "";  // Return empty string if request fails or WiFi not connected
}

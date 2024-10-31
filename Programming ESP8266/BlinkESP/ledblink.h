#ifndef LEDBLINK_H
#define LEDBLINK_H
#include <Arduino.h> //this will show on other sources with this header

// Create a class to manage LED blinking
class Blink {
public:
    Blink(int pin);           // Constructor to initialize the pin number
    void blinkRate(int rate); // Method to set the blinking rate
    void startBlinking();     // Method to start blinking the LED

private:
    int pinNumber; // Store the pin number
    int rate;      // Blinking rate in milliseconds

    // Private method to initialize the LED
    void init();
    void on();   // Method to turn the LED on
    void off();  // Method to turn the LED off
};

#endif

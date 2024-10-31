#include "ledblink.h"

// Initialize the pin
Blink::Blink(int pin) {
    this->pinNumber = pin;
    init(); // Initialize the pin mode
}

// Initialize the class Blink
void Blink::init() {
    pinMode(pinNumber, OUTPUT); // Set the pin as an output
    off(); // Start with the LED off
}

void Blink::on() {
    digitalWrite(pinNumber, HIGH); // Turn the LED on
}

void Blink::off() {
    digitalWrite(pinNumber, LOW); // Turn the LED off
}

void Blink::blinkRate(int rate) {
    this->rate = rate; // Set the blinking rate
}

void Blink::startBlinking() {
    while (true) { // Infinite loop to blink the LED
        on(); // Turn the LED on
        delay(rate); // Wait for the specified rate
        off(); // Turn the LED off
        delay(rate); // Wait for the specified rate
    }
}

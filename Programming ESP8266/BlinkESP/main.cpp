#include <Arduino.h>
#include "ledblink.h"

Blink LED(13); // Create a Blink object for pin 13

void setup() {
    LED.blinkRate(250); // Set the blinking rate to 250 milliseconds
    LED.startBlinking(); // Start blinking the LED
}

void loop() {
    // The loop function is empty because blinking is handled in the startBlinking method
}

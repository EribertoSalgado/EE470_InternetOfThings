#!/usr/bin/env python3
# This prgram checks if the CPU temp is above 40deg Celcius
# use this command to display temp in C: /usr/bin/vcgencmd measure_temp
import os
import time

def measure_temp():
        temp_output = os.popen("/usr/bin/vcgencmd measure_temp").readline()
        temp = float(temp_output.replace("temp=", "").replace("'C\n", ""))
        return temp
while True:
        threshold = 40 # temperature threshold in Celcius
        temp = measure_temp()
        if temp > threshold:
                print(f"Warning: CPU temperature is {temp}°C!")
        time.sleep(1)

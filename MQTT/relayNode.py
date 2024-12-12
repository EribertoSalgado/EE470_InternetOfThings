#relay node that subscribes to the broker
#subscribe to HIVEMQ broker and grab the potentiometer data. Then, oush this 
#data to Hostinger DB.
import paho.mqtt.client as mqtt
import requests
from datetime import datetime

# ThingSpeak settings
THINGSPEAK_API_KEY = '2MBTA9VLPARRBIDL'  # Replace with your ThingSpeak Write API Key
THINGSPEAK_URL = f'https://api.thingspeak.com/update?api_key=2MBTA9VLPARRBIDL&field1=0'

# HiveMQ settings
MQTT_SERVER = "broker.mqtt-dashboard.com"
MQTT_PORT = 1883
MQTT_TOPIC = "testtopic/temp/outTopic/eriberto"

# Callback when connected
def on_connect(client, userdata, flags, rc):
    if rc == 0:
        print("Connected to MQTT broker")
        client.subscribe(MQTT_TOPIC)
    else:
        print(f"Failed to connect, return code {rc}")

# Callback when message is received
def on_message(client, userdata, message):
    try:
        payload = str(message.payload.decode("utf-8"))
        print(f"Received data: {payload}")

        # Process only messages containing potentiometer data
        if "Pot Value:" in payload:
            pot_value = payload.split(":")[1].strip()  # Extract the numeric value
            print(f"Extracted Potentiometer Value: {pot_value}")

            # Send data to ThingSpeak
            response = requests.get(f"{THINGSPEAK_URL}&field1={pot_value}")
            print(f"ThingSpeak response: {response.status_code}")
        else:
            print("Message does not contain potentiometer data. Ignoring.")
    except Exception as e:
        print(f"Error processing message: {e}")

# MQTT Client setup
client = mqtt.Client()
client.on_connect = on_connect
client.on_message = on_message

# Connect to HiveMQ broker
client.connect(MQTT_SERVER, MQTT_PORT, 60)
client.loop_forever()

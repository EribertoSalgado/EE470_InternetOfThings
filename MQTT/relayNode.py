#relay node that subscribes to the broker
#subscribe to HIVEMQ broker and grab the potentiometer data. Then, oush this 
#data to Hostinger DB.
import paho.mqtt.client as mqtt
import requests
from datetime import datetime

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

        # Send data to Hostinger database
        url = "https://lightpink-sheep-430801.hostingersite.com/mqtt.php"
        timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        params = {
            "pot": payload,
            "timestamp": timestamp
        }
        response = requests.get(url, params=params)
        print(f"Database response: {response.status_code}")
    except Exception as e:
        print(f"Error processing message: {e}")

# MQTT Client setup
client = mqtt.Client()
client.on_connect = on_connect
client.on_message = on_message

# Connect to HiveMQ broker
client.connect(MQTT_SERVER, MQTT_PORT, 60)
client.loop_forever()

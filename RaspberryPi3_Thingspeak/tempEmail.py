#!/usr/bin/env python3
# When the CPU temp is greater than 40, send an email.
import smtplib
import os
import time
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart

# function to extract temperature
def measure_temp():
        temp_output = os.popen("/usr/bin/vcgencmd measure_temp").readline()
        temp = float(temp_output.replace("temp=", "").replace("'C\n", ""))
        return temp
def sendAlert():
## Email account credentials
        GMAIL_USER = 'salgadoeIOT@gmail.com'
        GMAIL_PASS = <appPassword>  #READ NOTES BELOW [*] APP PASSWORD

        # Recipient email (sending to yourself in this case)
        recipient_email = 'salgadoeIOT@gmail.com'


        # Email content
        subject = 'YOUR RPI IS ABOVE NOMINAL TEMPERATURE!'
        body = f'Warning: CPU temperature is {temp}°C!'

        # Setting up the message
        msg = MIMEMultipart()
        msg['From'] = GMAIL_USER
        msg['To'] = recipient_email
        msg['Subject'] = subject

        # Attach the message body
        msg.attach(MIMEText(body, 'plain'))

        # Send the email
        try:
                with smtplib.SMTP('smtp.gmail.com', 587) as smtpserver:
                        smtpserver.ehlo()
                        smtpserver.starttls()  # Secure the connection
                        smtpserver.ehlo()
                        smtpserver.login(GMAIL_USER, GMAIL_PASS)  # Log in to your Gmail account
                        smtpserver.sendmail(GMAIL_USER, recipient_email, msg.as_string())  # Send the email
                        print("Email sent successfully!")
        except Exception as e:
                print(f"Failed to send email: {e}")

while True:
        threshold = 40 # temperature threshold in Celcius
        temp = measure_temp()
        print(temp) # prints current temperature
        if temp > threshold:
                sendAlert()
                #print(f"Warning: CPU temperature is {temp}°C!")
        time.sleep(1)

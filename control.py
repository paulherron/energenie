#import the required modules
import RPi.GPIO as GPIO
import datetime
import json
import os
import sys
import time

# set the pins numbering mode
GPIO.setmode(GPIO.BOARD)

# Select the GPIO pins used for the encoder K0-K3 data inputs
GPIO.setup(11, GPIO.OUT)
GPIO.setup(15, GPIO.OUT)
GPIO.setup(16, GPIO.OUT)
GPIO.setup(13, GPIO.OUT)

# Select the signal to select ASK/FSK
GPIO.setup(18, GPIO.OUT)

# Select the signal used to enable/disable the modulator
GPIO.setup(22, GPIO.OUT)

# Disable the modulator by setting CE pin lo
GPIO.output (22, False)

# Set the modulator to ASK for On Off Keying 
# by setting MODSEL pin lo
GPIO.output (18, False)

# Initialise K0-K3 inputs of the encoder to 0000
GPIO.output (11, False)
GPIO.output (15, False)
GPIO.output (16, False)
GPIO.output (13, False)

# The On/Off code pairs correspond to the hand controller codes.
# True = '1', False ='0'

def updateJson(status, switch):
    with open(os.path.dirname(__file__) + '/www/switch-' + switch + '-status.json', 'w') as outfile:
        json.dump({'lastCommand': status, 'lastCommandTime': datetime.datetime.now().isoformat()}, outfile)

if sys.argv[1] not in ('1', '2'):
    print "Specify a switch to control: 1 or 2"
    GPIO.cleanup()
    quit()

if sys.argv[2] == 'on':
    # Set K0-K3
    print "turning socket " + sys.argv[1] + " on"
    GPIO.output (11, True if sys.argv[1] == '1' else False)
    GPIO.output (15, True)
    GPIO.output (16, True)
    GPIO.output (13, True)
    # let it settle, encoder requires this
    time.sleep(0.1)	
    # Enable the modulator
    GPIO.output (22, True)
    # keep enabled for a period
    time.sleep(0.25)
    # Disable the modulator
    GPIO.output (22, False)

    updateJson('on', sys.argv[1]);
else:
    # Set K0-K3
    print "turning socket " + sys.argv[1] + " off"
    GPIO.output (11, True if sys.argv[1] == '1' else False)
    GPIO.output (15, True)
    GPIO.output (16, True)
    GPIO.output (13, False)
    # let it settle, encoder requires this
    time.sleep(0.1)
    # Enable the modulator
    GPIO.output (22, True)
    # keep enabled for a period
    time.sleep(0.25)
    # Disable the modulator
    GPIO.output (22, False)

    updateJson('off', sys.argv[1])

GPIO.cleanup()

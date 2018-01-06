Raspberry Pi (with Espruino) Plant Monitor
==========================================
For the Espruino part see [Espurino-Plant-Monitoring-System](https://github.com/huntlyc/Espurino-Plant-Monitoring-System)

This raspberry pi component is a python program to read the data of the Espruino and send it to a HTTP URL as a POST request.  I've included a PHP example server application that takes this post data and saves it into a file - overwriting the file each time new data arrives.

# Hardware Setup
The setup is pretty straight forward:

* Make sure your pi is interent enabled via lan port / wifi dongle
* Plug the Espruino into a spare USB slot and Bob's your mother's brother.

# Software Setup
You'll need to probably `apt-get install python-serial` at least. For the other requirements, you may have them if you've done any GPIO work on the pi.

If you're using the example php app then you'll need to have some web server somewhere setup.  The code was tested on php 7.0.

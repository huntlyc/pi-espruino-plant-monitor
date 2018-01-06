#!/user/bin/python
import time
import serial
import sys
import json
import re
import urllib
import urllib2
import hashlib

class PlantNetworkSave:
    def __init__(self, url=None, data=None):
        
        if(data != None):
            configData = None

            with open('config.json', 'r') as configFile:
                configData = configFile.read().replace('\n','')
                configData = json.loads(configData)

            
            if(configData != None):
                self.__sendData(configData['url'],data,configData['token'])


    def __sendData(self, data,token):


        h = hashlib.new('sha256')
        h.update(token)

        auth = h.hexdigest()

        data['auth'] = auth

        data = urllib.urlencode(data)

        request = urllib2.Request(self.__url,data=data, headers={"User-Agent":"Magic Browser"})

        response = urllib2.urlopen(request)

        responseContents = response.read();

        print responseContents


class PlantMonitor:
    def __init__(self):
        status = self.__getPlantStatus()

        if(status != ''):
            ns = PlantNetworkSave(data=status)


    def __getPlantStatus(self):
        result = ''

        ser = serial.Serial(port='/dev/ttyACM0',
                            baudrate=9600,
                            parity=serial.PARITY_NONE,
                            stopbits=serial.STOPBITS_ONE,
                            bytesize=serial.EIGHTBITS,
                            xonxoff=0,
                            rtscts=0,
                            dsrdtr=0)

        ser.isOpen()

        ser.write("pm.getSensorJSON(); \n");
        
        endtime = time.time() + 1 

        while time.time() < endtime:
            while ser.inWaiting() > 0:
                result = result + ser.read(1)

        ser.close()

        m = re.search('(\{.*\})', result);

        result = json.loads(m.group(0))

        return result


if __name__ == '__main__':
    pyPlantMon = PlantMonitor();
    exit(1);


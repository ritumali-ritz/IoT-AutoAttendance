import serial
import requests
import time

SERIAL_PORT = 'COM3'  # change as needed
BAUD = 9600
API_URL_ATTEND = 'http://localhost/rfid-attendance/api/insert_attendance.php'
API_URL_SCAN = 'http://localhost/rfid-attendance/api/scan_attendance.php'
CLASS_ID = 3  # change as needed

def main():
    try:
        ser = serial.Serial(SERIAL_PORT, BAUD, timeout=1)
        print(f'Listening on {SERIAL_PORT}...')
    except Exception as e:
        print(f'Serial not available: {e}')
        return

    while True:
        try:
            line = ser.readline().decode('utf-8').strip()
            if not line:
                time.sleep(0.1)
                continue

            # If the serial line contains a pipe '|' assume it's roll|class and send attendance.
            # Otherwise treat the line as a scanned UID and insert into scans table.
            if '|' in line:
                parts = line.split('|')
                roll = parts[0].strip()
                cid = int(parts[1]) if len(parts) > 1 else CLASS_ID
                payload = {'roll_no': roll, 'class_id': cid}
                try:
                    r = requests.post(API_URL_ATTEND, json=payload, timeout=5)
                    print('Attendance Sent', payload, '->', r.status_code, r.text)
                except Exception as e:
                    print('Error sending attendance to server:', e)
            else:
                uid = line.strip()
                payload = {'rfid_uid': uid}
                try:
                    r = requests.post(API_URL_SCAN, json=payload, timeout=5)
                    print('Scan Sent', payload, '->', r.status_code, r.text)
                except Exception as e:
                    print('Error sending scan to server:', e)

        except Exception as e:
            print('Error reading serial data:', e)

if __name__ == '__main__':
    main()

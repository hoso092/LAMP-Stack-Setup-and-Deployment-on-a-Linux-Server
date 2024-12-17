# ATW
# LAMP Stack Setup and Deployment on a Linux Server

1. ### Step 1: Launching the EC2 Instance with Python

To start, I used a **Python script** with Boto3 to launch an EC2 instance. I established a session with AWS, specified the **region**, **instance type**, **key pair**, **Imageid** (ubuntu image 22), and then created the instance. Here's the code I used:

```python
import pprint
import boto3
try:
    session = boto3.session.Session(profile_name='default')
    client_obj = session.client(service_name='ec2', region_name='us-east-1')
    result = client_obj.run_instances(ImageId='ami-0e2c8caa4b6378d8c', InstanceType='t2.micro',
                                      KeyName='myfirstkeypair', MaxCount=1, MinCount=1)

    pprint.pprint(result)
except Exception as e:
    print(e)

    # Launch an EC2 instance
    result = client_obj.run_instances(
        ImageId='ami-0e2c8caa4b6378d8c',  # Replace with your chosen AMI ID
        InstanceType='t2.micro',
        KeyName='myfirstkeypair',        # Replace with your key pair name
        MaxCount=1,
        MinCount=1
    )

    # Print instance details to confirm
    pprint.pprint(result)

except Exception as e:
    print(e)
```
What’s happening here is:
I create a session with AWS using my default profile.
I call run_instances() to spin up an EC2 instance with the specified configuration.
Finally, I use pprint to display the details of the created instance.

2. ### Step 2: Verifying the EC2 Instance and Configuring Access
Once the script ran successfully, I logged into the AWS Management Console to verify the instance was created.
Here's what I did next:
Check the Instance:

I looked under the EC2 Dashboard to confirm the instance is running.
Configure the Security Group:

I updated the Inbound Rules in the security group to allow traffic:
HTTP (Port 80): I allowed access from all IPs `(0.0.0.0/0)` to make the website publicly accessible.
ICMP Traffic: I added a rule to allow ICMP (ping) requests so I could test connectivity later.
Attach Public Route Table and Subnet:

I ensured the instance was attached to a public route table and placed in a public subnet that I had created earlier.
This allows the instance to have a public IP and access the internet.

```bash
ping <ec2-public-ip>
```

3. ### Step 3: Configure the Ubuntu Machine to Host the Apache Server

To begin configuring the Ubuntu machine to host the Apache server, follow these steps:

## 1. Update the Package List:
Before installing any software, I updated the package list to ensure I was getting the latest version of the software:

```bash
sudo apt update
```
## 2. Install Apache2:
Next, I installed the Apache2 web server using the following command:

bash
```
sudo apt install apache2 -y
```
## 3. Enable Apache2 Service:
After installation, I enabled the Apache2 service to start automatically on boot:

bash
```
sudo systemctl enable apache2
```
## 4. Start the Apache2 Service:
Then, I started the Apache2 service with the following command:
bash
```
sudo systemctl start apache2
```
## 5. Verify Apache2 Status:
To confirm that the Apache2 server is running, I checked the status of the Apache2 service: 
bash
```
sudo systemctl status apache2
```
This will show whether the Apache server is active and running.

## 6. Access Apache2 Web Page:
Now that the Apache server is installed and running, I used the public IP assigned to the EC2 instance to visit the Apache default web page. This page is accessible through a browser at the following URL:

**http://<my-ec2-public-ip>**
should see the Apache2 Ubuntu default page.

By default, Apache2 creates an index.html page under /var/www/html/. If you want to modify this page to customize the content, you can edit it using the following command:

bash
```
sudo nano /var/www/html/index.html
```

### Step 4: Install MySQL Database and Create a User

In this step, I installed MySQL on the server, secured the installation, and created a database with a user.

## 1. Install MySQL Server and PHP Packages:
To begin, I installed the MySQL server along with necessary PHP packages to enable MySQL support in PHP. 
I used the following command:

```bash
sudo apt install mysql-server php-mysql -y
```
## 2. Secure Mysql connection:
Once MySQL was installed, I ran the mysql_secure_installation command to improve the security of the MySQL installation. This command helps configure various settings such as the root password, remove insecure default settings, and set up basic MySQL server security.

bash
```
sudo mysql_secure_installation
```
During this process, I set a root password, removed the test database, and disallowed remote root login.
## 3. Log in to MySQL as Root:
After securing MySQL, I logged into the MySQL shell as the root user to manage the database:

bash
```
sudo mysql -u root -p
```
## 4. Create a New Database and User:
Once logged into the MySQL shell, I created a new database and a new user with a password. I used the following SQL commands:

sql
```
-- Create a new database
CREATE DATABASE web_db;

-- Create a new user
CREATE USER 'web_user'@'localhost' IDENTIFIED BY 'StrongPassword123';

-- Grant all privileges on the database to the new user
GRANT ALL PRIVILEGES ON web_db.* TO 'web_user'@'localhost';

-- Apply the changes
FLUSH PRIVILEGES;
```
web_db: This is the name of the new database I created.
web_user: This is the username for the new user.
StrongPassword123: This is the password for the new user (you should replace it with a secure password).

## 5. Exit MySQL:
After creating the database and user, I exited the MySQL shell:

sql
```
EXIT;
```

### Step 5: Edit the Website to Show the Current Time and Visitor's IP Address

In this step, I edited the website to display both the visitor’s IP address and the current server time. 
This was achieved by modifying the `index.php` (rename index.html to index.php) file on the server.

## 1. Edit the `index.php` File:
I started by editing the `index.php` file located in `/var/www/html/` to include PHP code that retrieves and displays the current time and the visitor’s IP address.

To do this, I opened the `index.php` file for editing:

```bash
sudo nano /var/www/html/index.php
```
The webpage code attached to the file in the repository 

### Step 6: Assign an Elastic IP to the EC2 Instance for Long-Term Use and Attach it to a Domain Name

In this step, I assigned an **Elastic IP** (EIP) to my EC2 instance to ensure that it retains a static public IP address,
even if the instance is stopped and started. I also attached this Elastic IP to a custom domain name.

## 1. Allocate an Elastic IP in AWS Management Console:
- First, I logged into the **AWS Management Console** and navigated to the **EC2 Dashboard**.
- Under the **Network & Security** section, I clicked on **Elastic IPs**.
- I then clicked on the **Allocate Elastic IP address** button to request a new static IP address.
- After allocation, AWS provided me with a new Elastic IP address: **54.83.106.68**.

## 2. Associate the Elastic IP with the EC2 Instance:
- Once the Elastic IP was allocated, I selected the newly allocated IP from the **Elastic IPs** list in the AWS Management Console.
- I clicked the **Actions** button and selected **Associate Elastic IP address**.
- In the **Instance field**, I selected the EC2 instance I wanted to associate the IP with.
- I clicked **Associate** to attach the Elastic IP to my EC2 instance.

## 3. Update DNS Settings to Attach the Elastic IP to a Domain Name:
To associate the Elastic IP with a domain name (`myapacheserver.online`), I followed these steps:
- I logged into my **DNS provider's dashboard** (in this case was Hostinger).
- I created or modified the **A record** for the domain `myapacheserver.online` to point to the Elastic IP **54.83.106.68**.
- After saving the DNS settings, the domain `myapacheserver.online` should now resolve to the Elastic IP address.

## 4. Test the Domain:
Once the DNS changes propagated (which may take a few minutes),
I tested the configuration by opening a web browser and navigating to: 
http://myapacheserver.online/
if not work try this IP may Hostinger DNS (Paid Sevice as aws Route 53 Paied) didn't work yet as mentioned that takes 48 hrs max:
Elastic IP `54.83.106.68`.
should show the webpage 




------------------------------------
### Networking Basics
## 1. IP Address
A unique identifier for devices in a network.
Purpose: Allows communication between devices (like a postal address).
Example: 54.83.106.68 (my EC2 public IP).
## 2. MAC Address
A hardware-encoded address unique to a device's NIC.
Purpose: Identifies devices on a local network.
Difference:
IP Address: Logical, can change.
MAC Address: Physical, fixed.
Example: 00:1A:2B:3C:4D:5E.
## 3. Switches, Routers, and Routing Protocols
Switch: Connects devices within a LAN using MAC addresses.
Router: Connects networks, forwards data using IP addresses.
Routing Protocols: Define how routers communicate (e.g., OSPF, BGP).
## 4.Run the following command in my **keypair** to change permission file 
bash 
```
chmod 400 mykey.pem
```
then aws management console then security groups Open SSH Port (22) in Security Group
In the AWS Management Console:
Go to the EC2 Dashboard.
Select your instance.
Go to Security Groups > Inbound Rules.
Add a rule to allow SSH traffic:
Type: SSH
Protocol: TCP
Port Range: 22
Source:** 0.0.0.0/0**

then in console terminal use this command:
bash
```
ssh -i "mykey.pem" ubuntu@54.83.106.68
```
my repo link :**[https://github.com/hoso092/ATW](https://github.com/hoso092/LAMP-Stack-Setup-and-Deployment-on-a-Linux-Server/tree/main)**


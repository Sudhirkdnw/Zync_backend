Zync Backend - Docs        

documentation for

* * *

Main

*   [Introduction](#introduction)
*   [Setup database](#setup_database)
*   [Setup backend](#setup_backend)
*   [Cron jobs](#cronjobs)
*   [Setup Agora](#setup_agora)
*   [Setup Notification (FCM)](#setup_fcm)
*   [Setup Firebase Config](#setup_firebase_config)
*   [Sightengine](#sightengine)
*   [Storage setting](#storage_setting_aws_do)
*   [Host your backend](#host_your_backend)
*   [Getting the credential](#getting_the_credential)
*   [Update info](#update_info)

Zync
====

The Ultimate Flutter Social Media

*   [Backend](https://docs.retrytech.com/Zync/Zync_backend)
*   [Flutter](https://docs.retrytech.com/Zync/Zync_flutter)

Zync
----

##### The Ultimate Flutter Social Media

Zync app is an ultimate social media script built with flutter and Laravel as backend. It offers many advanced features and functions like Feed (Images, Videos, Text), Stories, Chat, Chat Rooms, Manage Chat Rooms & Members, Random Profiles, Interests, Push Notifications And Lot more... This package contains fully functional Zync flutter app, Backend, Database file & documentation. Using this script, any individual or company can save 100s of hours and publish twitter like social media app within couple of hours.

##### Version Details

dart: ">=3.7.0 < 4.0.0 "  
flutter: ">=3.27.0"  
laravel/framework: "^9.0"

##### Developer Friendly

Specially built for developers to give them freedom while coding.

##### Clean Code

Less bulky and clean code with Getx flutter state management.

##### Laravel/Flutter

A great combination of techstacks, trending now in market.

##### Well Documented

Comes with nice documentation to help you get started fast and ASAP.

##### Continuous Updates

Comes with continuous updates to keep you safe from exploitable holes

##### Realtime Chat

Realtime chats between users regarding properties powered by Firestore.

##### Multiple Languages

The App Comes with multiple languages (More than 20). RTL supported.

##### Powerful Admin Panel

Admin dashboard to manage the data of the app and control them.

##### Attractive UI/UX

Amazing UI/UX designed and developed by world class designers.

##### Active Support

Our support and developer team helps you solve any issues during installation.

Prerequisites
=============

*   VPS with cPanel is recommended
    
*   PHP 8.0
    
*   Hosting with cPanel (Strictly Recommended)
    
*   memory\_limit should be 500M
    
*   upload\_max\_filesize : 500M
    
*   post\_max\_size : 500M
    
*   max\_input\_time : 60
    
*   Firebase Blaze Plan
    
*   sightengine Account credential for Content Moderation : [https://sightengine.com/](https://sightengine.com/)
    

Extracting the project and settings up database
===============================================

*   Extract the folder you have downloaded from codecanyon and open the folder
    
*   Open it and find the Zync\_database.sql file
    
*   Then open your cPanel provided by your web hosting provider.
    
*   Click the MySQLDatabase option under DATABASES section and create database for your app as shown in the images below.
    

![db_1](https://docs.retrytech.com/asset/img/doctor/db_1.png)

*   Add database name and click on create database.
    

![db_1](https://docs.retrytech.com/asset/img/doctor/db_2.png)

*   Now we will create a user to access the database
    
*   Like as shown in the image, enter username and password (save it to use later)
    
*   Click on create user and user will be created
    

![db_1](https://docs.retrytech.com/asset/img/doctor/db_3.png)

*   Now we have to add that created user to database. so scroll down and you can see like image below
    
*   Select the user from the list and select the database, and click on add button below
    

![db_1](https://docs.retrytech.com/asset/img/doctor/db_4.png)

*   After clicking on add button you will see like below.
    
*   There you can see **ALL PRIVILEGES** option, check the box before that
    
*   This will tick all the boxes shown below (Check the image below). Click on make changes and you are done.
    

![db_1](https://docs.retrytech.com/asset/img/doctor/db_5.png)

*   Now we have to import the database file. Search for **phpMyAdmin** and click on it.
    

![db_1](https://docs.retrytech.com/asset/img/doctor/db_8.png)

*   Then system will redirect you to the phpMyAdmin where you will be able to find the database we just created
    
*   Click on that database
    
*   Then find the import button on the Top bar and click on it
    

![db_1](https://docs.retrytech.com/asset/img/doctor/db_6.png)

*   It will open the page like below
    
*   Click on **Choose File** button and load the database.sql file which will be there in the folder you have extracted.
    

![db_1](https://docs.retrytech.com/asset/img/doctor/db_7.png)

*   Click on go button at the bottom of the page.
    
*   Now database is ready to use.
    

Setting Up Database credentials to the project
==============================================

*   Now come back to the project folder and then extract **Zync\_backend.zip** file
    
*   In that folder, find the **.env** file, open it with any text editor and make the changes as below
    
*   There in the **APP\_URL** Replace the **https://yourdomain.com/** to Your Domain
    
*   Change Database Configuration, as shown in the example below
    

![db_1](https://docs.retrytech.com/asset/img/doctor/backend_1.png)

**DB\_DATABASE** \= database\_name

**DB\_USERNAME** \= database\_username

**DB\_PASSWORD** \= database\_password

*   And save the file by pressing **Ctrl + s**
    

Cron Jobs
=========

*   Search the Cron jobs in your cPanel Tools search.
    

![cronjobs_1](https://docs.retrytech.com/asset/img/cronjobs_1.png)

![cronjobs_2](https://docs.retrytech.com/asset/img/cronjobs_2.png)

*   Now Add your **curl --request GET 'https://yourdomain.com/deleteStoryFromWeb'** in Command field like as above image.
    
*   And then Click on **"Add New Cron Job"** Button.
    

![cronjobs_2](https://docs.retrytech.com/asset/img/cronjobs_3.png)

*   Your Cron Job is ready to work.
    

Setting up agora
================

*   Follow [This guide](https://docs.retrytech.com/agora_project_setup) and setup project at agora and collect **App id** and **App Certificate**
    
*   Add that **App id** and **App Certificate** to the **.env** file as show below.
    

![db_1](https://docs.retrytech.com/asset/img/doctor/agora_6.png)

*   Save the file by pressing **Ctrl + s**
    

Setup Notification (FCM)
========================

*   Follow [This guide](https://docs.retrytech.com/firebase_fcm_setup_latest) and setup project at firebase and collect private key **.json**
    
*   Open private key **.json** file in your text editor.
    
*   Copy content of private key **.json** file from your text editor.
    
*   Find the **googleCredentials.json** file in your backend folder and paste the content in that file.
    

Setup Firebase Config
=====================

*   Now go back to the **Project Overview** page in Firebase and follow the steps below carefully.
    

![firebase_config_1](https://docs.retrytech.com/asset/img/firebase_config_1.png)

*   Click on **\+ Add app**
    

![firebase_config_2](https://docs.retrytech.com/asset/img/firebase_config_2.png)

*   Click on **Web**
    

![firebase_config_3](https://docs.retrytech.com/asset/img/firebase_config_3.png)

*   Enter your app name
    
*   Click on **Register app**
    

![firebase_config_4](https://docs.retrytech.com/asset/img/firebase_config_4.png)

*   After clicking on **Register App** , you will see the **Add Firebase SDK** option.
    
*   Now, you need to copy only the **firebaseConfig part** , which is highlighted in red.
    
*   Please handle the copied part carefully.
    
*   Now, open your backend project and locate the file **firebase-init.js** .
    
*   which is placed at: **public\\asset\\script\\firebase-init.js**
    
*   Replace only the copied **firebaseConfig** part in that file with the new one.
    
*   For more details, please refer to the image shown below.
    

![firebase_config_5](https://docs.retrytech.com/asset/img/firebase_config_5.png)

*   And **CTRL + S** save the file.
    

sightengine (Optional)
======================

*   Sign up at [sightengine](https://sightengine.com/)
    
*   See Below image for getting API Keys **(API User & API secret)**
    

![sightengine_1](https://docs.retrytech.com/asset/img/sightengine_1.png)

* * *

*   ##### **Image**
    

![sightengine_2](https://docs.retrytech.com/asset/img/sightengine_2.png)

![sightengine_3](https://docs.retrytech.com/asset/img/sightengine_3.png)

![sightengine_4](https://docs.retrytech.com/asset/img/sightengine_4.png)

*   You will be able to select the rules that should be applied, and what actions should be taken based on those rules: **ACCEPT** or **REJECT.**
    

![sightengine_5](https://docs.retrytech.com/asset/img/sightengine_5.png)

*   When you click on **SAVE WORKFLOW** , Then you will get your **Workflow id** for **image**
    

![sightengine_6](https://docs.retrytech.com/asset/img/sightengine_6.png)

* * *

*   ##### **Video**
    

![sightengine_2](https://docs.retrytech.com/asset/img/sightengine_7.png)

![sightengine_3](https://docs.retrytech.com/asset/img/sightengine_8.png)

![sightengine_9](https://docs.retrytech.com/asset/img/sightengine_9.png)

*   You will be able to select the rules that should be applied, and what actions should be taken based on those rules: **ACCEPT** or **REJECT.**
    

*   When you click on **SAVE WORKFLOW** , Then you will get your **Workflow id** for video, Like below image
    

![sightengine_10](https://docs.retrytech.com/asset/img/sightengine_10.png)

* * *

*   Copy all 4 things, **API Key, API Secret, Image Workflow id, Video Workflow id**
    
*   Now Go to **Admin panel** and open **setting page.**
    
*   Now you will find sightengine section in setting page.
    
*   Keep **switch on** and **paste** them all like below image and save them
    

![sightengine_11](https://docs.retrytech.com/asset/img/sightengine_11.png)

*   Sightengine is now working in your app.
    

Storage setting (Optional)
==========================

*   ##### **AWS S3**
    

*   Follow [This guide](https://docs.retrytech.com/s3_bucket) and create AWS S3 Bucket and get API Keys and other credentials.
    
*   Once you collected all credentials, open .env file and paste those credentials. you will find something like below.
    

![](https://docs.retrytech.com/asset/img/aws_configuration.png)

* * *

*   ##### **DigitalOcean**
    

*   Follow [This guide](https://docs.retrytech.com/digital_ocean_space) and create Digital Ocean Space Bucket and get API Keys and other credentials.
    
*   Once you collected all credentials, open .env file and paste those credentials. you will find something like below.
    

![](https://docs.retrytech.com/asset/img/digital_ocean_configuration.png)

* * *

![storage_setting](https://docs.retrytech.com/asset/img/storage_setting.png)

*   Once you have set the credentials for required storage providers, You can set the required one here on the settings page. Only one at a time works. (So yes, You have to set the credentials only for that which you want to use.)
    
*   Local storage doesn't require any credentials.
    
*   Storage will work fine only if you have set the credentials correctly and made required configurations on their dashboards.
    

host your backend
=================

*   Create a zip of the **Zync\_backend** folder
    
*   Upload that folder to your domain-targeted directory on your cPanel provided by your hosting provider. and extract the zip file.
    
*   Make sure that your targeted directory has the project files directly, and not wrapped in a folder
    
*   Cheers! Now try accessing your domain, the admin panel should be live there on your domain
    

Getting the credential
======================

Now web setup is completed and let's collect some credentials for the app setup

*   **Admin Panel URL :** http://yourdomain.com/
    
*   **Admin Panel User Name :** admin
    
*   **Admin Panel Password :** admin123
    
*   **Baseurl :** http://yourdomain.com/api/
    
*   **apiKey :** 123
    

Now save these credentials somewhere and start following the documentation to setup application.

Update info
===========

*   To upgrade your project, you have to **Add/Update/Remove files** and **fields in database** . Please check below file for the information.
    
*   File name : **README.md**
    
*   Please be careful while making an updates this way, It might result in errors sometimes.
    
*   If it comes any issues while making an update, you are absolutely responsible for that since updating existing project is not included in the support.
    

Want to talk with us?
---------------------

*   Telegram (Support Desk) : [+91 7070799200](https://t.me/+917070799200)
*   Email : [sudhirkdnw@gmail.com](mailto:sudhirkdnw@gmail.com)
*   Whatsapp (No Support Here) : [+91 7070799200](https://wa.me/7070799200)


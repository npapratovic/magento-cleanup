# magento-cleanup
Magento db &amp; files Maintenance Script

#Instructions: 

Place the following script inside the root direcotory of your magento

In the Add Cron Job section (cpanel), select Once a day from the Common Settings dropdown list. 

In the Command field, enter the following line of code:

curl -s -o /dev/null http://yourwebsite.com/magento-cleanup.php?clean=log

and for files cleaning:

curl -s -o /dev/null http://yourwebsite.com/magento-cleanup.php?clean=var

It's a good idea to set the Email Address to something other than your username, 
otherwise your mail/new directory will fill up very quickly every time a cron job runs 
(assuming it produces output). You can leave it blank or use an actual email address.

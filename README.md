# chocolate-php

###### This project is to help php programmers have an easy time coding by having ready made functions for common and vital php operations

### Below is a list of codes that are present in this reposittory or folder


### fancy_date.php
This contain a function used to convert UNIX timestamp to very nice social network-type time format e.g. "5 minutes ago"  "an hour ago"

### mycrypt.php
This contain functions used to encrypt and decrypt a string using your own secret key and salt . It  does not require the installation of openssl.

### openssl_crypt.php
This contain functions used to encrypt and decrypt a string using your own secret key . It  will require that you have the openssl php extension 

### permalink.php
This contain fucntions that converts strings  to equivalent url format. e.g. How to write a blog post  will become how-to-write-a-blog-post.. It allows  only numbers and letters in the url while it removes every other character.More so, you can also change the url format back to a domain name, it removes the '-' and replaces it with space,just change the $mth variable with 'up' and 'down'

### php_mail.php
This contain functions  that help you to encrypt and decrypt a string using your own secret key and salt . It  does not require the installation of openssl.

### short_paragraph.php
This contain a function can shorten a block of text to a specific number of paragraphs as specified by you.

### word_check.php
This contain a fucntion that can check if a word is present in a block of text, I use it to auto-tag articles by checking the articles against some words

### word_short.php
This comtain s function that reduces the number of words in a string

**The functions are stored in individual files but I suggest you make use of the class file ( **cholocate.php** ) by inclusion  as it includes all the functions in a place**

**Endeavour to open each file as they contain each set functions for a certain type of operation**

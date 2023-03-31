**DATABASE CHANGES**

```
CREATE TABLE `users` (
    id INT AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL,
	password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	UNIQUE KEY (`name`)
);

CREATE TABLE `groups` (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE `group_members` (
    id INT PRIMARY KEY AUTO_INCREMENT,
    group_id INT NOT NULL,
    user_id INT NOT NULL,
	FOREIGN KEY (group_id) REFERENCES `groups`(id),
    FOREIGN KEY (user_id) REFERENCES `users`(id)
);

CREATE TABLE `group_messages` (
    id INT PRIMARY KEY AUTO_INCREMENT,
    group_id INT NOT NULL,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES `groups`(id),
    FOREIGN KEY (user_id) REFERENCES `users`(id)
);

CREATE TABLE `message_likes` (
    id INT PRIMARY KEY AUTO_INCREMENT,
    group_id INT NOT NULL,
    user_id INT NOT NULL,
    message_id INT NOT NULL,
	is_like INT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES `groups`(id),
    FOREIGN KEY (user_id) REFERENCES `users`(id),
	FOREIGN KEY (message_id) REFERENCES `group_messages`(id)
);

```

**HOW TO CALL WEB SERVICES DEMO**

```
=======================Admin API

//Create User
URL - localhost/api/admin.php
Method - POST
RAW DATA - {
    "name" : "TEST",
    "email" : "m@gmail.com",
    "password" : "Manik@123",
    "is_admin" : 1
}

//Get users
URL - localhost/api/admin.php
METHOD - Get

//Update DATA
URL - localhost/api/admin.php?id=1
Method - PUT
RAW DATA - 
{
"name" : "TEST",
}

//Delete User
URL - localhost/api/admin.php?id=1
Method - Delete

========================Authentication API
//Login API
URL - localhost/api/authentication.php
METHOD - POST
RAW DATA - 
{
    "username" : "rahul",
    "password" : "Rahul@123"
}

//Logput API

URL - localhost/api/authentication.php?logout=1
METHOD - POST

======================GROUP API

//CREATE GROUP
URL - localhost/api/groups.php
METHOD - POST
RAW DATA - 
{
    "name" : "TEST Group"
}

//Get groups
URL - localhost/api/groups.php
METHOD - GET

//Get groups starting with particular string name - 
URL - localhost/api/groups.php?q=test
METHOD - GET

//Create Group Members
URL - localhost/api/groups.php?id=1--- Here id is the group id
METHOD - POST
RAW DATA - 
{
    "user_id" : "1"
}


/Delete Group
URL - localhost/api/groups.php?id=13
METHOD - DELETE



========================== Group messages and Message Likes (User needs to be login to add messages or likes)
//Adding message in a group

URL - localhost/api/group-message.php?id=1--- Here id is the group id
METHOD - POST
RAW DATA - 
{
    "message" : "test message"
}


//Like a message
URL - localhost/api/group-message.php?id=1&message_id=1--- Here id is the group id
METHOD - POST
RAW DATA - 
{
	"like" : "1"
}


//Get all likes to a group
URL - localhost/api/group-message.php?id=1
METHOD - GET
```

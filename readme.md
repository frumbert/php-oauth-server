# oAuth2 Super Basic Example

This project is a back-end of an oAuth 2 provider (implemented using league/oauth2-server) that provides enough to get going and no more.

It uses a database to store users and clients only. Scopes and refresh tokens aren't implemented yet.

## database

I'm sure you could do better. Passwords are just password_hash(). 

```sql
-- Create syntax for TABLE 'oauth_clients'
CREATE TABLE `oauth_clients` (
  `id` varchar(80) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `secret` varchar(100) DEFAULT NULL,
  `redirect_uri` text DEFAULT NULL,
  `is_confidential` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `oauth_clients` (`id`, `name`, `secret`, `redirect_uri`, `is_confidential`)
VALUES
	('my-client', 'App-1', '$2y$10$yF8wZpQEK04.igxrPS7mMuR/mHZjcHHggWzLG7gc6IlYvKkg2pLiO', 'http://localhost:8081/oauth2callback.php', '1');

-- Create syntax for TABLE 'users'
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `title` varchar(10) DEFAULT NULL,
  `customerid` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`username`, `password`, `email`, `firstname`, `lastname`, `title`, `customerid`)
VALUES
	('alice', '$2y$10$Qdi0OCwnXaXtAMWVVf.8n.IT3SwwNXB/PGVrE8OyW2LOqne/jZl92', 'alice@example.com', 'Alice', 'Smith', 'Ms.', '1864001'); 

```


## private, public keys

These are in the /private directory. I just generated these using:

```sh
mkdir private
openssl genrsa -out private/private.key 4096
openssl rsa -in private/private.key -pubout -out private/public.key
```

## environment variables

Just have these in my servers environment variables for easy editing.

`OAUTH2_ENCRYPTION_KEY` - just did this with `php -r "echo 'base64:'.base64_encode(random_bytes(32)),PHP_EOL;"

`DB_NAME` - the name of the database

`DB_HOST` - etc - you get it

`DB_USER`

`DB_PASS`

## getting set up

1. Run the private and public keys
2. Set up your database and environment variables
3. composer update to get the dependencies
4. ...
5. Profit!

## Licence

MIT
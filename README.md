# Basic PHP API - README

Welcome to the Basic PHP API! This guide will help you set up and run the application using Docker.

## Getting Started

### Prerequisites

- Docker installed on your machine
- Docker Compose installed on your machine

### Building the Docker Image

To begin, you need to build the Docker image. Run the following command in your terminal:

```bash
docker build -t basic-php-api .

```

Starting the Docker Containers
Once the Docker image is built, you can start the Docker containers using Docker Compose. Run the following command in your terminal:

```bash
docker stack deploy -c docker-compose.yaml basic-api
```
or
```bash
docker-compose up
```

This will start up the necessary Docker containers, execute migrations, and create mock data in the database.

### Accessing the API
Finding Login Credentials
To log in to the system, you will need the default username and password. These can be found in the file located at:

```bash
/api/src/DataFixtures/OrdersAndProductsFixtures.php
```

By default, the credentials are:
```json
{
  "Username": "test",
  "Password": "test321!"
}

```
Obtaining an Access Token
To fully utilize the API, you need to obtain an access token. Make a POST request to the following URL:

http://localhost:9080/api/login_check

Include the username and password in the body of the request.

Upon successful authentication, you will receive an access token which you can use to access the protected endpoints of the API.


### Unit test

To run the unit test for Orders Controller, bash in php docker service with
```bash
docker exec -it {service_name} bash
```

run the command

```bash
vendor/bin/phpunit tests/Controller/OrdersControllerTest.php
```





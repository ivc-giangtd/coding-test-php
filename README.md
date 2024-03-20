## Get Started

This guide will walk you through the steps needed to get this project up and running on your local machine.

### Prerequisites

Before you begin, ensure you have the following installed:
- Postman
- Docker
- Docker Compose

### Building the Docker Environment
In case the docker already run once, need to run following command first

```
docker-compose down --volumes
```

Build and start the containers:

```
docker-compose up -d --build
```

### Installing Dependencies

```
docker-compose exec app sh
composer install
chmod -R 777 tmp/
```

### Database Setup

Set up the database:

```
bin/cake migrations migrate
```
Run seed to generate data for table `users`
```
bin/cake migrations seed
```

### Accessing the Application

The application should now be accessible at http://localhost:34251

## How to check

### Unit Test
To run unit test, use following command in the root application:
```
vendor/bin/phpunit  tests/TestCase/Controller/Api/
```
After running, the test should be alike the below result
```
Time: 01:11.804, Memory: 20.00 MB

There was 1 warning:

1) Warning
No tests found in class "App\Test\TestCase\Controller\Api\BaseApiControllerTest".

WARNINGS!
Tests: 24, Assertions: 134, Warnings: 1.
```

All above unit tests will cover below guideline of "how to check".
____

### Guideline

This guideline is made for testing using Postman: https://www.postman.com
- Assuming you installed Postman and logged in already.
- Import the collection to Postman to get all requests using json file: ~REPOSITORY_NAME/postman_collection/Cake PHP.postman_collection.json

### Authentication

Use following API to authorize your session: 
```
POST: http://localhost:34251/login.json
```
And using following account
User 1:
```json
email: admin@gmail.com
password: secret
```
User 2:
```json
email: admin1@gmail.com
password: secret
```
If your email and password are correct, you will receive a generated token. Your response is similar to the following.
```
HTTP Code: 200
```
```json
{
    "success": true,
    "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImV4cCI6MTcxMDk0NzYzMX0.XS9ON0FZ70YnIe19oFVz7eimcJlu4azYuZ0Wp-gTJIc"
    },
    "message": ""
}
```

Otherwise, you will receive error messages in the response.
```
HTTP Code: 400
```
```json
{
    "success": false,
    "message": "Invalid username or password"
}
```
Before head to the next section, you need to add the token to the Postman environment variables. 
- Click on folder title "Cake PHP" and take a look at the right panel.
- Select tab "Variables"
- Change "Current value" of variable TOKEN with the one you received when logged in.
- Just that and you are good to go to the next section.

If you don't add TOKEN as guided, you will get authorization error when trying to send APIs need to be authorized like Add / Edit / Delete /...
```
HTTP Code: 401
```
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

### Article Management
Only get list articles and show article detail can be accessible without authorization.
- Get list articles:
```
GET: http://localhost:34251/articles
```
The response should be like:
```
HTTP Code: 200
```
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "1",
            "body": null,
            "user_id": 1,
            "created_at": "2024-03-20T13:21:39+00:00",
            "updated_at": "2024-03-20T13:22:20+00:00",
            "total_likes": 0
        },
        {
            "id": 2,
            "title": "12",
            "body": null,
            "user_id": 1,
            "created_at": "2024-03-20T13:21:54+00:00",
            "updated_at": "2024-03-20T13:21:54+00:00",
            "total_likes": 0
        }
    ],
    "message": ""
}
```
If it is empty
```
HTTP Code: 200
```
```json
{
    "success": true,
    "data": [],
    "message": ""
}
```

- Get article detail:
```
GET: http://localhost:34251/articles/{id}.json
```
The response:
```
HTTP Code: 200
```
```json
{
    "success": true,
    "data": {
        "id": 2,
        "title": "12",
        "body": null,
        "user_id": 1,
        "created_at": "2024-03-20T13:21:54+00:00",
        "updated_at": "2024-03-20T13:21:54+00:00",
        "total_likes": 0
    },
    "message": ""
}
```

If the id of the article is invalid, you will receive following error
```
HTTP Code: 404
```
```json
{
    "success": false,
    "message": "Record not found in table \"articles\""
}
```
___
#### Using the authorized access token, you will be able to create, update and delete your post and your post only.
*** From now on, the parameters will be placed in the Body tab. Type "Raw" | "JSON"
- Add new article:
```
POST: http://localhost:34251/articles/{id}.json
```
The parameters: 
```json
{
 "title": "abc"
}
```
The response:
```
HTTP Code: 200
```
```json
{
    "success": true,
    "data": {
        "user_id": 1,
        "title": "abc",
        "created_at": "2024-03-20T13:51:29+00:00",
        "updated_at": "2024-03-20T13:51:29+00:00",
        "id": 3
    },
    "message": "The article has been saved."
}

```
If param "title" is empty
```
HTTP Code: 400
```

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "title": "This field cannot be left empty"
  }
}
```

- Edit an article:
```
PUT: http://localhost:34251/articles/{id}.json
```
The parameters
```json
{
 "title": "123"
}
```
The response
```
HTTP Code: 200
```
```json
{
    "success": true,
    "data": {
        "id": 3,
        "user_id": 1,
        "title": "123",
        "body": null,
        "created_at": "2024-03-20T13:51:29+00:00",
        "updated_at": "2024-03-20T13:53:47+00:00"
    },
    "message": "The article has been saved."
}
```
If the article is deleted or not exist
```
HTTP Code: 404
```
```json
{
    "success": false,
    "message": "Record not found in table \"articles\""
}
```

If the article is not owned by current user
```
HTTP Code: 403
```
```json
{
  "success": false,
  "message": "Identity is not authorized to perform `edit` on `App\\Model\\Entity\\Article`."
}
```

- Delete an article
```
DELETE: http://localhost:34251/articles/{id}.json
```
```
HTTP Code: 200
```
```json
{
    "success": true,
    "data": [],
    "message": "The article has been deleted."
}
```
If article is deleted, not exist, or delete another user's article.
```
HTTP Code: 404
```
```json
{
    "success": false,
    "message": "Record not found in table \"articles\""
}
```
If the article is not owned by current user
```
HTTP Code: 403
```
```json
{
  "success": false,
  "message": "Identity is not authorized to perform `edit` on `App\\Model\\Entity\\Article`."
}
```
### Like Feature

To like an article, use following API:
```
POST: http://localhost:34251/articles/{id}/like.json
```
The response
```json
{
    "success": true,
    "data": [],
    "message": "Article liked successfully."
}
```
If user like more than one time
```
HTTP Code: 200
```
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "user_id": {
      "unique": "You can only like an article once."
    }
  }
}

```

If the article doest not exist or get deleted
```
HTTP Code: 404
```
```json
{
"success": false,
"message": "Record not found in table \"articles\""
}

```

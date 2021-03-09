
# Shopping Cart API

Shopping Cart API.

## TECH STACK

- Codeigniter 4
- Php 7.4
- MySQL 5.7
- Nginx
- PhpMyAdmin
- Docker Compose

## REQUIREMENTS

- Docker
- Docker compose

## INSTALLATION

- Clone this repo
- Go to cloned folder
- Copy the env file to .env:

  cp env .env


- Set up Docker:

  docker-compose build app

  docker-compose up -d


- Install composer dependencies:

  docker-compose exec app composer update


- Check PhpMyAdmin works:

  Go to http://<SERVER_IP>:8080, wait till no connection errors show (2 min max)


- Set up initial database tables (IDEMPOTENT process):

  docker-compose exec app php /var/www/html/public/index.php api/v1/initialize


- You're ready to use the App running at http://<SERVER_IP>:8000 or PhpMyAdmin at http://<SERVER_IP>:8080

## ENDPOINTS

    - Welcome endpoint:
        GET http://<SERVER_IP>:8000/api/v1

    - Endpoint Add User
        POST http://<SERVER_IP>:8000/api/v1/users
        
        Body example:
        {
            "Name": "John",
            "Email": "john@email.com"
        }

    - Endpoint Add Seller
        POST http://<SERVER_IP>:8000/api/v1/sellers
        
        Body example:
        {
            "Name": "Farma 1",
            "Email": "info@farma1.com"
        }

    - Endpoint Delete Seller
        DELETE http://<SERVER_IP>:8000/api/v1/sellers/{SellerId}

    - Endpoint Add Products linked to a Seller 
        POST http://<SERVER_IP>:8000/api/v1/sellers/products
        
        Body example:
        {
            "SellerId": 1,
            "CodeIdentifier": "#A1C1",
            "Name": "Medicine1",
            "Price": 14.50
        }

    - Endpoint Delete Products linked to a Seller
        DELETE http://<SERVER_IP>:8000/api/v1/sellers/products/{SellerProductId}

    - Endpoint Add Products to a Cart
        POST http://<SERVER_IP>:8000/api/v1/carts/products
        
        Body example:
        {
            "UserId": 1,
            "SellerProductId": 1,
            "Quantity": 3
        }

    - Endpoint Delete Products of a Cart
        DELETE http://<SERVER_IP>:8000/api/v1/carts/products/{CartProductId}

    - Endpoint Get total amount of the Cart
        GET http://<SERVER_IP>:8000/api/v1/carts/users/{UserId}/amount

    - Endpoint Increase the number of units of a Product
        PATCH http://<SERVER_IP>:8000/api/v1/carts/increase/products

        Body example:
        {
            "CartProductId": 1,
            "Quantity": 5
        }

    - Endpoint Decrease the number of units of a Product
        PATCH http://<SERVER_IP>:8000/api/v1/carts/decrease/products

        Body example:
        {
            "CartProductId": 1,
            "Quantity": 5
        }

    - Endpoint Delete the entire Cart
        DELETE http://<SERVER_IP>:8000/api/v1/carts/users/{UserId}

    - Endpoint Confirm Cart -> commit to buy
        POST http://<SERVER_IP>:8000/api/v1/carts/commit

        Body exmaple:
        {
            "UserId": 1
        }

## NOTES

- Deletions that involve elements linked to others have not been tackled.

## PRINCIPLES APPLIED

- Use of framework (CodeIgniter 4) with MVC (Model-View-Controller) pattern
- Use of dependency manager (Composer)
- Use of container virtualization (Docker & Docker Compose)
- SOLID principles
- OPP (Object-Oriented Programming)
- ORM queries (Eloquent ORM) and raw queries when necessary
- ACID properties (transactions, ...)
- Database normalization (till 4th normal form)
- API standards (see corresponding section for more info, not all have been applied)
- Repo documentation clear
- Code properly commented

## API PRINCIPLES

- Nouns and non-verbs: /products/12345
- Do not include a slash at the end of the endpoints
- The separator bar in URIs should be used to reflect a semantic relationship
- The proper http methods (GET, POST, PUT / PATCH, DELETE)
- Suitable http codes: 200 OK, ...
- Plurals: products, ...
- Parameters: /products/?name="ABC"
- API standards & documentation: OpenApi Specification 3
- A versioning system: /api/v1/customers
- Filtering and paging: /api/v1/customers?page=1
- REST AND HATEOAS
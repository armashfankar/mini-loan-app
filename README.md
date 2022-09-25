# Mini Loan Application

### Problem Statment

- It is an app that allows authenticated users to go through a loan application. It doesn’t have to contain too many fields, but at least “amount
required” and “loan term.” All the loans will be assumed to have a “weekly” repayment frequency.

- After the loan is approved, the user must be able to submit the weekly loan repayments. It can be a simplified repay functionality, which won’t
need to check if the dates are correct but will just set the weekly amount to be repaid.

### Prerequisites
Here's a basic setup:

* Apache2 / Nginx
* PHP 7 - All the code has been tested against PHP 7.4
* Laravel (8.0) (Laravel Components ^8.0)
* Mysql (5.x), running locally
* Composer 2.x

### Execution

1. Clone the repository:
    ```shell script
    git clone https://github.com/armashfankar/mini-loan-app

    ```

2. Install the requirements for the repository using the `composer`:
   ```shell script
    cd mini-loan-app/
    composer install
    ```

3. Copy `.env.example` to create `.env` file:
    ```shell script
    cp .env.example .env
    ```

4. Generate keys:
    ```shell script
    php artisan key:generate
    ```

5. Configure Database Driver in `.env` file:
    
    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=aspire_loans
    DB_USERNAME=root
    DB_PASSWORD=

6. Create MySQL Database:
     ```shell script
    mysql -u root -p
    create database aspire_loans;
    ```

7. Migrate database:
    ```shell script
    php artisan migrate
    php artisan db:seed
    ```

8. Run Laravel in terminal:
    ```shell script
    php artisan serve
    ```

### Rest APIs

    ```shell script
    Postman Collection Import: https://www.getpostman.com/collections/aea9b31d4c201288b5d9
    ```

    ```shell script
    User Register:
    [POST] http://localhost:8000/api/register
    
    User Login:
    [POST] http://localhost:8000/api/login

    Admin Login:
        Default Credentials:
            email: admin@miniloan.com
            password: asdasdasd
    [POST] http://localhost:8000/api/admin/login

    Logout:
    [POST] http://localhost:8000/api/logout

    Loan Request:
    [POST] http://localhost:8000/api/loan

    Loan Repay / Admin Approval (if logged in as admin):
    [POST] http://localhost:8000/api/loan/ASP6146382789476274
    
    Delete Loan:
    [Delete] http://localhost:8000/api/loan/ASP6461371917745631

    My Loans:
    [GET] http://localhost:8000/api/loan
    ```
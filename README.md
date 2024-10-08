# Swimmy - Pool Rental API

Swimmy is an API that connects individuals for renting swimming pools. Pool owners can list their pools for rent, and users can book pools based on availability.

## Features

- **User Management**: Users can sign up, log in, and access platform features.
- **Pool Management**: Owners can add, edit, and delete their pools. Users can browse available pools.
- **Reservation System**: Users can book a pool for specific dates.
- **Security**: Access is restricted based on user roles (`ROLE_USER` and `ROLE_ADMIN`).
- **Admin Approval**: Pool creation, editing, and deletion by users must be validated by an admin.

## Tech Stack

- **Symfony** (version 7): Backend framework used for API management.
- **API Platform** (version 3.3): Provides API resources, routing, and Swagger documentation.
- **PHP** (version 8.3): Backend language.
- **Doctrine ORM**: Manages database operations.
- **JWT/OAuth2**: Used for user authentication.
- **PHPUnit**: For unit testing.

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/swimmy-api.git
   cd swimmy-api
   
2. Install dependencies:
   ```bash
   composer install

3. Configure your database in the .env file:
   ```bash
   DATABASE_URL="mysql://username:password@127.0.0.1:3306/swimmy_db"

4. Run migrations to create the database tables:
   ```bash
   php bin/console doctrine:migrations:migrate

5. Start the Symfony server:
   ```bash
   symfony server:start

6. Access the API: The API will be available at http://localhost:8000/api.

## Testing
   To run unit tests:
      ```bash
      php bin/phpunit


## API Documentation
   The API is documented with Swagger, available at:
      ```bash
      http://localhost:8000/api/docs

## API Endpoints:

- **GET** /api/pools: Retrieve a list of available pools.
- **POST** /api/pools: Add a new pool (restricted to logged-in users).
- **PATCH** /api/pools/{id}: Update a pool (restricted to owners or admins).
- **DELETE** /api/pools/{id}: Delete a pool (restricted to owners or admins).
- **GET** /api/pools/{id}: Get a specific pool.

## Security & Roles
- Logged-in users with the role ROLE_USER can view and create pools.
- Administrators with the role ROLE_ADMIN can approve, modify, or delete any pool.

## Roadmap
- Implement a payment system for pool reservations.
- Add a review and rating system for pools.
- Integrate a more detailed availability calendar.

## Contributing
Contributions are welcome! For suggestions or bug reports, feel free to create an issue or submit a pull request.

1. Fork the project.
2. Create your feature branch (git checkout -b feature/new-feature).
3. Commit your changes (git commit -am 'Add new feature').
4. Push to the branch (git push origin feature/new-feature).
5. Open a pull request.

## Author
Barsali boumediene - API Symfony Developer and creator of Swimmy.

## License
This project is licensed under the MIT License. See the LICENSE file for details.
   ```bash 
   This README provides a structured overview of your project and instructions for potential contributors, covering the purpose, setup, and contribution guidelines.

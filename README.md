Kazi Hustle

Kazi Hustle is a freelance job posting platform where clients can post jobs and receive quotes from freelancers. The platform ensures that clients are matched with the right candidates while providing freelancers with the opportunity to bid on jobs that match their skills.
Table of Contents

    Getting Started
    Technologies Used
    Installation
    Usage
    API Endpoints
    Contributing
    Contact

Getting Started

To get a local copy of Kazi Hustle up and running, follow these steps.
Prerequisites

    Node.js (version 14.x or higher)
    npm (Node package manager)

Installation

    Clone the repository:

    bash

git clone https://github.com/lorded/kazi-hustle.git

Navigate to the project directory:

bash

cd kazi-hustle

Install dependencies:

bash

npm install

Create a .env file in the root directory and configure the following environment variables:

makefile

PORT=3000
DATABASE_URL=your-database-url
SECRET_KEY=your-secret-key

Run the project:

bash

    npm start

    Access the platform at http://localhost:3000.

Technologies Used

    Frontend: HTML, CSS, Bootstrap
    Backend: Node.js, Express.js
    Database: MongoDB
    Authentication: JWT (JSON Web Tokens)
    Templating Engine: EJS

Usage
Posting a Job

    Create an account or log in.
    Navigate to the job posting form and fill in the job details, including the job title, description, budget, and timeline.
    Post the job, and freelancers will be able to send quotes.

Freelancers

Freelancers can:

    Search for jobs by category
    Send quotes for jobs they're interested in
    Manage their profile and job history

API Endpoints

The following are some of the key API endpoints for the platform:
Method	Endpoint	Description
POST	/api/jobs	Create a new job post
GET	/api/jobs	Get a list of job posts
GET	/api/jobs/:id	Get details of a specific job
POST	/api/auth/register	Register a new user
POST	/api/auth/login	Log in an existing user
Contributing

Contributions are welcome! Please follow the steps below to contribute to Kazi Hustle:

    Fork the repository.

    Create a new branch:

    bash

git checkout -b feature/your-feature-name

Make your changes.

Commit your changes:

bash

git commit -m "Add your commit message"

Push to the branch:

bash

    git push origin feature/your-feature-name

    Open a pull request on the main repository.

Contact

For any inquiries or suggestions, feel free to reach out:

    Email: dishonmigwi6@gmail.com
    GitHub: lorded

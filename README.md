# fabricon.immediate.co.uk
The Fabric Conference Site.

## Running Locally
Make sure you have [Docker](https://www.docker.com/), [Docker Compose](https://docs.docker.com/compose/),
and [Yarn](https://yarnpkg.com/) installed.


Clone the repo and run the following commands to get the site running locally:
```shell
# Build the Docker images
docker compose build

# Start the containers
docker compose up -d

# Install the Yarn dependencies
yarn install

# Start the Webpack dev server to build frontend assets and to enable HMR
yarn dev-server
```

The site should now be running at http://localhost:8000.

The Admin area can be accessed at http://localhost:8000/admin. Log in with the following credentials:
- Username: admin
- Password: password

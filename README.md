# fabricon.immediate.co.uk [![Laravel Forge Site Deployment Status](https://img.shields.io/endpoint?url=https%3A%2F%2Fforge.laravel.com%2Fsite-badges%2F490a7389-8a77-4100-933f-e289d11498e3%3Fdate%3D1%26commit%3D1&style=flat)](https://forge.laravel.com/servers/435083/sites/1251274)
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

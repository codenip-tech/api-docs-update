# API DOCS UPDATE

YouTube video link: 

## Instructions
- `make build` to build the containers
- `make start` to start the containers
- `make stop` to stop the containers
- `make restart` to restart the containers
- `make prepare` to install dependencies with composer
- `make logs` to see application logs
- `make ssh` to SSH into the application container
- Once you have installed you Symfony application go to http://localhost:1000/api/docs

### Supervisor config
```
[program:app]
command=/root/binaries/app/app php-server --domain [your_domain_here]
autostart=true
autorestart=true
stderr_logfile=/root/binaries/app/logs/logfile.err.log
stdout_logfile=/root/binaries/app/logs/logfile.out.log
```

### Deploy script
```
# Stop app
supervisorctl stop app

# Extract binary file from archive
gzip -d /root/download/build/app.gz

# Move binary to application folder
mv -f /root/download/build/app /root/binaries/app

# Update supervisor config
supervisorctl reread
supervisorctl update

# Start updated app
supervisorctl start app
```

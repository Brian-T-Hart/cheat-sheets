# Docker Cheatsheet
*After setting up your Dockerfile you can use the following docker commands*

---

## Images

### Build Container Image
```bash
$ docker build -t <name> <path>
```

---

## Containers

### Get ID of container
Use the a flag to display all
```bash
$ docker ps -a
```

### Start Container
Start the container using the docker run. The d flag runs the container in "detached" mode and the p flag is used to create a mapping between the host port and container port.
```bash
$ docker run -dp 3000:3000 <name>
```

### Start Container with Volume
```bash
$ docker run -dp 3000:3000 -v <volume-name>:<file-path> <container-name>

Example
$ docker run -dp 3000:3000 -v todo-db:/etc/todos getting-started
```

### Stop Container
```bash
$ docker stop <container-id>
```

### Remove Container
Once a container has been stopped, you can remove it using rm
```bash
$ docker rm <container-id>
```

### Stop and Remove Container
```bash
$ docker rm -f <container-id>
```

### Tag A Container
Use the docker tag command to give the image a new name
```bash
$ docker tag <container-name> <username/container-name>
```

---

## Volumes

### Creat A Volume
```bash
$ docker volume create <volume-name>
```

### Inspect A Volume
```bash
$ docker volume inspect <volume-name>
```

---

## Networks

### Create A Network
```bash
$ docker network create <network-name>
```

---

## Logs
You can watch the logs with the logs command
```bash
$ docker logs -f <container-id>
```
---

## Bind Mounts
[view docs](https://docs.docker.com/get-started/06_bind_mounts/)
```bash
Example:
$ docker run -dp 3000:3000 -w /app -v "$(pwd):/app" node:12-alpine sh -c "yarn install && yarn run dev"
```
---

## Docker Hub
### Login to Docker Hub
```bash
$ docker login -u <username>
```

### Push to Docker Hub
```bash
$ docker push <username/container-name>
```




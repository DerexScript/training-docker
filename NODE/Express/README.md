# training docker with node - Express
simple http server made with express

# instructions
- first build a project image
```sh
docker build -t hubuser/nestjs:1.0 .
```

- then build a container from the image
```sh
docker run --name front-nestjs -d -p 3000:3000 --volume "$(pwd):/usr/src/app-nestjs" hubuser/nestjs:1.0
```
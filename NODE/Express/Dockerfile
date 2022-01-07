FROM node:alpine

WORKDIR /usr/app

COPY . .

RUN npm install

EXPOSE 3000

CMD [ "node", "main.js" ]

VOLUME "$(pwd)" /usr/app

#docker build -t hubuser/nestjs:1.0 .
#docker run --name front-nestjs -d -p 3000:3000 --volume "$(pwd):/usr/app" hubuser/nestjs:1.0
#docker run --name front-nestjs -d -p 3000:3000 hubuser/nestjs:1.0
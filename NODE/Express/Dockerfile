FROM node:alpine

WORKDIR /usr/src/app-nestjs

COPY . .

RUN npm install

EXPOSE 3000

CMD [ "node", "main.js" ]
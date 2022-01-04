FROM mysql:5.6

RUN apt-get update

RUN apt-get install -y iputils-ping

RUN apt-get clean
RUN apt-get autoclean

COPY ./mysql-dump/CRUD_PHP.sql /docker-entrypoint-initdb.d/

#COPY ./docker/dbbkp/ /root/db/

#RUN /root/db/verifyTable.sh people


#/bin/bash
docker-compose down
docker system prune -f
chmod -R 777 ./docker/data-mysql
#rm -rf ./docker/data-mysql
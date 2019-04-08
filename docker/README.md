# compile an image

```
docker build -t yonkoma .
```

# run image

下面 `/path/to/wwwdir` 的地方可以換成自己想 mount 的資料夾

```
docker run -itd \
-p 80:80 \
--name yonkoma-test \
--hostname yonkoma-test \
-v /path/to/wwwdir:/var/www/html:Z \
yonkoma

docker cp ../nginx/php yonkoma-test:/etc/nginx/php
docker cp ../nginx/sample_site yonkoma-test:/etc/nginx/sites-available/default
docker exec -it yonkoma-test /etc/init.d/nginx restart
docker exec -it yonkoma-test /etc/init.d/php7.0-fpm start
```

# stop 

```
docker stop yonkoma-test
```

# remove and rebuild

```
docker stop yonkoma-test
docker rm yonkoma-test
docker image rm yonkoma
```

# bash

```
docker exec -it yonkoma-test bash
```
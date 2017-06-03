username = skocic
name = php71
all: image tag push
image:
	docker build -t $(name) .
tag:
	docker tag $$(docker images -q $(name) | head -n 1) $(username)/$(name)
push:
	docker push $(username)/$(name)
exec:
	docker exec -it $(name) /bin/bash
run:
	docker run -it $(name) /bin/bash
test:
	vendor/bin/phpunit
build:
	ant -Dftp.server=${FTP_SERVER} -Dftp.user=${FTP_USER} -Dftp.password=${FTP_PASSWORD}

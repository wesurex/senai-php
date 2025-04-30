# Sobe os containers em segundo plano
up:
	docker-compose -p $(PROJECT_NAME) up -d

# Para e remove os containers, volumes e rede
down:
	docker-compose -p $(PROJECT_NAME) down -v

# Mata todos os containers Docker em execução no sistema
killdb:
	sudo docker kill $$(sudo docker ps -q)
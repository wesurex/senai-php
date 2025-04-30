@echo off
SET PROJECT_NAME=meu_projeto_php

IF "%1"=="up" (
    docker-compose -p %PROJECT_NAME% up -d
) ELSE IF "%1"=="down" (
    docker-compose -p %PROJECT_NAME% down -v
) ELSE IF "%1"=="killdb" (
    FOR /F %%i IN ('docker ps -q') DO docker kill %%i
) ELSE (
    echo Comando inválido. Use: up, down ou killdb
)

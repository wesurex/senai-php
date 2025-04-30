# 🐳 Ambiente PHP + MySQL + phpMyAdmin com Docker

Este projeto configura um ambiente completo de desenvolvimento com **PHP**, **MySQL** e **phpMyAdmin** usando **Docker** e **Docker Compose**.

---

## 📁 Estrutura do Projeto

```
meu_projeto_php/
├── docker-compose.yml
└── src/
    └── index.php
```

---

## 🚀 Como usar

### ✅ Pré-requisitos

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)

---

## 💻 Passos para rodar o projeto

### 🪟 Windows

1. Instale o [Docker Desktop para Windows](https://www.docker.com/products/docker-desktop/).
2. Clone este repositório:
   ```bash
   git clone https://github.com/seu-usuario/meu_projeto_php.git
   cd meu_projeto_php
   ```
3. Inicie os containers:
   ```bash
   docker-compose up -d
   ```
4. Acesse no navegador:
   - PHP: http://localhost:8080
   - phpMyAdmin: http://localhost:8081  
     - Servidor: `db`
     - Usuário: `root`
     - Senha: `root`

---

### 🐧 Ubuntu

1. Instale o Docker:
   ```bash
   sudo apt update
   sudo apt install docker.io docker-compose -y
   sudo systemctl enable docker
   sudo systemctl start docker
   ```
2. Clone este repositório:
   ```bash
   git clone https://github.com/seu-usuario/meu_projeto_php.git
   cd meu_projeto_php
   ```
3. Inicie os containers:
   ```bash
   docker-compose up -d
   ```
4. Acesse no navegador:
   - PHP: http://localhost:8080
   - phpMyAdmin: http://localhost:8081  
     - Servidor: `db`
     - Usuário: `root`
     - Senha: `root`

---

## 🧼 Como parar o ambiente

Para desligar os containers:

```bash
docker-compose down
```

---

## 🗂️ Volumes e Dados

- Os dados do MySQL são persistidos em volume Docker: `db_data`
- O código PHP está na pasta `src/`

---

## 🛠️ Customizações

- Altere o PHP na pasta `src/`
- Modifique variáveis no `docker-compose.yml` para customizar credenciais ou portas

---

## 📬 Dúvidas?

Abra uma **issue** aqui no repositório ou entre em contato.

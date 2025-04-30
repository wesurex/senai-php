# 📘 Projeto: Ambiente de Programação com PHP, MySQL e phpMyAdmin usando Docker

Bem-vindo(a)! Este guia foi feito especialmente para **alunos do curso técnico do SENAI**, mesmo que **nunca tenham programado antes**. Aqui você vai aprender a rodar um ambiente de desenvolvimento moderno com **PHP**, **MySQL** (banco de dados) e **phpMyAdmin** (interface para acessar o banco) usando **Docker**, uma ferramenta que facilita a instalação e execução de programas.

---

## 🤔 O que é esse projeto?

É um pacote que cria, de forma automática:
- Um servidor que roda código PHP (linguagem usada em sites);
- Um banco de dados MySQL;
- Um painel web chamado phpMyAdmin para ver os dados no navegador;
- Tudo isso funcionando sem precisar instalar manualmente cada programa.

---

## ✅ O que você precisa instalar primeiro

### No Windows

1. Instale o **Docker Desktop**:
   👉 [https://www.docker.com/products/docker-desktop/](https://www.docker.com/products/docker-desktop/)

2. Depois de instalar, **reinicie o computador**.

### No Ubuntu

Abra o terminal e digite os comandos abaixo:

```bash
sudo apt update
sudo apt install docker.io docker-compose -y
sudo systemctl enable docker
sudo systemctl start docker
```

---

## 📦 Baixe os arquivos do projeto

1. Clique no botão de download ou use este comando se já souber usar Git:
```bash
git clone https://github.com/seu-usuario/meu_projeto_php.git
cd meu_projeto_php
```

---

## ▶️ Como iniciar o projeto

Abra o terminal ou prompt de comando na pasta do projeto e digite:

```bash
docker-compose up -d
```

Isso vai:
- Criar os containers (ambientes isolados que rodam os sistemas)
- Subir o PHP, o MySQL e o phpMyAdmin

---

## 🌐 Acesse no seu navegador

- Página PHP: [http://localhost:8080](http://localhost:8080)
- Painel phpMyAdmin: [http://localhost:8081](http://localhost:8081)

**Login do phpMyAdmin:**
- Servidor: `db`
- Usuário: `root`
- Senha: `root`

---

## 🧪 Testando se funcionou

Abra o navegador e vá para [http://localhost:8080](http://localhost:8080)

Se aparecer a frase `Ambiente PHP rodando no Docker com sucesso!`, deu tudo certo! 🎉

---

## 🛑 Como parar tudo

Se quiser desligar os serviços, digite:

```bash
docker-compose down
```

---

## 🛠️ Personalize seu código

Os arquivos PHP estão na pasta `src/`. Você pode abrir e editar o `index.php` com qualquer editor de texto, como o **VS Code** ou **Notepad++**.

---

## ❓ Dúvidas Frequentes

**1. O que é Docker?**  
É uma ferramenta que cria ambientes isolados (chamados "containers") para rodar programas de forma rápida e padronizada.

**2. O que é PHP?**  
É uma linguagem de programação usada em muitos sites.

**3. O que é MySQL?**  
É um banco de dados onde guardamos informações, como cadastros, produtos, etc.

**4. O que é phpMyAdmin?**  
É uma página onde você pode ver e editar os dados do banco de forma visual, como se fosse uma planilha.

---

## 👨‍🏫 Precisa de ajuda?

Se você é aluno do SENAI, fale com seu instrutor ou poste sua dúvida na plataforma do curso. Boa prática! 💡
